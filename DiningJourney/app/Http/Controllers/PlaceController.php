<?php

namespace App\Http\Controllers;

use App\Models\Place;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PlaceController extends Controller
{
    public function home(): View
    {
        return view('welcome', [
            'attractions' => Place::where('type', 'attraction')->orderByDesc('rating')->take(3)->get(),
            'restaurants' => Place::where('type', 'restaurant')->orderByDesc('rating')->take(3)->get(),
        ]);
    }

    public function index(string $type, Request $request): View
    {
        abort_unless(in_array($type, ['attraction', 'restaurant'], true), 404);

        $places = Place::query()
            ->where('type', $type)
            ->when($request->filled('q'), function (Builder $query) use ($request): void {
                $term = trim((string) $request->query('q'));
                $query->where(function (Builder $query) use ($term): void {
                    $query->where('name', 'like', "%{$term}%")
                        ->orWhere('category', 'like', "%{$term}%")
                        ->orWhere('area', 'like', "%{$term}%")
                        ->orWhere('description', 'like', "%{$term}%");
                });
            })
            ->when($request->filled('category'), fn (Builder $query) => $query->where('category', $request->query('category')))
            ->when($request->filled('location'), fn (Builder $query) => $query->where('area', 'like', '%'.trim((string) $request->query('location')).'%'))
            ->when($request->filled('budget'), function (Builder $query) use ($request): void {
                $query->where(function (Builder $query) use ($request): void {
                    $query->whereNull('price_level')->orWhere('price_level', '<=', (int) $request->query('budget'));
                });
            })
            ->orderByDesc('rating')
            ->paginate(9)
            ->withQueryString();

        return view('places.index', [
            'places' => $places,
            'type' => $type,
            'title' => $type === 'attraction' ? 'Explore Attractions' : 'Discover Restaurants',
            'categories' => Place::where('type', $type)->select('category')->distinct()->orderBy('category')->pluck('category'),
        ]);
    }

    public function search(Request $request): RedirectResponse
    {
        $filters = $request->validate([
            'type' => ['required', 'in:attraction,restaurant'],
            'q' => ['nullable', 'string', 'max:120'],
        ]);
        $route = $filters['type'] === 'attraction' ? 'places.attractions' : 'places.restaurants';

        return redirect()->route($route, ['q' => $filters['q'] ?? null]);
    }

    public function show(Place $place): View
    {
        return view('places.show', ['place' => $place]);
    }

    public function compare(Request $request): View
    {
        $data = $request->validate([
            'places' => ['required', 'array', 'min:2', 'max:3'],
            'places.*' => [
                'required',
                'integer',
                'distinct',
                Rule::exists('places', 'id')->where('type', 'restaurant'),
            ],
        ]);

        $places = Place::where('type', 'restaurant')->whereIn('id', $data['places'])->get();

        return view('places.compare', ['places' => $places]);
    }
}
