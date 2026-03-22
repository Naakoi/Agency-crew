<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CrewController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\HotelController;

// Public: Login
Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email'    => 'required|email',
        'password' => 'required',
    ]);

    if (!\Illuminate\Support\Facades\Auth::attempt($credentials)) {
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    $user  = \App\Models\User::where('email', $request->email)->first();
    $token = $user->createToken('api-token')->plainTextToken;

    return response()->json(['token' => $token, 'user' => $user]);
});

Route::post('/logout', function (Request $request) {
    $request->user()->currentAccessToken()->delete();
    return response()->json(['message' => 'Logged out']);
})->middleware('auth:sanctum');

// Protected API routes
Route::middleware('auth:sanctum')->group(function () {

    Route::get('/me', fn(Request $r) => response()->json($r->user()));

    Route::post('/fcm-token', function (Request $r) {
        $r->validate(['token' => 'required|string', 'device_type' => 'nullable|string']);
        \App\Models\FcmToken::updateOrCreate(
            ['user_id' => $r->user()->id, 'token' => $r->token],
            ['device_type' => $r->device_type ?? 'mobile']
        );
        return response()->json(['message' => 'Token saved successfully']);
    });

    // Bookings
    Route::get('/bookings', function (Request $r) {
        $q = \App\Models\Booking::with(['crew', 'company', 'hotel', 'statusLogs.user'])->orderByDesc('created_at');
        if ($r->status) $q->where('status', $r->status);
        return response()->json($q->paginate(20));
    });
    Route::post('/bookings', function (Request $r) {
        $data = $r->validate([
            'crew_id' => 'required|exists:crews,id', 'company_id' => 'required|exists:companies,id',
            'hotel_id' => 'required|exists:hotels,id', 'crew_title' => 'required|string',
            'check_in' => 'required|date', 'check_out' => 'required|date|after:check_in',
            'invoice_number' => 'nullable|string', 'remarks' => 'nullable|string',
            'status' => 'required|in:booked,pickup_to_hotel,in_hotel,pickup_to_plane',
        ]);
        $booking = \App\Models\Booking::create($data);
        $booking->statusLogs()->create(['status' => $booking->status, 'user_id' => auth()->id()]);
        return response()->json($booking->load(['crew','company','hotel','statusLogs.user']), 201);
    });
    Route::get('/bookings/{booking}', fn($id) => response()->json(\App\Models\Booking::with(['crew','company','hotel','statusLogs.user'])->findOrFail($id)));
    Route::put('/bookings/{booking}', function (Request $r, \App\Models\Booking $booking) {
        $booking->update($r->validate([
            'crew_id' => 'required|exists:crews,id', 'company_id' => 'required|exists:companies,id',
            'hotel_id' => 'required|exists:hotels,id', 'crew_title' => 'required|string',
            'check_in' => 'required|date', 'check_out' => 'required|date|after:check_in',
            'invoice_number' => 'nullable|string', 'remarks' => 'nullable|string',
            'status' => 'required|in:in_hotel,departed',
        ]));
        return response()->json($booking->load(['crew','company','hotel']));
    });
    Route::delete('/bookings/{booking}', function (\App\Models\Booking $booking) {
        $booking->delete(); return response()->json(['message' => 'Deleted']);
    });
    Route::post('/bookings/{booking}/toggle-status', function (\App\Models\Booking $booking) {
        $statusSequence = ['booked', 'pickup_to_hotel', 'in_hotel', 'pickup_to_plane', 'cancelled'];
        $nextIndex = (array_search($booking->status, $statusSequence) + 1) % count($statusSequence);
        $booking->status = $statusSequence[$nextIndex];
        $booking->save();
        $booking->statusLogs()->create(['status' => $booking->status, 'user_id' => auth()->id()]);
        return response()->json($booking->load('statusLogs.user'));
    });

    // Crews
    Route::get('/crews', fn() => response()->json(\App\Models\Crew::orderBy('full_name')->get()));
    Route::get('/crews/{crew}', fn(\App\Models\Crew $crew) => response()->json($crew->load('bookings.hotel','bookings.company')));

    // Companies
    Route::get('/companies', fn() => response()->json(\App\Models\Company::all()));

    // Hotels
    Route::get('/hotels', fn() => response()->json(\App\Models\Hotel::all()));

    // Stats
    Route::get('/stats', function () {
        return response()->json([
            'total'           => \App\Models\Booking::count(),
            'booked'          => \App\Models\Booking::where('status', 'booked')->count(),
            'pickup_to_hotel' => \App\Models\Booking::where('status', 'pickup_to_hotel')->count(),
            'in_hotel'        => \App\Models\Booking::where('status', 'in_hotel')->count(),
            'pickup_to_plane' => \App\Models\Booking::where('status', 'pickup_to_plane')->count(),
            'cancelled'       => \App\Models\Booking::where('status', 'cancelled')->count(),
        ]);
    });
});
