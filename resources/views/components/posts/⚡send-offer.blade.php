<?php

use App\Models\Post;
use App\PostOfferStatus;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Component;

new class extends Component {
    public Post $post;

    public bool $showModal = false;

    #[Validate('nullable|string|max:500')]
    public string $message = '';

    public function sendOffer(): void
    {
        $this->validate();

        if ($this->isOwner() || $this->hasOffered()) {
            return;
        }

        $this->post->offers()->create([
            'user_id' => Auth::id(),
            'message' => $this->message !== '' ? $this->message : null,
            'status' => PostOfferStatus::PENDING,
        ]);

        $this->reset('message', 'showModal');

        Flux::toast(text: __('Offer sent.'), variant: 'success');
    }

    public function isOwner(): bool
    {
        return $this->post->user_id === Auth::id();
    }

    public function hasOffered(): bool
    {
        return $this->post->offers()->where('user_id', Auth::id())->where('status', PostOfferStatus::PENDING)->exists();
    }
};
?>

<div>
    @if ($this->isOwner())
    @elseif ($this->hasOffered())
        <flux:badge size="sm" color="green" icon="check">{{ __('Offer sent') }}</flux:badge>
    @else
        <flux:button size="sm" icon="hand-raised" wire:click="$set('showModal', true)">{{ __('Send offer') }}
        </flux:button>

        <flux:modal wire:model.self="showModal" class="md:w-96">
            <form wire:submit="sendOffer" class="space-y-4">
                <flux:heading size="lg">{{ __('Send an offer') }}</flux:heading>
                <flux:text>{{ __('Offer to help :name with their post.', ['name' => $post->user->username]) }}
                </flux:text>

                <flux:textarea wire:model="message" :label="__('Message (optional)')" rows="3"
                    :placeholder="__('Introduce yourself or add details...')" />

                <div class="flex justify-end gap-2">
                    <flux:modal.close>
                        <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
                    </flux:modal.close>
                    <flux:button type="submit" variant="primary">{{ __('Send offer') }}</flux:button>
                </div>
            </form>
        </flux:modal>
    @endif
</div>
