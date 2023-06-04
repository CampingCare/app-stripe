<?php

namespace App;
use Session;
use Exception;

use App\Models\Logs;
use App\Models\StripeTokens;
use App\CareApi;
use App\StripeOauth;

class StripeTerminals
{

    static public function get(){

        $stripe = StripeOauth::getStripe() ;

        $stripeTokens = StripeTokens::find(Session::get('adminId') ) ;

        $terminals = $stripe->terminal->readers->all(['limit' => 20], ["stripe_account" => $stripeTokens->stripe_user_id]) ;

        return $terminals->data ;

    }

    static public function list($devices)
    {

        $stripe = StripeOauth::getStripe() ;

        $stripeTokens = StripeTokens::find(Session::get('adminId') ) ;

        $terminalsData = $stripe->terminal->readers->all(['limit' => 20], ["stripe_account" => $stripeTokens->stripe_user_id]) ;

        $terminals = $terminalsData->data ;

        foreach ($terminals as $terminal) {

            // dd($terminal);

            $terminal->careDeviceId = null ;

            foreach ($devices as $device) {
                
                if(
                    isset($device['brand']) && 
                    isset($device['meta']['stripe_terminal_id']) && 
                    $device['brand'] == 'stripe' &&
                    $device['meta']['stripe_terminal_id'] == $terminal->id
                ){
                    $terminal->careDeviceId = $device['id'] ;
                }

            }

        }


        return $terminals;
        
    }

    static public function sync($devices)
    {

        $terminals = self::list($devices) ;

        foreach ($terminals as $terminal) {

            if($terminal->careDeviceId == null){
                self::addDevice($terminal) ;
            }

        }

        return $terminals ;
        
    }

    static public function addDevice($terminal)
    {   
        
        $api = new CareApi() ;

        $requestData = [
            'name' => $terminal->label,
            'type' => 'pin_terminal',
            'brand' => 'stripe',
        ] ;

        $response = $api->post('/devices', $requestData) ;
        $device = $response->json() ;

        $requestData = [
            'brand' => 'stripe',
        ] ;

        $response = $api->put("/devices/".$device['id'], $requestData) ;

        $requestData = [
            'key' => 'stripe_terminal_id',
            'value' => $terminal->id,
        ] ;

        $response = $api->put("/devices/".$device['id']."/meta", $requestData) ;

        $requestData = [
            'key' => 'app_id',
            'value' => intval(Session::get('appId'))
        ] ;

        $response = $api->put("/devices/".$device['id']."/meta", $requestData) ;

        // dd($response->json() );

        return $response->json() ;
        
    }

    static public function removeDevice($deviceId)
    {   
        
        $api = new CareApi() ;

        $response = $api->delete('/devices/'.$deviceId) ;

        return $response->json() ;
        
    }

   
}
