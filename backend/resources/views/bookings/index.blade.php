@extends('layouts.app')
@section('title', 'Bookings')

@section('content')
<div class="page-header">
    <div>
        <h1><i class="fas fa-calendar-check" style="color:#0e9ae0;margin-right:10px;"></i>Accommodation Bookings</h1>
        <p>Manage all crew hotel bookings for CPPL Agency</p>
    </div>
    <a href="{{ route('bookings.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> New Booking
    </a>
</div>

{{-- Stats --}}
<div class="stats-row" style="grid-template-columns: repeat(6, 1fr); margin-bottom: 24px;">
    <a href="{{ route('bookings.index') }}" class="stat-card" style="text-decoration: none; display: block; background: {{ !request('status') ? 'rgba(26,120,194,0.15)' : 'var(--card)' }}; border-color: {{ !request('status') ? 'var(--accent)' : 'var(--border)' }};">
        <div class="stat-label">Total</div>
        <div class="stat-value">{{ $stats['total'] }}</div>
    </a>
    <a href="{{ route('bookings.index', ['status' => 'booked']) }}" class="stat-card" style="text-decoration: none; display: block; background: {{ request('status') === 'booked' ? 'rgba(26,120,194,0.15)' : 'var(--card)' }}; border-color: {{ request('status') === 'booked' ? 'var(--accent)' : 'var(--border)' }};">
        <div class="stat-label">Booked</div>
        <div class="stat-value" style="color:#1a78c2;">{{ $stats['booked'] }}</div>
    </a>
    <a href="{{ route('bookings.index', ['status' => 'pickup_to_hotel']) }}" class="stat-card" style="text-decoration: none; display: block; background: {{ request('status') === 'pickup_to_hotel' ? 'rgba(245,158,11,0.15)' : 'var(--card)' }}; border-color: {{ request('status') === 'pickup_to_hotel' ? 'var(--amber)' : 'var(--border)' }};">
        <div class="stat-label">To Hotel</div>
        <div class="stat-value" style="color:#f59e0b;">{{ $stats['pickup_to_hotel'] }}</div>
    </a>
    <a href="{{ route('bookings.index', ['status' => 'in_hotel']) }}" class="stat-card" style="text-decoration: none; display: block; background: {{ request('status') === 'in_hotel' ? 'rgba(34,197,94,0.15)' : 'var(--card)' }}; border-color: {{ request('status') === 'in_hotel' ? 'var(--green)' : 'var(--border)' }};">
        <div class="stat-label">In Hotel</div>
        <div class="stat-value" style="color:#22c55e;">{{ $stats['in_hotel'] }}</div>
    </a>
    <a href="{{ route('bookings.index', ['status' => 'pickup_to_plane']) }}" class="stat-card" style="text-decoration: none; display: block; background: {{ request('status') === 'pickup_to_plane' ? 'rgba(12,184,168,0.15)' : 'var(--card)' }}; border-color: {{ request('status') === 'pickup_to_plane' ? 'var(--teal)' : 'var(--border)' }};">
        <div class="stat-label">To Plane</div>
        <div class="stat-value" style="color:#0cb8a8;">{{ $stats['pickup_to_plane'] }}</div>
    </a>
    <a href="{{ route('bookings.index', ['status' => 'cancelled']) }}" class="stat-card" style="text-decoration: none; display: block; background: {{ request('status') === 'cancelled' ? 'rgba(239,68,68,0.15)' : 'var(--card)' }}; border-color: {{ request('status') === 'cancelled' ? 'var(--red)' : 'var(--border)' }};">
        <div class="stat-label">Cancelled</div>
        <div class="stat-value" style="color:var(--red);">{{ $stats['cancelled'] }}</div>
    </a>
</div>

<style>
    .stat-card { transition: all 0.2s ease; cursor: pointer; }
    .stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(0,0,0,0.35); border-color: var(--accent); }
    .stat-card.active { background: rgba(26,120,194,0.1); border-color: var(--accent); }
</style>

