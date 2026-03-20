@extends('layouts.app')
@section('title', 'Hotels')

@section('content')
<div class="page-header">
    <div><h1><i class="fas fa-hotel" style="color:#0e9ae0;margin-right:10px;"></i>Hotels</h1><p>Manage accommodation properties</p></div>
    <a href="{{ route('hotels.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Add Hotel</a>
</div>

<div class="card">
<div class="table-wrap">
<table>
    <thead><tr><th>Hotel Name</th><th>Location</th><th>Contact</th><th>Bookings</th><th>Actions</th></tr></thead>
    <tbody>
        @forelse($hotels as $hotel)
        <tr>
            <td style="font-weight:600;">{{ $hotel->hotel_name }}</td>
            <td><i class="fas fa-map-marker-alt" style="color:#0e9ae0;margin-right:5px;"></i>{{ $hotel->location ?? '—' }}</td>
            <td>{{ $hotel->contact ?? '—' }}</td>
            <td><span class="badge badge-green">{{ $hotel->bookings_count }}</span></td>
            <td>
                <div style="display:flex;gap:8px;">
                    <a href="{{ route('hotels.edit', $hotel) }}" class="btn btn-secondary btn-sm"><i class="fas fa-edit"></i></a>
                    <form method="POST" action="{{ route('hotels.destroy', $hotel) }}" onsubmit="return confirm('Delete this hotel?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                    </form>
                </div>
            </td>
        </tr>
        @empty
        <tr><td colspan="5" style="text-align:center;color:#94a3b8;padding:40px;">No hotels yet.</td></tr>
        @endforelse
    </tbody>
</table>
</div>
</div>
@endsection
