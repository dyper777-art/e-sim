@extends('frontend.auth.layout')

@section('content')

<form method="POST" action="{{ route('password.update') }}" class="login100-form validate-form">
    @csrf
    <span class="login100-form-title">
        Reset Password
    </span>

    <div class="wrap-input100 validate-input" data-validate="Valid email is required: ex@abc.xyz">
        <input class="input100" type="email" name="email" placeholder="Email" value="{{ old('email') }}" required>
        @error('email') <span class="text-danger">{{ $message }}</span> @enderror
        <span class="focus-input100"></span>
        <span class="symbol-input100">
            <i class="fa fa-envelope" aria-hidden="true"></i>
        </span>
    </div>

    <div class="wrap-input100 validate-input" data-validate="Password is required">
        <input class="input100" type="password" name="password" placeholder="New Password" required>
        @error('password') <span class="text-danger">{{ $message }}</span> @enderror
        <span class="focus-input100"></span>
        <span class="symbol-input100">
            <i class="fa fa-lock" aria-hidden="true"></i>
        </span>
    </div>

    <div class="wrap-input100 validate-input" data-validate="Confirm Password is required">
        <input class="input100" type="password" name="password_confirmation" placeholder="Confirm New Password" required>
        <span class="focus-input100"></span>
        <span class="symbol-input100">
            <i class="fa fa-lock" aria-hidden="true"></i>
        </span>
    </div>

    <div class="container-login100-form-btn">
        <button class="login100-form-btn">Reset Password</button>
    </div>

    <div class="text-center p-t-136">
        <a class="txt2" href="{{ route('login') }}">
            <i class="fa fa-long-arrow-left m-l-5" aria-hidden="true"></i>
            Back to Login
        </a>
    </div>
</form>


@endsection
