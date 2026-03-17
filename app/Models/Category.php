<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';
    protected $primaryKey = 'category_id';
    protected $fillable = [
        'title',
        'description',
        'is_banner',
        'is_active',
    ];

    public function postCategories()
    {
        return $this->hasMany(PostCategory::class, 'category', 'category_id');
    }
}
