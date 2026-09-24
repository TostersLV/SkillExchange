<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" @class(['dark' => request()->cookie('appearance') === 'dark'])>
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-page font-sans text-fg antialiased">
        <div class="grid min-h-svh lg:grid-cols-[1fr_1.1fr]">
            <aside class="hidden flex-col justify-between bg-navy p-12 text-paper lg:flex dark:border-e dark:border-line dark:bg-band">
                <x-app-logo href="{{ route('home') }}" :sidebar="true" inverse />

                <div class="max-w-md">
                    <span
                        class="inline-flex items-center gap-1.5 rounded-full border border-amber/50 px-3 py-1 text-xs font-semibold text-paper">
                        <flux:icon.banknotes variant="micro" class="size-3.5 text-amber" aria-hidden="true" />
                        No money involved
                    </span>
                    <p class="mt-6 text-4xl leading-[1.15] font-semibold tracking-tight text-paper">
                        Trade what you know for what you need
                    </p>
                    <p class="mt-4 leading-7 text-paper/75">
                        Swap a skill you have for one you want, and build your reputation one exchange at a time.
                    </p>
                </div>

                <p class="flex items-center gap-2 text-sm text-paper/70">
                    <flux:icon.shield-check variant="micro" class="size-4 text-amber" aria-hidden="true" />
                    Every exchange ends with a mutual review.
                </p>
            </aside>

            <main class="flex flex-col items-center justify-center gap-8 px-4 py-12 sm:px-6">
                <div class="lg:hidden">
                    <x-app-logo href="{{ route('home') }}" :sidebar="true" />
                </div>

                <x-swap.card padding="lg" class="w-full max-w-md">
                    {{ $slot }}
                </x-swap.card>
            </main>
        </div>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
