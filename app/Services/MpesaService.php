<?php

namespace App\Services;

use App\Models\{Booking, Payment};
use Illuminate\Support\Facades\{Http, Log};

class MpesaService
{
    private ?string $consumerKey;
    private ?string $consumerSecret;
    private ?string $shortcode;
    private ?string $passkey;
    private ?string $callbackUrl;
    private bool $sandbox;

    public function __construct()
    {
        $this->consumerKey    = config('mpesa.consumer_key');
        $this->consumerSecret = config('mpesa.consumer_secret');
        $this->shortcode      = config('mpesa.shortcode');
        $this->passkey        = config('mpesa.passkey');
        $this->callbackUrl    = config('mpesa.callback_url');
        $this->sandbox        = config('mpesa.sandbox', true);
    }

    private function baseUrl(): string
    {
        return $this->sandbox
            ? 'https://sandbox.safaricom.co.ke'
            : 'https://api.safaricom.co.ke';
    }

    /**
     * Get OAuth access token from Safaricom.
     */
    public function getAccessToken(): string
    {
        $response = Http::withBasicAuth($this->consumerKey, $this->consumerSecret)
            ->get("{$this->baseUrl()}/oauth/v1/generate?grant_type=client_credentials");

        if ($response->failed()) {
            throw new \Exception('Failed to get M-Pesa access token: ' . $response->body());
        }

        return $response->json('access_token');
    }

    /**
     * Initiate an STK Push (Lipa Na M-Pesa Online).
     *
     * @param  string $phone   Phone number e.g. 0712345678 or +254712345678
     * @param  int    $amount  Amount in KES (whole number)
     * @param  string $reference  Account reference shown on phone
     * @param  string $description  Transaction description
     */
    public function stkPush(string $phone, int $amount, string $reference, string $description): array
    {
        $token     = $this->getAccessToken();
        $timestamp = now()->format('YmdHis');
        $password  = base64_encode($this->shortcode . $this->passkey . $timestamp);

        // Normalize to 254XXXXXXXXX
        $phone = ltrim($phone, '+');
        $phone = preg_replace('/^0/', '254', $phone);
        $phone = preg_replace('/[^0-9]/', '', $phone);

        $payload = [
            'BusinessShortCode' => $this->shortcode,
            'Password'          => $password,
            'Timestamp'         => $timestamp,
            'TransactionType'   => 'CustomerPayBillOnline',
            'Amount'            => $amount,
            'PartyA'            => $phone,
            'PartyB'            => $this->shortcode,
            'PhoneNumber'       => $phone,
            'CallBackURL'       => $this->callbackUrl,
            'AccountReference'  => substr($reference, 0, 12),
            'TransactionDesc'   => substr($description, 0, 13),
        ];

        $response = Http::withToken($token)
            ->post("{$this->baseUrl()}/mpesa/stkpush/v1/processrequest", $payload);

        Log::info('MPesa STK Push', [
            'reference' => $reference,
            'phone'     => $phone,
            'amount'    => $amount,
            'response'  => $response->json(),
        ]);

        return $response->json();
    }

    /**
     * Query the status of an STK Push transaction.
     */
    public function queryStatus(string $checkoutRequestId): array
    {
        $token     = $this->getAccessToken();
        $timestamp = now()->format('YmdHis');
        $password  = base64_encode($this->shortcode . $this->passkey . $timestamp);

        $response = Http::withToken($token)
            ->post("{$this->baseUrl()}/mpesa/stkpushquery/v1/query", [
                'BusinessShortCode' => $this->shortcode,
                'Password'          => $password,
                'Timestamp'         => $timestamp,
                'CheckoutRequestID' => $checkoutRequestId,
            ]);

        return $response->json();
    }

    /**
     * Handle the M-Pesa STK callback. Updates the Payment and Booking accordingly.
     */
    public function handleCallback(array $payload): void
    {
        $body      = $payload['Body']['stkCallback'] ?? [];
        $resultCode = (int) ($body['ResultCode'] ?? 1);

        Log::info('MPesa Callback received', ['result_code' => $resultCode]);

        if ($resultCode !== 0) {
            Log::warning('MPesa payment failed/cancelled', $body);
            // Mark the pending payment as failed
            $checkoutId = $body['CheckoutRequestID'] ?? null;
            if ($checkoutId) {
                Payment::where('provider_reference', $checkoutId)
                    ->where('status', 'pending')
                    ->update(['status' => 'failed', 'provider_response' => $payload]);
            }
            return;
        }

        // Parse metadata items
        $items    = collect($body['CallbackMetadata']['Item'] ?? []);
        $metadata = $items->pluck('Value', 'Name');

        $mpesaRef  = $metadata->get('MpesaReceiptNumber');
        $amount    = (float) $metadata->get('Amount');
        $phone     = (string) $metadata->get('PhoneNumber');
        $checkoutId= $body['CheckoutRequestID'];

        // Find the pending payment by CheckoutRequestID stored in provider_reference
        $payment = Payment::where('provider_reference', $checkoutId)
            ->where('status', 'pending')
            ->first();

        if (!$payment) {
            Log::error('MPesa callback: no matching payment found', ['checkout_id' => $checkoutId]);
            return;
        }

        // Complete the payment
        $payment->update([
            'status'             => 'completed',
            'provider_reference' => $mpesaRef,
            'provider_response'  => $payload,
            'paid_at'            => now(),
        ]);

        // Update the booking's paid amount + payment status
        if ($payment->payable instanceof Booking) {
            $booking   = $payment->payable;
            $totalPaid = $booking->payments()
                ->where('status', 'completed')
                ->sum('amount');

            $booking->update([
                'paid_amount'    => $totalPaid,
                'payment_status' => $totalPaid >= $booking->total_amount ? 'paid' : 'partial',
            ]);

            if ($totalPaid >= $booking->total_amount) {
                app(BookingService::class)->confirmBooking($booking);

                // Send SMS confirmation
                app(SmsService::class)->sendBookingConfirmation($booking);
            }
        }
    }
}