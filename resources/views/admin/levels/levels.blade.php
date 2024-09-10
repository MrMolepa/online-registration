@extends('layouts.admin')

@section('content')
    <!-- ============================================================== -->
    <!-- Start Page Content -->
    <!-- ============================================================== -->
    <div class="main">
        <!-- MAIN CONTENT -->
        <div class="main-content">
            <div class="container-fluid">
                <h3 class="page-title">Levels</h3>
                <div class="row">
                    <div class="col-md-12">
                        <!-- PANEL NO CONTROLS -->
                        <div class="panel">
                            <div class="panel-heading">
                                <h3 class="panel-title">Center <b>{{ $center_no }}</b></h3>
                            </div>
                            <div class="panel-body">
                                <form action="{{ route('admin.centers.updateLevels', $center_no) }}" method="post">
                                    @method('PUT')
                                    @csrf
                                    <div class="row">

                                        @foreach ($levels as $level)
                                            <div class='form-group'>
                                                <label>
                                                    <input type="checkbox"
                                                        {{ in_array($level->id, $centerLevel) ? 'checked' : '' }}
                                                        class="form-check-input" name="levels[]"
                                                        value="{{ $level->id }}">
                                                    <span>{{ $level->level }}</span>
                                                </label>
                                            </div>
                                        @endforeach

                                    </div>
                                    <div class="form-group mt-2 mb-2">
                                        <input type="submit" class="btn btn-primary" value="Update">
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
