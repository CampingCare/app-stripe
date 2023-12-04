<x-layout>
    <div class="flex max-h-screen overflow-y-auto">
        <aside class="w-2/6 h-screen border-r-2 sticky top-0 left-0">
            <nav class="p-4">
                <div class="mb-4 flex items-center justify-between">
                    <a href="/" class="btn-secondary mr-2"><</a>

                    <h2 class="text-xl">{{ __('Stripe Logs') }}</h2>

                    <a href="/logs?action=clear" class="btn-secondary text-xs px-2">{{ __('Clear') }}</a>
                </div>

                <ul class="overflow-y-auto max-h-screen pb-4">
                    <li class="mb-4 cursor-pointer">
                        <button class="block btn-secondary text-xs w-full" onclick="location.reload()">{{ __('Reload') }}</button>
                    </li>

                    @foreach ($logs as $log)
                        <li class="mb-4 cursor-pointer">
                            <a href="#{{ $log['id'] }}" class="block btn-secondary">{{ $log['id'] }} - {{ $log['description'] }}</a>
                        </li>
                    @endforeach

                    @if (count($logs) <= 0)
                        <li class="mb-4 cursor-pointer">
                            <a class="block btn-secondary">{{ __('No logs') }}</a>
                        </li>
                    @endif
                </ul>
            </nav>
        </aside>

        <main class="w-4/6 ml-8 m-4">
            @foreach ($logs as $log)
                <section id="{{ $log['id'] }}" class="mb-8">
                    <h2 class="text-xl font-semibold mb-2">{{ $log['id'] }} - {{ $log['description'] }}</h2>
                    <div class="bg-gray-100 p-4 rounded-lg shadow-md">
                        <h3 class="font-semibold mb-2">{{ __('ID') }}: <span class="font-normal">{{ $log['id'] }}</span></h3>
                        <h3 class="font-semibold mb-2">{{ __('Description') }}: <span class="font-normal">{{ $log['description'] }}</span></h3>
                        <h3 class="font-semibold mb-2">{{ __('Created at') }}: <span class="font-normal">{{ $log['created_at'] }}</span></h3>
                        <h3 class="font-semibold mb-2">{{ __('Updated at') }}: <span class="font-normal">{{ $log['updated_at'] }}</span></h3>

                        <h3 class="font-semibold mb-2">{{ __('Request:') }}</span></h3>
                        <pre><code class="text-sm">{{ json_encode(json_decode($log['request']), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>

                        <h3 class="font-semibold mb-2">{{ __('Response:') }}</span></h3>
                        <pre><code class="text-sm">{{ json_encode(json_decode($log['response']), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
                    </div>
                </section>
            @endforeach
        </main>
    </div>
</x-layout>
