<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Models\PostOffer;
use App\PostOfferStatus;
use App\PostStatus;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class PostController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        $awaitingReview = PostOffer::query()->awaitingReviewBy($user)->with(['post', 'user'])->latest()->get();

        $pendingOffersCount = $user->pendingReceivedOffersCount();

        $activeExchangesCount = $user->exchanges()
            ->whereRelation('post', 'status', PostStatus::IN_PROGRESS)
            ->count();

        $completedExchangesCount = $user->completedExchangesCount();

        $reviewsCount = $user->reviewsReceived()->count();

        $taughtSkills = $user->posts()->latest()->pluck('offering_skill')->unique()->take(3);

        return view('posts.index', [
            'awaitingReview' => $awaitingReview,
            'pendingOffersCount' => $pendingOffersCount,
            'activeExchangesCount' => $activeExchangesCount,
            'completedExchangesCount' => $completedExchangesCount,
            'reviewsCount' => $reviewsCount,
            'taughtSkills' => $taughtSkills,
            'activity' => $this->recentActivity(),
        ]);
    }

    /**
     * Recent things that happened on the platform, for the live activity strip.
     *
     * @return list<array{lead: string, text: string, time: string|null}>
     */
    private function recentActivity(): array
    {
        $completedSwaps = Post::query()
            ->with('user')
            ->where('status', PostStatus::COMPLETED)
            ->latest('updated_at')
            ->limit(3)
            ->get()
            ->map(fn (Post $post) => $this->activityItem(
                $post->user->username,
                "swapped {$post->offering_skill} → {$post->looking_skill}",
                $post->updated_at->diffForHumans(),
            ));

        $newPosts = Post::query()
            ->where('status', PostStatus::AVAILABLE)
            ->latest()
            ->limit(3)
            ->get()
            ->map(fn (Post $post) => $this->activityItem(
                'New:',
                "{$post->offering_skill} ⇄ {$post->looking_skill}",
                $post->created_at->diffForHumans(),
            ));

        $completedThisWeek = Post::query()
            ->where('status', PostStatus::COMPLETED)
            ->where('updated_at', '>=', now()->subWeek())
            ->count();

        // Alternate new posts and completed swaps, then close with the weekly total.
        $activity = [];

        foreach (range(0, 2) as $index) {
            foreach ([$newPosts->get($index), $completedSwaps->get($index)] as $item) {
                if ($item !== null) {
                    $activity[] = $item;
                }
            }
        }

        if ($completedThisWeek > 0) {
            $activity[] = $this->activityItem(
                (string) $completedThisWeek,
                str('exchange')->plural($completedThisWeek).' completed this week',
            );
        }

        return $activity;
    }

    /**
     * One entry of the live activity strip.
     *
     * @return array{lead: string, text: string, time: string|null}
     */
    private function activityItem(string $lead, string $text, ?string $time = null): array
    {
        return ['lead' => $lead, 'text' => $text, 'time' => $time];
    }

    public function show(Post $post): View|RedirectResponse
    {
        if (Gate::denies('view', $post)) {
            return redirect()->route('home')->with('status', 'That post is no longer available.');
        }

        $post->load(['user', 'category']);

        return view('posts.show', compact('post'));
    }

    public function create(): View
    {
        $categories = Category::all();

        return view('posts.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'offering_skill' => ['required', 'string', 'min:4', 'max:30'],
            'looking_skill' => ['required', 'string', 'min:4', 'max:30'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'description' => ['nullable', 'string', 'min:4', 'max:200'],
        ]);

        $post = new Post;
        $post->user_id = $request->user()->id;
        $post->category_id = $request->category_id;
        $post->offering_skill = $request->offering_skill;
        $post->looking_skill = $request->looking_skill;
        $post->description = $request->description;
        $post->save();

        return redirect()->route('home');

    }

    public function edit(Post $post): View
    {
        Gate::authorize('update', $post);

        $categories = Category::all();

        return view('posts.edit', compact('post', 'categories'));
    }

    public function update(Request $request, Post $post): RedirectResponse
    {
        Gate::authorize('update', $post);

        $request->validate([
            'offering_skill' => ['required', 'string', 'min:4', 'max:30'],
            'looking_skill' => ['required', 'string', 'min:4', 'max:30'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'description' => ['nullable', 'string', 'min:4', 'max:200'],
        ]);

        $post->category_id = $request->category_id;
        $post->offering_skill = $request->offering_skill;
        $post->looking_skill = $request->looking_skill;
        $post->description = $request->description;
        $post->save();

        return redirect()->route('posts.show', $post);
    }

    public function destroy(Post $post): RedirectResponse
    {
        Gate::authorize('delete', $post);

        $post->delete();

        return redirect()->route('home');
    }

    /**
     * Take a post with offer history off the market without deleting it. Waiting offers are declined.
     */
    public function close(Post $post): RedirectResponse
    {
        DB::transaction(function () use ($post) {
            $post = Post::query()->lockForUpdate()->findOrFail($post->id);

            Gate::authorize('close', $post);

            $post->offers()->where('status', PostOfferStatus::PENDING)->update(['status' => PostOfferStatus::REJECTED]);

            $post->status = PostStatus::CANCELLED;
            $post->save();
        });

        return redirect()->route('posts.show', $post);
    }
}
