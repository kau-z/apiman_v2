<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function create()
    {
        return view('company.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_legal_name' => 'required|string|max:255',
            'company_reg_no' => 'required|string|max:100',
            'incorporation_date' => 'nullable|date',
            'financial_year' => 'nullable|string|max:50',
            'tin_no' => 'nullable|string|max:100',
            'vat_svat_no' => 'nullable|string|max:100',
            'nbt_reg_no' => 'nullable|string|max:100',
            'epf_etf_reg_no' => 'nullable|string|max:100',
            'payee_tax_no' => 'nullable|string|max:100',
            'address' => 'nullable|string',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('companies', 'public');
            $validated['image'] = $path;
        }

        Company::create($validated);

        return redirect()->route('register')->with('success', 'Company created successfully!');
    }
}
