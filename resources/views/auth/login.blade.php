@extends('layouts.auth')

@section('styles')
    
@endsection

@section('content')

    <div class="">
        <div class="login">
            <div class="row">
                <div class="col-12 text-center">
                    <img class="" src="{{ asset(get_template_url().'img/'.config('cms.app_icon_dark')) }}" alt="App Logo" height="60">
                </div>
            </div>
            <div class="login-area mt-4">
                <div class="card-body">
                    <h1 class="fw-black mt-3 mt-3">Sign In</h1>
                    <p class="fw-light text-secondary mb-4">Welcome back! Please sign in to continue.</p>
                    <form class="needs-validation" action="{{ route('custom_login') }}" method="POST" novalidate>
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-bold" for="InputEmail">{{__('Email address or Username')}}</label>
                            <input type="text" class="form-control" id="InputEmail" name="username" placeholder="yourname@yourmail.com" autocomplete="off" autofocus required>
                            <div class="invalid-feedback">Enter email or username.</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold" for="InputPassword">{{__('Password')}}</label>
                            <input type="password" class="form-control" id="InputPassword" name="password" placeholder="Enter your password" required>
                            <div class="invalid-feedback">Enter a password.</div>
                        </div>
                        <div class="mb-3 d-flex justify-content-between align-items-center">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="remember">
                                <label class="form-check-label" for="remember">Remember me</label>
                            </div>
                            <a href="{{ route('password.request') }}" class="link-danger small text-decoration-none">Forgot password ?</a>
                        </div>
                        <button type="submit" class="btn btn-danger w-100">Sign in</button>
                    </form>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="copyright mt-2">
                        <center>{!! get_widget('backend-byline') !!}</center>
                    </div>
                </div>
            </div>
            
        </div>
    </div>

@endsection

@section('scripts')
    
@endsection