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

            <h1 class="text-3xl font-bold">{{ __('Terminals') }}</h1>

            @if ($terminals == 'no_permission') 

                <div class="py-4 mt-4">
                    {{ __('We have no access to your terminals yet.') }}
                </div>
            
            @else 

                @foreach ($terminals as $terminal)
                    
                    <div class="rounded p-4 border border-grey  mt-4"">
                    
                        <div class="rounded p-4 border border-grey  mt-4">

                            <b>{{ $terminal->label }}</b>

                            <p class="pt-4">ID: {{ $terminal->id }}</p>
                            <p>device type: {{ $terminal->device_type }}</p>
                            <p>last_seen_at: {{ $terminal->last_seen_at }}</p>
                            <p>location: {{ $terminal->location }}</p>
                            <p>serialNumber: {{ $terminal->serial_number }}</p>
                            <p>status: {{ $terminal->status }}</p>

                        @if ($terminal->careDeviceId == null) 

                        <div class="py-4 mt-4">
                            <a href="/terminals?action=sync" class="bg-transparent hover:bg-blue-500 text-blue-700 font-semibold hover:text-white py-2 px-4 border border-blue-500 hover:border-transparent rounded">
                                {{ __('Connect terminal') }}
                            </a>
                        </div>
                        
                        @else 
                            
                            <div class="rounded p-4  mt-4 border border-green-500 text-green-700 ">
                                {{ __('Connected') }}
                            </div>

                            <div class="py-4 mt-4">
                                <a href="/terminals?action=delete&device_id={{ $terminal->careDeviceId }}" 
                                class="bg-transparent hover:bg-red-500 text-red-700 
                                font-semibold hover:text-white py-2 px-4 border border-red-500 hover:border-transparent rounded">{{ __('Disconnect') }}</a>
                            </div>

                        @endif

                    </div>

                @endforeach
                

            @endif


            <div class="py-4 mt-4">

                <a href="/" class="bg-transparent hover:bg-blue-500 text-blue-700 font-semibold hover:text-white py-2 px-4 border border-blue-500 hover:border-transparent rounded">{{ __('Back') }}</a>
                <a href="https://support.camping.care/en/how-to-setup-mollie-terminals" target="_blank" class="bg-transparent hover:bg-blue-500 text-blue-700 font-semibold hover:text-white py-2 px-4 border border-blue-500 hover:border-transparent rounded">{{ __('Need help?') }}</a>
            
            </div>


            <!-- <a href="/logs?action=clear">Clear logs</a>  -->

            <script src="{{ asset('js/widgets.js')}}"></script>

        </div>

    </body>

</html>
