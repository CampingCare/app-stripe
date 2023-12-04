<x-layout>
    <div class="p-4">
        @isset($stripeTokens)
            <h1 class="text-3xl font-bold">{{ __('Stripe connected') }}</h1>

            <p class="py-4">{{ __('Stripe has been installed and you are now able to receive payments.') }}</p>

            <div class="py-4 mt-4 flex gap-2">
                <a href="/logs" class="btn-secondary">
                    {{ __('Logs') }}
                </a>

                <a href="/payments" class="btn-secondary">
                    {{ __('Payments') }}
                </a>

                <a href="/terminals" class="btn-secondary">
                    {{ __('Terminals') }}
                </a>
            </div>

            <p class="py-2 mt-4">
                <a href="?action=disconnect"
                    class="btn bg-red-500 border-red-500 hover:bg-red-700">{{ __('Disconnect Stripe') }}</a>
            </p>

            <p class="p-4 pl-0 pr-0">
                <a
                    href="https://support.camping.care/en/how-to-set-up-the-stripe-app" target="_blank"
                    class="btn-secondary"
                >
                    {{ __('Read more') }}
                </a>
            </p>
        @else
            <div class="text-center py-8 mt-8">
                <h1 class="text-3xl font-bold">
                    {{ __('The app is installed.') }}
                </h1>

                <p class="py-4 max-w-md mx-auto">
                    {{ __('By connecting your Stripe account you can receive direct payments in to your bankaccount. Clients can pay via Creditcard, IDeal, Sofort, Mistercash or Paypal.') }}
                </p>

                <a
                    href="https://connect.stripe.com/oauth/authorize?response_type=code&client_id={{ $clientId }}&scope=read_write&state={{ $state }}"
                    class="btn"
                    target="_parent"
                >
                    {{ __('Connect or Create your Stripe Account') }}
                </a>

                <a
                    href="https://support.camping.care/en/how-to-set-up-the-stripe-app" target="_blank"
                    class="block mt-4 underline"
                >
                    {{ __('Read more') }}
                </a>
            </div>
        @endisset
    </div>
</x-layout>
