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

            <div class="text-center py-8 my-8">

                <h1 class="text-3xl font-bold">
                    {{ __('Welcome') }}
                </h1>

                <p class="p-4">{{ __('Install the app to use it.') }}</p>

                <p class="p-4 pl-0 pr-0">
                    <a href="https://support.camping.care/en/how-to-set-up-the-stripe-app" target="_blank" class="bg-transparent hover:bg-blue-500 text-blue-700 font-semibold hover:text-white py-2 px-4 border border-blue-500 hover:border-transparent rounded">{{ __('Read more') }}</a>
                </p>
                
            </div>

            <script src="{{ asset('js/widgets.js')}}"></script>

        </div>

    </body>

</html>
