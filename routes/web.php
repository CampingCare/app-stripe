<?php

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

use App\Models\StripePayment;
use App\Models\Logs;

use App\StripeOauth;
use App\StripeApp;
use App\CareApi;
use App\StripeTerminals;
use App\Helpers;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware(['care.app'])->group(function () {

    Route::get('/', function (Request $request) {
        if (!Session::get('installed'))
            return view('welcome');

        Helpers::log($request->all());

        if ($request->input('action') == 'disconnect')
            StripeOauth::removeAccessToken();

        $stripeTokens = StripeOauth::getTokens();
        if ($stripeTokens)
            view()->share('stripeTokens', $stripeTokens);

        return view('installed', [
            'clientId' => getenv('STRIPE_CLIENT_ID'),
            'state' => json_encode([
                'platformUrl' => Session::get('platformUrl'),
                'admin_id' => Session::get('adminId'),
                'app_id' => Session::get('appId')
            ])
        ]);
    });

    Route::get('/connect', function (Request $request) {
        if (!$request->has('state'))
            return redirect('https://app.camping.care');

        $state = json_decode($request->input('state'), true);

        if ($request->input('code'))
            StripeOauth::setAccessToken($request->input('code'), $state['admin_id']);

        return redirect("{$state['platformUrl']}/apps/{$state['app_id']}");
    });

    Route::get('/logs', function (Request $request) {
        $logs = 'no logs';

        if (Session::has('adminId')) {
            if ($request->input('action') == 'clear')
                Logs::where('admin_id', Session::get('adminId'))->delete();

            $logs = Logs::where('admin_id', Session::get('adminId'))
                ->orderBy('id', 'desc')
                ->take(10)
                ->get();
        }

        return view('/logs', ['logs' => $logs]);
    });

    Route::get('/payments', function (Request $request) {

        $payments = 'no payments' ;

        if(Session::has('adminId')){

            $payments = StripePayment::where('admin_id', Session::get('adminId'))
               ->orderBy('id', 'desc')
               ->take(100)
               ->get();

        }

        view()->share('payments', $payments) ;

        return view('/payments') ;

    })  ;

    Route::get('/terminals', function (Request $request) {
        if (!Session::has('adminId'))
            return redirect('/');

        $api = new CareApi();
        $devices = $api->get('/devices')->json();

        if ($request->input('action') == 'delete') {
            StripeTerminals::removeDevice($request->input('device_id'));
            $devices = $api->get('/devices')->json();
        }

        if ($request->input('action') == 'sync') {
            StripeTerminals::sync($devices);
            $devices = $api->get('/devices')->json();
        }

        $terminals = StripeTerminals::list($devices);

        view()->share('terminals', $terminals);
        view()->share('devices', $devices);

        return view('/terminals');
    });
});

Route::get('/payment/{uuid}', function (Request $request, $uuid) {

    $stripePayment = StripePayment::where('uuid', $uuid)->first() ;
    $stripePaymentData = json_decode($stripePayment->data) ;

    $reservationId = false ;

    if(isset($stripePaymentData->metadata->reservation_id)){
        $reservationId = $stripePaymentData->metadata->reservation_id ;
    }

    $api = new CareApi() ;
    $api->setTokens($stripePayment->admin_id) ;

    $guestPageUrl = $api->getGuestPageUrl($reservationId) ;

    if($stripePayment->status == 'done'){
        return redirect($guestPageUrl."?payment=success") ;
    } ;

    if($request->input('action') == 'canceled'){
        return redirect($guestPageUrl."?payment=canceled") ;
    } ;

    $tryAgainUrl = StripeApp::getTryAgainUrl($stripePayment) ;

    view()->share('tryAgainUrl', $tryAgainUrl) ;
    view()->share('guestPageUrl', $guestPageUrl) ;
    view()->share('payment', $stripePayment) ;

    return view('/payment') ;

})  ;
