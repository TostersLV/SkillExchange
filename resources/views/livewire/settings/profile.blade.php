<section class="mx-auto w-full max-w-5xl px-4 py-8 sm:px-6 lg:py-12">
    @include('partials.settings-heading')

    <flux:heading level="2" class="sr-only">{{ __('Profile settings') }}</flux:heading>

    <x-settings.layout :heading="__('Profile')" :subheading="__('Update your name and email address')">
        <form wire:submit="updateProfileInformation" class="my-6 w-full space-y-6">
            <div class="flex items-center gap-4">
                <flux:avatar
                    size="xl"
                    circle
                    :src="$photo?->temporaryUrl() ?? auth()->user()->profilePictureUrl()"
                    :name="auth()->user()->username"
                    :initials="auth()->user()->initials()"
                />

                <div class="flex-1 space-y-2">
                    <flux:input wire:model="photo" type="file" accept="image/png,image/jpeg,image/webp" :label="__('Profile picture')" />
                    <flux:text size="sm">{{ __('JPG, PNG or WebP, up to 2 MB.') }}</flux:text>

                    @if ($photo || auth()->user()->profile_picture)
                        <flux:button size="sm" variant="ghost" wire:click="removePhoto">
                            {{ $photo ? __('Discard selection') : __('Remove picture') }}
                        </flux:button>
                    @endif
                </div>
            </div>

            <flux:input wire:model="username" :label="__('Username')" type="text" required autofocus autocomplete="username" />

            <div>
                <flux:input wire:model="email" :label="__('Email')" type="email" required autocomplete="email" />

            </div>

            <div class="flex items-center gap-4">
                <flux:button variant="primary" type="submit">{{ __('Save') }}</flux:button>
            </div>
        </form>

            <livewire:settings.delete-user-form />
    </x-settings.layout>
</section>
