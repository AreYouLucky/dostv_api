<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    protected $table = 'testimonials';
    protected $primaryKey = 'testimonial_id';
    protected $fillable = [
        'title',
        'guest',
        'thumbnail',
        'url',
        'description',
        'excerpt',
        'date_published',
        'is_banner',
        'is_active',
        'slug'
    ];
}
