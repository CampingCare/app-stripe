<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ __('Payment') }}</title>

        @vite('resources/css/app.css')

    </head>

    <body>

        <div class="text-center py-8 my-8">

            @if ($payment->status == 'pending') 
                
                <h1 class="text-3xl font-bold">{{ __('Processing your payment') }}</h1> 

                <div class="p-4">{{ __('This can take a minute...') }}</div>

                <script>
                    setTimeout(function(){
                        window.location.reload(1);
                    }, 2000);
                </script>

            @endif 

            @if ($payment->status == 'canceled') 

                <h1 class="text-3xl font-bold">{{ __('Payment failed') }}</h1> 

                <div class="mt-8">
                    <a href="{{$tryAgainUrl}}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">{{ __('Try again') }}</a>
                </div>
                
            @endif 

            <div class="mt-8">
                <a href="{{ $guestPageUrl }}" class="bg-transparent hover:bg-blue-500 text-blue-700 font-semibold hover:text-white py-2 px-4 border border-blue-500 hover:border-transparent rounded">{{ __('Cancel') }}</a>
            </div>

            <!-- <div class="py-4 mt-4">
                <a href="/" class="bg-transparent hover:bg-blue-500 text-blue-700 font-semibold hover:text-white py-2 px-4 border border-blue-500 hover:border-transparent rounded">{{ __('Back') }}</a>
            </div>

            <pre>{{ print_r(json_decode(json_encode($payment))) }}</pre> -->

        </div>

    </body>

</html>
