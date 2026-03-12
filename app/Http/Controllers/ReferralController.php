<?php

namespace App\Http\Controllers;

use App\Models\ReferralCode;
use Illuminate\Http\Request;

class ReferralController extends Controller
{
    /**
     * Handle a referral link click.
     * Increments the click counter, stores code in session, and redirects to registration.
     */
    public function redirect(Request $request, string $code)
    {
        $referralCode = ReferralCode::where('code', $code)->first();

        if ($referralCode) {
            // Increment click counter
            $referralCode->increment('clicks');

            // Store in session so we can attribute the signup later
            session(['referral_code' => $code]);
        }

        return redirect()->route('register');
    }
}
