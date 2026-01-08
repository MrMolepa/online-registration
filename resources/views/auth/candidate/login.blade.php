@extends('layouts.candidatelogin')
@section('content')
    <div class="row w-100 mx-0">
        <div class="col-12 d-flex justify-content-center">
            <div class="auth-split-card">
                <div class="auth-left p-4">
                    <div class="auth-card text-left py-2 px-2 px-sm-4">
                        <div class="auth-brand text-center">
                            @php
                                $png = public_path('candidates/images/logo.png');
                                $svg = asset('candidates/images/logo.svg');
                            @endphp
                            <img src="{{ file_exists($png) ? asset('candidates/images/logo.png') : $svg }}" alt="ECoL logo" class="auth-logo">
                        </div>
                        <h4 class="auth-title text-center">Candidate Login</h4>
                        <form class="pt-2" method="POST" action="{{ route('candidate.login') }}">
                    @csrf
                    <div class="form-group">
                        <label for="national_id" class="form-label">National ID</label>
                        <input id="national_id" type="text" name="national_id" value="{{ old('national_id') }}" class="form-control form-control-sm @error('national_id') is-invalid @enderror" placeholder="National ID">
                        @error('national_id')
                            <span class="invalid-feedback" role="alert">
                                  {{ $message }}
                            </span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <input id="password" type="password" name="password" class="form-control form-control-sm @error('password') is-invalid @enderror" placeholder="Password">
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
                <div class="auth-right d-none d-md-flex" style="background-image: url('{{ asset('candidates/images/students-across-florida-are-set-to-resume-SUNUCTHRCFBDFDXQALXPTPA3ME.avif') }}');">
                    <div class="auth-right-inner">
                        <h1 class="welcome-title">Welcome to the Candidate Portal</h1>
                        <p class="welcome-sub">Login to access your examination details.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="disclaimer-overlay">
        <div class="disclaimer">
            <h2 class="disclaimer-title">Terms and Conditions </h2>
            <div class="disclaimer-message">
                <h5>By engaging with ECoL regarding your examinations, you agree to the terms outlined below:</h5>
                <ol>
                    <li>Introduction
                        <ul>
                            <p>The Examinations Council of Lesotho (ECoL) is committed to protecting your personal
                                information.</p>
                        </ul>
                    </li>
                    <li>
                        Collection and Use of Personal Information
                        <p>ECoL collects and processes your personal information solely for the purposes of
                            administering,
                            processing, and calculating your examination results. This may include, but is not limited
                            to.</p>
                        <ul>
                            <li>Your name, surname, and identification details.</li>
                            <li>Examination registration details.</li>
                            <li>Examination scores and performance data.</li>
                        </ul>
                    </li>


                    <li>
                        Sharing of Personal Information
                        <p>Your personal information will only be shared with third parties who are.</p>
                        <ul>
                            <li>Formal ECoL partners.</li>
                            <li>Drectly involved in the processing and calculation of examination results.</li>
                            <li>Bound by confidentiality and data protection agreements</li>
                            <p>ECoL will not sell, distribute, or disclose your personal information to unauthorized
                                third parties</p>
                        </ul>
                    </li>


                    <li>
                        Data Protection, Security and Compliance
                        <p>CoL takes reasonable technical and administrative measures to protect your personal
                            information against unauthorized access, loss, or misuse.
                            ECoL adheres to internationally recognized security and quality standards, including:</p>
                        <ul>
                            <li>ISO/IEC 27001:2013 – Information Security Management System (ISMS) certification.</li>
                            <li>ISO 9001: 2015 – Quality Management System (QMS) certification.</li>
                            <li>Bound by confidentiality and data protection agreements</li>
                            <p>These certifications ensure that your personal data is managed securely and processed
                                with high-quality standards.</p>
                        </ul>
                    </li>


                    <li>
                        Your Rights
                        <p>You have the right to:</p>
                        <ul>
                            <li>Request access to your personal information.</li>
                            <li>Request correction of inaccurate information.</li>
                            <li>Object to the processing of your data under certain circumstances.</li>
                            <li>Decline and not register for examinations.</li>
                        </ul>
                    </li>
                    <li>
                        Retention of Personal Information
                        <p>Your personal information will be retained for as long as necessary to fulfill the purposes outlined in this document and in accordance with applicable laws.</p>

                    </li>

                    <li>
                        Contact Information
                        <p>For any inquiries or concerns regarding the processing of your personal information, please contact ECoL through official communication channels.</p>

                    </li>
                </ol>
            </div>
            <div class="disclaimer-buttons">
                <button class="agree-btn" disabled>I Agree</button>
                <button class="decline-btn">Decline</button>
            </div>
            <div class="decline-message">
                Access to this website has been denied. Thank you for understanding.
            </div>
        </div>
    </div>


@push('scripts')
<script>
    // Disclaimer
    $(document).on("click", ".agree-btn", function() {
            $('.disclaimer-overlay').css({
                "display": "none"
            });
        });

        $(document).on("click", ".decline-btn", function() {
            location.href = "/";
        });

        $('.disclaimer-message').scroll(function() {
            if ($(this).scrollTop() + $(this).height() >= $(this)[0].scrollHeight - 100) {
                $('.agree-btn').attr('disabled', null);
            }
        });

        // floating label removed: no extra JS required
  </script>

@endpush

@endsection
