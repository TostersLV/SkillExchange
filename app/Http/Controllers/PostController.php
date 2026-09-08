<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PostController extends Controller
{
    public function index(): View
    {
        return view('posts.index');
    }

    public function show(Post $post): View
    {
        $post->load(['user', 'category']);

        return view('posts.show', compact('post'));
    }

    public function create(): View
    {
        $categories = Category::all();

        return view('posts.create', compact('categories'));
    }

    public function store(Request $request)
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

    public function update(Request $request, Post $post)
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
}
