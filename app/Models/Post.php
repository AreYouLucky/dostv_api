<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $table =  'posts';
    protected $primaryKey = 'post_id';
    protected $fillable = [
        'title',
        'type',
        'program_id',
        'description',
        'excerpt',
        'episode',
        'content',
        'platform',
        'url',
        'trailer',
        'banner',
        'thumbnail',
        'guest',
        'agency',
        'tags',
        'date_published',
        'is_featured',
        'status',
        'slug',
        'is_converted',
        'female',
        'male'
    ];

    public function categories()
    {
        return $this->hasMany(PostCategory::class, 'post_id', 'post_id');
    }
    public function agencies()
    {
        return $this->hasMany(PostAgency::class, 'post_id', 'post_id');
    }
        public function regions()
    {
        return $this->hasMany(PostRegion::class, 'post_id', 'post_id');
    }

    public function post_program()
    {
        return $this->belongsTo(Program::class, 'program_id', 'program_id');
    }
}
