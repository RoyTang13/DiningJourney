<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Place;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PlaceController extends Controller
{
    public function index(string $type, Request $request): View
    {
        $this->ensureType($type);
        $places = Place::where('type', $type)
            ->when($request->filled('q'), function (Builder $query) use ($request): void {
                $term = trim((string) $request->query('q'));
                $query->where(function (Builder $query) use ($term): void {
                    $query->where('name', 'like', "%{$term}%")
                        ->orWhere('category', 'like', "%{$term}%")
                        ->orWhere('area', 'like', "%{$term}%");
                });
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.places.index', [
            'places' => $places,
            'type' => $type,
            'title' => $this->title($type),
        ]);
    }

    public function create(string $type): View
    {
        $this->ensureType($type);

        return view('admin.places.form', [
            'place' => new Place(['type' => $type, 'rating' => 0, 'review_count' => 0]),
            'type' => $type,
            'title' => $this->title($type),
        ]);
    }

    public function store(Request $request, string $type): RedirectResponse
    {
        $this->ensureType($type);
        $place = Place::create($this->validatedData($request, $type) + ['type' => $type]);

        return redirect()->route('admin.places.index', $type)->with('status', "{$place->name} added.");
    }

    public function edit(string $type, int $place): View
    {
        $this->ensureType($type);
        $place = Place::where('type', $type)->findOrFail($place);

        return view('admin.places.form', [
            'place' => $place,
            'type' => $type,
            'title' => $this->title($type),
        ]);
    }

    public function update(Request $request, string $type, int $place): RedirectResponse
    {
        $this->ensureType($type);
        $place = Place::where('type', $type)->findOrFail($place);
        $place->update($this->validatedData($request, $type, $place));

        return redirect()->route('admin.places.index', $type)->with('status', "{$place->name} updated.");
    }

    public function destroy(string $type, int $place): RedirectResponse
    {
        $this->ensureType($type);
        $place = Place::where('type', $type)->findOrFail($place);
        $name = $place->name;
        $place->delete();

        return back()->with('status', "{$name} deleted.");
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedData(Request $request, string $type, ?Place $place = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'alpha_dash:ascii', 'max:255', Rule::unique('places', 'slug')->ignore($place?->id)],
            'category' => ['required', 'string', 'max:100'],
            'area' => ['required', 'string', 'max:150'],
            'address' => ['required', 'string', 'max:500'],
            'description' => ['required', 'string', 'max:5000'],
            'image_path' => ['required', 'string', 'max:2048', 'regex:/^(https?:\/\/|\/images\/)[^\s]+$/i'],
            'rating' => ['required', 'numeric', 'between:0,5'],
            'review_count' => ['required', 'integer', 'min:0'],
            'price_level' => ['nullable', 'integer', 'between:1,4'],
            'tags' => ['nullable', 'string', 'max:500'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
        ]);
        $data['type'] = $type;
        $data['tags'] = collect(explode(',', $data['tags'] ?? ''))
            ->map(fn (string $tag): string => trim($tag))
            ->filter()
            ->unique()
            ->values()
            ->all();

        return $data;
    }

    private function ensureType(string $type): void
    {
        abort_unless(in_array($type, ['attraction', 'restaurant'], true), 404);
    }

    private function title(string $type): string
    {
        return $type === 'attraction' ? 'Attractions' : 'Restaurants';
    }
}
