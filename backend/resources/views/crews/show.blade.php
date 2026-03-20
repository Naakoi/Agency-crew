@extends('layouts.app')
@section('title', $crew->full_name)

@section('content')
<div class="page-header">
    <div>
        <h1>{{ $crew->full_name }}</h1>
        <p>Crew member profile</p>
    </div>
    <div style="display:flex;gap:10px;">
        <a href="{{ route('crews.edit', $crew) }}" class="btn btn-secondary btn-sm"><i class="fas fa-edit"></i> Edit</a>
        <a href="{{ route('crews.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
    </div>
</div>

<div style="display:grid;grid-template-columns:260px 1fr;gap:20px;align-items:start;">
    {{-- Profile card --}}
    <div class="card" style="text-align:center;">
        @if($crew->photo)
            <img src="{{ Storage::url($crew->photo) }}" style="width:90px;height:90px;border-radius:50%;object-fit:cover;border:3px solid #1a78c2;margin:0 auto 14px;display:block;" alt="">
        @else
            <div style="width:90px;height:90px;border-radius:50%;background:linear-gradient(135deg,#1a78c2,#0cb8a8);display:flex;align-items:center;justify-content:center;font-size:34px;font-weight:800;color:#fff;margin:0 auto 14px;">
                {{ strtoupper(substr($crew->full_name,0,1)) }}
            </div>
        @endif
        <div style="font-size:18px;font-weight:800;color:#fff;margin-bottom:4px;">{{ $crew->full_name }}</div>
        <div style="color:#94a3b8;font-size:13px;margin-bottom:12px;">{{ $crew->nationality ?? 'Unknown nationality' }}</div>
        
        <div style="margin-bottom:16px;">
            <span class="badge {{ $crew->status_color_class }}">
                <i class="fas fa-circle" style="font-size:7px;"></i>
                {{ $crew->status_label }}
            </span>
        </div>

        @if($crew->biodata_file)
        <a href="{{ Storage::url($crew->biodata_file) }}" target="_blank" class="btn btn-secondary" style="width:100%;justify-content:center;font-size:13px;">
            <i class="fas fa-file-download"></i> Biodata File
        </a>
        @endif

        <div style="margin-top:16px;text-align:left;border-top:1px solid rgba(30,58,95,0.6);padding-top:14px;">
            <div style="font-size:11px;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px;">Details</div>
            <div style="font-size:13px;color:#e2e8f0;margin-bottom:6px;"><i class="fas fa-passport" style="color:#0e9ae0;width:18px;"></i> {{ $crew->passport_number ?? 'N/A' }}</div>
            <div style="font-size:13px;color:#e2e8f0;margin-bottom:6px; {{ $crew->is_passport_soon_expiring ? 'color:#f59e0b;' : ($crew->is_passport_expired ? 'color:#ef4444;' : '') }}">
                <i class="fas fa-calendar-times" style="color:#0e9ae0;width:18px;"></i> 
                Exp: {{ $crew->passport_expiry_date ? $crew->passport_expiry_date->format('d M Y') : 'N/A' }}
                @if($crew->is_passport_soon_expiring)
                    <i class="fas fa-exclamation-triangle" title="Expiring soon"></i>
                @elseif($crew->is_passport_expired)
                    <i class="fas fa-times-circle" title="Expired!"></i>
                @endif
            </div>
            <div style="font-size:13px;color:#e2e8f0;margin-bottom:6px;"><i class="fas fa-birthday-cake" style="color:#0e9ae0;width:18px;"></i> {{ $crew->date_of_birth ? $crew->date_of_birth->format('d M Y') : 'N/A' }}</div>
            <div style="font-size:13px;color:#e2e8f0;margin-bottom:6px;"><i class="fas fa-calendar-check" style="color:#0e9ae0;width:18px;"></i> {{ $crew->bookings->count() }} booking(s)</div>
            @if($crew->creator)
            <div style="font-size:13px;color:#e2e8f0;"><i class="fas fa-user-edit" style="color:#0e9ae0;width:18px;"></i> By: {{ $crew->creator->name }}</div>
            @endif
        </div>
    </div>

    {{-- Booking history --}}
    <div class="card">
        <div style="font-size:13px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:1px;margin-bottom:16px;">Booking History</div>
        @forelse($crew->bookings as $booking)
        <div style="padding:14px;background:rgba(255,255,255,0.03);border-radius:10px;border:1px solid rgba(30,58,95,0.4);margin-bottom:12px;">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:10px;">
                <div>
                    <div style="font-weight:700;font-size:14px;">{{ $booking->hotel->hotel_name }}</div>
                    <div style="font-size:12px;color:#94a3b8;">{{ $booking->company->company_name }} — {{ $booking->company->ship_name }}</div>
                </div>
                <span class="badge {{ $booking->status_color_class }}">
                    {{ $booking->status_label }}
                </span>
            </div>
            <div style="font-size:12px;color:#94a3b8;display:flex;gap:16px;">
                <span><i class="fas fa-sign-in-alt" style="color:#0e9ae0;"></i> {{ $booking->check_in->format('d M Y') }}</span>
                <span><i class="fas fa-sign-out-alt" style="color:#0e9ae0;"></i> {{ $booking->check_out->format('d M Y') }}</span>
                @if($booking->invoice_number)<span><i class="fas fa-file-invoice" style="color:#0e9ae0;"></i> {{ $booking->invoice_number }}</span>@endif
            </div>
        </div>
        @empty
        <div style="text-align:center;color:#94a3b8;padding:30px 0;">No bookings yet.</div>
        @endforelse
    </div>
</div>
@endsection
