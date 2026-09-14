@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-9">
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <h4 class="mb-0 font-weight-bold text-primary"><i class="fas fa-building mr-2"></i> Company Registration</h4>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('company.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold">Company Legal Name <span class="text-danger">*</span></label>
                            <input type="text" name="company_legal_name" class="form-control" value="{{ old('company_legal_name') }}" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold">Company Registration No <span class="text-danger">*</span></label>
                            <input type="text" name="company_reg_no" class="form-control" value="{{ old('company_reg_no') }}" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold">Incorporation Date</label>
                            <input type="date" name="incorporation_date" class="form-control" value="{{ old('incorporation_date') }}">
                        </div>
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold">Financial Year</label>
                            <input type="text" name="financial_year" class="form-control" value="{{ old('financial_year') }}" placeholder="e.g. 2025/2026">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold">TIN No</label>
                            <input type="text" name="tin_no" class="form-control" value="{{ old('tin_no') }}">
                        </div>
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold">VAT / SVAT No</label>
                            <input type="text" name="vat_svat_no" class="form-control" value="{{ old('vat_svat_no') }}">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label class="font-weight-bold">NBT Reg No</label>
                            <input type="text" name="nbt_reg_no" class="form-control" value="{{ old('nbt_reg_no') }}">
                        </div>
                        <div class="form-group col-md-4">
                            <label class="font-weight-bold">EPF / ETF Reg No</label>
                            <input type="text" name="epf_etf_reg_no" class="form-control" value="{{ old('epf_etf_reg_no') }}">
                        </div>
                        <div class="form-group col-md-4">
                            <label class="font-weight-bold">PAYE Tax No</label>
                            <input type="text" name="payee_tax_no" class="form-control" value="{{ old('payee_tax_no') }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Registered Address</label>
                        <textarea name="address" class="form-control" rows="3">{{ old('address') }}</textarea>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Company Logo / Image</label>
                        <input type="file" name="image" class="form-control-file">
                    </div>

                    <div class="text-right mt-4">
                        <button type="submit" class="btn btn-primary px-4 shadow-sm"><i class="fas fa-save mr-1"></i> Register Company</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
