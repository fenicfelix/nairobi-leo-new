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
                    <h1 class="fw-black mt-3 mt-3">Register Account</h1>
                    <form class="needs-validation" action="{{ route('register') }}" method="POST" novalidate>
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-bold" for="InputName">{{__('Full Name')}}</label>
                            <input type="text" class="form-control" id="InputName" name="name" placeholder="Full name" autofocus required>
                            <div class="invalid-feedback">Full Name</div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold" for="InputPhone">{{__('Phone Number')}}</label>
                            <input type="text" class="form-control" id="InputPhone" name="phone_number" placeholder="Phone number" required>
                            <div class="invalid-feedback">Phone Number</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold" for="InputEmail">{{__('Email address')}}</label>
                            <input type="email" class="form-control" id="InputEmail" name="email" placeholder="Enter email address" required>
                            <div class="invalid-feedback">Enter email.</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold" for="InputPassword">{{__('Password')}}</label>
                            <input type="password" class="form-control" id="InputPassword" name="password" placeholder="Enter your password" required>
                            <div class="invalid-feedback">Enter a password.</div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold" for="InputPassword">{{__('Confirm Password')}}</label>
                            <input type="password" class="form-control" id="InputPassword" name="password_confirmation" placeholder="Enter your password again" required>
                            <div class="invalid-feedback">Confirm Password</div>
                        </div>
                        <button type="submit" class="btn btn-danger w-100">Register</button>
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
{{-- <x-guest-layout>
    <x-auth-card>
        <x-slot name="logo">
            <a href="/">
                <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
            </a>
        </x-slot>

        <!-- Validation Errors -->
        <x-auth-validation-errors class="mb-4" :errors="$errors" />

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <!-- Name -->
            <div>
                <x-label for="name" :value="__('Name')" />

                <x-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
            </div>

            <!-- Email Address -->
            <div class="mt-4">
                <x-label for="email" :value="__('Email')" />

                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <x-label for="password" :value="__('Password')" />

                <x-input id="password" class="block mt-1 w-full"
                                type="password"
                                name="password"
                                required autocomplete="new-password" />
            </div>

            <!-- Confirm Password -->
            <div class="mt-4">
                <x-label for="password_confirmation" :value="__('Confirm Password')" />

                <x-input id="password_confirmation" class="block mt-1 w-full"
                                type="password"
                                name="password_confirmation" required />
            </div>

            <div class="flex items-center justify-end mt-4">
                <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('login') }}">
                    {{ __('Already registered?') }}
                </a>

                <x-button class="ml-4">
                    {{ __('Register') }}
                </x-button>
            </div>
        </form>
    </x-auth-card>
</x-guest-layout> --}}
