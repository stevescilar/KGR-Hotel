<?php

namespace App\Http\Controllers;

use App\Models\{GiftCard, Payment};
use App\Services\{MpesaService, SmsService};
use Illuminate\Http\{Request, RedirectResponse};
use Illuminate\View\View;

class GiftCardController extends Controller
{
    public function index(): View
    {
        return view('public.gift-cards');
    }

    public function purchase(Request $request): RedirectResponse
    {
        $request->validate([
            'amount'               => 'required|integer|min:1000',
            'purchased_by_name'    => 'required|string|max:100',
            'purchased_by_email'   => 'required|email',
            'recipient_name'       => 'required|string|max:100',
            'recipient_email'      => 'nullable|email',
            'message'              => 'nullable|string|max:300',
            'expires_months'       => 'nullable|integer|in:3,6,12',
        ]);

        $expires = $request->expires_months
            ? now()->addMonths($request->expires_months)->toDateString()
            : null;

        $card = GiftCard::create([
            'original_value'      => $request->amount,
            'remaining_value'     => $request->amount,
            'purchased_by_name'   => $request->purchased_by_name,
            'purchased_by_email'  => $request->purchased_by_email,
            'recipient_name'      => $request->recipient_name,
            'recipient_email'     => $request->recipient_email,
            'message'             => $request->message,
            'expires_at'          => $expires,
            'status'              => 'inactive', // activated after payment
        ]);

        session(['gift_card_id' => $card->id]);

        // Redirect to M-Pesa payment (reuse booking flow pattern)
        return redirect()->route('gift-cards.index')
            ->with('gift_card', $card)
            ->with('pending_payment', true);
    }

    public function redeem(Request $request): RedirectResponse
    {
        $request->validate(['code' => 'required|string']);

        $card = GiftCard::where('code', strtoupper(trim($request->code)))->first();

        if (!$card) {
            return back()->withErrors(['code' => 'Gift card not found.']);
        }

        if (!$card->isValid()) {
            return back()->withErrors(['code' => 'This gift card is expired, used up, or inactive.']);
        }

        return back()->with('valid_card', $card);
    }
}
