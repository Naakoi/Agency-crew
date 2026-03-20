<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use Illuminate\Http\Request;

class HotelController extends Controller
{
    public function index()
    {
        $hotels = Hotel::withCount('bookings')->orderBy('hotel_name')->paginate(15);
        return view('hotels.index', compact('hotels'));
    }

    public function create()
    {
        return view('hotels.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'hotel_name' => 'required|string|max:150',
            'location'   => 'nullable|string|max:200',
            'contact'    => 'nullable|string|max:80',
            'email'      => 'nullable|email|max:150',
        ]);
        $hotel = Hotel::create($data);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Hotel added!',
                'hotel'   => $hotel
            ]);
        }

        return redirect()->route('hotels.index')->with('success', 'Hotel added!');
    }

    public function edit(Hotel $hotel)
    {
        return view('hotels.edit', compact('hotel'));
    }

    public function update(Request $request, Hotel $hotel)
    {
        $data = $request->validate([
            'hotel_name' => 'required|string|max:150',
            'location'   => 'nullable|string|max:200',
            'contact'    => 'nullable|string|max:80',
            'email'      => 'nullable|email|max:150',
        ]);
        $hotel->update($data);
        return redirect()->route('hotels.index')->with('success', 'Hotel updated!');
    }

    public function destroy(Hotel $hotel)
    {
        $hotel->delete();
        return redirect()->route('hotels.index')->with('success', 'Hotel deleted.');
    }

    public function show(Hotel $hotel) { return redirect()->route('hotels.index'); }
}
