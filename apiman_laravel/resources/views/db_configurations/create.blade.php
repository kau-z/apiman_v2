@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h4 class="mb-0 font-weight-bold text-primary"><i class="fas fa-plus-circle mr-2"></i> Create Database Configuration</h4>
                <a href="{{ route('db-configurations.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left mr-1"></i> Back</a>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('db-configurations.store') }}">
                    @csrf

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold">Active Group <span class="text-danger">*</span></label>
                            <input type="text" name="active_group" class="form-control" value="{{ old('active_group') }}" placeholder="e.g. mysql_pms" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold">Active Record <span class="text-danger">*</span></label>
                            <input type="text" name="active_record" class="form-control" value="{{ old('active_record', 'TRUE') }}" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold">Hostname <span class="text-danger">*</span></label>
                            <input type="text" name="hostname" class="form-control" value="{{ old('hostname', 'localhost') }}" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold">Database Name <span class="text-danger">*</span></label>
                            <input type="text" name="database" class="form-control" value="{{ old('database') }}" placeholder="e.g. pms_demo" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold">Username <span class="text-danger">*</span></label>
                            <input type="text" name="username" class="form-control" value="{{ old('username', 'root') }}" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold">Password</label>
                            <input type="password" name="password" class="form-control" value="{{ old('password') }}">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label class="font-weight-bold">Port</label>
                            <input type="text" name="port" class="form-control" value="{{ old('port', '3306') }}">
                        </div>
                        <div class="form-group col-md-4">
                            <label class="font-weight-bold">DB Driver <span class="text-danger">*</span></label>
                            <input type="text" name="dbdriver" class="form-control" value="{{ old('dbdriver', 'mysqli') }}" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label class="font-weight-bold">DB Prefix</label>
                            <input type="text" name="dbprefix" class="form-control" value="{{ old('dbprefix') }}">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label class="font-weight-bold">Persistent Connection <span class="text-danger">*</span></label>
                            <select name="pconnect" class="form-control">
                                <option value="FALSE">FALSE</option>
                                <option value="TRUE">TRUE</option>
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label class="font-weight-bold">DB Debug <span class="text-danger">*</span></label>
                            <select name="db_debug" class="form-control">
                                <option value="TRUE">TRUE</option>
                                <option value="FALSE">FALSE</option>
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label class="font-weight-bold">Cache On <span class="text-danger">*</span></label>
                            <select name="cache_on" class="form-control">
                                <option value="FALSE">FALSE</option>
                                <option value="TRUE">TRUE</option>
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label class="font-weight-bold">Strict Mode <span class="text-danger">*</span></label>
                            <select name="stricton" class="form-control">
                                <option value="FALSE">FALSE</option>
                                <option value="TRUE">TRUE</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label class="font-weight-bold">Character Set <span class="text-danger">*</span></label>
                            <input type="text" name="char_set" class="form-control" value="{{ old('char_set', 'utf8') }}" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label class="font-weight-bold">Collation <span class="text-danger">*</span></label>
                            <input type="text" name="dbcollat" class="form-control" value="{{ old('dbcollat', 'utf8_general_ci') }}" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label class="font-weight-bold">Auto Init <span class="text-danger">*</span></label>
                            <input type="text" name="autoinit" class="form-control" value="{{ old('autoinit', 'TRUE') }}" required>
                        </div>
                    </div>

                    <div class="text-right mt-4">
                        <a href="{{ route('db-configurations.index') }}" class="btn btn-secondary mr-2">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4 shadow-sm"><i class="fas fa-save mr-1"></i> Save Configuration</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
