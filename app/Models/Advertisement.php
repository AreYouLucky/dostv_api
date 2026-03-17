<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Advertisement extends Model
{
    protected $table = 'advertisements';
    protected $primaryKey = 'advertisement_id';
    protected $fillable = [
        'title',
        'thumbnail',
        'url',
        'slug',
        'description',
        'excerpt',
        'is_redirect',
        'is_active',
        'is_banner',
        'order'
    ];
}
