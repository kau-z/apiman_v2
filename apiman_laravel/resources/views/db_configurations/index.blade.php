@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="mb-0 font-weight-bold text-primary"><i class="fas fa-database mr-2"></i> Database Configurations</h3>
            <a href="{{ route('db-configurations.create') }}" class="btn btn-primary shadow-sm">
                <i class="fas fa-plus mr-1"></i> New DB Configuration
            </a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>Active Group</th>
                                <th>Hostname</th>
                                <th>Database</th>
                                <th>Username</th>
                                <th>Driver</th>
                                <th>Debug</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($configurations as $config)
                                <tr>
                                    <td>{{ $config->id }}</td>
                                    <td class="font-weight-bold text-primary">{{ $config->active_group }}</td>
                                    <td><code>{{ $config->hostname }}</code></td>
                                    <td><strong>{{ $config->database }}</strong></td>
                                    <td>{{ $config->username }}</td>
                                    <td><span class="badge badge-secondary">{{ $config->dbdriver }}</span></td>
                                    <td><span class="badge badge-{{ $config->db_debug === 'TRUE' ? 'success' : 'secondary' }}">{{ $config->db_debug }}</span></td>
                                    <td class="text-right">
                                        <a href="{{ route('db-configurations.edit', $config->id) }}" class="btn btn-sm btn-outline-primary mr-1">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <form action="{{ route('db-configurations.destroy', $config->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this DB configuration?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">
                                        <i class="fas fa-inbox fa-3x mb-3 text-secondary d-block"></i>
                                        No Database Configurations found. <a href="{{ route('db-configurations.create') }}">Create one now</a>.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
