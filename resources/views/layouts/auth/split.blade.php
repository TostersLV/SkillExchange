<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white antialiased dark:bg-linear-to-b dark:from-neutral-950 dark:to-neutral-900">
        <div class="relative grid h-dvh flex-col items-center justify-center px-8 sm:px-0 lg:max-w-none lg:grid-cols-2 lg:px-0">
            <div class="bg-muted relative hidden h-full flex-col p-10 text-white lg:flex dark:border-e dark:border-neutral-800">
                <div class="absolute inset-0 bg-neutral-900"></div>
                <a href="{{ route('home') }}" class="relative z-20 flex items-center gap-2 text-lg font-medium" wire:navigate>
                    <span class="flex size-9 items-center justify-center rounded-lg bg-accent text-accent-foreground">
                        <x-app-logo-icon class="size-5" />
                    </span>
                    {{ config('app.name', 'Laravel') }}
                </a>

                <div class="relative z-20 mt-auto">
                    <flux:badge size="sm" color="teal" icon="sparkles">No money changes hands</flux:badge>

                    <blockquote class="mt-4 space-y-2">
                        <flux:heading size="lg">&ldquo;Trade what you know for what you need.&rdquo;</flux:heading>
                        <flux:text class="text-neutral-300">
                            Swap a skill you have for one you want, and build your reputation one exchange at a time.
                        </flux:text>
                    </blockquote>
                </div>
            </div>
            <div class="w-full lg:p-8">
                <div class="mx-auto flex w-full flex-col justify-center space-y-6 sm:w-[350px]">
                    <a href="{{ route('home') }}" class="z-20 flex flex-col items-center gap-2 font-medium lg:hidden" wire:navigate>
                        <span class="flex size-9 items-center justify-center rounded-lg bg-accent text-accent-foreground">
                            <x-app-logo-icon class="size-5" />
                        </span>

                        <span class="sr-only">{{ config('app.name', 'Laravel') }}</span>
                    </a>
                    {{ $slot }}
                </div>
            </div>
        </div>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
