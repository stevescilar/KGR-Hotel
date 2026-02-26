<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LoyaltyController extends Controller
{
    /**
     * Show the loyalty dashboard for the authenticated guest.
     * Guests are linked via email match between User and Guest records.
     */
    public function index(Request $request): View
    {
        $guest = Guest::where('email', $request->user()->email)->firstOrFail();

        $recentTransactions = $guest->loyaltyTransactions()
            ->latest()
            ->take(5)
            ->get();

        $tierThresholds = [
            'bronze' => 1000,
            'silver' => 5000,
            'gold'   => 10000,
        ];

        return view('public.loyalty.index', compact('guest', 'recentTransactions', 'tierThresholds'));
    }

    public function transactions(Request $request): View
    {
        $guest = Guest::where('email', $request->user()->email)->firstOrFail();

        $transactions = $guest->loyaltyTransactions()
            ->latest()
            ->paginate(20);

        return view('public.loyalty.transactions', compact('guest', 'transactions'));
    }
}
