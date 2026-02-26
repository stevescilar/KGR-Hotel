<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GiftCard;
use Illuminate\Http\{Request, RedirectResponse};
use Illuminate\View\View;

class GiftCardController extends Controller
{
    public function index(Request $request): View
    {
        $giftCards = GiftCard::when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(30)
            ->withQueryString();

        $totalOutstanding = GiftCard::where('status', 'active')->sum('remaining_value');

        return view('admin.gift-cards.index', compact('giftCards', 'totalOutstanding'));
    }

    public function show(GiftCard $giftCard): View
    {
        return view('admin.gift-cards.show', compact('giftCard'));
    }
}
