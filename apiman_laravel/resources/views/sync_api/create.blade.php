@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <h4 class="mb-0 font-weight-bold text-primary"><i class="fas fa-plus-circle mr-2"></i> Create New Sync API</h4>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('sync-api.store') }}">
                    @csrf

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold">API Name <span class="text-danger">*</span></label>
                            <input type="text" name="api_name" class="form-control" value="{{ old('api_name') }}" placeholder="e.g. sync_invoices" required>
                        </div>

                        <div class="form-group col-md-6">
                            <label class="font-weight-bold">Category</label>
                            <div class="input-group">
                                <select name="category_id" id="category_id" class="form-control selectpicker" data-live-search="true">
                                    <option value="">-- Select Category --</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->description }}</option>
                                    @endforeach
                                </select>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" data-toggle="modal" data-target="#createCategoryModal">
                                        <i class="fas fa-plus"></i> New
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold">From Database Connection</label>
                            <input type="text" name="from_db" class="form-control" value="{{ old('from_db', 'mysql_pms') }}" placeholder="e.g. mysql_pms">
                            <small class="form-text text-muted">Configured connection name from config/database.php</small>
                        </div>

                        <div class="form-group col-md-6">
                            <label class="font-weight-bold">To Database Connection</label>
                            <input type="text" name="to_db" class="form-control" value="{{ old('to_db', 'mysql_finance') }}" placeholder="e.g. mysql_finance (leave blank for DB to JSON)">
                            <small class="form-text text-muted">Leave empty if exporting DB to JSON</small>
                        </div>
                    </div>

                    <hr class="my-4">
                    <h5 class="text-secondary font-weight-bold mb-3"><i class="fas fa-database mr-1"></i> Data Insertion Pipeline</h5>

                    <div class="form-group">
                        <label class="font-weight-bold">From Query (Added Records)</label>
                        <textarea class="form-control font-monospace" name="from_added_query" rows="3" placeholder="SELECT * FROM table WHERE created_at > @{{AddedDate}}">{{ old('from_added_query') }}</textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold">JSON Prefix</label>
                            <textarea class="form-control font-monospace" name="Prefix" rows="2" placeholder='{"data": [ '>{{ old('Prefix') }}</textarea>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold">JSON Suffix</label>
                            <textarea class="form-control font-monospace" name="Suffix" rows="2" placeholder=' ]}'>{{ old('Suffix') }}</textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">To Query (Insert Query Template)</label>
                        <textarea class="form-control font-monospace" name="to_added_query" rows="3" placeholder="INSERT INTO target_table (col1, col2) VALUES (@{{col1}}, @{{col2}})">{{ old('to_added_query') }}</textarea>
                    </div>

                    <hr class="my-4">
                    <h5 class="text-secondary font-weight-bold mb-3"><i class="fas fa-sync-alt mr-1"></i> Data Update & Delete Pipeline</h5>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold">From Query (Updated Records)</label>
                            <textarea class="form-control font-monospace" name="from_Updated_query" rows="3">{{ old('from_Updated_query') }}</textarea>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold">To Query (Update Records)</label>
                            <textarea class="form-control font-monospace" name="to_Updated_query" rows="3">{{ old('to_Updated_query') }}</textarea>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold">From Query (Deleted Records)</label>
                            <textarea class="form-control font-monospace" name="from_delete_query" rows="3">{{ old('from_delete_query') }}</textarea>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold">To Query (Delete Records)</label>
                            <textarea class="form-control font-monospace" name="to_delete_query" rows="3">{{ old('to_delete_query') }}</textarea>
                        </div>
                    </div>

                    <hr class="my-4">
                    <h5 class="text-secondary font-weight-bold mb-3"><i class="fas fa-clock mr-1"></i> Sync Status Check Queries</h5>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold">Last Added Time Query</label>
                            <input type="text" name="last_added_time_query" class="form-control font-monospace" value="{{ old('last_added_time_query') }}" placeholder="SELECT MAX(created_at) FROM source_table">
                        </div>
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold">Last Updated Time Query</label>
                            <input type="text" name="last_updated_time_query" class="form-control font-monospace" value="{{ old('last_updated_time_query') }}" placeholder="SELECT MAX(updated_at) FROM source_table">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold">Last Deleted Time Query</label>
                            <input type="text" name="last_deleted_time_query" class="form-control font-monospace" value="{{ old('last_deleted_time_query') }}">
                        </div>
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold">Total Count Query</label>
                            <input type="text" name="total_count_query" class="form-control font-monospace" value="{{ old('total_count_query') }}" placeholder="SELECT COUNT(*) FROM source_table">
                        </div>
                    </div>

                    <div class="form-row mt-3">
                        <div class="form-group col-md-6">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="wait_till_confirmation" name="wait_till_confirmation" value="1" {{ old('wait_till_confirmation') ? 'checked' : '' }}>
                                <label class="custom-control-label font-weight-bold" for="wait_till_confirmation">Wait Till Confirmation</label>
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="status_to_aync_api_detail" name="status_to_aync_api_detail" value="0" {{ old('status_to_aync_api_detail') === '0' ? 'checked' : '' }}>
                                <label class="custom-control-label font-weight-bold" for="status_to_aync_api_detail">Disable sync logging to details table</label>
                            </div>
                        </div>
                    </div>

                    <div class="text-right mt-4">
                        <a href="{{ route('sync-api.index') }}" class="btn btn-secondary mr-2"><i class="fas fa-times mr-1"></i> Cancel</a>
                        <button type="submit" class="btn btn-primary px-4 shadow-sm"><i class="fas fa-save mr-1"></i> Save Configuration</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Create Category -->
<div class="modal fade" id="createCategoryModal" tabindex="-1" role="dialog" aria-labelledby="createCategoryLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createCategoryLabel"><i class="fas fa-tag mr-2"></i> Create Category</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="createCategoryForm" action="{{ route('sync-api.category') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="category_description" class="font-weight-bold">Category Description</label>
                        <input type="text" class="form-control" id="category_description" name="description" placeholder="e.g. Finance, Logistics, HR" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Category</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $('#createCategoryForm').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: form.serialize(),
            success: function(response) {
                if (response.status) {
                    $('#category_id').append('<option value="' + response.id + '" selected>' + response.description + '</option>');
                    $('#category_id').selectpicker('refresh');
                    $('#createCategoryModal').modal('hide');
                    form[0].reset();
                }
            },
            error: function() {
                alert('Error creating category');
            }
        });
    });
</script>
@endpush
