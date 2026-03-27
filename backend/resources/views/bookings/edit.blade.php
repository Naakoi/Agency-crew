@extends('layouts.app')
@section('title', 'Edit Booking')

@section('content')
<div class="page-header">
    <div>
        <h1><i class="fas fa-edit" style="color:#0e9ae0;margin-right:10px;"></i>Edit Booking #{{ $booking->id }}</h1>
        <p>Update accommodation record for {{ $booking->crew->full_name }}</p>
    </div>
    <a href="{{ route('bookings.show', $booking) }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
</div>

<div class="card">
<form method="POST" action="{{ route('bookings.update', $booking) }}">
@csrf @method('PUT')

<h3 style="font-size:14px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:1px;margin-bottom:20px;padding-bottom:12px;border-bottom:1px solid rgba(30,58,95,0.6);">
    <i class="fas fa-user" style="color:#0e9ae0;margin-right:8px;"></i>Crew & Company Information
</h3>

<div class="form-row">
    <div class="form-group">
        <label class="form-label">Crew Member *</label>
        <select name="crew_id" id="crew_id" class="form-control" required>
            @foreach($crews as $crew)
                <option value="{{ $crew->id }}" {{ $booking->crew_id == $crew->id ? 'selected' : '' }}>
                    {{ $crew->full_name }}
                </option>
            @endforeach
        </select>
        <div class="d-flex" style="margin-top:6px;">
            <a href="javascript:void(0)" onclick="openModal('crewModal')" style="font-size:12px;color:#0e9ae0;">+ Add new crew member</a>
        </div>
    </div>
    <div class="form-group">
        <label class="form-label">Crew Title / Rank *</label>
        <input type="text" name="crew_title" class="form-control" value="{{ old('crew_title', $booking->crew_title) }}" required>
    </div>
</div>

<div class="form-row">
    <div class="form-group">
        <label class="form-label">Company / Vessel *</label>
        <select name="company_id" id="company_id" class="form-control" required>
            @foreach($companies as $company)
                <option value="{{ $company->id }}" {{ $booking->company_id == $company->id ? 'selected' : '' }}>
                    {{ $company->company_name }} — {{ $company->ship_name }}
                </option>
            @endforeach
        </select>
        <div style="margin-top:6px;">
            <a href="javascript:void(0)" onclick="openModal('companyModal')" style="font-size:12px;color:#0e9ae0;">+ Add new company</a>
        </div>
    </div>
    <div class="form-group">
        <label class="form-label">Hotel *</label>
        <select name="hotel_id" id="hotel_id" class="form-control" required>
            @foreach($hotels as $hotel)
                <option value="{{ $hotel->id }}" {{ $booking->hotel_id == $hotel->id ? 'selected' : '' }}>
                    {{ $hotel->hotel_name }}
                </option>
            @endforeach
        </select>
        <div style="margin-top:6px;">
            <a href="javascript:void(0)" onclick="openModal('hotelModal')" style="font-size:12px;color:#0e9ae0;">+ Add new hotel</a>
        </div>
    </div>
</div>

<h3 style="font-size:14px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:1px;margin:24px 0 20px;padding-bottom:12px;border-bottom:1px solid rgba(30,58,95,0.6);">
    <i class="fas fa-calendar-alt" style="color:#0e9ae0;margin-right:8px;"></i>Stay Information
</h3>

<div class="form-row">
    <div class="form-group">
        <label class="form-label">Check-In *</label>
        <input type="datetime-local" name="check_in" class="form-control"
            value="{{ old('check_in', $booking->check_in->format('Y-m-d\TH:i')) }}" required>
    </div>
    <div class="form-group">
        <label class="form-label">Check-Out *</label>
        <input type="datetime-local" name="check_out" class="form-control"
            value="{{ old('check_out', $booking->check_out->format('Y-m-d\TH:i')) }}" required>
    </div>
</div>

