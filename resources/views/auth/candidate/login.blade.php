@extends('layouts.candidatelogin')
@section('content')
    <div class="row w-100 mx-0">
        <div class="col-lg-4 mx-auto">
            <div class="auth-form-light text-left py-4 px-2 px-sm-5">
                <h4>Candidate</h4>
                <form class="pt-2" method="POST" action="{{ route('candidate.login') }}">
                    @csrf
                    <div class="form-group">
                        <input type="text" name="national_id" value="{{ old('national_id') }}"   class="form-control form-control-sm  @error('national_id') is-invalid @enderror"
                            placeholder="National ID">
                        @error('national_id')
                            <span class="invalid-feedback" role="alert">
                                  {{ $message }}
                            </span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <input type="password"  name="password" class="form-control form-control-sm @error('password') is-invalid @enderror" placeholder="Password">
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-block btn-primary btn-sm font-weight-medium auth-form-btn">
                            Login
                        </button>
                    </div>
                    <div class="my-2 d-flex justify-content-between align-items-center">
                        <div class="form-check">
                            <label class="form-check-label text-muted">
                                <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}
                                    class="form-check-input">
                                Keep me signed in
                            </label>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
