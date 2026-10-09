<?php

use App\Models\Category;
use App\Models\Post;
use App\Models\Review;
use App\PostStatus;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

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

    /**
     * A new search or category starts again from the first page.
     */
    public function updated(string $property): void
    {
        if (in_array($property, ['search', 'teach', 'categoryId'], true)) {
            $this->resetPage();
        }
    }

    public function clearFilters(): void
    {
        $this->reset('search', 'teach', 'categoryId');
        $this->resetPage();
    }

    public function swapFields(): void
    {
        [$this->search, $this->teach] = [$this->teach, $this->search];
        $this->resetPage();
    }

    public function sortBy(string $sort): void
    {
        $this->sort = in_array($sort, ['best', 'newest', 'rated'], true) ? $sort : 'best';
        $this->resetPage();
    }

    /**
     * Adds an "is_match" column: 1 when the post's author wants a skill the current user offers in one of
     * their own posts (either skill name containing the other), otherwise 0. It's worked out in SQL so
     * "best match" sorts across all pages, not just the page being shown.
     *
     * @param  Builder<Post>  $query
     */
    private function selectIsMatch(Builder $query): void
    {
        $user = auth()->user();
        $mySkills = $user->posts()->pluck('offering_skill')
            ->map(fn (string $skill) => mb_strtolower(trim($skill)))
            ->filter()
            ->unique()
            ->values();

        if ($mySkills->isEmpty()) {
            $query->selectRaw('0 as is_match');

            return;
        }

        // INSTR(text, part) > 0 means "text contains part", and it works on both MySQL and SQLite
        $skillChecks = $mySkills
            ->map(fn () => '(INSTR(LOWER(posts.looking_skill), ?) > 0 OR INSTR(?, LOWER(posts.looking_skill)) > 0)')
            ->implode(' OR ');

        $query->selectRaw(
            "CASE WHEN posts.user_id <> ? AND ({$skillChecks}) THEN 1 ELSE 0 END as is_match",
            [$user->id, ...$mySkills->flatMap(fn (string $skill) => [$skill, $skill])],
        );
    }

    public function with(): array
    {
        $query = Post::query()
            ->select('posts.*')
            ->with(['user' => fn ($query) => $query->withAvg('reviewsReceived', 'review'), 'category'])
            // Explore lists only exchanges that are still open to offers
            ->where('posts.status', PostStatus::AVAILABLE)
            ->when($this->search !== '', function ($query) {
                $query->where(function ($query) {
                    $query
                        ->where('offering_skill', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->teach !== '', fn ($query) => $query->where('looking_skill', 'like', '%' . $this->teach . '%'))
            ->when($this->categoryId !== '', fn ($query) => $query->where('category_id', $this->categoryId));

        $this->selectIsMatch($query);

        match ($this->sort) {
            'rated' => $query->orderByDesc(
                Review::query()->selectRaw('AVG(review)')->whereColumn('reviewee_id', 'posts.user_id')
            ),
            'newest' => null,
            default => $query->orderByDesc('is_match'),
        };

        $posts = $query->latest()->paginate(15);

        return [
            'posts' => $posts,
            'matches' => $posts->getCollection()
                ->filter(fn (Post $post) => (int) $post->getAttribute('is_match') === 1)
                ->pluck('id'),
            'closedOffers' => auth()->user()->closedOfferStatusesByPost(),
            'categories' => Category::orderBy('name')->get(),
            'isFiltered' => $this->search !== '' || $this->teach !== '' || $this->categoryId !== '',
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
                <strong class="font-bold text-strong">{{ $posts->total() }}</strong> {{ str('exchange')->plural($posts->total()) }}
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

        @if ($isFiltered)
            <x-swap.button wire:click="clearFilters" variant="ghost" size="sm">
                <flux:icon.x-mark variant="micro" />
                Clear
            </x-swap.button>
        @endif
    </div>

    <x-posts.results :posts="$posts" :matches="$matches" :closed-offers="$closedOffers" :filtered="$isFiltered" />

    @if ($posts->hasPages())
        <nav class="flex items-center justify-between gap-3 pt-2" aria-label="Pagination">
            <x-swap.button wire:click="previousPage" variant="secondary" size="sm" :disabled="$posts->onFirstPage()">
                <flux:icon.arrow-left variant="micro" />
                Previous
            </x-swap.button>

            <p class="text-sm text-muted">
                Page <strong class="font-bold text-strong">{{ $posts->currentPage() }}</strong> of {{ $posts->lastPage() }}
            </p>

            <x-swap.button wire:click="nextPage" variant="secondary" size="sm" :disabled="! $posts->hasMorePages()">
                Next
                <flux:icon.arrow-right variant="micro" />
            </x-swap.button>
        </nav>
    @endif
</div>
