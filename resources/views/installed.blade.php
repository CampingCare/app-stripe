<x-layout>
    <div>
        @isset($stripeTokens)
            <h1 class="text-3xl font-bold">{{ __('Stripe connected') }}</h1>

            <p class="py-4">{{ __('Stripe has been installed and you are now able to receive payments.') }}</p>

            <div class="py-4 mt-4">

                <a
                    href="/logs"
                    class="bg-transparent hover:bg-blue-500 text-blue-700 font-semibold hover:text-white py-2 px-4 border border-blue-500 hover:border-transparent rounded"
                >
                    {{ __('Logs') }}
                </a>
                <a
                    href="/payments"
                    class="bg-transparent hover:bg-blue-500 text-blue-700 font-semibold hover:text-white py-2 px-4 border border-blue-500 hover:border-transparent rounded"
                >
                    {{ __('Payments') }}
                </a>
                <a
                    href="/terminals"
                    class="bg-transparent hover:bg-blue-500 text-blue-700 font-semibold hover:text-white py-2 px-4 border border-blue-500 hover:border-transparent rounded"
                >
                    {{ __('Terminals') }}
                </a>

            </div>

            <p class="py-2 mt-4">
                <a href="?action=disconnect"
                    class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">{{ __('Disconnect Stripe') }}</a>
            </p>
        @else
            <div class="text-center py-8 mt-8">

                <h1 class="text-3xl font-bold">
                    {{ __('The app is installed.') }}
                </h1>

                <p class="py-4 max-w-md mx-auto">
                    {{ __('By connecting your Stripe account you can receive direct payments in to your bankaccount. Clients can pay via Creditcard, IDeal, Sofort, Mistercash or Paypal.') }}
                </p>

                <p class="py-4">
                    <a
                        href="https://connect.stripe.com/oauth/authorize?response_type=code&client_id={{ $clientId }}&scope=read_write&state={{ $state }}"
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                        target="_parent"
                    >
                        {{ __('Connect or Create your Stripe Account') }}
                    </a>
                </p>

            </div>
        @endisset

        <p class="p-4 pl-0 pr-0">
            <a
                href="https://support.camping.care/en/how-to-set-up-the-stripe-app" target="_blank"
                class="bg-transparent hover:bg-blue-500 text-blue-700 font-semibold hover:text-white py-2 px-4 border border-blue-500 hover:border-transparent rounded"
            >
                {{ __('Read more') }}
            </a>
        </p>
    </div>
</x-layout>
