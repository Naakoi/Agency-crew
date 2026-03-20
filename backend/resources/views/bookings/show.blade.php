@extends('layouts.app')
@section('title', 'Booking Details')
@push('styles')
<style>
.detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; }
.detail-item .label { font-size: 11px; font-weight: 600; color: #94a3b8; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 5px; }
.detail-item .value { font-size: 14.5px; font-weight: 500; color: #e2e8f0; }
.section-title {
    font-size: 13px; font-weight: 700; color: #94a3b8; text-transform: uppercase;
    letter-spacing: 1px; margin: 28px 0 18px;
    padding-bottom: 12px; border-bottom: 1px solid rgba(30,58,95,0.6);
    display: flex; align-items: center; gap: 8px;
}
.section-title i { color: #0e9ae0; }
</style>
@endpush

@section('content')
<div class="page-header">
    <div>
        <h1>Booking #{{ $booking->id }}</h1>
        <p>Crew accommodation record</p>
    </div>
    <div style="display:flex;gap:10px;align-items:center;">
        <span class="badge {{ $booking->status_color_class }}" style="font-size:13px;padding:6px 16px;">
            <i class="fas fa-circle" style="font-size:8px;"></i>
            {{ $booking->status_label }}
        </span>
        <a href="{{ route('bookings.edit', $booking) }}" class="btn btn-secondary btn-sm"><i class="fas fa-edit"></i> Edit</a>
        <form method="POST" action="{{ route('bookings.destroy', $booking) }}" onsubmit="return confirm('Delete this booking?')">
            @csrf @method('DELETE')
            <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
        </form>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 340px;gap:20px;align-items:start;">

    {{-- Main details --}}
    <div>
        <div class="card">
            <div class="section-title"><i class="fas fa-user"></i> Crew Information</div>
            <div style="display:flex;align-items:center;gap:16px;margin-bottom:22px;">
                @if($booking->crew->photo)
                    <img src="{{ Storage::url($booking->crew->photo) }}" style="width:70px;height:70px;border-radius:50%;object-fit:cover;border:3px solid #1a78c2;" alt="">
                @else
                    <div style="width:70px;height:70px;border-radius:50%;background:linear-gradient(135deg,#1a78c2,#0cb8a8);display:flex;align-items:center;justify-content:center;font-size:26px;font-weight:800;color:#fff;flex-shrink:0;">
                        {{ strtoupper(substr($booking->crew->full_name,0,1)) }}
                    </div>
                @endif
                <div>
                    <div style="font-size:20px;font-weight:800;color:#fff;">{{ $booking->crew->full_name }}</div>
                    <div style="color:#0e9ae0;font-weight:600;font-size:14px;">{{ $booking->crew_title }}</div>
                    <a href="{{ route('crews.show', $booking->crew) }}" style="font-size:12px;color:#94a3b8;text-decoration:none;">View full profile →</a>
                </div>
            </div>
            <div class="detail-grid">
                <div class="detail-item"><div class="label">Nationality</div><div class="value">{{ $booking->crew->nationality ?? '—' }}</div></div>
                <div class="detail-item"><div class="label">Passport No.</div><div class="value">{{ $booking->crew->passport_number ?? '—' }}</div></div>
            </div>

            <div class="section-title"><i class="fas fa-ship"></i> Company & Vessel</div>
            <div class="detail-grid">
                <div class="detail-item"><div class="label">Company</div><div class="value">{{ $booking->company->company_name }}</div></div>
                <div class="detail-item"><div class="label">Ship / Vessel</div><div class="value">{{ $booking->company->ship_name }}</div></div>
            </div>

            <div class="section-title"><i class="fas fa-calendar-alt"></i> Stay Details</div>
            <div class="detail-grid">
                <div class="detail-item"><div class="label">Check-In</div><div class="value">{{ $booking->check_in->format('d M Y, H:i') }}</div></div>
                <div class="detail-item"><div class="label">Check-Out</div><div class="value">{{ $booking->check_out->format('d M Y, H:i') }}</div></div>
                <div class="detail-item"><div class="label">Duration</div>
                    <div class="value">{{ $booking->check_in->diffInDays($booking->check_out) }} night(s)</div>
                </div>
                <div class="detail-item"><div class="label">Invoice No.</div><div class="value">{{ $booking->invoice_number ?? '—' }}</div></div>
            </div>

            @if($booking->remarks)
            <div class="section-title"><i class="fas fa-comment-alt"></i> Remarks</div>
            <div style="font-size:14px;color:#e2e8f0;line-height:1.6;background:rgba(255,255,255,0.04);padding:14px;border-radius:10px;border:1px solid rgba(30,58,95,0.4);">
                {{ $booking->remarks }}
            </div>
            @endif
        </div>
    </div>

    {{-- Side panel --}}
    <div style="display:flex;flex-direction:column;gap:16px;">
        {{-- Hotel card --}}
        <div class="card">
            <div style="font-size:12px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:1px;margin-bottom:14px;">Hotel</div>
            <div style="font-size:17px;font-weight:700;color:#fff;margin-bottom:6px;">{{ $booking->hotel->hotel_name }}</div>
            @if($booking->hotel->location)
            <div style="color:#94a3b8;font-size:13px;margin-bottom:4px;"><i class="fas fa-map-marker-alt" style="color:#0e9ae0;margin-right:6px;"></i>{{ $booking->hotel->location }}</div>
            @endif
            @if($booking->hotel->contact)
            <div style="color:#94a3b8;font-size:13px;"><i class="fas fa-phone" style="color:#0e9ae0;margin-right:6px;"></i>{{ $booking->hotel->contact }}</div>
            @endif
        </div>

        {{-- Status card and toggle --}}
        <div class="card">
            <div style="font-size:12px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:1px;margin-bottom:14px;display:flex;align-items:center;gap:8px;">
                Current Status
                <div class="tooltip">
                    <i class="fas fa-question-circle" style="font-size:14px;"></i>
                    <span class="tooltip-text">
                        <strong>Status Definitions:</strong><br>
                    • <b>Hotel Booked</b>: Accommodation confirmed.<br>
                    • <b>Pickup to Hotel</b>: Crew in transit to hotel.<br>
                    • <b>In Hotel</b>: Crew currently at hotel.<br>
                    • <b>Pickup to Plane</b>: Crew in transit to airport.
                    </span>
                </div>
            </div>
            
            <form method="POST" action="{{ route('bookings.toggle', $booking) }}" style="margin-bottom:20px;">
                @csrf
                <div style="font-size:11px; color:#94a3b8; margin-bottom:6px; font-weight:600; text-transform:uppercase;">Change Status</div>
                <select name="status" onchange="this.form.submit()" class="form-control" style="width:100%; border-color:rgba(14,154,224,0.3);">
                    <option value="booked"           {{ $booking->status === 'booked' ? 'selected' : '' }}>Hotel Booked</option>
                    <option value="pickup_to_hotel"  {{ $booking->status === 'pickup_to_hotel' ? 'selected' : '' }}>Pickup to Hotel</option>
                    <option value="in_hotel"         {{ $booking->status === 'in_hotel' ? 'selected' : '' }}>In Hotel</option>
                    <option value="pickup_to_plane"  {{ $booking->status === 'pickup_to_plane' ? 'selected' : '' }}>Pickup to Plane</option>
                </select>
            </form>

            <div style="font-size:12px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:1px;margin-bottom:12px;">Timeline</div>
            <div style="display:flex; flex-direction:column; gap:8px;">
                @foreach($booking->statusLogs->sortByDesc('created_at') as $log)
                <div style="background:{{ $loop->first ? 'rgba(14,154,224,0.08)' : 'rgba(255,255,255,0.03)' }}; padding:12px; border-radius:10px; border-left:4px solid {{ $log->status_color }}; position:relative; {{ $loop->first ? 'border:1px solid rgba(14,154,224,0.3);' : '' }}">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                        <div style="font-size:13px; font-weight:700; color:#fff; display:flex; align-items:center; gap:8px;">
                            @if($loop->first)
                                <i class="fas fa-history" style="color:#0ea5e9;"></i>
                            @endif
                            {{ $log->status_label }}
                        </div>
                        @if($loop->first)
                            <span style="font-size:9px; background:#0ea5e9; color:#fff; padding:2px 6px; border-radius:4px; text-transform:uppercase; font-weight:800;">Latest</span>
                        @endif
                    </div>
                    <div style="font-size:11px; color:var(--muted); margin-top:5px;">
                        Updated by <span style="color:#e2e8f0; font-weight:500;">{{ $log->user->name ?? 'System' }}</span>
                    </div>
                    <div style="font-size:10px; color:#94a3b8; margin-top:2px; display:flex; justify-content:space-between; align-items:center;">
                        <span><i class="far fa-clock" style="margin-right:4px;"></i>{{ $log->created_at->format('d M Y, H:i') }} ({{ $log->created_at->diffForHumans() }})</span>
                        @if(auth()->id() === $log->user_id || auth()->user()->isAdmin())
                        <form action="{{ route('bookings.delete-status', $log) }}" method="POST" style="display:inline;" onsubmit="return confirm('Remove this status log?')">
                            @csrf @method('DELETE')
                            <button type="submit" style="background:none; border:none; padding:0; color:#ef4444; cursor:pointer; font-size:12px;" title="Delete log">
                                <i class="fas fa-trash-alt"></i> Delete
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Biodata file --}}
        @if($booking->crew->biodata_file)
        <div class="card">
            <div style="font-size:12px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:1px;margin-bottom:14px;">Biodata File</div>
            <a href="{{ Storage::url($booking->crew->biodata_file) }}" target="_blank" class="btn btn-secondary" style="width:100%;justify-content:center;">
                <i class="fas fa-file-download"></i> Download Biodata
            </a>
        </div>
        @endif
    </div>

</div>
@endsection
