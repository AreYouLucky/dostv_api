<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostRegion extends Model
{
    protected $table = 'post_regions';
    protected $fillable = [
        'post_id', 'region_id', 'region_name'
    ];
}
