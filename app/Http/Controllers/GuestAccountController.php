<?php

namespace App\Http\Controllers;

use App\Models\{Guest, User};
use Illuminate\Http\{Request, RedirectResponse};
use Illuminate\View\View;
use Illuminate\Support\Facades\{Hash, Auth};

class GuestAccountController extends Controller
{
    /**
     * Show the account creation form using the token sent via SMS.
     */
    public function create(string $token): View|RedirectResponse
    {
        $guest = Guest::where('account_token', $token)
            ->where('account_token_expires_at', '>', now())
            ->firstOrFail();

        if ($guest->user_id) {
            return redirect()->route('login')->with('status', 'Your account already exists. Please sign in.');
        }

        return view('public.account.create', compact('guest', 'token'));
    }

    /**
     * Create the user account and log them in.
     */
    public function store(Request $request, string $token): RedirectResponse
    {
        $guest = Guest::where('account_token', $token)
            ->where('account_token_expires_at', '>', now())
            ->firstOrFail();

        $request->validate([
            'password'              => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required',
        ]);

        // Create User account
        $user = User::create([
            'name'     => $guest->first_name . ' ' . $guest->last_name,
            'email'    => $guest->email,
            'password' => Hash::make($request->password),
        ]);

        // Link guest to user and clear token
        $guest->update([
            'user_id'                  => $user->id,
            'account_token'            => null,
            'account_token_expires_at' => null,
        ]);

        // Log them in
        Auth::login($user);

        return redirect()->route('loyalty.index')
            ->with('success', "Welcome, {$guest->first_name}! Your account is set up. Your booking points have been added.");
    }
}