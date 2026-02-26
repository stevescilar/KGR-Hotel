<?php

namespace App\Services;

use App\Models\Booking;
use Illuminate\Support\Facades\{Http, Log};

class SmsService
{
    private string $username;
    private string $apiKey;
    private string $from;

    public function __construct()
    {
        $this->username = config('africastalking.username', 'sandbox');
        $this->apiKey   = config('africastalking.api_key', '');
        $this->from     = config('africastalking.from', 'KGR');
    }

    /**
     * Send a raw SMS message.
     */
    public function send(string $to, string $message): array
    {
        // Normalize phone to international format
        $to = ltrim($to, '+');
        $to = preg_replace('/^0/', '254', $to);
        $to = '+' . preg_replace('/[^0-9]/', '', $to);

        $response = Http::withHeaders([
            'apiKey'       => $this->apiKey,
            'Accept'       => 'application/json',
            'Content-Type' => 'application/x-www-form-urlencoded',
        ])->asForm()->post('https://api.africastalking.com/version1/messaging', [
            'username' => $this->username,
            'to'       => $to,
            'message'  => $message,
            'from'     => $this->from,
        ]);

        Log::info('SMS sent', ['to' => $to, 'status' => $response->status()]);

        return $response->json() ?? [];
    }

    /**
     * Send booking confirmation SMS.
     */
    public function sendBookingConfirmation(Booking $booking): void
    {
        $guest = $booking->guest;

        if (!$guest->phone) return;

        $msg = "Hi {$guest->first_name}! Your booking at Kitonga Garden Resort is CONFIRMED.\n"
             . "Ref: {$booking->booking_ref}\n"
             . "Check-in: {$booking->check_in->format('D, M j Y')}\n"
             . "Room: {$booking->room->roomType->name}\n"
             . "We look forward to hosting you! For help call: +254 113 262 688";

        $this->send($guest->phone, $msg);
    }

    /**
     * Send check-in reminder SMS (day before arrival).
     */
    public function sendCheckInReminder(Booking $booking): void
    {
        $guest = $booking->guest;
        if (!$guest->phone) return;

        $msg = "Hi {$guest->first_name}! Reminder: You check in tomorrow at Kitonga Garden Resort.\n"
             . "Ref: {$booking->booking_ref} · Check-in from 2:00 PM.\n"
             . "Need directions? Call +254 113 262 688";

        $this->send($guest->phone, $msg);
    }

    /**
     * Send gate ticket confirmation SMS.
     */
    public function sendTicketConfirmation(\App\Models\GateTicket $ticket): void
    {
        if (!$ticket->guest_phone) return;

        $msg = "Hi {$ticket->guest_name}! Your Kitonga Garden Resort ticket is confirmed.\n"
             . "Ticket: {$ticket->ticket_number}\n"
             . "Date: {$ticket->visit_date->format('D, M j Y')}\n"
             . "Qty: {$ticket->quantity} × {$ticket->ticketType->name}\n"
             . "Present this SMS or QR code at the gate.";

        $this->send($ticket->guest_phone, $msg);
    }
}
