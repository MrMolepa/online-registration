@extends('layouts.admin')
@section('content')

    <div class="main">
        <!-- MAIN CONTENT -->
        <div class="main-content">
            <div class="container-fluid">
                <h3 class="page-title">Settings</h3>
                <div class="row d-flex justify-content-center">
                    <div class="profile-container ">
                        <!-- PANEL NO CONTROLS -->
                        <div class="panel">
                            <div class="panel-heading">
                                <h3 class="panel-title">Settings</h3>
                            </div>
                            <div class="panel-body">
                                @if (session()->has('success'))
                                    <div class="alert alert-success alert-dismissible" role="alert">
                                        <button type="button" class="close" data-dismiss="alert"
                                            aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                        <strong>Success! </strong> {{ session('success') }}
                                    </div>
                                @endif
                                <div>
                                    <form action="{{ route('admin.users.updatepassword') }}" method="post">
                                        @csrf
                                        <div class="form-group @error('current_password') has-error @enderror">
                                            <label for="current_password">Old Password</label>
                                            <input type="password" class="form-control" name="current_password"
                                                id="current_password" value="">
                                            @error('current_password')
                                                <span class="help-block">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="form-group  @error('password') has-error @enderror">
                                            <label for="password">New Password</label>
                                            <input type="password" class="form-control" name="password" id="password"
                                                value="">
                                            @error('password')
                                                <span class="help-block">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="form-group  @error('confirm_password') has-error @enderror">
                                            <label for="confirm_password">Confirm Password</label>
                                            <input type="confirm_password" class="form-control" name="confirm_password"
                                                id="confirm_password" value="">
                                            @error('confirm_password')
                                                <span class="help-block">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group">
                                                <input type="submit" name="changepassword" class="btn btn-primary"
                                                    value="Save">
                                            </div>

                                        </div>

                                    </form>
                                </div>
                            </div>
                        </div>
                        <!-- END PANEL NO CONTROLS -->
                    </div>

                </div>


            </div>
        </div>
        <!-- END MAIN CONTENT -->
    </div>
    <!-- END MAIN -->
    <div class="clearfix"></div>

    <!-- /. PAGE WRAPPER  -->
@endsection
