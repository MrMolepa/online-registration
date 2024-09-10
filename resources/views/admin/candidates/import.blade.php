@extends('layouts.admin')
@section('content')

    <!-- MAIN -->
    <div class="main">
        <!-- MAIN CONTENT -->
        <div class="main-content">
            <div class="container-fluid">
                <h3 class="page-title">Import Candidates List </h3>
                <div class="row">
                    <div class="col-md-8">
                        <!-- PANEL NO CONTROLS -->
                        <div class="panel">
                            <div class="panel-heading">
                                <h3 class="panel-title">Total Candidates ({{ $totalCandidte }})</h3>
                            </div>
                            <div class="panel-body">
                                @if (session()->has('errors-csv'))
                                    <div class="alert alert-success alert-dismissible" role="alert">
                                        <button type="button" class="close" data-dismiss="alert"
                                            aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                        <i class="fa fa-check-circle"></i>
                                        <div>
                                            <div>
                                            Total Candidates : {{ session('totalCandidates') }}
                                            <br>
                                            Inserted Candidates : {{ session('insertedCandidate') }}
                                            <br>
                                            Not Inserted Candidates :
                                            {{ session('totalCandidates') - session('insertedCandidate') }}
                                        </div>
                                    </div>
                                @endif
                                @if (session()->has('errors-csv'))
                                    <div class="alert alert-danger alert-dismissible" role="alert">
                                        <button type="button" class="close" data-dismiss="alert"
                                            aria-label="Close"><span aria-hidden="true">×</span></button>
                                        <i class="fa fa-times-circle"></i>
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Row</th>
                                                    <th>Massage</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach (session('errors-csv') as $error)
                                                    @foreach ($error['messages'] as $massage)
                                                        <tr>
                                                            <td>{{ $error['row'] }}</td>
                                                            <td>{!! $massage !!}</td>
                                                        </tr>
                                                    @endforeach
                                                @endforeach


                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                                <form action="{{ route('admin.candidate-profile.import') }}" method="post"
                                    enctype="multipart/form-data" accept-charset="utf-8">
                                    @csrf
                                    <div class="form-group">
                                        <p>The first line in downloaded csv file should remain as it is. Please do not
                                            change the order of columns in csv file.</p>
                                        <p>
                                            The correct column order is (candidate number, Candidate Surname,
                                            Candidate Other Names, Date of birth, Gender). Remember,
                                            the date format should be (YYYY-mm-dd) and the company, department, designation,
                                            shift and role name must be matched with your existing data. You must follow the
                                            csv file, otherwise you will get an error while importing the csv file.
                                        </p>

                                        <a href=" {{ asset('adminAssets/assets/download/sample.csv') }}" download
                                            class="btn btn-primary">Download Sample File</a>
                                    </div>

                                    <div class="form-group  @error('fileup') has-error @enderror">
                                        <label class="control-label" for="fileup">Upload File</label>
                                        <input type="file" name="fileup" class="form-control" id="fileup">
                                        @error('fileup')
                                            <span class="help-block">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group">
                                            <input type="submit" name="import" class="btn btn-primary" value="import">
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
    <!-- END MAIN -->
    <div class="clearfix"></div>

    <!-- /. PAGE WRAPPER  -->
@endsection
