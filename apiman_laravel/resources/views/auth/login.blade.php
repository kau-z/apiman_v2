@extends('layouts.app')

@section('content')
<div class="row justify-content-center mt-5">
    <div class="col-md-5">
        <div class="card shadow">
            <div class="card-header bg-dark text-white text-center py-4">
                <h4 class="mb-0"><i class="fas fa-network-wired text-info mr-2"></i> Cirrus API</h4>
                <small class="text-muted">Sign in to manage your APIs and Databases</small>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('login.post') }}">
                    @csrf

                    <div class="form-group mb-3">
                        <label for="username" class="font-weight-bold">Username</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                            </div>
                            <input type="text" id="username" name="username" class="form-control" value="{{ old('username') }}" placeholder="Enter username" required autofocus>
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label for="password" class="font-weight-bold">Password</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            </div>
                            <input type="password" id="password" name="password" class="form-control" placeholder="Enter password" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block btn-lg shadow-sm">
                        <i class="fas fa-sign-in-alt mr-2"></i> Sign In to Dashboard
                    </button>
                </form>
            </div>
            <div class="card-footer text-center bg-light py-3">
                <p class="mb-0 text-muted">Don't have an account? <a href="{{ route('register') }}" class="font-weight-bold">Register here</a></p>
            </div>
        </div>
    </div>
</div>
@endsection