<div class="form-row">
    <div class="form-group">
        <label class="form-label">Invoice Number</label>
        <input type="text" name="invoice_number" class="form-control" value="{{ old('invoice_number', $booking->invoice_number) }}">
    </div>
    <div class="form-group">
        <label class="form-label">Status *</label>
        <select name="status" class="form-control" required>
            <option value="booked"           {{ $booking->status === 'booked' ? 'selected' : '' }}>Hotel Booked</option>
            <option value="pickup_to_hotel"  {{ $booking->status === 'pickup_to_hotel' ? 'selected' : '' }}>Pickup to Hotel</option>
            <option value="in_hotel"         {{ $booking->status === 'in_hotel' ? 'selected' : '' }}>In Hotel</option>
            <option value="pickup_to_ship"  {{ $booking->status === 'pickup_to_ship' ? 'selected' : '' }}>Pick up to Ship</option>
            <option value="pickup_to_plane"  {{ $booking->status === 'pickup_to_plane' ? 'selected' : '' }}>Pickup to Plane</option>
            <option value="cancelled"        {{ $booking->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
        </select>
    </div>
    @if(auth()->user()->isAdmin())
    <div class="form-group">
        <label class="form-label">Assigned Staff</label>
        <select name="assigned_user_id" class="form-control">
            <option value="">— Unassigned —</option>
            @foreach($users as $user)
                <option value="{{ $user->id }}" {{ old('assigned_user_id', $booking->assigned_user_id) == $user->id ? 'selected' : '' }}>
                    {{ $user->name }}
                </option>
            @endforeach
        </select>
    </div>
    @endif
</div>

<div class="form-group">
    <label class="form-label">Remarks</label>
    <textarea name="remarks" class="form-control">{{ old('remarks', $booking->remarks) }}</textarea>
</div>

<div style="display:flex;gap:12px;margin-top:8px;">
    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Changes</button>
    <a href="{{ route('bookings.show', $booking) }}" class="btn btn-secondary">Cancel</a>
</div>

</form>
</div>

{{-- Modals --}}
<div id="crewModal" class="modal">
    <div class="modal-content" style="max-width: 700px;">
        <div class="modal-header">
            <h3>Add New Crew Member</h3>
            <span class="close" onclick="closeModal('crewModal')">&times;</span>
        </div>
        <form id="ajaxCrewForm" enctype="multipart/form-data">
            @csrf
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Full Name *</label>
                    <input type="text" name="full_name" class="form-control" required placeholder="Name as in passport">
                </div>
                <div class="form-group">
                    <label class="form-label">Nationality</label>
                    <input type="text" name="nationality" class="form-control" placeholder="e.g. Kiribati">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Passport Number</label>
                    <input type="text" name="passport_number" class="form-control" placeholder="e.g. KI123456">
                </div>
                <div class="form-group">
                    <label class="form-label">Passport Expiry Date</label>
                    <input type="date" name="passport_expiry_date" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">Date of Birth</label>
                    <input type="date" name="date_of_birth" class="form-control">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Photo <small>(JPG/PNG)</small></label>
                    <input type="file" name="photo" class="form-control" accept="image/*" id="ajax-photo-input">
                    <div id="ajax-photo-preview" style="margin-top:10px;display:none;">
                        <img id="ajax-photo-img" style="width:60px;height:60px;border-radius:50%;object-fit:cover;border:2px solid #0e9ae0;" alt="">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Biodata File <small>(PDF/DOC/Img)</small></label>
                    <input type="file" name="biodata_file" class="form-control" accept=".pdf,.doc,.docx,.jpg,.png">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Notes</label>
                <textarea name="notes" class="form-control" placeholder="Any additional notes..."></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('crewModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Crew Member</button>
            </div>
        </form>
    </div>
</div>

<div id="companyModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Add New Company / Vessel</h3>
            <span class="close" onclick="closeModal('companyModal')">&times;</span>
        </div>
        <form id="ajaxCompanyForm">
            @csrf
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Company Name *</label>
                    <input type="text" name="company_name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Ship / Vessel Name *</label>
                    <input type="text" name="ship_name" class="form-control" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Contact</label>
                    <input type="text" name="contact" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">Address</label>
                    <input type="text" name="address" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('companyModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Company</button>
            </div>
        </form>
    </div>
</div>

<div id="hotelModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Add New Hotel</h3>
            <span class="close" onclick="closeModal('hotelModal')">&times;</span>
        </div>
        <form id="ajaxHotelForm">
            @csrf
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Hotel Name *</label>
                    <input type="text" name="hotel_name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Location</label>
                    <input type="text" name="location" class="form-control" placeholder="e.g. Tarawa">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Contact Number</label>
                    <input type="text" name="contact" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('hotelModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Hotel</button>
            </div>
        </form>
    </div>
</div>

@push('styles')
<style>
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(15, 23, 42, 0.85);
    backdrop-filter: blur(8px);
}
.modal-content {
    background: #1e293b;
    margin: 5% auto;
    padding: 0;
    border: 1px solid rgba(255,255,255,0.1);
    width: 95%;
    max-width: 600px;
    border-radius: 16px;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
    animation: modalSlideUp 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    position: relative;
}
@keyframes modalSlideUp {
    from { opacity: 0; transform: translateY(30px) scale(0.98); }
    to { opacity: 1; transform: translateY(0) scale(1); }
}
.modal-header {
    padding: 20px 24px;
    border-bottom: 1px solid rgba(255,255,255,0.1);
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: rgba(255,255,255,0.02);
    border-radius: 16px 16px 0 0;
}
.modal-header h3 { font-size: 18px; font-weight: 700; color: #f8fafc; margin: 0; }
.close { color: #94a3b8; font-size: 28px; font-weight: 400; cursor: pointer; transition: all 0.2s; line-height: 1; }
.close:hover { color: #f8fafc; transform: rotate(90deg); }
#ajaxCrewForm, #ajaxCompanyForm, #ajaxHotelForm { padding: 24px; }
.modal-footer {
    padding: 16px 24px;
    border-top: 1px solid rgba(255,255,255,0.1);
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    background: rgba(255,255,255,0.02);
    border-radius: 0 0 16px 16px;
}
.modal .form-group { margin-bottom: 16px; }
.modal .form-label { color: #94a3b8; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
.modal .form-control { 
    background: #0f172a; 
    border: 1px solid rgba(255,255,255,0.1); 
    color: #f8fafc;
    padding: 10px 14px;
    border-radius: 8px;
}
.modal .form-control:focus { border-color: #0e9ae0; box-shadow: 0 0 0 3px rgba(14, 154, 224, 0.2); }
</style>
@endpush

@push('scripts')
<script>
function openModal(id) { document.getElementById(id).style.display = "block"; }
function closeModal(id) { document.getElementById(id).style.display = "none"; }

// Photo preview for AJAX form
document.getElementById('ajax-photo-input')?.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = (ev) => {
        document.getElementById('ajax-photo-img').src = ev.target.result;
        document.getElementById('ajax-photo-preview').style.display = 'block';
    };
    reader.readAsDataURL(file);
});

// Handle Form Submissions via AJAX
document.getElementById('ajaxCrewForm').addEventListener('submit', function(e) {
    e.preventDefault();
    submitForm(this, '{{ route("crews.store") }}', 'crew_id');
});

document.getElementById('ajaxCompanyForm').addEventListener('submit', function(e) {
    e.preventDefault();
    submitForm(this, '{{ route("companies.store") }}', 'company_id');
});

document.getElementById('ajaxHotelForm').addEventListener('submit', function(e) {
    e.preventDefault();
    submitForm(this, '{{ route("hotels.store") }}', 'hotel_id');
});

function submitForm(form, url, selectId) {
    const formData = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';

    fetch(url, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const select = document.getElementById(selectId);
            const option = document.createElement('option');
            
            if (selectId === 'crew_id') {
                option.value = data.crew.id;
                option.text = `${data.crew.full_name}`;
            } else if (selectId === 'company_id') {
                option.value = data.company.id;
                option.text = `${data.company.company_name} — ${data.company.ship_name}`;
            } else if (selectId === 'hotel_id') {
                option.value = data.hotel.id;
                option.text = data.hotel.hotel_name;
            }
            
            select.add(option);
            select.value = option.value;
            
            closeModal(form.closest('.modal').id);
            form.reset();
            if (document.getElementById('ajax-photo-preview')) document.getElementById('ajax-photo-preview').style.display = 'none';
        } else {
            alert('Error: ' + JSON.stringify(data.errors || data.message));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while saving.');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
}

window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.style.display = "none";
    }
}
</script>
@endpush

</form>
</div>
@endsection
