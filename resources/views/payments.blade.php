<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Stripe App</title>

        @vite('resources/css/app.css')


    </head>

    <body>

        <div>

            <h1 class="text-3xl font-bold">{{ __('Payments') }}</h1>

            <p class="py-4 mt-4">{{ __('Last payments (Maximal 100)') }}</p>

            <div class="py-4 mt-4">
                <a href="/" class="bg-transparent hover:bg-blue-500 text-blue-700 font-semibold hover:text-white py-2 px-4 border border-blue-500 hover:border-transparent rounded">{{ __('Back') }}</a>
            </div>

            @if (count($payments) == 0) 

                <div class="py-4 mt-4">
                    {{ __('No Payments have been found') }}
                </div>

            @endif

            @foreach ($payments as $payment)
                <pre><?php print_r(json_decode(json_encode($payment))) ?></pre>
            @endforeach

            <!-- <a href="/logs?action=clear">Clear logs</a>  -->

            <script src="{{ asset('js/widgets.js')}}"></script>

        </div>

    </body>

</html>
