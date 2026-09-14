@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h4 class="mb-0 font-weight-bold text-primary"><i class="fas fa-edit mr-2"></i> Edit Sync API Configuration</h4>
                <a href="{{ route('sync-api.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left mr-1"></i> Back</a>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('sync-api.update') }}">
                    @csrf
                    <input type="hidden" name="mainId" value="{{ $syncApi->main_id ?? '' }}">

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold">API Name <span class="text-danger">*</span></label>
                            <input type="text" name="api_name" class="form-control" value="{{ old('api_name', $syncApi->api_name ?? '') }}" required>
                        </div>

                        <div class="form-group col-md-6">
                            <label class="font-weight-bold">Category</label>
                            <select name="category_id" class="form-control selectpicker" data-live-search="true">
                                <option value="">-- Select Category --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ (old('category_id', $syncApi->category_id ?? '') == $cat->id) ? 'selected' : '' }}>{{ $cat->description }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold">From Database Connection</label>
                            <input type="text" name="from_db" class="form-control" value="{{ old('from_db', $syncApi->from_db ?? '') }}">
                        </div>

                        <div class="form-group col-md-6">
                            <label class="font-weight-bold">To Database Connection</label>
                            <input type="text" name="to_db" class="form-control" value="{{ old('to_db', $syncApi->to_db ?? '') }}">
                        </div>
                    </div>

                    <hr class="my-4">
                    <h5 class="text-secondary font-weight-bold mb-3"><i class="fas fa-database mr-1"></i> Insertion Pipeline</h5>

                    <div class="form-group">
                        <label class="font-weight-bold">From Query (Added Records)</label>
                        <textarea class="form-control font-monospace" name="from_added_query" rows="3">{{ old('from_added_query', $syncApi->from_added_query ?? '') }}</textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold">JSON Prefix</label>
                            <textarea class="form-control font-monospace" name="Prefix" rows="2">{{ old('Prefix', $syncApi->Prefix ?? '') }}</textarea>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold">JSON Suffix</label>
                            <textarea class="form-control font-monospace" name="Suffix" rows="2">{{ old('Suffix', $syncApi->Suffix ?? '') }}</textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">To Query (Insert Template)</label>
                        <textarea class="form-control font-monospace" name="to_added_query" rows="3">{{ old('to_added_query', $syncApi->to_added_query ?? '') }}</textarea>
                    </div>

                    <hr class="my-4">
                    <h5 class="text-secondary font-weight-bold mb-3"><i class="fas fa-sync-alt mr-1"></i> Update & Delete Pipeline</h5>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold">From Query (Updated Records)</label>
                            <textarea class="form-control font-monospace" name="from_Updated_query" rows="3">{{ old('from_Updated_query', $syncApi->from_Updated_query ?? '') }}</textarea>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold">To Query (Update Records)</label>
                            <textarea class="form-control font-monospace" name="to_Updated_query" rows="3">{{ old('to_Updated_query', $syncApi->to_Updated_query ?? '') }}</textarea>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold">From Query (Deleted Records)</label>
                            <textarea class="form-control font-monospace" name="from_delete_query" rows="3">{{ old('from_delete_query', $syncApi->from_delete_query ?? '') }}</textarea>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold">To Query (Delete Records)</label>
                            <textarea class="form-control font-monospace" name="to_delete_query" rows="3">{{ old('to_delete_query', $syncApi->to_delete_query ?? '') }}</textarea>
                        </div>
                    </div>

                    <hr class="my-4">
                    <h5 class="text-secondary font-weight-bold mb-3"><i class="fas fa-clock mr-1"></i> Status Queries</h5>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold">Last Added Time Query</label>
                            <input type="text" name="last_added_time_query" class="form-control font-monospace" value="{{ old('last_added_time_query', $syncApi->last_added_time_query ?? '') }}">
                        </div>
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold">Last Updated Time Query</label>
                            <input type="text" name="last_updated_time_query" class="form-control font-monospace" value="{{ old('last_updated_time_query', $syncApi->last_updated_time_query ?? '') }}">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold">Last Deleted Time Query</label>
                            <input type="text" name="last_deleted_time_query" class="form-control font-monospace" value="{{ old('last_deleted_time_query', $syncApi->last_deleted_time_query ?? '') }}">
                        </div>
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold">Total Count Query</label>
                            <input type="text" name="total_count_query" class="form-control font-monospace" value="{{ old('total_count_query', $syncApi->total_count_query ?? '') }}">
                        </div>
                    </div>

                    <div class="form-row mt-3">
                        <div class="form-group col-md-6">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="wait_till_confirmation" name="wait_till_confirmation" value="1" {{ old('wait_till_confirmation', $syncApi->wait_till_confirmation ?? 0) ? 'checked' : '' }}>
                                <label class="custom-control-label font-weight-bold" for="wait_till_confirmation">Wait Till Confirmation</label>
                            </div>
                        </div>
                    </div>

                    <div class="text-right mt-4">
                        <a href="{{ route('sync-api.index') }}" class="btn btn-secondary mr-2"><i class="fas fa-times mr-1"></i> Cancel</a>
                        <button type="submit" class="btn btn-primary px-4 shadow-sm"><i class="fas fa-save mr-1"></i> Update Configuration</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
