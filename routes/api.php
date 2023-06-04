<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Models\Logs;
use App\Models\StripePayment;
use App\Models\StripeTokens;

use App\StripeOauth;
use App\StripeApp;
use App\CareApi;

use Carbon\Carbon;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/webhooks/payment-request', function (Request $request) {

    $uuid = Str::uuid() ;
    $admin_id = $request->input('admin_id') ;
    $amount = number_format($request->input('amount'), 2, '', '');
    $currency = strtoupper($request->input('currency')) ;

    $stripePayment = new StripePayment;

    $stripePayment->uuid = $uuid ;
    $stripePayment->admin_id = $admin_id ;
    $stripePayment->status = 'pending' ;
    $stripePayment->amount = $request->input('amount') ;

    $stripePayment->save() ;

    Session::put('adminId', $admin_id) ;
    $stripeTokens = StripeTokens::find($admin_id) ;

    $metadata = [] ; 
    $description = __('Payment')." ".$stripePayment->id ;

    $metadata['uuid'] = $uuid ;

    if($request->input('admin_id')){
        $metadata['admin_id'] = $admin_id ;
    }

    if($request->input('reservation_id')){
        $metadata['reservation_id'] = $request->input('reservation_id') ;
    }

    if($request->input('reservation_number')){
        $metadata['reservation_number'] = $request->input('reservation_number') ;
        $description = __('Reservation')." ".$request->input('reservation_number') ;
    }

    if($request->input('invoice_id')){
        $metadata['invoice_id'] = $request->input('invoice_id') ;
    }  

    $stripe = StripeOauth::getStripe();

    if($request->input('device_id')){
        
        $metadata['device_id'] = $request->input('device_id') ;

        $api = new CareApi() ;
        $api->setTokens($metadata['admin_id']) ;

        $response = $api->get('/devices/'.$metadata['device_id']) ;

        $device = $response->json() ;

        if(!isset($device['meta']['stripe_terminal_id'])){
            dd('Could not find the Stripe Terminal ID');
        }

        $requestData = [
            'currency' => strtolower($currency),
            'payment_method_types' => ['card_present'],
            'capture_method' => 'automatic',
            'amount' => $amount,
            'metadata' => $metadata,
        ] ;

        if($request->input('application_fee')){
            $requestData['application_fee_amount'] = number_format($request->input('application_fee'), 2, '', '') ;
        }

        $paymentIntent = $stripe->paymentIntents->create($requestData, ["stripe_account" => $stripeTokens->stripe_user_id]) ;

        $stripe->terminal->readers->processPaymentIntent(
            $device['meta']['stripe_terminal_id'],
            ['payment_intent' => $paymentIntent->id],
            ["stripe_account" => $stripeTokens->stripe_user_id]
        );

        $stripePayment->provider_id = $paymentIntent->id ;
        $stripePayment->data = json_encode($request) ;
        $stripePayment->save() ;
        
        return response()->json('ok') ;

    } else {

        $metadata['invoice_id'] = $request->input('invoice_id') ;

        $requestData = 
        [
            'line_items' => [[
                'price_data' => [
                    'currency' => strtolower($currency),
                    'product_data' => [
                        'name' => $description
                    ],
                    'unit_amount' => $amount,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => getenv('APP_URL')."/payment/".$uuid,
            'cancel_url' => getenv('APP_URL')."/payment/".$uuid."?action=canceled",
            'automatic_tax' => [
                'enabled' => false,
            ],
            'metadata' => $metadata,
        ] ;

        if($request->input('application_fee')){

            $requestData['payment_intent_data'] = [
                "application_fee_amount" => number_format($request->input('application_fee'), 2, '', '')
            ] ;

        }

        $checkout_session = $stripe->checkout->sessions->create($requestData, ["stripe_account" => $stripeTokens->stripe_user_id]) ;
        
        $stripePayment->provider_id = $checkout_session->payment_intent ;
        $stripePayment->data = json_encode($request) ;
        $stripePayment->save() ;
        
        return redirect($checkout_session->url) ;

    }

}) ; 

Route::post('/webhooks/payment', function (Request $request) {

    // This is your Stripe CLI webhook secret for testing your endpoint locally.
    $endpoint_secret = getenv('STRIPE_ENDPOINT_SECRET') ;

    $payload = @file_get_contents('php://input');
    $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
    $event = null;

    try {
        $event = \Stripe\Webhook::constructEvent($payload, $sig_header, $endpoint_secret);
    } catch(\UnexpectedValueException $e) {

        $log = new Logs;
        $log->description = 'UnexpectedValueException Stripe' ;
        $log->admin_id = 77 ;
        $log->request = json_encode($e->getMessage()) ;
        $log->save() ;

        // Invalid payload
        http_response_code(400);

    exit();
    } catch(\Stripe\Exception\SignatureVerificationException $e) {

        $log = new Logs;
        $log->description = 'SignatureVerificationException Stripe' ;
        $log->admin_id = 77 ;
        $log->request = json_encode($e->getMessage()) ;
        $log->save() ;

        // Invalid signature
        http_response_code(400);
    exit();
    }

    if($event->type != 'charge.succeeded'){

        $log = new Logs;
        $log->description = 'Received unknown event type ' . $event->type ;
        $log->admin_id = 77 ;
        $log->save() ;

        return response()->json('ok') ;

    }

    $charge = $event->data->object;

    $stripePayment = StripePayment::where('provider_id', $charge->payment_intent)->first() ;

    if(!isset($stripePayment->data)){
        return response()->json('ok') ; // this is not a valid payment webhook
    };

    $stripePaymentData = json_decode($stripePayment->data) ;

    if(isset($stripePaymentData->metadata->invoice_id)){

        try {
            
            if(intval($stripePayment->care_id) > 0){

                $log = new Logs;
                $log->description = 'CareId already exists';
                $log->admin_id = $stripePayment->admin_id ;
                $log->save() ;

                return response()->json('ok') ;

            }

            $api = new CareApi() ;
            $api->setTokens($stripePayment->admin_id) ;

            $paidDate = Carbon::createFromTimestamp($charge->created) ;
            $amount = substr($charge->amount, 0, -2).".".substr($charge->amount, -2) ;

            $requestData = [
                'type' => 'invoice',
                'type_id' => $stripePaymentData->metadata->invoice_id,
                'amount' => $amount,
                'currency' => strtoupper($charge->currency),
                'pay_date' => $paidDate->toDateTimeString(),
                'provider' => 6,
                'method' => StripeApp::getMethodId($charge->payment_method_details->type),
            ] ;

            $response = $api->post('/payments', $requestData) ;

            if($response->getStatusCode() == 200){

                $stripePayment->care_id = $response->json('id');
                $stripePayment->save();

                $requestDataMeta = [
                    'key' => 'stripe_payment_intent',
                    'value' => $stripePayment->provider_id
                ] ;
    
                $api->put('/payments/'.$response->json('id').'/meta', $requestDataMeta) ;

                $requestDataMeta = [
                    'key' => 'source',
                    'value' => $stripePaymentData->id
                ] ;
    
                $api->put('/payments/'.$response->json('id').'/meta', $requestDataMeta) ;

                $log = new Logs;
                $log->description = 'Charge added via CareApi' ;
                $log->admin_id = $stripePayment->admin_id ;
                $log->response = json_encode($charge) ;
                $log->save() ;
                
            }

        } catch (Exception $e) {
            
            $log = new Logs;
            $log->description = 'CareApiError ' . $e->getMessage();
            $log->admin_id = $stripePayment->admin_id ;
            $log->save() ;

            http_response_code(400);

            return response()->json('error') ;
            
        }

        $stripePayment->status = 'done' ;
        $stripePayment->save() ;

    };

    return response()->json('ok') ;

})  ; 