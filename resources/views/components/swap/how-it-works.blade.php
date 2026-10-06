<section aria-labelledby="how-it-works" class="mx-auto w-full max-w-[1200px] px-4 py-20 sm:px-6 lg:px-8 lg:py-28">
    <div class="mx-auto max-w-2xl text-center">
        <p class="eyebrow">How it works</p>
        <h2 id="how-it-works" class="mt-2 text-3xl font-extrabold tracking-[-0.03em] text-strong sm:text-4xl">
            Three steps to your first exchange
        </h2>
        <p class="mt-4 text-lg leading-8 text-muted">No fees, no complicated setup. Just people helping each other learn.</p>
    </div>

    <ol class="relative mt-14 grid gap-10 md:grid-cols-3 md:gap-8">
        {{-- Connecting line between the step numbers (desktop only). --}}
        <span aria-hidden="true" class="absolute top-6 right-[16.66%] left-[16.66%] hidden h-px bg-line-strong md:block"></span>

        @foreach ([
            ['icon' => 'pencil-square', 'title' => 'Post your skill', 'body' => 'Describe what you can teach and what you would like to learn in return.'],
            ['icon' => 'user-group', 'title' => 'Match with someone', 'body' => 'Browse offers or wait for someone to reach out, then accept the match that fits.'],
            ['icon' => 'star', 'title' => 'Exchange & review', 'body' => 'Meet, trade skills, then rate each other so the next person knows who to trust.'],
        ] as $step)
            <li class="relative flex flex-col items-center text-center">
                <span
                    class="relative flex size-12 items-center justify-center rounded-full border border-line-strong bg-surface text-strong shadow-sm">
                    <flux:icon :name="$step['icon']" variant="outline" class="size-5" aria-hidden="true" />
                    <span
                        class="absolute -top-1.5 -right-1.5 flex size-5 items-center justify-center rounded-full bg-swap-strong text-[11px] font-bold text-white">
                        {{ $loop->iteration }}
                    </span>
                </span>
                <h3 class="mt-5 text-lg font-semibold text-strong">{{ $step['title'] }}</h3>
                <p class="mt-2 max-w-xs text-sm leading-6 text-muted">{{ $step['body'] }}</p>
            </li>
        @endforeach
    </ol>
</section>
