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
                    <h1 class="fw-black mt-3 mt-3">Forgot Password?</h1>
                    <p class="fw-light text-secondary mb-4">Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.</p>
                    <form class="needs-validation" action="{{ route('password.email') }}" method="POST" novalidate>
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-bold" for="InputEmail">{{__('Email address')}}</label>
                            <input type="email" class="form-control" id="InputEmail" name="email" placeholder="yourname@yourmail.com" autocomplete="off" autofocus required>
                            <div class="invalid-feedback">Enter email</div>
                        </div>
                        <button type="submit" class="btn btn-danger w-100">Sign in</button>
                        <a class="btn btn-light btn-sm d-inline-flex align-items-center gap-2 mt-3" href="{{ route('login') }}">
                            <svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" fill="none" viewbox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                            Back to Login
                        </a>
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