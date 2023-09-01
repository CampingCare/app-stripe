<?php

namespace App;

use Illuminate\Support\Facades\Session;
use App\Models\StripeTokens;
use App\Models\Logs;

class StripeOauth
{
    public static function getStripe()
    {
        // Set your secret key. Remember to switch to your live secret key in production.
        // See your keys here: https://dashboard.stripe.com/apikeys
        return new \Stripe\StripeClient(getenv('STRIPE_API_KEY'));
    }

    public static function setAccessToken($code, $adminId)
    {
        $stripe = self::getStripe();

        $response = $stripe->oauth->token([
            'grant_type' => 'authorization_code',
            'code' => $code,
        ]);

        $stripeTokens = StripeTokens::find($adminId);

        if (!$stripeTokens) {
            $stripeTokens = new StripeTokens;
            $stripeTokens->id = $adminId;
        }

        $stripeTokens->stripe_user_id = $response->stripe_user_id;
        $stripeTokens->access_token = $code;
        $stripeTokens->testmodes = false;

        $stripeTokens->save();

        return $stripeTokens;
    }

    public static function getAccessToken()
    {
        $tokens = self::getTokens();

        if ($tokens)
            return $tokens->access_token;

        return false;
    }

    public static function getTokens()
    {
        $StripeTokens = StripeTokens::find(Session::get('adminId'));

        if (!$StripeTokens) {
            Logs::create([
                'description' => 'No valid access token found',
                'admin_id' => Session::get('adminId')
            ]);

            return false;
        }

        return $StripeTokens;
    }

    public static function removeAccessToken()
    {
        StripeTokens::find(Session::get('adminId'))->delete();
    }
}
