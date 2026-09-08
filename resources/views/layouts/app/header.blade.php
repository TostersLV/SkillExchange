<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:header container class="border-b border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.toggle class="lg:hidden mr-2" icon="bars-2" inset="left" />

            <x-app-logo href="{{ route('home') }}" wire:navigate />

            <flux:navbar class="-mb-px max-lg:hidden">
                <flux:navbar.item icon="layout-grid" :href="route('home')" :current="request()->routeIs('home')" wire:navigate>{{ __('Home') }}</flux:navbar.item>
                <flux:navbar.item icon="plus" :href="route('posts.create')" :current="request()->routeIs('posts.create')" wire:navigate>{{ __('Create') }}</flux:navbar.item>
                <flux:navbar.item icon="inbox" :href="route('posts.requests')" :current="request()->routeIs('posts.requests')" wire:navigate>{{ __('Requests') }}</flux:navbar.item>
            </flux:navbar>

            <flux:spacer />

            <flux:button x-data x-on:click="$flux.dark = ! $flux.dark" icon="moon" variant="subtle" size="sm" square class="mr-1" aria-label="{{ __('Toggle dark mode') }}" />

            <x-desktop-user-menu />
        </flux:header>

        <!-- Mobile Menu -->
        <flux:sidebar collapsible="mobile" sticky class="lg:hidden border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.header>
                <x-app-logo :sidebar="true" href="{{ route('home') }}" wire:navigate />
                <flux:sidebar.collapse class="in-data-flux-sidebar-on-desktop:not-in-data-flux-sidebar-collapsed-desktop:-mr-2" />
            </flux:sidebar.header>

            <flux:sidebar.nav>
                <flux:sidebar.group :heading="__('Platform')">
                    <flux:sidebar.item icon="layout-grid" :href="route('home')" :current="request()->routeIs('home')" wire:navigate>{{ __('Home') }}</flux:sidebar.item>
                    <flux:sidebar.item icon="plus" :href="route('posts.create')" :current="request()->routeIs('posts.create')" wire:navigate>{{ __('Create') }}</flux:sidebar.item>
                    <flux:sidebar.item icon="inbox" :href="route('posts.requests')" :current="request()->routeIs('posts.requests')" wire:navigate>{{ __('Requests') }}</flux:sidebar.item>
                </flux:sidebar.group>
            </flux:sidebar.nav>

            <flux:spacer />

            
        </flux:sidebar>

        {{ $slot }}

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
