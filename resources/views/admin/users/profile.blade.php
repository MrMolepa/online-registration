@extends('layouts.admin')
@section('content')

    <div class="main">
        <!-- MAIN CONTENT -->
        <div class="main-content">
            <div class="container-fluid">
                <h3 class="page-title">My Profile</h3>
                <div class="row d-flex justify-content-center">
                    <div class="profile-container ">
                        <!-- PANEL NO CONTROLS -->
                        <div class="panel">
                            <div class="panel-heading">
                                <h3 class="panel-title">My Profile</h3>
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
                                    <form action="{{ route('admin.users.updateprofile', auth()->user()->id) }}"
                                        id="profileform" method="post" enctype="multipart/form-data">
                                        @csrf
                                        @method("PUT")
                                        <div class="form-group  text-center">
                                            @if (auth()->user()->profile)
                                                <img src="{{ asset('uploads/profile/' . auth()->user()->profile) }}"
                                                    width="220px" class="img-circle" id="LogInprofile" alt="Avatar">
                                            @else
                                                <img src="{{ asset('adminAssets/assets/img/profile.png') }}" width="220px"
                                                    class="img-circle" id="LogInprofile" alt="Avatar">
                                            @endif

                                            <label for="LogInprofileImage">Profile Image</label>
                                            <input type="file" name="profileImage" id="LogInprofileImage"
                                                class="form-control">

                                        </div>
                                        <div class="form-group @error('username') has-error  @enderror">
                                            <label for="userid">User ID</label>
                                            <input type="text" class="form-control" id="username" name="userid"
                                                readonly="readonly" value="{{ auth()->user()->username }}">

                                            @error('username')
                                                <span class="help-block">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="form-group @error('email') has-error  @enderror">
                                            <label for="email">Email Address</label>
                                            <input type="text" class="form-control" name="email" id="email"
                                                value="{{ auth()->user()->email }}">
                                            @error('email')
                                                <span class="help-block">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="form-group @error('occupation') has-error  @enderror">
                                            <label for="occupation">Occupation</label>
                                            <input type="text" class="form-control" name="occupation" id="occupation"
                                                value="{{ auth()->user()->occupation }} ">
                                            @error('occupation')
                                                <span class="help-block">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group">
                                                <input type="submit" name="updateprofile"
                                                    class="form-control btn btn-primary" value="Edit">
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
