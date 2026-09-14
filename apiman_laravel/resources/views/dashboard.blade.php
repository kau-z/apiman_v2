@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="mb-0 font-weight-bold"><i class="fas fa-chart-line text-primary mr-2"></i> API Synchronization Dashboard</h3>
            <a href="{{ route('sync-api.create') }}" class="btn btn-primary shadow-sm">
                <i class="fas fa-plus mr-1"></i> New Sync API
            </a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <form method="GET" action="{{ route('dashboard') }}" class="mb-4">
                    <div class="form-row align-items-center">
                        <div class="col-md-5 my-1">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                </div>
                                <input type="text" name="search" class="form-control" placeholder="Search by API name, category, remarks..." value="{{ $search_text ?? '' }}">
                            </div>
                        </div>
                        <div class="col-auto my-1">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-filter mr-1"></i> Filter</button>
                            @if(!empty($search_text))
                                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary ml-1"><i class="fas fa-times"></i> Reset</a>
                            @endif
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover table-bordered table-striped mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>API Name</th>
                                <th>Category</th>
                                <th>Sync Time</th>
                                <th>From PK</th>
                                <th>To PK</th>
                                <th>Status</th>
                                <th>Type</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dataset as $row)
                                <tr>
                                    <td class="font-weight-bold text-primary">{{ $row->api_name }}</td>
                                    <td><span class="badge badge-secondary">{{ $row->category ?? 'General' }}</span></td>
                                    <td>{{ $row->sync_time ? \Carbon\Carbon::parse($row->sync_time)->format('Y-m-d H:i:s') : '-' }}</td>
                                    <td><code>{{ $row->from_primary_key ?? '-' }}</code></td>
                                    <td><code>{{ $row->to_primary_key ?? '-' }}</code></td>
                                    <td>
                                        @if($row->status == 1)
                                            <span class="badge badge-success"><i class="fas fa-check-circle"></i> Success</span>
                                        @elseif($row->status == 9)
                                            <span class="badge badge-warning text-dark"><i class="fas fa-clock"></i> Waiting</span>
                                        @else
                                            <span class="badge badge-danger"><i class="fas fa-times-circle"></i> Failed</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($row->type == 1)
                                            <span class="badge badge-info">Insert</span>
                                        @elseif($row->type == 2)
                                            <span class="badge badge-primary">Update</span>
                                        @elseif($row->type == 3)
                                            <span class="badge badge-danger">Delete</span>
                                        @else
                                            <span class="badge badge-light">Type {{ $row->type }}</span>
                                        @endif
                                    </td>
                                    <td><small class="text-muted">{{ Str::limit($row->remarks, 50) }}</small></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">
                                        <i class="fas fa-inbox fa-3x mb-3 text-secondary d-block"></i>
                                        No synchronization logs found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3 d-flex justify-content-end">
                    {{ $dataset->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
