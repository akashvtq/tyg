@extends('frontend.layouts.user.auth')
@section('title')
    {{ __('Forgot Password') }}
@endsection
@section('user-auth-content')
    @if (session('status'))
    <div class="alert alert-success">
        {{ session('status') }}
    </div>
    @endif
    
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    <form method="POST" action="{{ route('password.email') }}" class="mt-4">
        @csrf
        <div class="form-group mb-4">
            <label for="email" class="form-label text-white">{{ __('Your Email') }}</label>
            <div class="input-group">
                                        <span class="input-group-text" id="basic-addon1">
                                            <x-svg i="mail"/>
                                        </span>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="Email" id="email" autofocus required>
            </div>
        </div>
        <div class="d-grid">
            <button type="submit" class="btn btn-primary"> <x-svg i="login"/> {{ __('Email Password Reset Link') }}</button>
        </div>
    </form>
@endsection
