@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="mb-0 font-weight-bold text-primary"><i class="fas fa-cogs mr-2"></i> API Configurations</h3>
            <a href="{{ route('api-configurations.create') }}" class="btn btn-primary shadow-sm">
                <i class="fas fa-plus mr-1"></i> New API Configuration
            </a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Base URL</th>
                                <th>Endpoint</th>
                                <th>Method</th>
                                <th>Auth Type</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($configurations as $config)
                                <tr>
                                    <td>{{ $config->id }}</td>
                                    <td class="font-weight-bold">{{ $config->name }}</td>
                                    <td><code>{{ $config->base_url }}</code></td>
                                    <td><code>{{ $config->endpoint ?? '/' }}</code></td>
                                    <td><span class="badge badge-info">{{ $config->request_method ?? 'GET' }}</span></td>
                                    <td><span class="badge badge-secondary">{{ $config->auth_type }}</span></td>
                                    <td class="text-right">
                                        <a href="{{ route('api-configurations.edit', $config->id) }}" class="btn btn-sm btn-outline-primary mr-1">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <form action="{{ route('api-configurations.destroy', $config->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this configuration?');">
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
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        <i class="fas fa-inbox fa-3x mb-3 text-secondary d-block"></i>
                                        No API Configurations found. <a href="{{ route('api-configurations.create') }}">Create one now</a>.
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
