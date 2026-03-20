<?php

namespace App\Http\Controllers;

use App\Models\Crew;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CrewController extends Controller
{
    public function index()
    {
        $crews = Crew::with(['latestBooking'])->withCount('bookings')->orderBy('full_name')->paginate(15);
        return view('crews.index', compact('crews'));
    }

    public function create()
    {
        return view('crews.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'full_name'      => 'required|string|max:150',
            'nationality'    => 'nullable|string|max:80',
            'passport_number'=> 'nullable|string|max:50',
            'passport_expiry_date' => 'nullable|date',
            'date_of_birth'  => 'nullable|date',
            'notes'          => 'nullable|string',
            'photo'          => 'nullable|image|max:5120',
            'biodata_file'   => 'nullable|file|mimes:pdf,doc,docx,jpg,png|max:10240',
        ]);

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('crew_photos', 'public');
        }
        if ($request->hasFile('biodata_file')) {
            $data['biodata_file'] = $request->file('biodata_file')->store('crew_biodata', 'public');
        }

        $data['created_by'] = auth()->id();

        $crew = Crew::create($data);
        
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Crew member added!',
                'crew'    => $crew
            ]);
        }
        
        return redirect()->route('crews.index')->with('success', 'Crew member added!');
    }

    public function show(Crew $crew)
    {
        $crew->load('bookings.hotel', 'bookings.company');
        return view('crews.show', compact('crew'));
    }

    public function edit(Crew $crew)
    {
        return view('crews.edit', compact('crew'));
    }

    public function update(Request $request, Crew $crew)
    {
        $data = $request->validate([
            'full_name'      => 'required|string|max:150',
            'nationality'    => 'nullable|string|max:80',
            'passport_number'=> 'nullable|string|max:50',
            'passport_expiry_date' => 'nullable|date',
            'date_of_birth'  => 'nullable|date',
            'notes'          => 'nullable|string',
            'photo'          => 'nullable|image|max:5120',
            'biodata_file'   => 'nullable|file|mimes:pdf,doc,docx,jpg,png|max:10240',
        ]);

        if ($request->hasFile('photo')) {
            if ($crew->photo) Storage::disk('public')->delete($crew->photo);
            $data['photo'] = $request->file('photo')->store('crew_photos', 'public');
        }
        if ($request->hasFile('biodata_file')) {
            if ($crew->biodata_file) Storage::disk('public')->delete($crew->biodata_file);
            $data['biodata_file'] = $request->file('biodata_file')->store('crew_biodata', 'public');
        }

        $crew->update($data);
        return redirect()->route('crews.show', $crew)->with('success', 'Crew updated!');
    }

    public function destroy(Crew $crew)
    {
        if ($crew->photo) Storage::disk('public')->delete($crew->photo);
        if ($crew->biodata_file) Storage::disk('public')->delete($crew->biodata_file);
        $crew->delete();
        return redirect()->route('crews.index')->with('success', 'Crew member removed.');
    }
}
