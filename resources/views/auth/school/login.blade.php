@extends('layouts.app')

@section('content')
    <div class="card fat">
        <div class="card-body">
            <h4 class="card-title">Login Schools</h4>
            <form method="POST" action="{{ route('center.login') }}">
                @csrf
                <div class="form-group">
                    <label for="email">Centre No</label>
                    <input id="username" type="text"
                        class="form-control rounded-left @error('username') is-invalid @enderror
                @error('email') is-invalid @enderror"
                        name="username" value="{{ old('username') }}" placeholder="Center No or Email"
                        autocomplete="username" autofocus>
                    @error('username')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror

                </div>
                <div class="form-group">
                    <label for="password">Password
                        <a href=" " class="float-right">
                            Forgot Password?
                        </a>
                    </label>
                    <input id="password" placeholder="Enter password" type="password" class="form-control" name="password"
                        data-eye>

                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                    <div class="invalid-feedback">
                        Password is required
                    </div>
                </div>

                <div class="form-group">

                    <div class="custom-checkbox custom-control">
                        <input type="checkbox" name="remember" id="remember" class="custom-control-input"
                            {{ old('remember') ? 'checked' : '' }}>
                        <label for="remember" class="custom-control-label">Remember Me</label>
                    </div>




                </div>

                <div class="container-login100-form-btn">
                    <button type="submit" class="btn btn-primary btn-block">
                        Login
                    </button>

                    <a class="back float-right mt-2" href="/"><i class='bx bx-arrow-back'></i>Back to
                        home</a>
                </div>
            </form>
        </div>
    </div>
@endsection
