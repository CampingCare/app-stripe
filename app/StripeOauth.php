<?php

namespace App;
use Session;
use Exception;

use App\Models\Logs;
use App\Models\StripeTokens;
use App\CareApi;

class StripeOauth
{

    static public function getStripe(){

        // Set your secret key. Remember to switch to your live secret key in production.
        // See your keys here: https://dashboard.stripe.com/apikeys
        return new \Stripe\StripeClient(getenv('STRIPE_API_KEY'));

    }

    static public function setAccessToken($code){
        
        $stripe = self::getStripe();

        $response = $stripe->oauth->token([
            'grant_type' => 'authorization_code',
            'code' => $code,
        ]) ;

        $StripeTokens = StripeTokens::find(Session::get('adminId'));

        if(!$StripeTokens){
            $StripeTokens = new StripeTokens ;
            $StripeTokens->id = Session::get('adminId') ;
        }
 
        $StripeTokens->stripe_user_id = $response->stripe_user_id ;
        $StripeTokens->access_token = $code ;
        $StripeTokens->testmodes = false ;
        
        $StripeTokens->save();

  
        return $StripeTokens  ;

    }

    static public function getAccessToken()
    {
        $tokens = self::getTokens();

        if($tokens){
            return $tokens->access_token ;
        }

        return false ;
        
    }

    static public function getTokens()
    {

        $StripeTokens = StripeTokens::find(Session::get('adminId')) ;

        if(!$StripeTokens){

            $log = new Logs;

            $log->description = 'No valid access token found';
            $log->admin_id = Session::get('adminId') ;
            $log->save();

            return false ;

        }

        return $StripeTokens ;
     
    }

    static public function removeAccessToken()
    {

        // check if mollie is already installed

        $StripeTokens = StripeTokens::find(Session::get('adminId'));

        $StripeTokens->delete();

    }

}
