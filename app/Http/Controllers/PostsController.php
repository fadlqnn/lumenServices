<?php

namespace App\Http\Controllers;
use App\Models\Post;

class PostsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function index()
    {
        $posts = Post::orderBy('id', 'desc')->paginate(10);
        $output = [
            "message" => "posts",
            "results" => $posts
        ];

        return response()->json($output, 200);
    }

    //
}
