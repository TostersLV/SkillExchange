<?php

namespace App\Http\Controllers;

use App\Models\PostOffer;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class PostRequestController extends Controller
{
    public function index(): View
    {
        $offers = PostOffer::query()->with('post.user')->where('user_id', Auth::id())->latest()->get();

        return view('postsrequest.index', compact('offers'));
    }
}
