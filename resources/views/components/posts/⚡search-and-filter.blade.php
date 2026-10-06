<?php

use App\Models\Category;
use App\Models\Post;
use App\PostStatus;
use Illuminate\Support\Collection;
use Livewire\Attributes\Url;
use Livewire\Component;

new class extends Component {
    /** What the visitor wants to learn: matched against what posts offer. */
    #[Url(as: 'q', except: '')]
    public string $search = '';

    /** What the visitor can teach: matched against what posts are looking for. */
    #[Url(as: 'teach', except: '')]
    public string $teach = '';

    #[Url(as: 'category', except: '')]
    public string $categoryId = '';

    #[Url(except: 'best')]
    public string $sort = 'best';

    #[Url(as: 'available', except: false)]
    public bool $onlyAvailable = false;

    public function clearFilters(): void
    {
        $this->reset('search', 'teach', 'categoryId', 'onlyAvailable');
    }

    public function swapFields(): void
    {
        [$this->search, $this->teach] = [$this->teach, $this->search];
    }

    public function sortBy(string $sort): void
    {
        $this->sort = in_array($sort, ['best', 'newest', 'rated'], true) ? $sort : 'best';
    }

    /**
     * A post is a great match when its author wants a skill the current user offers in one of their own posts.
     *
     * @param  Collection<int, Post>  $posts
     * @return Collection<int, int>
     */
    private function matchingPostIds(Collection $posts): Collection
    {
        $user = auth()->user();
        $mySkills = $user->posts()->pluck('offering_skill')->map(fn (string $skill) => mb_strtolower(trim($skill)))->unique();

        return $posts
            ->filter(fn (Post $post) => $post->user_id !== $user->id && $post->status === PostStatus::AVAILABLE)
            ->filter(function (Post $post) use ($mySkills) {
                $wanted = mb_strtolower($post->looking_skill);

                return $mySkills->contains(fn (string $skill) => str_contains($wanted, $skill) || str_contains($skill, $wanted));
            })
            ->pluck('id')
            ->values();
    }

    public function with(): array
    {
        $posts = Post::query()
            ->with(['user', 'category'])
            ->when($this->search !== '', function ($query) {
                $query->where(function ($query) {
                    $query
                        ->where('offering_skill', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->teach !== '', fn ($query) => $query->where('looking_skill', 'like', '%' . $this->teach . '%'))
            ->when($this->categoryId !== '', fn ($query) => $query->where('category_id', $this->categoryId))
            ->when($this->onlyAvailable, fn ($query) => $query->where('status', PostStatus::AVAILABLE))
            ->latest()
            ->get();

        $matches = $this->matchingPostIds($posts);

        // Open exchanges always come first; the chosen sort orders within them.
        $posts = $posts->sortBy([
            fn (Post $a, Post $b) => ($b->status === PostStatus::AVAILABLE) <=> ($a->status === PostStatus::AVAILABLE),
            match ($this->sort) {
                'rated' => fn (Post $a, Post $b) => (float) $b->user->reputation <=> (float) $a->user->reputation,
                'newest' => fn (Post $a, Post $b) => 0,
                default => fn (Post $a, Post $b) => $matches->contains($b->id) <=> $matches->contains($a->id),
            },
            fn (Post $a, Post $b) => $b->created_at <=> $a->created_at,
        ])->values();

        return [
            'posts' => $posts,
            'matches' => $matches,
            'categories' => Category::orderBy('name')->get(),
            'isFiltered' => $this->search !== '' || $this->teach !== '' || $this->categoryId !== '' || $this->onlyAvailable,
        ];
    }
};
?>

<div class="space-y-5">
    {{-- Converter-style search: what you want to learn ⇄ what you can teach. --}}
    <form wire:submit.prevent
        class="flex flex-col gap-2 rounded-[1.25rem] border border-line bg-surface p-2 card-shadow md:flex-row md:items-stretch md:gap-0">
        <label class="flex flex-1 cursor-text flex-col gap-1 rounded-2xl bg-page px-5 py-3 focus-within:ring-2 focus-within:ring-swap/40">
            <span class="text-[11px] font-extrabold tracking-[0.1em] text-swap-text uppercase">I want to learn</span>
            <input wire:model.live.debounce.300ms="search" type="search" placeholder="What do you want to learn?"
                class="w-full border-0 bg-transparent p-0 text-lg font-semibold text-strong placeholder:font-medium placeholder:text-muted/70 focus:outline-none" />
        </label>

        <div class="flex items-center justify-center md:w-16">
            <button type="button" wire:click="swapFields" aria-label="Swap what you want to learn and what you can teach"
                class="flex size-11 items-center justify-center rounded-full border border-line bg-surface text-swap-text shadow-sm transition-transform duration-300 hover:rotate-180 max-md:rotate-90 max-md:hover:rotate-[270deg]">
                <x-swap.icon class="size-5" />
            </button>
        </div>

        <label class="flex flex-1 cursor-text flex-col gap-1 rounded-2xl bg-page px-5 py-3 focus-within:ring-2 focus-within:ring-swap/40">
            <span class="text-[11px] font-extrabold tracking-[0.1em] text-muted uppercase">I can teach</span>
            <input wire:model.live.debounce.300ms="teach" type="search" placeholder="What can you teach?"
                class="w-full border-0 bg-transparent p-0 text-lg font-semibold text-strong placeholder:font-medium placeholder:text-muted/70 focus:outline-none" />
        </label>
    </form>

    <div class="-mx-4 flex gap-2 overflow-x-auto px-4 pb-1 [scrollbar-width:none] sm:mx-0 sm:px-0" role="group" aria-label="Filter by category">
        <x-swap.chip wire:click="$set('categoryId', '')" :active="$categoryId === ''" class="shrink-0">
            <flux:icon.squares-2x2 variant="micro" class="size-3.5" aria-hidden="true" />
            All
        </x-swap.chip>

        @foreach ($categories as $category)
            <x-swap.chip wire:click="$set('categoryId', '{{ $category->id }}')" wire:key="category-{{ $category->id }}"
                :active="(string) $categoryId === (string) $category->id" class="shrink-0 whitespace-nowrap">
                <x-swap.category-icon :name="$category->name" />
                {{ $category->name }}
            </x-swap.chip>
        @endforeach
    </div>

    <div class="flex flex-wrap items-center justify-between gap-4 pt-3">
        <div class="flex flex-wrap items-center gap-4">
            <p class="text-sm text-muted" aria-live="polite">
                <strong class="font-bold text-strong">{{ $posts->count() }}</strong> {{ str('exchange')->plural($posts->count()) }}
            </p>

            <div class="flex gap-0.5 rounded-xl bg-raised p-1" role="group" aria-label="Sort by">
                @foreach (['best' => 'Best match', 'newest' => 'Newest', 'rated' => 'Highest rated'] as $value => $label)
                    <button type="button" wire:click="sortBy('{{ $value }}')" aria-pressed="{{ $sort === $value ? 'true' : 'false' }}"
                        @class([
                            'h-9 rounded-lg px-3.5 text-sm font-semibold transition-colors',
                            'bg-surface text-strong shadow-sm' => $sort === $value,
                            'text-muted hover:text-strong' => $sort !== $value,
                        ])>{{ $label }}</button>
                @endforeach
            </div>
        </div>

        <div class="flex items-center gap-4">
            @if ($isFiltered)
                <x-swap.button wire:click="clearFilters" variant="ghost" size="sm">
                    <flux:icon.x-mark variant="micro" />
                    Clear
                </x-swap.button>
            @endif

            <button type="button" role="switch" wire:click="$toggle('onlyAvailable')" aria-checked="{{ $onlyAvailable ? 'true' : 'false' }}"
                class="flex min-h-11 items-center gap-2.5 text-sm font-semibold text-strong">
                <span @class([
                    'relative h-6 w-10 rounded-full transition-colors',
                    'bg-swap-strong' => $onlyAvailable,
                    'bg-line-strong' => ! $onlyAvailable,
                ])>
                    <span @class([
                        'absolute top-[3px] size-[18px] rounded-full bg-white shadow transition-[left]',
                        'left-[19px]' => $onlyAvailable,
                        'left-[3px]' => ! $onlyAvailable,
                    ])></span>
                </span>
                Only available
            </button>
        </div>
    </div>

    <x-posts.results :posts="$posts" :matches="$matches" :filtered="$isFiltered" />
</div>
