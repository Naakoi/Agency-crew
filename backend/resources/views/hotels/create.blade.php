@extends('layouts.app')
@section('title', 'Add Hotel')
@section('content')
<div class="page-header">
    <div><h1><i class="fas fa-plus" style="color:#0e9ae0;margin-right:10px;"></i>Add Hotel</h1></div>
    <a href="{{ route('hotels.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
</div>
<div class="card">
<form method="POST" action="{{ route('hotels.store') }}">@csrf
<div class="form-row">
    <div class="form-group">
        <label class="form-label">Hotel Name *</label>
        <input type="text" name="hotel_name" class="form-control" placeholder="e.g. Betio Inn" value="{{ old('hotel_name') }}" required>
    </div>
    <div class="form-group">
        <label class="form-label">Location</label>
        <input type="text" name="location" class="form-control" placeholder="e.g. Betio, Tarawa" value="{{ old('location') }}">
    </div>
</div>
<div class="form-row">
    <div class="form-group">
        <label class="form-label">Contact Number</label>
        <input type="text" name="contact" class="form-control" value="{{ old('contact') }}">
    </div>
    <div class="form-group">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" value="{{ old('email') }}">
    </div>
</div>
<div style="display:flex;gap:12px;margin-top:8px;">
    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Hotel</button>
    <a href="{{ route('hotels.index') }}" class="btn btn-secondary">Cancel</a>
</div>
</form>
</div>
@endsection
