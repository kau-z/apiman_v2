@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <h4 class="mb-0 font-weight-bold text-primary"><i class="fas fa-edit mr-2"></i> Update Sync API Configuration</h4>
            </div>
            <div class="card-body p-4 text-center">
                <div class="form-group my-4">
                    <label class="font-weight-bold d-block text-secondary mb-3">Select API Configuration to Edit</label>
                    <select id="apiSelector" class="form-control selectpicker show-tick" data-live-search="true" data-size="8">
                        <option value="">-- Select an API --</option>
                        @foreach($apiNames as $api)
                            <option value="{{ $api->main_id }}">{{ $api->api_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mt-4">
                    <button type="button" id="btnEdit" class="btn btn-primary px-5 py-2 shadow-sm font-weight-bold">
                        <i class="fas fa-pen mr-2"></i> Edit Selected API
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $('#btnEdit').on('click', function() {
        var id = $('#apiSelector').val();
        if (!id) {
            alert('Please select an API to edit');
            return;
        }
        window.location.href = "{{ url('/sync-api') }}/" + id + "/edit";
    });
</script>
@endpush
