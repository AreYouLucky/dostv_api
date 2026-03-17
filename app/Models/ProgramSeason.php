<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgramSeason extends Model
{
    protected $table = 'program_seasons';
    protected $fillable = [
        'program_id', 'title', 'description', 'thumbnail','season'
    ];

    public function program()
    {
        return $this->belongsTo(Program::class, 'program_id', 'program_id');
    }

    public function posts()
    {
        return $this->hasMany(Post::class,  'season', 'title');
    }
}
