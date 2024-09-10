@extends('layouts.admin')

@section('content')
    <!-- ============================================================== -->
    <!-- Start Page Content -->
    <!-- ============================================================== -->
    <div class="main">
        <!-- MAIN CONTENT -->
        <div class="main-content">
            <div class="container-fluid">
                <h3 class="page-title">New Role</h3>
                <div class="row">
                    <div class="col-md-6">
                        <!-- PANEL NO CONTROLS -->
                        <div class="panel">
                            <div class="panel-heading">
                                <h3 class="panel-title">New Role</h3>
                            </div>
                            <div class="panel-body">
                                <form action="{{ route('admin.roles.store') }}" method="post">
                                    @csrf
                                    <div class="form-group ">
                                        <label for="display_name">{{ __('Display name') }}</label>
                                        <input id="display_name" type="text"
                                            class="form-control @error('display_name') is-invalid @enderror"
                                            name="display_name" value="" autocomplete="display_name" autofocus>

                                        @error('display_name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-group ">
                                        <label for="name">{{ __('name') }}</label>
                                        <input id="name" type="text"
                                            class="form-control @error('name') is-invalid @enderror" name="name"
                                            autocomplete="name" autofocus>
                                        @error('name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-group ">
                                        <label for="description">{{ __('description') }}</label>
                                        <textarea id="description" name="description" rows="4"
                                            class=" form-control @error('description') is-invalid @enderror"
                                            cols="50"></textarea>

                                        @error('description')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>



                                    <div class="form-group mt-2 mb-2">
                                        <input type="submit" class="btn btn-primary" value="+ Add">
                                    </div>
                                </form>



                            </div>
                        </div>
                        <!-- END PANEL NO CONTROLS -->
                    </div>

                </div>


            </div>
        </div>
        <!-- END MAIN CONTENT -->
    </div>
    <!-- ============================================================== -->
    <!-- End PAge Content -->
    <!-- ============================================================== -->
@endsection
