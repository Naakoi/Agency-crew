@extends('layouts.app')
@section('title', 'User Management')

@section('content')
<div class="page-header">
    <div>
        <h1><i class="fas fa-user-shield" style="color:var(--teal);margin-right:10px;"></i>User Management</h1>
        <p>Manage system access and staff roles for CPPL Agency</p>
    </div>
    <a href="{{ route('users.create') }}" class="btn btn-primary">
        <i class="fas fa-user-plus"></i> Add New User
    </a>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>User</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Joined</th>
                    <th style="text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:12px;">
                            <div class="avatar" style="width:32px;height:32px;font-size:12px;">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <span style="font-weight:600;">{{ $user->name }}</span>
                        </div>
                    </td>
                    <td style="color:var(--muted);">{{ $user->email }}</td>
                    <td>
                        <span class="badge {{ $user->role === 'admin' ? 'badge-green' : 'badge-gray' }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </td>
                    <td style="color:var(--muted); font-size:12px;">{{ $user->created_at->format('d M Y') }}</td>
                    <td style="text-align:right;">
                        <div style="display:inline-flex;gap:8px;">
                            <a href="{{ route('users.edit', $user) }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            @if(auth()->id() !== $user->id)
                            <form action="{{ route('users.destroy', $user) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this user?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
