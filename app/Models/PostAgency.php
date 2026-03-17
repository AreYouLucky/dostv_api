<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostAgency extends Model
{
    protected $table = 'post_agencies';
    protected $fillable = [
        'post_id', 'agency_id', 'agency_name'
    ];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
