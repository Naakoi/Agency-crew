@extends('layouts.app')

@section('title', 'Activity Log')

@section('content')
<div class="page-header">
    <div>
        <h1>System Activity Log</h1>
        <p>Monitor system events and user actions</p>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Time</th>
                    <th>User</th>
                    <th>Action</th>
                    <th>Description</th>
                    <th>IP Address</th>
                </tr>
            </thead>
            <tbody>
                @foreach($logs as $log)
                <tr>
                    <td style="white-space:nowrap; color:#64748b; font-size:13px;">
                        {{ $log->created_at->format('d M, H:i:s') }}
                        <div style="font-size:10px; opacity:0.7;">{{ $log->created_at->diffForHumans() }}</div>
                    </td>
                    <td>
                        <div style="display:flex; align-items:center; gap:8px;">
                            <div style="width:24px; height:24px; border-radius:50%; background:#1e293b; display:flex; align-items:center; justify-content:center; font-size:10px; font-weight:700; color:#fff;">
                                {{ strtoupper(substr($log->user->name, 0, 1)) }}
                            </div>
                            <span style="font-weight:600;">{{ $log->user->name }}</span>
                        </div>
                    </td>
                    <td>
                        <span style="display:inline-block; padding:2px 8px; border-radius:4px; font-size:11px; font-weight:700; text-transform:uppercase; background:rgba(14,154,224,0.1); color:#0ea5e9;">
                            {{ str_replace('_', ' ', $log->action) }}
                        </span>
                    </td>
                    <td style="color:#e2e8f0; font-size:14px;">{{ $log->description }}</td>
                    <td style="font-family:monospace; font-size:12px; color:var(--muted);">{{ $log->ip_address }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <div style="margin-top:20px;">
        {{ $logs->links() }}
    </div>
</div>
@endsection
