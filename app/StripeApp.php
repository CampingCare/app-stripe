<?php

namespace App;
use Session;
use Exception;

use App\Models\Logs;
use App\Models\StripeTokens;
use App\StripeOauth;
use App\CareApi;

class StripeApp
{

    static public function getTryAgainUrl($stripePayment)
    {

        $stripePaymentData = json_decode($stripePayment->data) ;

        $amount = substr($stripePaymentData->line_items[0]->price_data->unit_amount, 0, -2).".".substr($stripePaymentData->line_items[0]->price_data->unit_amount, -2) ;

        $params = [] ; 
        $params['admin_id'] = $stripePayment->admin_id ;
        $params['amount'] = $amount ;
        $params['currency'] = strtoupper($stripePaymentData->line_items[0]->price_data->currency) ;

        if(isset($stripePaymentData->metadata->reservation_id)){
            $params['reservation_id'] = $stripePaymentData->metadata->reservation_id ;
        }

        if(isset($stripePaymentData->metadata->reservation_number)){
            $params['reservation_number'] = $stripePaymentData->metadata->reservation_number ;
        }

        if(isset($stripePaymentData->metadata->invoice_id)){
            $params['invoice_id'] = $stripePaymentData->metadata->invoice_id ;
        }

        return '/api/webhooks/payment-request?'.http_build_query($params) ;

    }

    static public function getMethodId($stripeMethodKey)
    {

        $stripeMethodKey = strtolower($stripeMethodKey);
        
        if($stripeMethodKey == 'bancontact'){
            return 15 ;
        }

        if($stripeMethodKey == 'card'){
            return 21 ;
        }

        if($stripeMethodKey == 'card_present'){
            return 4 ;
        }

        if($stripeMethodKey == 'eps'){
            return 11 ;
        }

        if($stripeMethodKey == 'giropay'){
            return 22 ;
        }

        if($stripeMethodKey == 'ideal'){
            return 8 ;
        }

        if($stripeMethodKey == 'klarna'){
            return 13 ;
        }

        if($stripeMethodKey == 'sofort'){
            return 13 ;
        }

        return 1 ;
        
    }

}
