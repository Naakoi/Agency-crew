@extends('layouts.app')
@section('title', 'Add Crew Member')

@section('content')
<div class="page-header">
    <div>
        <h1><i class="fas fa-user-plus" style="color:#0e9ae0;margin-right:10px;"></i>Add Crew Member</h1>
        <p>Register a new crew member with their biodata</p>
    </div>
    <a href="{{ route('crews.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
</div>

<div class="card">
<form method="POST" action="{{ route('crews.store') }}" enctype="multipart/form-data">
@csrf

<div class="form-row">
    <div class="form-group">
        <label class="form-label">Full Name *</label>
        <input type="text" name="full_name" class="form-control {{ $errors->has('full_name') ? 'is-invalid' : '' }}"
            placeholder="e.g. Taaroaiti Tearo" value="{{ old('full_name') }}" required>
        @error('full_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="form-group">
        <label class="form-label">Nationality</label>
        <input type="text" name="nationality" class="form-control" placeholder="e.g. Kiribati" value="{{ old('nationality') }}">
    </div>
</div>

<div class="form-row">
    <div class="form-group">
        <label class="form-label">Passport Number</label>
        <input type="text" name="passport_number" class="form-control" placeholder="e.g. KI123456" value="{{ old('passport_number') }}">
    </div>
    <div class="form-group">
        <label class="form-label">Passport Expiry Date</label>
        <input type="date" name="passport_expiry_date" class="form-control" value="{{ old('passport_expiry_date') }}">
    </div>
    <div class="form-group">
        <label class="form-label">Date of Birth</label>
        <input type="date" name="date_of_birth" class="form-control" value="{{ old('date_of_birth') }}">
    </div>
</div>

<div class="form-row">
    <div class="form-group">
        <label class="form-label">Photo <small style="font-weight:400;text-transform:none;">(JPG/PNG, max 5MB)</small></label>
        <input type="file" name="photo" class="form-control" accept="image/*" id="photo-input">
        <div id="photo-preview" style="margin-top:10px;display:none;">
            <img id="photo-img" style="width:80px;height:80px;border-radius:50%;object-fit:cover;border:3px solid #1a78c2;" alt="">
        </div>
    </div>
    <div class="form-group">
        <label class="form-label">Biodata File <small style="font-weight:400;text-transform:none;">(PDF/DOC/Image, max 10MB)</small></label>
        <input type="file" name="biodata_file" class="form-control" accept=".pdf,.doc,.docx,.jpg,.png">
    </div>
</div>

<div class="form-group">
    <label class="form-label">Notes</label>
    <textarea name="notes" class="form-control" placeholder="Any additional notes about this crew member...">{{ old('notes') }}</textarea>
</div>

<div style="display:flex;gap:12px;margin-top:8px;">
    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Crew Member</button>
    <a href="{{ route('crews.index') }}" class="btn btn-secondary">Cancel</a>
</div>

</form>
</div>

@push('scripts')
<script>
document.getElementById('photo-input').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = (ev) => {
        document.getElementById('photo-img').src = ev.target.result;
        document.getElementById('photo-preview').style.display = 'block';
    };
    reader.readAsDataURL(file);
});
</script>
@endpush
@endsection
