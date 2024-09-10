@extends('layouts.admin')

@section('content')
    <!-- ============================================================== -->
    <!-- Start Page Content -->
    <!-- ============================================================== -->
    <div class="main">
        <!-- MAIN CONTENT -->
        <div class="main-content">
            <div class="container-fluid">
                <h3 class="page-title">{{$center_no}}</h3>
                <div class="row">
                    <div class="col-md-12">
                        <!-- PANEL NO CONTROLS -->
                        <div class="panel">
                            <div class="panel-heading">
                                <h3 class="panel-title">Center <b>{{ $center_no }}</b></h3>
                            </div>
                            <div class="panel-body">
                                <form action="{{ route('admin.centers.updateSubjects', $center_no) }}" method="post">
                                    @method('PUT')
                                    @csrf
                                    <div class="row">
                                        @foreach ($subjects as $subject)
                                            <div class='form-group col-md-4'>
                                                <label>
                                                    <input type="checkbox"  {{ in_array($subject->subject_code,$centerSubjects) ? 'checked' : '' }} class="form-check-input" name="subjects[]"
                                                        value="{{ $subject->subject_code }}">
                                                    <span>{{ $subject->subject_name }} ({{ $subject->subject_code }})</span>
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
