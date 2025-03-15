<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PostController extends Controller
{

    public static function createSlug(string $title)
    {
        $slug = Str::of($title)->slug('-');

        // lookup for the slug that matched anything that ends with '-n',
        // where 'n' is any number that corresponds with the correlative of the slug
        $results = DB::table('posts')
            ->where('slug', 'like', $slug . '%')
            ->get();

        return $slug . strval(count($results)); // Always increases by one in relation to the last repeated title
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PostRequest $request)
    {
        $data = $request->validated();
        $data['slug'] = PostController::createSlug($data['title']);

        /** @var \App\Models\User */
        $user = Auth::user();

        $post = Post::create($data);

        $post->user()->save($user);
        $post->categories()->sync($data['categories']);

        return response()->json($post);
    }
}
