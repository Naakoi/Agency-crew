@extends('layouts.app')
@section('title', 'Companies')

@section('content')
<div class="page-header">
    <div><h1><i class="fas fa-ship" style="color:#0e9ae0;margin-right:10px;"></i>Companies</h1><p>Fishing vessel companies</p></div>
    <a href="{{ route('companies.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Add Company</a>
</div>

<div class="card">
<div class="table-wrap">
<table>
    <thead><tr><th>Company Name</th><th>Ship / Vessel</th><th>Contact</th><th>Bookings</th><th>Actions</th></tr></thead>
    <tbody>
        @forelse($companies as $company)
        <tr>
            <td style="font-weight:600;">{{ $company->company_name }}</td>
            <td><i class="fas fa-ship" style="color:#0e9ae0;margin-right:6px;"></i>{{ $company->ship_name }}</td>
            <td>{{ $company->contact ?? '—' }}</td>
            <td><span class="badge badge-green">{{ $company->bookings_count }}</span></td>
            <td>
                <div style="display:flex;gap:8px;">
                    <a href="{{ route('companies.edit', $company) }}" class="btn btn-secondary btn-sm"><i class="fas fa-edit"></i></a>
                    <form method="POST" action="{{ route('companies.destroy', $company) }}" onsubmit="return confirm('Delete this company?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                    </form>
                </div>
            </td>
        </tr>
        @empty
        <tr><td colspan="5" style="text-align:center;color:#94a3b8;padding:40px;">No companies yet.</td></tr>
        @endforelse
    </tbody>
</table>
</div>
</div>
@endsection
