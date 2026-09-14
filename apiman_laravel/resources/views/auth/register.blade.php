@extends('layouts.app')

@section('content')
<div class="row justify-content-center mt-4">
    <div class="col-md-6">
        <div class="card shadow">
            <div class="card-header bg-dark text-white text-center py-4">
                <h4 class="mb-0"><i class="fas fa-user-plus text-info mr-2"></i> Register New User</h4>
                <small class="text-muted">Create an account to access Cirrus API platform</small>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('register.post') }}">
                    @csrf

                    <div class="form-group mb-3">
                        <label for="username" class="font-weight-bold">Username <span class="text-danger">*</span></label>
                        <input type="text" id="username" name="username" class="form-control" value="{{ old('username') }}" placeholder="Enter unique username" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="full_name" class="font-weight-bold">Full Name</label>
                        <input type="text" id="full_name" name="full_name" class="form-control" value="{{ old('full_name') }}" placeholder="Enter full name">
                    </div>

                    <div class="form-group mb-3">
                        <label for="email" class="font-weight-bold">Email Address</label>
                        <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="Enter email address">
                    </div>

                    <div class="form-group mb-4">
                        <label for="password" class="font-weight-bold">Password <span class="text-danger">*</span></label>
                        <input type="password" id="password" name="password" class="form-control" placeholder="Choose a password" required>
                    </div>

                    <button type="submit" class="btn btn-success btn-block btn-lg shadow-sm">
                        <i class="fas fa-check-circle mr-2"></i> Complete Registration
                    </button>
                </form>
            </div>
            <div class="card-footer text-center bg-light py-3">
                <p class="mb-0 text-muted">Already have an account? <a href="{{ route('login') }}" class="font-weight-bold">Sign In here</a></p>
            </div>
        </div>
    </div>
</div>
@endsection
