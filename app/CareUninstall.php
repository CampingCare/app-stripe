<?php

namespace App;
use Session;

use App\Models\Tokens;
use App\Models\Logs;
use App\Models\StripePayment;
use App\Models\StripeTokens;

use App\StripeOauth;
use App\StripeApp;
use App\CareApi;

class CareUninstall
{
    
    static public function run()
    {


        // remove the care tokens
        $CareTokens = Tokens::where('admin_id', Session::get('adminId'))->first() ;

        if($CareTokens){
            $CareTokens->delete() ;
        }
        
        // remove the mollie tokens
        $stripeTokens = StripeTokens::find(Session::get('adminId')) ;

        if($stripeTokens){
            $stripeTokens->delete() ;
        }

        // remove the app logs
        Logs::where('admin_id', Session::get('adminId'))->delete();

        // remove the MolliePayment logs
        StripePayment::where('admin_id', Session::get('adminId'))->delete();

        // remove the entire session
        Session::flush();

    }

}
