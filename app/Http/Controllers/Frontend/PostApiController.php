<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Program;
use App\Models\Post;
use Illuminate\Support\Facades\Cache;

class PostApiController extends Controller
{
    public function getPost(string $code)
    {
        $post = Post::with(['post_program' => function ($q) {
            $q->select('title', 'code', 'program_id');
        }])
            ->where('slug', $code)
            ->firstOrFail();

        $searchText = trim(
            $post->title . ' ' .
                ($post->excerpt ?? '') . ' ' .
                ($post->description ?? '')
        );

        $related = Post::selectRaw("
                posts.*,
                MATCH(title, excerpt, description, tags)
                AGAINST (? IN NATURAL LANGUAGE MODE) AS relevance
            ", [$searchText])
            ->with(['post_program', 'categories'])
            ->where('posts.post_id', '!=', $post->post_id)
            ->where('posts.status', 'published')
            ->whereRaw("
                MATCH(title, excerpt, description, tags)
                AGAINST (? IN NATURAL LANGUAGE MODE)
            ", [$searchText])
            ->orderByDesc('relevance')
            ->limit(7)
            ->get();

        return response()->json([
            'post' => $post,
            'related' => $related
        ]);
    }

    public function getRelatedPostByProgram(String $code)
    {
        return Post::select('posts.*', 'programs.title as program_title')->with('post_program')
            ->with('categories')
            ->join('programs', 'posts.program_id', '=', 'programs.program_id')
            ->where('programs.code', $code)
            ->where('status', 'published')
            ->inRandomOrder()
            ->take(6)
            ->get();
    }
}
