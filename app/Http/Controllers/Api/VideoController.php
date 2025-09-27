<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Video;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class VideoController extends Controller
{
    /**
     * Display a listing of videos.
     */
    public function index(): JsonResponse
    {
        $videos = Video::with('profile')->get();
        return response()->json($videos);
    }

    /**
     * Store a newly created video.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'profile_id' => 'required|exists:profiles,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'video_url' => 'required|url',
            'thumbnail_url' => 'nullable|url',
            'duration' => 'nullable|integer|min:1',
            'category' => 'nullable|string|max:255',
            'is_featured' => 'boolean',
        ]);

        $video = Video::create($validated);
        $video->load('profile');
        
        return response()->json($video, 201);
    }

    /**
     * Display the specified video.
     */
    public function show(Video $video): JsonResponse
    {
        $video->load('profile');
        return response()->json($video);
    }

    /**
     * Update the specified video.
     */
    public function update(Request $request, Video $video): JsonResponse
    {
        $validated = $request->validate([
            'profile_id' => 'sometimes|exists:profiles,id',
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'video_url' => 'sometimes|url',
            'thumbnail_url' => 'nullable|url',
            'duration' => 'nullable|integer|min:1',
            'category' => 'nullable|string|max:255',
            'is_featured' => 'boolean',
        ]);

        $video->update($validated);
        $video->load('profile');
        
        return response()->json($video);
    }

    /**
     * Remove the specified video.
     */
    public function destroy(Video $video): JsonResponse
    {
        $video->delete();
        return response()->json(null, 204);
    }

    /**
     * Get featured videos.
     */
    public function featured(): JsonResponse
    {
        $videos = Video::with('profile')->where('is_featured', true)->get();
        return response()->json($videos);
    }

    /**
     * Get videos by profile.
     */
    public function byProfile(Profile $profile): JsonResponse
    {
        $videos = $profile->videos()->with('profile')->get();
        return response()->json($videos);
    }
}