@extends('layouts.admin')

@section('content')
    <!-- ============================================================== -->
    <!-- Start Page Content -->
    <!-- ============================================================== -->
    <div class="main">
        <!-- MAIN CONTENT -->
        <div class="main-content">
            <div class="container-fluid">
                <h3 class="page-title">New Permission</h3>
                <div class="row">
                    <div class="col-md-6">
                        <!-- PANEL NO CONTROLS -->
                        <div class="panel">
                            <div class="panel-heading">
                                <h3 class="panel-title">New Permission</h3>
                            </div>
                            <div class="panel-body">

                                <form class="row g-3" method="POST" action="">
                                    @csrf

                                    <div class="form-group">
                                        <label for="resource">{{ __('Resource') }}</label>
                                        <input id="resource" type="text"
                                            class="form-control @error('resource') is-invalid @enderror" name="resource"
                                            value="{{ old('resource') }}" autocomplete="name" autofocus>
                                        @error('resource')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                        @error('resource')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror

                                    </div>



                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary">Add</button>
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
