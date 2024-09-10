@extends('layouts.admin')

@section('content')
    <!-- ============================================================== -->
    <!-- Start Page Content -->
    <!-- MAIN -->
    <div class="main">
        <!-- MAIN CONTENT -->
        <div class="main-content">
            <div class="container-fluid">
                <h3 class="page-title">All Roles</h3>
                <div class="row">
                    <div class="col-md-6">
                        <!-- PANEL NO CONTROLS -->
                        <div class="panel">
                            <div class="panel-heading">
                                <h3 class="panel-title">All Roles</h3>
                            </div>
                            <div class="panel-body">
                                <form action="{{ route('admin.roles.update', $role->id) }}" method="post">

                                    
                                    @method('PUT')
                                    @csrf
                                    <div class="form-group @error('display_name') has-error  @enderror">
                                        <label for="display_name">{{ __('Display name') }}</label>
                                        <input id="display_name" type="text" class="form-control" name="display_name"
                                            value="{{ $role->display_name }}" autocomplete="display_name" autofocus>

                                        @error('display_name')
                                            <span class="help-block">{{ $message }}</span>

                                        @enderror
                                    </div>
                                    <div class="form-group   @error('name') has-error  @enderror">
                                        <label for="name">{{ __('name') }}</label>
                                        <input id="name" type="text" class="form-control" name="name"
                                            value="{{ $role->name }}" autocomplete="name" disabled autofocus>

                                        @error('name')
                                            <span class="help-block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group ">
                                        <label for="description">{{ __('description') }}</label>
                                        <textarea id="description" name="description" rows="4"
                                            class=" form-control @error('description') is-invalid @enderror"
                                            cols="50"> {{ $role->description }}</textarea>
                                        @error('description')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-group mt-2 mb-2">
                                        <button type="submit" class="btn btn-primary"> update</button>

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
    <!-- END MAIN -->
@endsection
