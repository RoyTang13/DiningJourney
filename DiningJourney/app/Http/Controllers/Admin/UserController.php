<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $users = User::query()
            ->when($request->filled('q'), function (Builder $query) use ($request): void {
                $term = trim((string) $request->query('q'));
                $query->where(function (Builder $query) use ($term): void {
                    $query->where('name', 'like', "%{$term}%")
                        ->orWhere('email', 'like', "%{$term}%");
                });
            })
            ->when($request->query('role') === 'admin', fn (Builder $query) => $query->where('is_admin', true))
            ->when($request->query('role') === 'user', fn (Builder $query) => $query->where('is_admin', false))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', ['users' => $users]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate(['is_admin' => ['required', 'boolean']]);
        $shouldBeAdmin = (bool) $data['is_admin'];

        if ($user->is($request->user()) && ! $shouldBeAdmin) {
            throw ValidationException::withMessages([
                'is_admin' => 'You cannot remove your own administrator access.',
            ]);
        }

        DB::transaction(function () use ($user, $shouldBeAdmin): void {
            $adminCount = User::where('is_admin', true)->lockForUpdate()->get(['id'])->count();
            if ($user->isAdmin() && ! $shouldBeAdmin && $adminCount <= 1) {
                throw ValidationException::withMessages([
                    'is_admin' => 'At least one administrator account must remain.',
                ]);
            }

            $user->forceFill(['is_admin' => $shouldBeAdmin])->save();
        });

        return back()->with('status', 'User role updated.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->is($request->user())) {
            throw ValidationException::withMessages([
                'user' => 'You cannot delete your own account from the admin panel.',
            ]);
        }

        DB::transaction(function () use ($user): void {
            $adminCount = User::where('is_admin', true)->lockForUpdate()->get(['id'])->count();
            if ($user->isAdmin() && $adminCount <= 1) {
                throw ValidationException::withMessages([
                    'user' => 'The last administrator account cannot be deleted.',
                ]);
            }

            $user->delete();
        });

        return back()->with('status', 'User account deleted.');
    }
}
