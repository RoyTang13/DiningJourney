<?php

namespace App\Http\Controllers;

use App\Models\Place;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class RecommendationController extends Controller
{
    public function index(Request $request): View
    {
        $type = $request->query('type', 'attraction');
        abort_unless(in_array($type, ['attraction', 'restaurant'], true), 404);

        $user = $request->user();
        $interests = collect($type === 'attraction'
            ? ($user?->travel_interests ?? [])
            : ($user?->dining_interests ?? []))
            ->map(fn (string $interest): string => Str::lower($interest));
        $location = $type === 'restaurant' ? $user?->dining_location : null;
        $budget = $type === 'restaurant' ? $user?->budget : null;

        $places = Place::where('type', $type)->get()
            ->filter(fn (Place $place): bool => ! $budget || ! $place->price_level || $place->price_level <= $budget)
            ->sortByDesc(function (Place $place) use ($interests, $location): int {
                $tags = collect($place->tags)->map(fn (string $tag): string => Str::lower($tag));
                $interestScore = $tags->intersect($interests)->count() * 10;
                $locationScore = $location && Str::contains(Str::lower($place->area), Str::lower($location)) ? 5 : 0;

                return $interestScore + $locationScore + (int) round((float) $place->rating);
            })->take(6);

        return view('recommendations.index', [
            'places' => $places,
            'type' => $type,
            'title' => $type === 'attraction' ? 'Attraction Recommendations' : 'Restaurant Recommendations',
            'personalized' => $user !== null && ($interests->isNotEmpty() || $location || $budget),
        ]);
    }
}
