<?php

use App\Models\Post;
use App\PostOfferStatus;
use App\PostStatus;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Component;

new class extends Component {
    public Post $post;

    public bool $showModal = false;

    public string $message = '';

    public function sendOffer(): void
    {
        $this->validate(['message' => 'nullable|string|max:500']);

        if ($this->isOwner() || $this->hasOffered() || ! $this->isAvailable()) {
            return;
        }

        $this->post->offers()->create([
            'user_id' => Auth::id(),
            'message' => $this->message !== '' ? $this->message : null,
            'status' => PostOfferStatus::PENDING,
        ]);

        $this->reset('message', 'showModal');

        Flux::toast(text: 'Offer sent.', variant: 'success');
    }

    public function isOwner(): bool
    {
        return $this->post->user_id === Auth::id();
    }

    public function hasOffered(): bool
    {
        return $this->post->offers()->where('user_id', Auth::id())->where('status', PostOfferStatus::PENDING)->exists();
    }

    public function isAvailable(): bool
    {
        return $this->post->status === PostStatus::AVAILABLE;
    }
};
?>

<div>
    @if ($this->isOwner())
    @elseif ($this->hasOffered())
        <x-swap.badge icon="check-circle">Offer sent</x-swap.badge>
    @elseif (! $this->isAvailable())
        <x-swap.badge icon="lock-closed">No longer available</x-swap.badge>
    @else
        <x-swap.button size="sm" wire:click="$set('showModal', true)">
            <x-swap.icon class="size-4" />
            Send offer
        </x-swap.button>

        <flux:modal wire:model.self="showModal" class="md:w-96">
            <form wire:submit="sendOffer" class="space-y-4">
                <flux:heading size="lg">Send an offer</flux:heading>
                <flux:text>Offer to help {{ $post->user->username }} with their post.</flux:text>

                <flux:textarea wire:model="message" label="Message (optional)" rows="3"
                    placeholder="Introduce yourself or add details..." />

                <div class="flex justify-end gap-2">
                    <flux:modal.close>
                        <x-swap.button variant="ghost">Cancel</x-swap.button>
                    </flux:modal.close>
                    <x-swap.button type="submit" variant="primary">Send offer</x-swap.button>
                </div>
            </form>
        </flux:modal>
    @endif
</div>
