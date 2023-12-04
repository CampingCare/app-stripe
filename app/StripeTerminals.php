<?php

namespace App;

use Illuminate\Support\Facades\Session;
use App\Models\StripeTokens;
use App\StripeOauth;
use App\CareApi;
use Exception;

class StripeTerminals
{
    public static function get()
    {
        $stripe = StripeOauth::getStripe();

        $stripeTokens = StripeTokens::find(Session::get('adminId') );

        $terminals = $stripe->terminal->readers->all(['limit' => 20], ["stripe_account" => $stripeTokens->stripe_user_id]);

        return $terminals->data;
    }

    public static function getDevice($deviceId)
    {
        $stripe = StripeOauth::getStripe();
        return $stripe->terminal->readers->retrieve($deviceId);
    }

    public static function list($devices)
    {
        $stripe = StripeOauth::getStripe();

        // $stripeTokens = StripeTokens::find(Session::get('adminId'));

        $terminals = $stripe->terminal->readers->all(['limit' => 20])->data;

        foreach ($terminals as $terminal) {
            $terminal->careDeviceId = null;

            foreach ($devices as $device) {
                if (
                    isset($device['brand']) &&
                    isset($device['meta']['stripe_terminal_id']) &&
                    $device['brand'] == 'stripe' &&
                    $device['meta']['stripe_terminal_id'] == $terminal->id
                ) {
                    $terminal->careDeviceId = $device['id'];
                }
            }
        }

        return $terminals;
    }

    public static function sync($devices)
    {
        $terminals = self::list($devices);

        foreach ($terminals as $terminal) {
            if ($terminal->careDeviceId == null)
                self::addDevice($terminal);
        }

        return $terminals;
    }

    public static function addDevice($terminal)
    {
        $api = new CareApi();

        $requestData = [
            'name' => $terminal->label,
            'type' => 'pin_terminal',
            'brand' => 'stripe'
        ];

        $response = $api->post('/devices', $requestData);
        $device = $response->json();

        if ($response->getStatusCode() != 200)
            throw new Exception("Camping Care API error: {$device['error']['message']}", $response->getStatusCode());

        $requestData = [
            'brand' => 'stripe'
        ];

        $response = $api->put("/devices/" . $device['id'], $requestData);

        $requestData = [
            'key' => 'stripe_terminal_id',
            'value' => $terminal->id,
        ];

        $response = $api->put("/devices/" . $device['id'] . "/meta", $requestData);

        $requestData = [
            'key' => 'app_id',
            'value' => intval(Session::get('appId'))
        ];

        $response = $api->put("/devices/" . $device['id'] . "/meta", $requestData);

        return $response->json();
    }

    public static function removeDevice($deviceId)
    {
        $api = new CareApi();

        $response = $api->delete('/devices/' . $deviceId);

        if ($response->getStatusCode() != 200)
            throw new Exception($response->json()['error']['message'], $response->getStatusCode());

        return $response->json();
    }
}
