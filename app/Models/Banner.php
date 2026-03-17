<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $table = 'banners';
    protected $primaryKey = 'banner_id';
    protected $fillable = [
        'title',
        'media',
        'code',
        'highlight_text',
        'episodes',
        'description',
        'url',
        'type',
        'is_banner',
        'is_active',
        'order',
        'duration',
        'icon',
        'bg'
    ];
}
