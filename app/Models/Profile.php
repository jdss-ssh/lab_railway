<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'profession',
        'bio',
        'image_url',
        'linkedin_url',
        'github_url',
        'website_url'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the videos for the profile.
     */
    public function videos()
    {
        return $this->hasMany(Video::class);
    }
}