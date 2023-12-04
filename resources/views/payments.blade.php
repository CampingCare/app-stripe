<x-layout>
    <div class="p-4">
        <h1 class="text-3xl font-bold">{{ __('Payments') }}</h1>

        <p class="py-4 mt-4">{{ __('Last payments (Maximal 100)') }}</p>

        <div class="py-4 mt-4">
            <a
                href="/"
                class="bg-transparent hover:bg-blue-500 text-blue-700 font-semibold hover:text-white py-2 px-4 border border-blue-500 hover:border-transparent rounded"
            >
                {{ __('Back') }}
            </a>
        </div>

        <table class="w-full text-left text-sm font-light">
            <thead class="border-b font-medium">
                <tr>
                    <th scope="col" class="p-4">#</th>
                    <th scope="col" class="p-4">Amount</th>
                    <th scope="col" class="p-4">Status</th>
                    <th scope="col" class="p-4">Uuid</th>
                    <th scope="col" class="p-4">Provider id</th>
                </tr>
            </thead>
            <tbody>
                @if (count($payments) == 0)
                    <tr class="border-b">
                        <td class="p-4" colspan="100%">{{ __('No payments') }}</td>
                    </tr>
                @else
                    @foreach ($payments as $payment)
                        <tr class="border-b">
                            <td class="p-4 font-medium">{{ $payment['id'] }}</td>
                            <td class="p-4">{{ $payment['amount'] }} {{ json_decode($payment['data'])->currency }}</td>
                            <td class="p-4">{{ $payment['status'] }}</td>
                            <td class="p-4">{{ $payment['uuid'] }}</td>
                            <td class="p-4">{{ $payment['provider_id'] }}</td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
</x-layout>
