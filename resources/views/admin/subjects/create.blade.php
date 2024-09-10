@extends('layouts.admin')
@section('content')

    <div class="main">
        <!-- MAIN CONTENT -->
        <div class="main-content">
            <div class="container-fluid">
                <h3 class="page-title"> Add New Subject</h3>
                <div class="row d-flex justify-content-center">
                    <div class="profile-container ">
                        <!-- PANEL NO CONTROLS -->
                        <div class="panel">
                            <div class="panel-heading">
                                <h3 class="panel-title">Add new Subject</h3>
                            </div>
                            <div class="panel-body">
                                <div>
                                    <form action="{{ route('admin.subjects.store') }}" method="post">
                                        @csrf
                                        <div class="form-group  @error('subject_name') has-error  @enderror">
                                            <label for="subject_code">Subject Code</label>
                                            <input type="text" class="form-control" name="subject_code" id="subject_code"
                                                value="{{ old('subject_code') }}">
                                            @error('subject_code')
                                                <span class="help-block">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="form-group  @error('subject_name') has-error  @enderror">
                                            <label for="subject_name">Subject Name</label>
                                            <input type="text" class="form-control" name="subject_name" id="subject_name"
                                                value="{{ old('subject_name') }}">

                                            @error('subject_name')
                                                <span class="help-block">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group">
                                                <input type="submit" name="save" class="form-control btn btn-primary"
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
