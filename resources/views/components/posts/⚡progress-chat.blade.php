<?php

use App\Models\PostOffer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

new class extends Component {
    public PostOffer $offer;

    public string $body = '';

    public function sendMessage(): void
    {
        Gate::authorize('view', $this->offer);

        $this->validate(['body' => 'required|string|max:1000']);

        $this->offer->messages()->create([
            'user_id' => Auth::id(),
            'body' => $this->body,
        ]);

        $this->reset('body');
    }

    public function with(): array
    {
        return [
            'messages' => $this->offer->messages()->with('user')->get(),
            'otherUsername' => $this->offer->user_id === Auth::id()
                ? $this->offer->post->user->username
                : $this->offer->user->username,
        ];
    }
};
?>

<div class="flex min-h-0 flex-1 flex-col" wire:poll.5s>
    <div class="flex items-center gap-3">
        <span class="flex size-9 items-center justify-center rounded-lg bg-brand/6 text-strong">
            <flux:icon.chat-bubble-left-right variant="outline" class="size-5" />
        </span>
        <h2 class="text-base font-semibold text-strong">Chat with {{ $otherUsername }}</h2>
    </div>

    <div class="mt-4 flex-1 space-y-3 overflow-y-auto rounded-lg border border-line bg-page p-4" role="log" aria-label="Messages">
        @forelse ($messages as $message)
            @php($isMine = $message->user_id === auth()->id())
            <div class="flex {{ $isMine ? 'justify-end' : 'justify-start' }}">
                <div
                    class="max-w-[80%] rounded-xl px-3.5 py-2.5 {{ $isMine ? 'rounded-br-sm bg-brand text-on-brand' : 'rounded-bl-sm border border-line bg-surface text-fg' }}">
                    <p class="text-sm whitespace-pre-line">{{ $message->body }}</p>
                    <p class="mt-1 text-[11px] {{ $isMine ? 'text-on-brand/70' : 'text-muted' }}">
                        {{ $message->created_at->diffForHumans() }}
                    </p>
                </div>
            </div>
        @empty
            <div class="flex h-full flex-col items-center justify-center gap-2 text-center">
                <flux:icon.chat-bubble-oval-left variant="outline" class="size-7 text-strong" />
                <p class="text-sm text-muted">No messages yet. Say hi!</p>
            </div>
        @endforelse
    </div>

    <form wire:submit="sendMessage" class="mt-4 flex items-end gap-2">
        <flux:textarea wire:model="body" rows="1" placeholder="Write your message here" aria-label="Message" class="flex-1" />
        <x-swap.button type="submit" variant="primary">
            Send
            <flux:icon.paper-airplane variant="micro" />
        </x-swap.button>
    </form>
</div>
