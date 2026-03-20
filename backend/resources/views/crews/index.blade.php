@extends('layouts.app')
@section('title', 'Crew Members')

@section('content')
<div class="page-header">
    <div>
        <h1><i class="fas fa-users" style="color:#0e9ae0;margin-right:10px;"></i>Crew Members</h1>
        <p>All registered crew members</p>
    </div>
    <a href="{{ route('crews.create') }}" class="btn btn-primary"><i class="fas fa-user-plus"></i> Add Crew</a>
</div>

<div class="card">
<div class="table-wrap">
<table>
    <thead>
        <tr>
            <th>Crew Member</th>
            <th>Passport Expiry</th>
            <th>Nationality</th>
            <th>Status</th>
            <th>Bookings</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($crews as $crew)
        <tr>
            <td>
                <div style="display:flex;align-items:center;gap:12px;">
                    @if($crew->photo)
                        <img src="{{ Storage::url($crew->photo) }}" style="width:38px;height:38px;border-radius:50%;object-fit:cover;border:2px solid #1a78c2;" alt="">
                    @else
                        <div style="width:38px;height:38px;border-radius:50%;background:linear-gradient(135deg,#1a78c2,#0cb8a8);display:flex;align-items:center;justify-content:center;font-weight:700;color:#fff;">
                            {{ strtoupper(substr($crew->full_name,0,1)) }}
                        </div>
                    @endif
                    <div>
                        <div style="font-weight:600;">{{ $crew->full_name }}</div>
                        <div style="font-size:11px;color:#94a3b8;">{{ $crew->date_of_birth ? 'DOB: '.$crew->date_of_birth->format('d M Y') : '' }}</div>
                    </div>
                </div>
            </td>
            <td>
                @if($crew->passport_expiry_date)
                    <div style="display:flex;align-items:center;gap:6px; {{ $crew->is_passport_soon_expiring ? 'color:#f59e0b;' : ($crew->is_passport_expired ? 'color:#ef4444;' : '') }}">
                        {{ $crew->passport_expiry_date->format('d M Y') }}
                        @if($crew->is_passport_soon_expiring)
                            <i class="fas fa-exclamation-triangle" title="Expiring soon (within 6 months)"></i>
                        @elseif($crew->is_passport_expired)
                            <i class="fas fa-times-circle" title="Expired!"></i>
                        @endif
                    </div>
                @else
                    <span style="color:#94a3b8;">—</span>
                @endif
            </td>
            <td>{{ $crew->nationality ?? '—' }}</td>
            <td>
                <span class="badge {{ $crew->status_color_class }}">
                    <i class="fas fa-circle" style="font-size:6px;"></i>
                    {{ $crew->status_label }}
                </span>
            </td>
            <td><span class="badge badge-success" style="background:rgba(26,120,194,0.15);color:#0e9ae0;">{{ $crew->bookings_count }}</span></td>
            <td>
                <div style="display:flex;gap:8px;">
                    <a href="{{ route('crews.show', $crew) }}" class="btn btn-secondary btn-sm"><i class="fas fa-eye"></i></a>
                    <a href="{{ route('crews.edit', $crew) }}" class="btn btn-secondary btn-sm"><i class="fas fa-edit"></i></a>
                    <form method="POST" action="{{ route('crews.destroy', $crew) }}" onsubmit="return confirm('Delete this crew member?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                    </form>
                </div>
            </td>
        </tr>
        @empty
        <tr><td colspan="5" style="text-align:center;color:#94a3b8;padding:40px;">No crew members yet.</td></tr>
        @endforelse
    </tbody>
</table>
</div>
</div>

<div>{{ $crews->links('pagination::simple-tailwind') }}</div>
@endsection
