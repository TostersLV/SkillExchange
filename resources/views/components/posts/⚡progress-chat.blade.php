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
    @php
        $otherUser = $offer->user_id === auth()->id() ? $offer->post->user : $offer->user;
    @endphp

    {{-- Header --}}
    <div class="flex items-center gap-3 border-b border-line px-5 py-4">
        <flux:avatar size="sm" :name="$otherUser->username" :initials="$otherUser->initials()" />
        <div class="min-w-0">
            <h2 class="truncate text-sm font-semibold text-strong">{{ $otherUsername }}</h2>
            <p class="truncate text-xs text-muted">{{ $offer->post->offering_skill }} &harr; {{ $offer->post->looking_skill }}</p>
        </div>
    </div>

    {{-- Messages: column-reverse keeps the view pinned to the newest message without JavaScript. --}}
    <div class="flex min-h-0 flex-1 flex-col-reverse overflow-y-auto bg-raised px-4 py-5 sm:px-5" role="log" aria-label="Messages">
        <div class="space-y-1">
            @forelse ($messages as $message)
                @php
                    $isMine = $message->user_id === auth()->id();
                    $previous = $loop->first ? null : $messages[$loop->index - 1];
                    $newDay = ! $previous || ! $previous->created_at->isSameDay($message->created_at);
                    $startsGroup = $newDay || $previous->user_id !== $message->user_id
                        || $previous->created_at->diffInMinutes($message->created_at) > 5;
                @endphp

                @if ($newDay)
                    <div class="flex items-center gap-3 py-3" wire:key="day-{{ $message->id }}">
                        <span class="h-px flex-1 bg-line"></span>
                        <span class="text-[11px] font-medium text-muted">
                            {{ $message->created_at->isToday() ? 'Today' : ($message->created_at->isYesterday() ? 'Yesterday' : $message->created_at->format('M j, Y')) }}
                        </span>
                        <span class="h-px flex-1 bg-line"></span>
                    </div>
                @endif

                <div wire:key="message-{{ $message->id }}" @class([
                    'flex items-end gap-2',
                    'justify-end' => $isMine,
                    'pt-3' => $startsGroup && ! $newDay,
                ])>
                    @unless ($isMine)
                        <div class="w-7 shrink-0">
                            @if ($startsGroup)
                                <flux:avatar size="xs" :name="$message->user->username" :initials="$message->user->initials()" />
                            @endif
                        </div>
                    @endunless

                    <div @class(['flex max-w-[78%] flex-col', 'items-end' => $isMine, 'items-start' => ! $isMine])>
                        <div @class([
                            'rounded-2xl px-3.5 py-2 text-sm leading-6 break-words whitespace-pre-line',
                            'bg-brand text-on-brand' => $isMine,
                            'border border-line bg-surface text-fg' => ! $isMine,
                            'rounded-br-md' => $isMine && $startsGroup,
                            'rounded-bl-md' => ! $isMine && $startsGroup,
                        ])>{{ $message->body }}</div>

                        @if ($startsGroup)
                            <span class="mt-1 px-1 text-[11px] text-muted">{{ $message->created_at->format('H:i') }}</span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="flex flex-col items-center justify-center gap-3 py-16 text-center">
                    <span class="flex size-12 items-center justify-center rounded-full bg-brand/6 text-strong">
                        <flux:icon.chat-bubble-left-right variant="outline" class="size-6" />
                    </span>
                    <div>
                        <p class="text-sm font-semibold text-strong">Start the conversation</p>
                        <p class="mt-1 text-sm text-muted">Say hi to {{ $otherUsername }} and agree on when to meet.</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Composer --}}
    <form wire:submit="sendMessage" class="border-t border-line p-3 sm:p-4">
        <div class="chat-composer flex items-end gap-2 rounded-2xl border border-line-strong bg-surface py-1.5 ps-2 pe-1.5 transition-colors focus-within:border-brand">
            <flux:textarea wire:model="body" rows="auto" resize="none" placeholder="Write a message..." aria-label="Message" class="max-h-32 flex-1" />
            <button type="submit" aria-label="Send message"
                class="mb-0.5 flex size-9 shrink-0 items-center justify-center rounded-full bg-brand text-on-brand transition-colors duration-150 hover:bg-brand/85 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand">
                <flux:icon.paper-airplane variant="micro" class="size-4" />
            </button>
        </div>
        @error('body')
            <p class="mt-2 px-1 text-xs text-error">{{ $message }}</p>
        @enderror
    </form>
</div>
