<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Partner;

class PartnersApiController extends Controller
{
    public function loadPartners(){
        return Partner::orderBy('order', 'desc')->get();
    }
}
