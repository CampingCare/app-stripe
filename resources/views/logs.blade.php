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

            <h1 class="text-3xl font-bold">{{ __('Logs') }}</h1>

            <p class="py-4 mt-4">{{ __('Last logs (Maximal 100)') }}</p>

            <div class="py-4">
                <a href="/" class="bg-transparent hover:bg-blue-500 text-blue-700 font-semibold hover:text-white py-2 px-4 border border-blue-500 hover:border-transparent rounded">{{ __('Back') }}</a>
            </div>

            @if (count($logs) == 0) 

                <div class="py-4 mt-4">
                    {{ __('No logs have been found') }}
                </div>

            @endif

            @foreach ($logs as $log)
                <pre><?php print_r(json_decode(json_encode($log))) ?></pre>
            @endforeach
            
            @if (count($logs) > 0) 

                <a href="/logs?action=clear">Clear logs</a> 

            @endif
            
            
            <script src="{{ asset('js/widgets.js')}}"></script>

        </div>

    </body>

</html>
