@extends('admin.layouts.app')

@section('title', 'Manage users · DiningJourney')

@section('content')
    <header class="admin-page-heading">
        <div>
            <p class="eyebrow">Access and accounts</p>
            <h1>Manage users</h1>
            <p>Search registered accounts, change administrator access, or remove an account.</p>
        </div>
    </header>
    <form class="admin-search" action="{{ route('admin.users.index') }}" method="get">
        <label class="sr-only" for="user-search">Search users</label>
        <input id="user-search" name="q" value="{{ request('q') }}" placeholder="Search by name or email">
        <select name="role" aria-label="Filter by role">
            <option value="">All roles</option>
            <option value="admin" @selected(request('role') === 'admin')>Administrators</option>
            <option value="user" @selected(request('role') === 'user')>Users</option>
        </select>
        <button class="filled-button" type="submit">Search</button>
        @if (request()->filled('q'))
            <a class="outline-button" href="{{ route('admin.users.index') }}">Clear</a>
        @endif
    </form>
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr><th>User</th><th>Joined</th><th>Role</th><th>Actions</th></tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    <tr>
                        <td data-label="User"><strong>{{ $user->name }}</strong><small>{{ $user->email }}</small></td>
                        <td data-label="Joined">{{ $user->created_at->format('M j, Y') }}</td>
                        <td data-label="Role"><span class="admin-role {{ $user->isAdmin() ? 'is-admin' : '' }}">{{ $user->isAdmin() ? 'Admin' : 'User' }}</span></td>
                        <td data-label="Actions">
                            <div class="admin-row-actions">
                                @if ($user->is(auth()->user()))
                                    <span class="admin-self-label">Your account</span>
                                @else
                                    <form action="{{ route('admin.users.update', $user) }}" method="post">
                                        @csrf
                                        @method('patch')
                                        <input type="hidden" name="is_admin" value="{{ $user->isAdmin() ? '0' : '1' }}">
                                        <button class="admin-text-button" type="submit">{{ $user->isAdmin() ? 'Remove admin' : 'Make admin' }}</button>
                                    </form>
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="post" onsubmit="return confirm('Delete this user account? This cannot be undone.');">
                                        @csrf
                                        @method('delete')
                                        <button class="admin-text-button danger" type="submit">Delete</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="admin-empty">No users matched your search.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination">{{ $users->links() }}</div>
@endsection
