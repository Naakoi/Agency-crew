@extends('layouts.app')
@section('title', 'Edit Crew — ' . $crew->full_name)

@section('content')
<div class="page-header">
    <div><h1><i class="fas fa-user-edit" style="color:#0e9ae0;margin-right:10px;"></i>Edit Crew</h1></div>
    <a href="{{ route('crews.show', $crew) }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
</div>

<div class="card">
<form method="POST" action="{{ route('crews.update', $crew) }}" enctype="multipart/form-data">
@csrf @method('PUT')

<div class="form-row">
    <div class="form-group">
        <label class="form-label">Full Name *</label>
        <input type="text" name="full_name" class="form-control" value="{{ old('full_name', $crew->full_name) }}" required>
    </div>
    <div class="form-group">
        <label class="form-label">Nationality</label>
        <input type="text" name="nationality" class="form-control" value="{{ old('nationality', $crew->nationality) }}">
    </div>
</div>

<div class="form-row">
    <div class="form-group">
        <label class="form-label">Passport Number</label>
        <input type="text" name="passport_number" class="form-control" value="{{ old('passport_number', $crew->passport_number) }}">
    </div>
    <div class="form-group">
        <label class="form-label">Passport Expiry Date</label>
        <input type="date" name="passport_expiry_date" class="form-control" value="{{ old('passport_expiry_date', $crew->passport_expiry_date ? $crew->passport_expiry_date->format('Y-m-d') : '') }}">
    </div>
    <div class="form-group">
        <label class="form-label">Date of Birth</label>
        <input type="date" name="date_of_birth" class="form-control" value="{{ old('date_of_birth', $crew->date_of_birth ? $crew->date_of_birth->format('Y-m-d') : '') }}">
    </div>
</div>

<div class="form-row">
    <div class="form-group">
        <label class="form-label">Photo <small style="font-weight:400;text-transform:none;">(Leave blank to keep current)</small></label>
        @if($crew->photo)
            <div style="margin-bottom:8px;"><img src="{{ Storage::url($crew->photo) }}" style="width:60px;height:60px;border-radius:50%;object-fit:cover;border:2px solid #1a78c2;" alt=""></div>
        @endif
        <input type="file" name="photo" class="form-control" accept="image/*">
    </div>
    <div class="form-group">
        <label class="form-label">Biodata File <small style="font-weight:400;text-transform:none;">(Leave blank to keep current)</small></label>
        @if($crew->biodata_file)
            <div style="margin-bottom:8px;font-size:12px;color:#0e9ae0;"><i class="fas fa-file"></i> File on record</div>
        @endif
        <input type="file" name="biodata_file" class="form-control" accept=".pdf,.doc,.docx,.jpg,.png">
    </div>
</div>

<div class="form-group">
    <label class="form-label">Notes</label>
    <textarea name="notes" class="form-control">{{ old('notes', $crew->notes) }}</textarea>
</div>

<div style="display:flex;gap:12px;margin-top:8px;">
    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Changes</button>
    <a href="{{ route('crews.show', $crew) }}" class="btn btn-secondary">Cancel</a>
</div>
</form>
</div>
@endsection
