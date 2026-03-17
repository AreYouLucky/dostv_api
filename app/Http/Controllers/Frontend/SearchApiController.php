<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Region;
use App\Models\Agency;
use App\Models\Program;
use App\Models\Category;

class SearchApiController extends Controller
{
    public function search(Request $request)
    {
        if (!$request->filled('search')) {
            return response()->json([]);
        }

        $terms = collect(explode(' ', $request->search))
            ->filter()
            ->map(fn($term) => $term . '*')
            ->implode(' ');

        $query = Post::selectRaw("
            posts.*,
            MATCH(title, excerpt, description, tags)
            AGAINST (? IN BOOLEAN MODE) AS relevance
        ", [$terms])
            ->whereRaw("
            MATCH(title, excerpt, description, tags)
            AGAINST (? IN BOOLEAN MODE)
        ", [$terms])
            ->with('post_program')
            ->where('status', 'published')
            ->orderByDesc('relevance');

        return $query->paginate(6);
    }
    public function advanceSearch(Request $request)
    {
        $query = Post::query()
            ->select('posts.*')
            ->with(['post_program', 'categories'])
            ->where('status', 'published');

        if ($request->filled('search')) {

            $terms = collect(explode(' ', $request->search))
                ->filter()
                ->map(fn($term) => $term . '*')
                ->implode(' ');

            $query->selectRaw("
            posts.*,
            MATCH(title, excerpt, description, tags)
            AGAINST (? IN BOOLEAN MODE) AS relevance
        ", [$terms])
                ->whereRaw("
            MATCH(title, excerpt, description, tags)
            AGAINST (? IN BOOLEAN MODE)
        ", [$terms]);

            $query->orderByDesc('relevance');
        }

        $query->when($request->filled('program'), function ($q) use ($request) {
            $q->where('posts.program_id', $request->program);
        });

        $query->when($request->filled('year'), function ($q) use ($request) {
            $q->whereYear('posts.date_published', $request->year);
        });

        if (!empty($request->categories)) {
            $query->whereExists(function ($q) use ($request) {
                $q->selectRaw(1)
                    ->from('post_categories')
                    ->whereColumn('post_categories.post_id', 'posts.post_id')
                    ->whereIn('post_categories.category', $request->categories);
            });
        }

        if (!empty($request->agencies)) {
            $query->whereExists(function ($q) use ($request) {
                $q->selectRaw(1)
                    ->from('post_agencies')
                    ->whereColumn('post_agencies.post_id', 'posts.post_id')
                    ->whereIn('post_agencies.agency_id', $request->agencies);
            });
        }

        if (!empty($request->regions)) {
            $query->whereExists(function ($q) use ($request) {
                $q->selectRaw(1)
                    ->from('post_regions')
                    ->whereColumn('post_regions.post_id', 'posts.post_id')
                    ->whereIn('post_regions.region_id', $request->regions);
            });
        }
        $query->distinct('posts.post_id');
        $query->orderBy('posts.date_published', 'desc');

        return response()->json(
            $query->paginate(6, ['*'], 'page', $request->query('page', 1))
        );
    }

    public function loadSearchMounts()
    {
        $regions = Region::all();
        $agencies = Agency::all();
        $programs = Program::where('is_active', 1)->orderBy('order', 'desc')->get();
        $categories = Category::where('is_active', 1)->get();
        return [
            'regions' => $regions,
            'agencies' => $agencies,
            'programs' => $programs,
            'categories' => $categories,
        ];
    }
}
