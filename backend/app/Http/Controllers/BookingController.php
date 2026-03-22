<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Crew;
use App\Models\Company;
use App\Models\Hotel;
use App\Models\User;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with(['crew.creator', 'hotel', 'company', 'assignedUser', 'statusUpdatedBy', 'statusLogs.user'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            if ($request->status === 'assigned_to_me') {
                $query->where('assigned_user_id', auth()->id());
            } else {
                $query->where('status', $request->status);
            }
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('crew', fn($sq) => $sq->where('full_name', 'like', "%$search%"))
                  ->orWhereHas('company', fn($sq) => $sq->where('company_name', 'like', "%$search%"))
                  ->orWhere('invoice_number', 'like', "%$search%");
            });
        }

        $bookings = $query->paginate(12);
        $stats = [
            'total'           => Booking::count(),
            'booked'          => Booking::where('status', 'booked')->count(),
            'pickup_to_hotel' => Booking::where('status', 'pickup_to_hotel')->count(),
            'in_hotel'        => Booking::where('status', 'in_hotel')->count(),
            'pickup_to_plane' => Booking::where('status', 'pickup_to_plane')->count(),
            'cancelled'       => Booking::where('status', 'cancelled')->count(),
        ];
        return view('bookings.index', compact('bookings', 'stats'));
    }

    public function create()
    {
        $crews     = Crew::orderBy('full_name')->get();
        $companies = Company::orderBy('company_name')->get();
        $hotels    = Hotel::orderBy('hotel_name')->get();
        $users     = User::where('role', 'staff')->orderBy('name')->get();
        return view('bookings.create', compact('crews', 'companies', 'hotels', 'users'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'crew_id'        => 'required|exists:crews,id',
            'company_id'     => 'required|exists:companies,id',
            'hotel_id'       => 'required|exists:hotels,id',
            'crew_title'     => 'required|string|max:100',
            'check_in'       => 'required|date',
            'check_out'      => 'required|date|after:check_in',
            'invoice_number' => 'nullable|string|max:100',
            'remarks'        => 'nullable|string',
            'status'         => 'required|in:booked,pickup_to_hotel,in_hotel,pickup_to_plane',
        ]);

        $booking = Booking::create($data);
        
        $booking->statusLogs()->create([
            'status' => $booking->status,
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('bookings.index')->with('success', 'Booking created successfully!');
    }

    public function show(Booking $booking)
    {
        $booking->load(['crew', 'company', 'hotel']);
        return view('bookings.show', compact('booking'));
    }

    public function edit(Booking $booking)
    {
        $crews     = Crew::orderBy('full_name')->get();
        $companies = Company::orderBy('company_name')->get();
        $hotels    = Hotel::orderBy('hotel_name')->get();
        $users     = User::where('role', 'staff')->orderBy('name')->get();
        return view('bookings.edit', compact('booking', 'crews', 'companies', 'hotels', 'users'));
    }

    public function update(Request $request, Booking $booking)
    {
        $data = $request->validate([
            'crew_id'        => 'required|exists:crews,id',
            'company_id'     => 'required|exists:companies,id',
            'hotel_id'       => 'required|exists:hotels,id',
            'crew_title'     => 'required|string|max:100',
            'check_in'       => 'required|date',
            'check_out'      => 'required|date|after:check_in',
            'invoice_number' => 'nullable|string|max:100',
            'remarks'        => 'nullable|string',
            'status'         => 'required|in:booked,pickup_to_hotel,in_hotel,pickup_to_plane',
        ]);

        $oldStatus = $booking->status;
        $booking->update($data);
        $booking->load('crew'); // Load crew to access full_name for logging
        \App\Models\ActivityLog::log('updated_booking', "Updated booking #{$booking->id} for {$booking->crew->full_name}", $booking);

        if ($oldStatus !== $booking->status) {
            \App\Events\BookingStatusChanged::dispatch($booking, $oldStatus, $booking->status, auth()->id());
        }

        return redirect()->route('bookings.show', $booking)->with('success', 'Booking updated!');
    }

    public function destroy(Booking $booking)
    {
        $booking->load('crew'); // Load crew to access full_name for logging
        \App\Models\ActivityLog::log('deleted_booking', "Deleted booking #{$booking->id} for {$booking->crew->full_name}", $booking);
        $booking->delete();
        return redirect()->route('bookings.index')->with('success', 'Booking deleted.');
    }

    public function toggle(Request $request, Booking $booking)
    {
        $oldStatus = $booking->status;
        $statusSequence = ['booked', 'pickup_to_hotel', 'in_hotel', 'pickup_to_plane', 'cancelled'];
        
        if ($request->has('status') && in_array($request->status, $statusSequence)) {
            $booking->status = $request->status;
        } else {
            $currentIndex = array_search($oldStatus, $statusSequence);
            if ($currentIndex === false) $currentIndex = -1;
            $nextIndex = ($currentIndex + 1) % count($statusSequence);
            $booking->status = $statusSequence[$nextIndex];
        }
        
        $booking->status_updated_by = auth()->id();
        $booking->save();

        if ($oldStatus !== $booking->status) {
            \App\Events\BookingStatusChanged::dispatch($booking, $oldStatus, $booking->status, auth()->id());
        }
        
        $booking->statusLogs()->create([
            'status' => $booking->status,
            'user_id' => auth()->id(),
        ]);
        
        return back()->with('success', 'Status updated to: ' . $booking->status_label);
    }

    public function deleteStatusLog(\App\Models\BookingStatusLog $log)
    {
        $booking = $log->booking;
        $statusLabel = $log->status_label;
        $log->delete();

        \App\Models\ActivityLog::log('deleted_status_log', "Removed status history entry '{$statusLabel}' for booking #{$booking->id}", $booking);

        // After deleting, update the booking to the now-latest status log if any
        $latestLog = $booking->statusLogs()->latest()->first();
        if ($latestLog) {
            $booking->update([
                'status' => $latestLog->status,
                'status_updated_by' => $latestLog->user_id
            ]);
        }

        return back()->with('success', 'Status log removed.');
    }
}