{{-- Filter bar --}}
<div class="filter-bar">
    <form method="GET" style="display:flex;gap:12px;width:100%;flex-wrap:wrap;">
        <input type="text" name="search" class="form-control" placeholder="🔍  Search crew, company, or invoice..."
            value="{{ request('search') }}" style="flex:1;min-width:220px;">
        <select name="status" class="form-control" style="width:180px;" onchange="this.form.submit()">
            <option value="">All Status</option>
            <option value="booked"           {{ request('status') === 'booked' ? 'selected' : '' }}>Hotel Booked</option>
            <option value="pickup_to_hotel"  {{ request('status') === 'pickup_to_hotel' ? 'selected' : '' }}>Pickup to Hotel</option>
            <option value="in_hotel"         {{ request('status') === 'in_hotel' ? 'selected' : '' }}>In Hotel</option>
            <option value="pickup_to_plane"  {{ request('status') === 'pickup_to_plane' ? 'selected' : '' }}>Pickup to Plane</option>
            <option value="cancelled"        {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            <option value="assigned_to_me"   {{ request('status') === 'assigned_to_me' ? 'selected' : '' }}>Assigned to Me</option>
        </select>
        <button class="btn btn-secondary" type="submit"><i class="fas fa-search"></i></button>
        @if(request('search') || request('status'))
            <a href="{{ route('bookings.index') }}" class="btn btn-secondary"><i class="fas fa-times"></i> Clear</a>
        @endif
    </form>
</div>

{{-- Booking cards --}}
@if($bookings->count())
<div class="booking-grid">
    @foreach($bookings as $booking)
    <div class="booking-card">
        <div class="card-header">
            <div style="display:flex;align-items:center;gap:12px;">
                @if($booking->crew->photo)
                    <img src="{{ Storage::url($booking->crew->photo) }}" class="crew-avatar" alt="">
                @else
                    <div class="crew-avatar-placeholder">{{ strtoupper(substr($booking->crew->full_name,0,1)) }}</div>
                @endif
                <div>
                    <div class="crew-name">{{ $booking->crew->full_name }}</div>
                    <div class="crew-title-text">{{ $booking->crew_title }}</div>
                </div>
            </div>
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
            <span class="badge {{ $booking->status_color_class }}">
                {{ $booking->status_label }}
            </span>
            <div class="tooltip">
                <i class="fas fa-info-circle"></i>
                <span class="tooltip-text">
                    <strong>Status Definitions:</strong><br>
                    • <b>Hotel Booked</b>: Booking created.<br>
                    • <b>Pickup to Hotel</b>: Crew being transported to hotel.<br>
                    • <b>In Hotel</b>: Crew is staying at the hotel.<br>
                    • <b>Pickup to Plane</b>: Crew leaving hotel for flight.
                </span>
            </div>
        </div>
        </div>
        <hr class="divider">
        <div class="meta-row"><i class="fas fa-hotel"></i> {{ $booking->hotel->hotel_name }}</div>
        <div class="meta-row"><i class="fas fa-ship"></i> {{ $booking->company->company_name }} — {{ $booking->company->ship_name }}</div>
        <div class="meta-row"><i class="fas fa-sign-in-alt"></i> {{ $booking->check_in->format('d M Y H:i') }}</div>
        <div class="meta-row"><i class="fas fa-sign-out-alt"></i> {{ $booking->check_out->format('d M Y H:i') }}</div>
        
        @if($booking->invoice_number)
        <div class="meta-row" style="margin-top:4px; font-weight:700;">
            <i class="fas fa-file-invoice" style="color:#0ea5e9;"></i> 
            Invoice: 
            @if($booking->crew->biodata_file)
                <a href="{{ Storage::url($booking->crew->biodata_file) }}" target="_blank" style="color:#0ea5e9; text-decoration:underline;">
                    #{{ $booking->invoice_number }}
                </a>
                <i class="fas fa-external-link-alt" style="font-size:10px; margin-left:2px; opacity:0.7;"></i>
            @else
                #{{ $booking->invoice_number }}
            @endif
        </div>
        @endif

        @if($booking->crew->creator)
        <div class="meta-row"><i class="fas fa-file-upload"></i> Files by: {{ $booking->crew->creator->name }}</div>
        @endif
        @if($booking->assignedUser)
        <div class="meta-row"><i class="fas fa-user-tag" style="color:var(--teal);"></i> Assigned to: {{ $booking->assignedUser->name }}</div>
        @endif

        <div style="margin-top:16px; display:flex; gap:8px;">
            <a href="{{ route('bookings.show', $booking) }}" class="btn btn-secondary btn-sm" style="flex:1; justify-content:center;">View</a>
            <form action="{{ route('bookings.toggle', $booking) }}" method="POST" style="flex:1;">
                @csrf
                <select name="status" onchange="this.form.submit()" class="form-control" style="font-size:12px; height:32px; padding:0 8px;">
                    <option value="booked"           {{ $booking->status === 'booked' ? 'selected' : '' }}>Hotel Booked</option>
                    <option value="pickup_to_hotel"  {{ $booking->status === 'pickup_to_hotel' ? 'selected' : '' }}>Pickup to Hotel</option>
                    <option value="in_hotel"         {{ $booking->status === 'in_hotel' ? 'selected' : '' }}>In Hotel</option>
                    <option value="pickup_to_plane"  {{ $booking->status === 'pickup_to_plane' ? 'selected' : '' }}>Pickup to Plane</option>
                    <option value="cancelled"        {{ $booking->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </form>
        </div>
        @if($booking->statusLogs->count())
        <div style="margin-top:12px; padding-top:10px; border-top:1px solid var(--border);">
            <div style="font-size:10px; font-weight:700; color:#94a3b8; text-transform:uppercase; margin-bottom:8px; opacity:0.8;">Recent Timeline</div>
            <div style="display:flex; flex-direction:column; gap:4px;">
                @foreach($booking->statusLogs->sortByDesc('created_at')->take(3) as $log)
                <div style="background:{{ $loop->first ? 'rgba(14,154,224,0.06)' : 'transparent' }}; padding:6px 8px; border-radius:6px; {{ $loop->first ? 'border:1px solid rgba(14,154,224,0.2);' : '' }} display:flex; justify-content:space-between; align-items:center;">
                    <div style="display:flex; align-items:center; gap:8px;">
                        <i class="fas {{ $loop->first ? 'fa-history' : 'fa-check-circle' }}" style="font-size:10px; color:{{ $loop->first ? '#0ea5e9' : 'rgba(148,163,184,0.4)' }};"></i>
                        <span style="font-size:11px; font-weight:{{ $loop->first ? '700' : '500' }}; color:{{ $loop->first ? '#fff' : 'var(--muted)' }};">
                            {{ $log->status_label }}
                        </span>
                    </div>
                    <div style="font-size:9px; color:#64748b; text-align:right; display:flex; align-items:center; gap:8px;">
                        <span>{{ $log->user->name ?? 'System' }} • {{ $log->created_at->diffForHumans() }}</span>
                        @if(auth()->id() === $log->user_id || auth()->user()->isAdmin())
                        <form action="{{ route('bookings.delete-status', $log) }}" method="POST" style="display:inline;" onsubmit="return confirm('Remove this status log?')">
                            @csrf @method('DELETE')
                            <button type="submit" style="background:none; border:none; padding:0; color:#ef4444; cursor:pointer;" title="Delete log">
                                <i class="fas fa-times-circle"></i>
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
    @endforeach
</div>
<div>{{ $bookings->appends(request()->query())->links('pagination::simple-tailwind') }}</div>
@else
<div class="card" style="text-align:center;padding:60px 20px;">
    <i class="fas fa-calendar-times" style="font-size:48px;color:#334155;margin-bottom:16px;display:block;"></i>
    <p style="color:#94a3b8;font-size:15px;">No bookings found. <a href="{{ route('bookings.create') }}" style="color:#0e9ae0;">Create one now →</a></p>
</div>
@endif
@endsection
