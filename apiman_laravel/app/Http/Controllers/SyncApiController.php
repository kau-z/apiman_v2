<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\SyncApi;
use Illuminate\Http\Request;

class SyncApiController extends Controller
{
    public function create()
    {
        $categories = Category::all();

        return view('sync_api.create', [
            'categories' => $categories,
            'viewName' => 'sync_api',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'api_name' => 'required|string|unique:sync_api,api_name',
            'category_id' => 'nullable|integer',
            'from_db' => 'nullable|string',
            'to_db' => 'nullable|string',
            'from_added_query' => 'nullable|string',
            'Prefix' => 'nullable|string',
            'Suffix' => 'nullable|string',
            'to_added_query' => 'nullable|string',
            'from_Updated_query' => 'nullable|string',
            'to_Updated_query' => 'nullable|string',
            'from_updated_query' => 'nullable|string',
            'to_updated_query' => 'nullable|string',
            'from_delete_query' => 'nullable|string',
            'to_delete_query' => 'nullable|string',
            'last_added_time_query' => 'nullable|string',
            'last_updated_time_query' => 'nullable|string',
            'last_deleted_time_query' => 'nullable|string',
            'total_count_query' => 'nullable|string',
            'status_to_aync_api_detail' => 'nullable|string',
            'wait_till_confirmation' => 'nullable|integer',
        ]);

        $data = [
            'api_name' => $validated['api_name'],
            'category_id' => $validated['category_id'] ?? null,
            'from_db' => $validated['from_db'] ?? '',
            'to_db' => $validated['to_db'] ?? '',
            'from_added_query' => $validated['from_added_query'] ?? '',
            'Prefix' => $validated['Prefix'] ?? '',
            'Suffix' => $validated['Suffix'] ?? '',
            'to_added_query' => $validated['to_added_query'] ?? '',
            'from_updated_query' => $validated['from_Updated_query'] ?? $validated['from_updated_query'] ?? '',
            'to_updated_query' => $validated['to_Updated_query'] ?? $validated['to_updated_query'] ?? '',
            'from_delete_query' => $validated['from_delete_query'] ?? '',
            'to_delete_query' => $validated['to_delete_query'] ?? '',
            'last_added_time_query' => $validated['last_added_time_query'] ?? '',
            'last_updated_time_query' => $validated['last_updated_time_query'] ?? '',
            'last_deleted_time_query' => $validated['last_deleted_time_query'] ?? '',
            'total_count_query' => $validated['total_count_query'] ?? '',
            'status_to_aync_api_detail' => $validated['status_to_aync_api_detail'] ?? 0,
            'wait_till_confirmation' => $validated['wait_till_confirmation'] ?? 1,
        ];

        $syncApi = SyncApi::create($data);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['status' => true, 'message' => 'success', 'id' => $syncApi->main_id]);
        }

        return redirect()->route('sync-api.index')->with('success', 'API successfully created!');
    }

    public function index()
    {
        $apiNames = SyncApi::select('main_id', 'api_name')->get();

        return view('sync_api.view', [
            'apiNames' => $apiNames,
            'viewName' => 'sync_api_view',
        ]);
    }

    public function edit(Request $request, $id = null)
    {
        $syncApi = $id ? SyncApi::findOrFail($id) : null;
        $categories = Category::all();
        $apiNames = SyncApi::select('main_id', 'api_name')->get();

        return view('sync_api.edit', [
            'syncApi' => $syncApi,
            'categories' => $categories,
            'apiNames' => $apiNames,
            'viewName' => 'sync_api_edit',
        ]);
    }

    public function update(Request $request, $id = null)
    {
        $mainId = $id ?? $request->input('mainId');
        $syncApi = SyncApi::findOrFail($mainId);

        $validated = $request->validate([
            'api_name' => 'required|string|unique:sync_api,api_name,' . $mainId . ',main_id',
            'category_id' => 'nullable|integer',
            'from_db' => 'nullable|string',
            'to_db' => 'nullable|string',
            'from_added_query' => 'nullable|string',
            'Prefix' => 'nullable|string',
            'Suffix' => 'nullable|string',
            'to_added_query' => 'nullable|string',
            'from_Updated_query' => 'nullable|string',
            'to_Updated_query' => 'nullable|string',
            'from_updated_query' => 'nullable|string',
            'to_updated_query' => 'nullable|string',
            'from_delete_query' => 'nullable|string',
            'to_delete_query' => 'nullable|string',
            'last_added_time_query' => 'nullable|string',
            'last_updated_time_query' => 'nullable|string',
            'last_deleted_time_query' => 'nullable|string',
            'total_count_query' => 'nullable|string',
            'status_to_aync_api_detail' => 'nullable|string',
            'wait_till_confirmation' => 'nullable|integer',
        ]);

        $data = [
            'api_name' => $validated['api_name'],
            'category_id' => $validated['category_id'] ?? null,
            'from_db' => $validated['from_db'] ?? '',
            'to_db' => $validated['to_db'] ?? '',
            'from_added_query' => $validated['from_added_query'] ?? '',
            'Prefix' => $validated['Prefix'] ?? '',
            'Suffix' => $validated['Suffix'] ?? '',
            'to_added_query' => $validated['to_added_query'] ?? '',
            'from_updated_query' => $validated['from_Updated_query'] ?? $validated['from_updated_query'] ?? '',
            'to_updated_query' => $validated['to_Updated_query'] ?? $validated['to_updated_query'] ?? '',
            'from_delete_query' => $validated['from_delete_query'] ?? '',
            'to_delete_query' => $validated['to_delete_query'] ?? '',
            'last_added_time_query' => $validated['last_added_time_query'] ?? '',
            'last_updated_time_query' => $validated['last_updated_time_query'] ?? '',
            'last_deleted_time_query' => $validated['last_deleted_time_query'] ?? '',
            'total_count_query' => $validated['total_count_query'] ?? '',
            'status_to_aync_api_detail' => $validated['status_to_aync_api_detail'] ?? 0,
            'wait_till_confirmation' => $validated['wait_till_confirmation'] ?? 1,
        ];

        $syncApi->update($data);

        if ($request->wantsJson() || $request->ajax()) {
            return response('success');
        }

        return redirect()->route('sync-api.index')->with('success', 'API successfully updated!');
    }

    public function category(Request $request)
    {
        $request->validate([
            'description' => 'required|string|max:255',
        ]);

        $cat = Category::create([
            'description' => $request->input('description'),
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['status' => true, 'id' => $cat->id, 'description' => $cat->description]);
        }

        return back()->with('success', 'Category added!');
    }

    public function getDetails($id)
    {
        $syncApi = SyncApi::findOrFail($id);
        return response()->json($syncApi);
    }
}
