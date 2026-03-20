@extends('layouts.app')
@section('title', 'Add User')

@section('content')
<div class="page-header">
    <div>
        <h1><i class="fas fa-user-plus" style="color:var(--teal);margin-right:10px;"></i>Add New User</h1>
        <p>Create a new account for agency staff or administrators</p>
    </div>
    <a href="{{ route('users.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to List
    </a>
</div>

<div style="max-width: 600px; margin: 0 auto;">
    <div class="card">
        <form action="{{ route('users.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Full Name *</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required placeholder="e.g. John Doe">
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Email Address *</label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required placeholder="e.g. john@cppltd.com">
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Role *</label>
                <select name="role" class="form-control @error('role') is-invalid @enderror" required>
                    <option value="staff" {{ old('role') === 'staff' ? 'selected' : '' }}>Agency Staff</option>
                    <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Administrator</option>
                </select>
                @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Password *</label>
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required placeholder="••••••••">
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Confirm Password *</label>
                <input type="password" name="password_confirmation" class="form-control" required placeholder="••••••••">
            </div>

            <div style="margin-top: 24px;">
                <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center;">
                    <i class="fas fa-save"></i> Create User Account
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
