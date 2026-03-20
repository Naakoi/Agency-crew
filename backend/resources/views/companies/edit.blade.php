@extends('layouts.app')
@section('title', 'Edit Company')
@section('content')
<div class="page-header">
    <div><h1>Edit Company</h1></div>
    <a href="{{ route('companies.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
</div>
<div class="card">
<form method="POST" action="{{ route('companies.update', $company) }}">@csrf @method('PUT')
<div class="form-row">
    <div class="form-group">
        <label class="form-label">Company Name *</label>
        <input type="text" name="company_name" class="form-control" value="{{ old('company_name', $company->company_name) }}" required>
    </div>
    <div class="form-group">
        <label class="form-label">Ship / Vessel Name *</label>
        <input type="text" name="ship_name" class="form-control" value="{{ old('ship_name', $company->ship_name) }}" required>
    </div>
</div>
<div class="form-row">
    <div class="form-group">
        <label class="form-label">Contact</label>
        <input type="text" name="contact" class="form-control" value="{{ old('contact', $company->contact) }}">
    </div>
    <div class="form-group">
        <label class="form-label">Address</label>
        <input type="text" name="address" class="form-control" value="{{ old('address', $company->address) }}">
    </div>
</div>
<div style="display:flex;gap:12px;margin-top:8px;">
    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save</button>
    <a href="{{ route('companies.index') }}" class="btn btn-secondary">Cancel</a>
</div>
</form>
</div>
@endsection
