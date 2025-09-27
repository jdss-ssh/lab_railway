<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProfileController extends Controller
{
    /**
     * Display a listing of profiles.
     */
    public function index(): JsonResponse
    {
        $profiles = Profile::with('videos')->get();
        return response()->json($profiles);
    }

    /**
     * Store a newly created profile.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:profiles',
            'profession' => 'required|string|max:255',
            'bio' => 'nullable|string',
            'image_url' => 'nullable|url',
            'linkedin_url' => 'nullable|url',
            'github_url' => 'nullable|url',
            'website_url' => 'nullable|url',
        ]);

        $profile = Profile::create($validated);
        return response()->json($profile, 201);
    }

    /**
     * Display the specified profile.
     */
    public function show(Profile $profile): JsonResponse
    {
        $profile->load('videos');
        return response()->json($profile);
    }

    /**
     * Update the specified profile.
     */
    public function update(Request $request, Profile $profile): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:profiles,email,' . $profile->id,
            'profession' => 'sometimes|string|max:255',
            'bio' => 'nullable|string',
            'image_url' => 'nullable|url',
            'linkedin_url' => 'nullable|url',
            'github_url' => 'nullable|url',
            'website_url' => 'nullable|url',
        ]);

        $profile->update($validated);
        return response()->json($profile);
    }

    /**
     * Remove the specified profile.
     */
    public function destroy(Profile $profile): JsonResponse
    {
        $profile->delete();
        return response()->json(null, 204);
    }
}