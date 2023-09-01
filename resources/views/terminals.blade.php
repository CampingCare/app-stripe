<x-layout>
    <div>
        <h1 class="text-3xl font-bold mb-4">{{ __('Terminals') }}</h1>

        {{-- Error messages --}}
        @if ($errors->any())
            <div>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li class="mt-4 border border-red-500 p-4 text-red-500">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if ($terminals == 'no_permission')
            <div class="py-4 rounded border border-grey">
                {{ __('We have no access to your terminals yet.') }}
            </div>
        @elseif (count($terminals) <= 0)
            <div class="rounded p-4 border border-grey">
                {{ __('No terminals found') }}
            </div>
        @else
            <div>
                @foreach ($terminals as $terminal)
                    <div class="rounded p-4 border border-grey mt-4">
                        <b>{{ $terminal->label }}</b>

                        <p class="pt-4">ID: {{ $terminal->id }}</p>
                        <p>device type: {{ $terminal->device_type }}</p>
                        <p>last_seen_at: {{ $terminal->last_seen_at }}</p>
                        <p>location: {{ $terminal->location }}</p>
                        <p>serialNumber: {{ $terminal->serial_number }}</p>
                        <p>status: {{ $terminal->status }}</p>

                        @if ($terminal->careDeviceId == null)
                            <div class="py-4 mt-4">
                                <form action="/terminals/connect/{{ $terminal->id }}" method="POST">
                                    @csrf

                                    <button
                                        type="submit"
                                        onclick="showSpinner('connect')"
                                        class="bg-transparent hover:bg-blue-500 text-blue-700 font-semibold hover:text-white py-2 px-4 border border-blue-500 hover:border-transparent rounded"
                                    >
                                        <span id="btn-text-connect">{{ __('Connect terminal') }}</span>
                                        <span id="btn-spinner-connect" class="hidden animate-spin border-t-4 text-black h-6 w-3 rounded-full"></span>
                                    </button>
                                </form>
                            </div>
                        @else
                            <div class="rounded p-4  mt-4 border border-green-500 text-green-700 ">
                                {{ __('Connected') }}
                            </div>

                            <div class="py-4 mt-4">
                                <form action="/terminals/disconnect/{{ $terminal->careDeviceId }}" method="POST">
                                    @csrf

                                    <button
                                        type="submit"
                                        class="bg-transparent hover:bg-red-500 text-red-700 font-semibold hover:text-white py-2 px-4 border border-red-500 hover:border-transparent rounded"
                                    >
                                        {{ __('Disconnect') }}
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

        <form action="/terminals/sync" method="POST" class="mt-4">
            @csrf
            <button
                type="submit"
                onclick="showSpinner('connect-all')"
                class="bg-transparent hover:bg-blue-500 text-blue-700 font-semibold hover:text-white py-2 px-4 border border-blue-500 hover:border-transparent rounded"
            >
                <span id="btn-text-connect-all">{{ __('Connect all terminals') }}</span>
                <span id="btn-spinner-connect-all" class="hidden animate-spin border-t-4 text-black h-6 w-3 rounded-full"></span>
            </button>
        </form>

        <div class="py-4 mt-4">
            <a
                href="/"
                class="bg-transparent hover:bg-blue-500 text-blue-700 font-semibold hover:text-white py-2 px-4 border border-blue-500 hover:border-transparent rounded"
            >
                {{ __('Back') }}
            </a>
            <a
                href="https://support.camping.care/en/how-to-setup-mollie-terminals" target="_blank"
                class="bg-transparent hover:bg-blue-500 text-blue-700 font-semibold hover:text-white py-2 px-4 border border-blue-500 hover:border-transparent rounded"
            >
                {{ __('Need help?') }}
            </a>
        </div>
    </div>

    <script>
        function showSpinner(id) {
            document.getElementById(`btn-text-${id}`).style.display = 'none';
            document.getElementById(`btn-spinner-${id}`).style.display = 'block';
        }
    </script>
</x-layout>
