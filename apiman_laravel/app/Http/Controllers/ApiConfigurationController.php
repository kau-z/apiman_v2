<?php

namespace App\Http\Controllers;

use App\Models\ApiConfiguration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiConfigurationController extends Controller
{
    public function index()
    {
        $configurations = ApiConfiguration::all();

        return view('api_configurations.index', [
            'configurations' => $configurations,
            'viewName' => 'apiConfig',
        ]);
    }

    public function create()
    {
        return view('api_configurations.create', [
            'viewName' => 'apiConfig',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'base_url' => 'required|url',
            'auth_type' => 'required|string',
            'auth_username' => 'nullable|string',
            'auth_password' => 'nullable|string',
            'auth_token' => 'nullable|string',
            'request_method' => 'nullable|string',
            'request_body_format' => 'nullable|string',
            'endpoint' => 'nullable|string',
            'header_key' => 'nullable|string',
            'header_value' => 'nullable|string',
            'param_key' => 'nullable|string',
            'param_value' => 'nullable|string',
        ]);

        $validated['user_id'] = Auth::id();

        ApiConfiguration::create($validated);

        return redirect()->route('api-configurations.index')->with('success', 'Successfully Created!');
    }

    public function edit($id)
    {
        $configuration = ApiConfiguration::findOrFail($id);

        return view('api_configurations.edit', [
            'configuration' => $configuration,
            'viewName' => 'apiConfig',
        ]);
    }

    public function update(Request $request, $id)
    {
        $configuration = ApiConfiguration::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'base_url' => 'required|url',
            'auth_type' => 'required|string',
            'auth_username' => 'nullable|string',
            'auth_password' => 'nullable|string',
            'auth_token' => 'nullable|string',
            'request_method' => 'nullable|string',
            'request_body_format' => 'nullable|string',
            'endpoint' => 'nullable|string',
            'header_key' => 'nullable|string',
            'header_value' => 'nullable|string',
            'param_key' => 'nullable|string',
            'param_value' => 'nullable|string',
        ]);

        $configuration->update($validated);

        return redirect()->route('api-configurations.index')->with('success', 'Successfully Updated!');
    }

    public function destroy($id)
    {
        $configuration = ApiConfiguration::findOrFail($id);
        $configuration->delete();

        return redirect()->route('api-configurations.index')->with('success', 'Successfully Deleted!');
    }
}
