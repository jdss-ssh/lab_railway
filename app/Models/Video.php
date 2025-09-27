<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;

    protected $fillable = [
        'profile_id',
        'title',
        'description',
        'video_url',
        'thumbnail_url',
        'duration',
        'category',
        'is_featured'
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the profile that owns the video.
     */
    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }
}