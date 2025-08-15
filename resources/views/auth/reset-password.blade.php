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
                    <h1 class="fw-black mt-3 mt-3">Reset Password</h1>
                    <form class="needs-validation" action="{{ route('password.update') }}" method="POST" novalidate>
                        @csrf
                        <!-- Password Reset Token -->
                        <input type="hidden" name="token" value="{{ $request->route('token') }}">
                        <div class="mb-3">
                            <label class="form-label fw-bold" for="email">{{__('Email address')}}</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{old('email', $request->email)}}" placeholder="yourname@yourmail.com" autocomplete="off" autofocus required>
                            <div class="invalid-feedback">Enter email.</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold" for="password">{{__('Password')}}</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                            <div class="invalid-feedback">Password.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold" for="password_confirmation">{{__('Password')}}</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Confirm your password" required>
                            <div class="invalid-feedback">Confirm Password.</div>
                        </div>
                        <button type="submit" class="btn btn-danger w-100">Reset Password</button>
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

    <script>
        // Example starter JavaScript for disabling form submissions if there are invalid fields
        void(function() {
        document.querySelectorAll('.needs-validation').forEach(form => {
            form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault()
                event.stopPropagation()
            }

            form.classList.add('was-validated')
            })
        })
        })()
    </script>
    
@endsection
