@extends('layouts.school')

@section('content')
    <div id="page-wrapper">

        <div class="header">
            <h1 class="page-header">
                Settings
            </h1>

            <ol class="breadcrumb">

                <li><a href="javascript:void();">Setting</a></li>
                <li class="active"><a href="javascript:void();">Setting( change Password)</a></li>
            </ol>

        </div>

        <div id="page-inner" class="reports">

            <div class="row">
                <div class="col-md-6">
                    <!-- Advanced Tables -->
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            change Password
                        </div>
                        <div class="panel-body ">
                            <div class="col-md-12">
                                <div>
                                    @if (session()->has('success'))
                                        <div class="alert alert-success alert-dismissible" role="alert">
                                            <button type="button" class="close" data-dismiss="alert"
                                                aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                            <strong>Congratulations!</strong> {{ session('success') }}
                                        </div>
                                    @endif
                                    @if (session()->has('error'))
                                        <div class="alert alert-danger alert-dismissible" role="alert">
                                            <button type="button" class="close" data-dismiss="alert"
                                                aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                            <strong>Error!</strong> {{ session('error') }}
                                        </div>
                                    @endif
                                    <form action="{{ route('center.users.updatepassword') }}" method="post">
                                        @csrf
                                        <div class="form-group @error('current_password') has-error @enderror">
                                            <label for="current_password">Current Password</label>
                                            <input type="password" class="form-control" name="current_password"
                                                id="current_password" value="">
                                            @error('current_password')
                                                <span class="help-block">{{ $message }}</span>
                                            @enderror

                                        </div>
                                        <div class="form-group mb-2  @error('password') has-error @enderror">
                                            <label for="password">New Password</label>
                                            <input type="password" class="form-control" name="password" id="password"
                                                value="">
                                            @error('password')
                                                <span class="help-block">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="form-group mb-2 @error('confirm_password') has-error @enderror">
                                            <label for="confirm_password">Confirm Password</label>
                                            <input type="password" class="form-control" name="confirm_password"
                                                id="confirm_password" value="">
                                            @error('confirm_password')
                                                <span class="help-block">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="form-group  mt-4 p-2">
                                            <input type="submit" name="changepassword" class="btn btn-primary" value="Save">
                                        </div>


                                    </form>
                                </div>
                            </div>


                        </div>
                    </div>
                </div>
                <!--End Advanced Tables -->
            </div>

        </div>
        <!-- /. PAGE INNER  -->

    </div>
    <!-- /. PAGE WRAPPER  -->
@endsection

@section('script')

@endsection
