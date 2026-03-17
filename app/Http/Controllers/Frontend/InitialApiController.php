<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\Category;
use App\Models\Advertisement;

class InitialApiController extends Controller
{
    public function loadPrograms()
    {
        return Program::select('code', 'title', 'description', 'image', 'program_type',)->where('is_active', 1)->orderBy('order', 'desc')->get();
    }

    public function loadCategories(){
        return Category::select('category_id', 'title', 'description')->where('is_active', 1)->orderBy('title', 'asc')->get();
    }

    public function loadAdvertisements()
    {
        return Advertisement::select('title', 'excerpt', 'thumbnail', 'url')->where('is_active', 1)->orderBy('order', 'desc')->get();
    }

    public function loadNavigationData(){
        return [
            'programs' => $this->loadPrograms(),
            'categories' => $this->loadCategories(),
            'advertisements' => $this->loadAdvertisements(),
        ];
    }
}
