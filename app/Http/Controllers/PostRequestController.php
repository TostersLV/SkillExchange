<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class PostRequestController extends Controller
{
    public function index(): View
    {
        return view('postsrequest.index');
    }
}
