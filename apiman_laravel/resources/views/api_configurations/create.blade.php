@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h4 class="mb-0 font-weight-bold text-primary"><i class="fas fa-plus-circle mr-2"></i> Create API Configuration</h4>
                <a href="{{ route('api-configurations.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left mr-1"></i> Back</a>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('api-configurations.store') }}">
                    @csrf

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold">Configuration Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="e.g. Payment Gateway" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold">Base URL <span class="text-danger">*</span></label>
                            <input type="url" name="base_url" class="form-control" value="{{ old('base_url') }}" placeholder="https://api.example.com" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label class="font-weight-bold">Auth Type <span class="text-danger">*</span></label>
                            <select name="auth_type" class="form-control">
                                <option value="none" {{ old('auth_type') == 'none' ? 'selected' : '' }}>None</option>
                                <option value="basic" {{ old('auth_type') == 'basic' ? 'selected' : '' }}>Basic Auth</option>
                                <option value="bearer" {{ old('auth_type') == 'bearer' ? 'selected' : '' }}>Bearer Token</option>
                                <option value="api_key" {{ old('auth_type') == 'api_key' ? 'selected' : '' }}>API Key</option>
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label class="font-weight-bold">Auth Username</label>
                            <input type="text" name="auth_username" class="form-control" value="{{ old('auth_username') }}">
                        </div>
                        <div class="form-group col-md-4">
                            <label class="font-weight-bold">Auth Password</label>
                            <input type="password" name="auth_password" class="form-control" value="{{ old('auth_password') }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Auth Token</label>
                        <input type="text" name="auth_token" class="form-control" value="{{ old('auth_token') }}">
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label class="font-weight-bold">Request Method</label>
                            <select name="request_method" class="form-control">
                                <option value="GET">GET</option>
                                <option value="POST">POST</option>
                                <option value="PUT">PUT</option>
                                <option value="DELETE">DELETE</option>
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label class="font-weight-bold">Body Format</label>
                            <input type="text" name="request_body_format" class="form-control" value="{{ old('request_body_format', 'JSON') }}" placeholder="JSON">
                        </div>
                        <div class="form-group col-md-4">
                            <label class="font-weight-bold">Endpoint</label>
                            <input type="text" name="endpoint" class="form-control" value="{{ old('endpoint') }}" placeholder="/v1/records">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold">Header Key</label>
                            <input type="text" name="header_key" class="form-control" value="{{ old('header_key') }}" placeholder="X-API-KEY">
                        </div>
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold">Header Value</label>
                            <input type="text" name="header_value" class="form-control" value="{{ old('header_value') }}">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold">Param Key</label>
                            <input type="text" name="param_key" class="form-control" value="{{ old('param_key') }}">
                        </div>
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold">Param Value</label>
                            <input type="text" name="param_value" class="form-control" value="{{ old('param_value') }}">
                        </div>
                    </div>

                    <div class="text-right mt-4">
                        <a href="{{ route('api-configurations.index') }}" class="btn btn-secondary mr-2">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4 shadow-sm"><i class="fas fa-save mr-1"></i> Save Configuration</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
