<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostCategory extends Model
{
    protected $table = 'post_categories';
    protected $primaryKey = 'post_category_id';
    protected $fillable = [
        'post_id',
        'category',
        'category_name'
    ];
}
