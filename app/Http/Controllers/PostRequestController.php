<?php

namespace App\Http\Controllers;

use App\Models\PostOffer;
use App\PostOfferStatus;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class PostRequestController extends Controller
{
    public function index(): View
    {
        $offers = PostOffer::query()->with('post.user')->where('user_id', Auth::id())->where('status', PostOfferStatus::PENDING)->latest()->get();

        return view('postsrequest.index', compact('offers'));
    }
}
