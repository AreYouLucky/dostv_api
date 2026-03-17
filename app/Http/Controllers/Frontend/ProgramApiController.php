<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Program;
use App\Models\ProgramSeason;

class ProgramApiController extends Controller
{
    public function getProgramInfo(string $program_slug)
    {
        $program = Program::where('code', $program_slug)->firstOrFail();
        $featured_post = Post::where('program_id', $program->program_id)
            ->with('post_program')
            ->with('categories')
            ->where('is_featured', 1)
            ->where('status', '!=', 'trashed')
            ->where('status', 'published')
            ->latest('date_published')
            ->first();
        $total = Post::where('program_id', $program->program_id)
            ->where('status', 'published')
            ->count();
        return response()->json([
            'program' => $program,
            'featured_post' => $featured_post,
            'total' => $total
        ]);
    }

    public function getProgramSeasonsPosts(string $program_slug)
    {
        $program = Program::where('code', $program_slug)->firstOrFail();

        $seasons = ProgramSeason::where('program_id', $program->program_id)
            ->with([
                'posts' => function ($query) {
                    $query->select(
                        'post_id',
                        'program_id',
                        'season',
                        'title',
                        'slug',
                        'date_published',
                        'thumbnail',
                        'banner',
                        'trailer',
                        'type',
                        'excerpt'
                    )
                        ->where('status', 'published')
                        ->orderByDesc('date_published')
                        ->with([
                            'categories' => function ($q) {
                                $q->select('post_id', 'category', 'category_name', 'post_category_id');
                            }
                        ]);
                }
            ])->orderBy('season', 'desc')
            ->get();

        return response()->json([
            'seasons' => $seasons,
            'program' => $program
        ]);
    }

    public function getProgramPosts(String $string)
    {
        $program = Program::where('code', $string)->firstOrFail();
        $posts = Post::where('program_id', $program->program_id)
            ->where('season', null)
            ->where('status', 'published')
            ->with('categories')
            ->orderBy('date_published', 'desc')
            ->paginate(6);

        return response()->json([
            'posts' => $posts,
            'program' => $program
        ]);
    }
}
