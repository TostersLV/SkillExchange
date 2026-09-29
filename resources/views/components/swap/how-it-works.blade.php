<section aria-labelledby="how-it-works" class="bg-navy text-paper dark:border-y dark:border-line dark:bg-band">
    <div class="mx-auto w-full max-w-[1200px] px-4 py-16 sm:px-6 lg:px-8 lg:py-20">
        <div class="max-w-2xl">
            <p class="text-xs font-semibold tracking-[0.08em] text-paper/70 uppercase">How it works</p>
            <h2 id="how-it-works" class="mt-2 text-2xl font-semibold tracking-tight text-paper sm:text-3xl">
                Three steps to your first exchange
            </h2>
        </div>

        <ol class="mt-10 grid gap-8 md:grid-cols-3 md:gap-10">
            @foreach ([
                ['icon' => 'pencil-square', 'title' => 'Post your skill', 'body' => 'Describe what you can teach and what you would like to learn in return.'],
                ['icon' => 'user-group', 'title' => 'Match with someone', 'body' => 'Browse offers or wait for someone to reach out, then accept the match that fits.'],
                ['icon' => 'star', 'title' => 'Exchange & leave a review', 'body' => 'Meet, trade skills, then rate each other so the next person knows who to trust.'],
            ] as $step)
                <li class="flex gap-4">
                    <span class="flex size-10 shrink-0 items-center justify-center rounded-lg border border-paper/20 text-paper">
                        <flux:icon :name="$step['icon']" variant="outline" class="size-5" aria-hidden="true" />
                    </span>
                    <div>
                        <p class="text-sm font-medium text-paper/60">Step {{ $loop->iteration }}</p>
                        <h3 class="mt-0.5 text-base font-semibold text-paper">{{ $step['title'] }}</h3>
                        <p class="mt-1.5 text-sm leading-6 text-paper/75">{{ $step['body'] }}</p>
                    </div>
                </li>
            @endforeach
        </ol>

        <div class="mt-12 flex items-start gap-3 rounded-xl border border-paper/15 bg-paper/5 p-5 sm:items-center">
            <flux:icon.shield-check variant="outline" class="size-6 shrink-0 text-amber" aria-hidden="true" />
            <p class="text-sm leading-6 text-paper/85">
                <span class="font-semibold text-paper">Safe exchanges.</span>
                After every completed swap both people leave a rating, so each member builds a visible reputation
                over time. Check someone's profile and reviews before you accept.
            </p>
        </div>
    </div>
</section>
