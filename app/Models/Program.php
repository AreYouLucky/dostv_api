<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    protected $table = 'programs';
    protected $primaryKey = 'program_id';
    protected $fillable = [
        'code',
        'title',
        'description',
        'agency',
        'image',
        'trailer',
        'date_started',
        'is_active',
        'is_banner',
        'order',
        'program_type'
    ];

    public function episodes()
    {
        return $this->hasMany(Post::class, 'program_id','program_id');
    }
}
