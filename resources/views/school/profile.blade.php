@extends('layouts.school')

@section('content')
    <div id="page-wrapper">

        <div class="header">
            <h1 class="page-header">
                Profile
            </h1>

            <ol class="breadcrumb">

                <li><a href="javascript:void();">Home</a></li>
                <li class="active"><a href="javascript:void();">Profile</a></li>
            </ol>

        </div>

        <div id="page-inner" class="reports">

            <!-- List of reports available -->

            <div class="row">
                <div class="col-md-12">
                    <!-- Advanced Tables -->
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Profile
                        </div>
                        <div class="panel-body ">
                            @if (session()->has('success'))
                                <div class="alert alert-success alert-dismissible" role="alert">
                                    <button type="button" class="close" data-dismiss="alert"
                                        aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <strong>Congratulations!</strong> {{ session('success') }}
                                </div>
                            @endif

                            @if (auth()->user()->user_type != 'center')


                                <div class="offset-md-4 col-md-6 ">
                                    <form action="{{ route('center.users.updateprofile', auth()->user()->id) }}"
                                        id="profileform" method="post" enctype="multipart/form-data">
                                        @csrf
                                        @method("PUT")
                                        <div class="form-group  text-center">
                                            @if (auth()->user()->profile)
                                                <img src="{{ asset('uploads/profile/' . auth()->user()->profile) }}"
                                                    width="220px" id="LogInprofile" alt="">
                                            @else
                                                <img src="{{ asset('school/assets/img/profile.png') }}" width="220px"
                                                    id="LogInprofile" alt="" title="Upload image">
                                            @endif
                                            <label for="LogInprofileImage">Profile Image</label>
                                            <input type="file" name="profileImage" id="LogInprofileImage"
                                                class="form-control">
                                        </div>
                                        <div class="form-group  @error('userid') has-error @enderror">
                                            <label for="userid">User ID</label>
                                            <input type="text" class="form-control" id="userid" name="userid"
                                                readonly="readonly" value="{{ auth()->user()->username }}">
                                            @error('userid')
                                                <span class="help-block">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="form-group @error('centreNo') has-error @enderror">
                                            <label for="centreNo">Centre No</label>
                                            <input type="text" class="form-control" readonly="readonly" name="centreNo"
                                                id="centreNo" value="{{ auth()->user()->center_no }}">
                                            @error('centreNo')
                                                <span class="help-block">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="form-group @error('email') has-error @enderror">
                                            <label for="email">Email Address</label>
                                            <input type="text" class="form-control" name="email" id="email"
                                                value="{{ auth()->user()->email }}">
                                            @error('email')
                                                <span class="help-block">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="form-group @error('occupation') has-error @enderror ">
                                            <label for="occupation">Occupation</label>
                                            <input type="text" class="form-control" name="occupation" id="occupation"
                                                value="{{ auth()->user()->occupation }}">
                                            @error('occupation')
                                                <span class="help-block">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group">
                                                <input type="submit" name="updateprofile"
                                                    class="form-control btn btn-primary" value="Update">
                                            </div>

                                        </div>

                                    </form>
                                </div>

                            @else
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="userid">Centre no</label>
                                        <input type="text" class="form-control" readonly="readonly" name="centreNo"
                                            id="centreNo" value="{{ auth()->user()->center_no }}">
                                    </div>

                                    <div class="form-group">
                                        <label for="centre-name">Centre Name</label>
                                        <input type="text" class="form-control" readonly="readonly" name="centre-name"
                                            id="centre-name" value="{{ auth()->user()->center_name }} ">
                                    </div>
                                    <div class="form-group ">
                                        <label for="occupation">district</label>
                                        <input type="text" class="form-control" name="district" readonly="readonly"
                                            id="occupation" value="{{ auth()->user()->occupation }}">
                                    </div>
                                </div>
                            @endif

                        </div>




                    </div>
                </div>
                <!--End Advanced Tables -->
            </div>
        </div>
        <!-- end List of reports available -->

    </div>
    <!-- /. PAGE INNER  -->

    </div>
    <!-- /. PAGE WRAPPER  -->
@endsection



