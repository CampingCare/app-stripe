<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

use App\Models\StripePayment;
use App\Models\Logs;

use App\StripeOauth;
use App\StripeApp;
use App\CareApi;

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

        if(Session::get('installed')){

            // $api = new CareApi() ;

            // $result = $api->get('/users/me', ['get_rights' => true]) ;

            // view()->share('user', $result->object()) ;

            if($request->input('action') == 'disconnect'){

                StripeOauth::removeAccessToken() ;

            };

            if($request->input('code')){

                StripeOauth::setAccessToken($request->input('code')) ;
                return redirect(Session::get('platformUrl').'/apps/'.Session::get('appId')) ;

            }else{
                
                $stripeTokens = StripeOauth::getTokens() ;

                if($stripeTokens){
                    view()->share('stripeTokens', $stripeTokens) ;
                }

            }

            view()->share('clientId', getenv('STRIPE_CLIENT_ID')) ;

            return view('installed') ;

        }
        
        return view('welcome') ;

    })  ; 

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

        $tryAgainUrl = StripeApp::getTryAgainUrl($stripePayment) ;

        view()->share('tryAgainUrl', $tryAgainUrl) ;
        view()->share('guestPageUrl', $guestPageUrl) ;
        view()->share('payment', $stripePayment) ;

        return view('/payment') ;

    })  ; 

    Route::get('/logs', function (Request $request) {
        
        $logs = 'no logs' ; 

        if(Session::has('adminId')){

            if($request->input('action') == 'clear'){
                Logs::where('admin_id', Session::get('adminId'))->delete();
            }

            $logs = Logs::where('admin_id', Session::get('adminId'))
               ->orderBy('id', 'desc')
               ->take(10)
               ->get();

        }

        view()->share('logs', $logs) ;

        return view('/logs') ;

    })  ; 

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

    

}) ;