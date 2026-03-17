<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\InitialApiController;
use App\Http\Controllers\Frontend\PostApiController;
use App\Http\Controllers\Frontend\YoutubeController;
use App\Http\Controllers\Frontend\ProgramApiController;
use App\Http\Controllers\Frontend\HomeApiController;
use App\Http\Controllers\Frontend\PartnersApiController;
use App\Http\Controllers\Frontend\SearchApiController;

Route::get('/load-home-page-mounts', [HomeApiController::class, 'loadHomePageMounts']);
Route::middleware(['api.token', 'throttle:40,1'])->group(function () {
    // Mount Load | App Shell
    Route::get('/load-programs', [InitialApiController::class, 'loadPrograms']);
    Route::get('/load-categories', [InitialApiController::class, 'loadCategories']);
    Route::get('/load-navigation-data', [InitialApiController::class, 'loadNavigationData']);

    //Home Page
    Route::get('/load-banners', [HomeApiController::class, 'loadBanners']);
    Route::get('/get-recent-posts', [HomeApiController::class, 'getRecentPosts']);
    Route::get('/get-featured-posts', [HomeApiController::class, 'getFeaturedPosts']);
    Route::get('/youtube/top-videos/{year}', [YoutubeController::class, 'topVideos']);
    Route::get('/get-featured-programs', [HomeApiController::class, 'getFeaturedPrograms']);
    Route::get('/get-featured-programs-posts', [HomeApiController::class, 'getFeaturedProgramsPosts']);

    //Program related post
    Route::get('/get-program-info/{code}', [ProgramApiController::class, 'getProgramInfo']);
    Route::get('/get-program-seasons-posts/{code}', [ProgramApiController::class, 'getProgramSeasonsPosts']);
    Route::get('/get-program-posts/{code}', [ProgramApiController::class, 'getProgramPosts']);

    //Partners
    Route::get('/load-partners', [PartnersApiController::class, 'loadPartners']);

    Route::get('/load-advertisements', [InitialAPIController::class, 'loadAdvertisements']);
    Route::get('/get-dashboard-posts', [PostApiController::class, 'getDashboardPost']);

    //Posts
    // Route::get('/recent-posts', [PostApiController::class, 'loadRecentPosts']);
    Route::get('/get-post/{slug}', [PostApiController::class, 'getPost']);
    Route::get('/get-related-post-by-program/{program_id}', [PostApiController::class, 'getRelatedPostByProgram']);
    //Search
    Route::get('/search', [SearchApiController::class, 'search']);
    Route::get('/advance-search', [SearchApiController::class, 'advanceSearch']);
    Route::get('/load-search-mounts', [SearchApiController::class, 'loadSearchMounts']);
});
