<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfilePhotoController extends Controller
{
    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'photo' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
                'dimensions:min_width=64,min_height=64,max_width=2048,max_height=2048',
            ],
        ], [
            'photo.image' => 'Choose a valid JPG, PNG, or WEBP image.',
            'photo.mimes' => 'Profile photos must be JPG, PNG, or WEBP.',
            'photo.max' => 'The cropped photo must be 5 MB or smaller.',
            'photo.dimensions' => 'The cropped photo must be at least 64 × 64 pixels.',
        ]);

        $user = $request->user();
        $oldPath = $user->profile_photo_path;
        $path = $validated['photo']->store("profile-photos/{$user->id}", 'public');

        if (! is_string($path) || $path === '') {
            return response()->json(['message' => 'The profile photo could not be saved. Please try again.'], 500);
        }

        if (! $user->forceFill(['profile_photo_path' => $path])->save()) {
            Storage::disk('public')->delete($path);

            return response()->json(['message' => 'Your profile could not be updated. Please try again.'], 500);
        }

        if (is_string($oldPath) && str_starts_with($oldPath, "profile-photos/{$user->id}/")) {
            Storage::disk('public')->delete($oldPath);
        }

        return response()->json([
            'message' => 'Profile photo updated.',
            'url' => Storage::disk('public')->url($path),
        ]);
    }
}
