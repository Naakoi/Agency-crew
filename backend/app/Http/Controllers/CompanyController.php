<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function index()
    {
        $companies = Company::withCount('bookings')->orderBy('company_name')->paginate(15);
        return view('companies.index', compact('companies'));
    }

    public function create()
    {
        return view('companies.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'company_name' => 'required|string|max:150',
            'ship_name'    => 'required|string|max:150',
            'contact'      => 'nullable|string|max:80',
            'address'      => 'nullable|string',
        ]);
        $company = Company::create($data);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Company added!',
                'company' => $company
            ]);
        }

        return redirect()->route('companies.index')->with('success', 'Company added!');
    }

    public function edit(Company $company)
    {
        return view('companies.edit', compact('company'));
    }

    public function update(Request $request, Company $company)
    {
        $data = $request->validate([
            'company_name' => 'required|string|max:150',
            'ship_name'    => 'required|string|max:150',
            'contact'      => 'nullable|string|max:80',
            'address'      => 'nullable|string',
        ]);
        $company->update($data);
        return redirect()->route('companies.index')->with('success', 'Company updated!');
    }

    public function destroy(Company $company)
    {
        $company->delete();
        return redirect()->route('companies.index')->with('success', 'Company deleted.');
    }

    public function show(Company $company) { return redirect()->route('companies.index'); }
}
