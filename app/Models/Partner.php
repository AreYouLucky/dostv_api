<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    protected $table = 'partners';
    protected $primaryKey = 'partner_id';
    protected $fillable = [
        'label',
        'description',
        'featured_image',
        'is_active',
    ];
}
