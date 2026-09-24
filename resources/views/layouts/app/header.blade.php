<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" @class(['dark' => request()->cookie('appearance') === 'dark'])>
    <head>
        @include('partials.head')
    </head>
    <body class="flex min-h-screen flex-col bg-page font-sans text-fg antialiased">
        <a href="#main"
            class="sr-only z-50 rounded-lg bg-brand px-4 py-2 text-sm font-semibold text-on-brand focus:not-sr-only focus:fixed focus:top-3 focus:left-3">
            Skip to content
        </a>

        <flux:header container class="sticky top-0 z-10 h-16 border-b border-line !bg-surface">
            <flux:sidebar.toggle class="mr-2 lg:hidden" icon="bars-2" inset="left" aria-label="Open menu" />

            <x-app-logo href="{{ route('home') }}" />

            {{-- Absolutely centered in the header so it stays in the middle regardless of the logo/user-menu widths. --}}
            <flux:navbar class="absolute inset-y-0 left-1/2 flex -translate-x-1/2 items-center gap-1 !py-0 max-lg:hidden">
                <flux:navbar.item :href="route('home')" :current="request()->routeIs('home')" wire:navigate>Home</flux:navbar.item>
                <flux:navbar.item :href="route('posts.create')" :current="request()->routeIs('posts.create')" wire:navigate>Create</flux:navbar.item>
                <flux:navbar.item :href="route('posts.requests')" :current="request()->routeIs('posts.requests')" wire:navigate>Requests</flux:navbar.item>
                <flux:navbar.item :href="route('posts.offers')" :current="request()->routeIs('posts.offers')" wire:navigate>Offers</flux:navbar.item>
                <flux:navbar.item :href="route('posts.progress')" :current="request()->routeIs('posts.progress')" wire:navigate>In Progress</flux:navbar.item>
            </flux:navbar>

            <flux:spacer />

            <flux:button x-data x-on:click="$flux.dark = ! $flux.dark" variant="subtle" size="sm" square class="mr-1"
                aria-label="Toggle dark mode">
                <flux:icon.moon variant="mini" class="dark:hidden" />
                <flux:icon.sun variant="mini" class="hidden dark:block" />
            </flux:button>

            <x-desktop-user-menu />
        </flux:header>

        <!-- Mobile Menu -->
        <flux:sidebar collapsible="mobile" sticky class="border-e border-line !bg-surface lg:hidden">
            <flux:sidebar.header>
                <x-app-logo :sidebar="true" href="{{ route('home') }}" />
                <flux:sidebar.collapse class="in-data-flux-sidebar-on-desktop:not-in-data-flux-sidebar-collapsed-desktop:-mr-2" />
            </flux:sidebar.header>

            <flux:sidebar.nav>
                <flux:sidebar.group heading="Menu">
                    <flux:sidebar.item icon="layout-grid" :href="route('home')" :current="request()->routeIs('home')" wire:navigate>Home</flux:sidebar.item>
                    <flux:sidebar.item icon="plus" :href="route('posts.create')" :current="request()->routeIs('posts.create')" wire:navigate>Create</flux:sidebar.item>
                    <flux:sidebar.item icon="inbox" :href="route('posts.requests')" :current="request()->routeIs('posts.requests')" wire:navigate>Requests</flux:sidebar.item>
                    <flux:sidebar.item icon="hand-raised" :href="route('posts.offers')" :current="request()->routeIs('posts.offers')" wire:navigate>Offers</flux:sidebar.item>
                    <flux:sidebar.item icon="arrow-path" :href="route('posts.progress')" :current="request()->routeIs('posts.progress')" wire:navigate>In Progress</flux:sidebar.item>
                </flux:sidebar.group>
            </flux:sidebar.nav>

            <flux:spacer />
        </flux:sidebar>

        <main id="main" class="flex-1">
            {{ $slot }}
        </main>

        <footer class="mt-24 bg-navy text-paper dark:border-t dark:border-line dark:bg-band">
            <div class="mx-auto flex w-full max-w-[1200px] flex-col gap-8 px-4 py-12 sm:px-6 md:flex-row md:items-start md:justify-between lg:px-8">
                <div class="max-w-xs space-y-3">
                    <x-app-logo href="{{ route('home') }}" :sidebar="true" inverse />
                    <p class="text-sm leading-6 text-paper/70">A community for trading skills. No money changes hands, just people teaching each other.</p>
                </div>

                <nav aria-label="Footer" class="grid grid-cols-2 gap-x-12 gap-y-3 text-sm sm:grid-cols-3">
                    <a href="{{ route('home') }}" class="rounded text-paper/80 transition-colors hover:text-paper focus-visible:outline-paper" wire:navigate>Browse offers</a>
                    <a href="{{ route('posts.create') }}" class="rounded text-paper/80 transition-colors hover:text-paper focus-visible:outline-paper" wire:navigate>Share a skill</a>
                    <a href="{{ route('posts.requests') }}" class="rounded text-paper/80 transition-colors hover:text-paper focus-visible:outline-paper" wire:navigate>Requests</a>
                    <a href="{{ route('posts.offers') }}" class="rounded text-paper/80 transition-colors hover:text-paper focus-visible:outline-paper" wire:navigate>Offers</a>
                    <a href="{{ route('posts.progress') }}" class="rounded text-paper/80 transition-colors hover:text-paper focus-visible:outline-paper" wire:navigate>In Progress</a>
                    <a href="{{ route('profile.edit') }}" class="rounded text-paper/80 transition-colors hover:text-paper focus-visible:outline-paper" wire:navigate>Settings</a>
                </nav>
            </div>
        </footer>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
