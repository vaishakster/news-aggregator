<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'preferred_sources',
        'preferred_categories',
        'preferred_authors',
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];
}
