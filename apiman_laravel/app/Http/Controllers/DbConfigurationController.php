<?php

namespace App\Http\Controllers;

use App\Models\DbConfiguration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DbConfigurationController extends Controller
{
    public function index()
    {
        $configurations = DbConfiguration::all();

        return view('db_configurations.index', [
            'configurations' => $configurations,
            'viewName' => 'index',
        ]);
    }

    public function create()
    {
        return view('db_configurations.create', [
            'viewName' => 'index',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'active_group' => 'required|string',
            'active_record' => 'required|string',
            'hostname' => 'required|string',
            'username' => 'required|string',
            'password' => 'nullable|string',
            'database' => 'required|string',
            'dbdriver' => 'required|string',
            'port' => 'nullable|string',
            'dbprefix' => 'nullable|string',
            'pconnect' => 'required|string',
            'db_debug' => 'required|string',
            'cache_on' => 'required|string',
            'cachedir' => 'nullable|string',
            'char_set' => 'required|string',
            'dbcollat' => 'required|string',
            'swap_pre' => 'nullable|string',
            'autoinit' => 'required|string',
            'stricton' => 'required|string',
        ]);

        $validated['user_id'] = Auth::id();

        DbConfiguration::create($validated);

        return redirect()->route('db-configurations.index')->with('success', 'Successfully Created!');
    }

    public function edit($id)
    {
        $configuration = DbConfiguration::findOrFail($id);

        return view('db_configurations.edit', [
            'configuration' => $configuration,
            'viewName' => 'index',
        ]);
    }

    public function update(Request $request, $id)
    {
        $configuration = DbConfiguration::findOrFail($id);

        $validated = $request->validate([
            'active_group' => 'required|string',
            'active_record' => 'required|string',
            'hostname' => 'required|string',
            'username' => 'required|string',
            'password' => 'nullable|string',
            'database' => 'required|string',
            'dbdriver' => 'required|string',
            'port' => 'nullable|string',
            'dbprefix' => 'nullable|string',
            'pconnect' => 'required|string',
            'db_debug' => 'required|string',
            'cache_on' => 'required|string',
            'cachedir' => 'nullable|string',
            'char_set' => 'required|string',
            'dbcollat' => 'required|string',
            'swap_pre' => 'nullable|string',
            'autoinit' => 'required|string',
            'stricton' => 'required|string',
        ]);

        $configuration->update($validated);

        return redirect()->route('db-configurations.index')->with('success', 'Successfully Updated!');
    }

    public function destroy($id)
    {
        $configuration = DbConfiguration::findOrFail($id);
        $configuration->delete();

        return redirect()->route('db-configurations.index')->with('success', 'Successfully Deleted!');
    }
}
