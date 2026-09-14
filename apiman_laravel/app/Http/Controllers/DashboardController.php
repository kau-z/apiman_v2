<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $query = DB::table('sync_api_detail')
            ->leftJoin('sync_api', 'sync_api_detail.main_id', '=', 'sync_api.main_id')
            ->leftJoin('category', 'sync_api.category_id', '=', 'category.id');

        // Check if transaction_history table exists for from_primary_key / to_primary_key
        if (Schema::hasTable('transaction_history')) {
            $query->leftJoin('transaction_history', 'sync_api_detail.id', '=', 'transaction_history.sync_detail_id')
                ->select(
                    'sync_api.api_name',
                    'category.description as category',
                    'sync_api_detail.sync_time',
                    'transaction_history.from_primary_key',
                    'transaction_history.to_primary_key',
                    'sync_api_detail.status',
                    'sync_api_detail.type',
                    'sync_api_detail.remarks'
                );
        } else {
            // Check if from_primary_key exists in sync_api_detail itself
            $hasFromPk = Schema::hasColumn('sync_api_detail', 'from_primary_key');
            $hasToPk = Schema::hasColumn('sync_api_detail', 'to_primary_key');

            $query->select(
                'sync_api.api_name',
                'category.description as category',
                'sync_api_detail.sync_time',
                $hasFromPk ? 'sync_api_detail.from_primary_key' : DB::raw('NULL as from_primary_key'),
                $hasToPk ? 'sync_api_detail.to_primary_key' : DB::raw('NULL as to_primary_key'),
                'sync_api_detail.status',
                'sync_api_detail.type',
                'sync_api_detail.remarks'
            );
        }

        $query->orderByDesc('sync_api_detail.id');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('sync_api.api_name', 'LIKE', "%{$search}%")
                  ->orWhere('category.description', 'LIKE', "%{$search}%")
                  ->orWhere('sync_api_detail.remarks', 'LIKE', "%{$search}%");
            });
        }

        $dataset = $query->paginate(20)->withQueryString();

        return view('dashboard', [
            'dataset' => $dataset,
            'search_text' => $search,
            'viewName' => 'dashboard',
        ]);
    }
}
