@extends('layouts.admin')
@section('content')
    <div class="main">
        <!-- MAIN CONTENT -->
        <div class="main-content">
            <div class="container-fluid">
                <h3 class="page-title">PDF Settings</h3>
                <div class="row d-flex justify-content-center">
                    <div class="profile-container ">
                        <!-- PANEL NO CONTROLS -->
                        <div class="panel">
                            <div class="panel-heading">
                                <h3 class="panel-title">PDF Settings</h3>
                            </div>
                            <div class="panel-body">

                                <div>

                                    <form action="{{ route('admin.certificates.print') }}" method="post">
                                        @csrf

                                        <div class="form-group ">
                                            <label for="center_no">Centers</label>
                                            <select name="center_no" class="form-control">
                                                <option value="">Please Select</option>
                                                @foreach ($centers as $center)
                                                    <option value="{{ $center->center_no }}">
                                                        {{ $center->center_no }} - {{ $center->center_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="candidate_no" class="control-label">Candidate Number</label>
                                            <input type="text" class="form-control" name="candidate_no" value=""
                                                id="candidate_no">
                                        </div>

                                        <div class="form-group ">
                                            <label for="statement_type">Statement Type</label>
                                            <select name="statement_type" class="form-control">
                                                <option value="">Please Select</option>
                                                <option value="LBSE">LBSE</option>
                                                <option value="LGCSE">LGCSE</option>
                                            </select>
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group">
                                                <input type="submit" name="print-statement"
                                                    class="form-control btn btn-primary" value="Print">
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
