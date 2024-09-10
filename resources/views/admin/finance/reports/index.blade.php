@extends('layouts.admin')

@section('content')
    <!-- ============================================================== -->
    <!-- Start Page Content -->
    <!-- MAIN -->
    <div class="main">
        <!-- MAIN CONTENT -->
        <div class="main-content">
            <div class="container-fluid">
                <h3 class="page-title">Financial Report</h3>
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel">
                            <div class="panel-heading">
                                <h3 class="panel-title">Filter</h3>
                            </div>
                            <div class="panel-body">
                                @if (session()->has('success'))
                                    <div class="alert alert-success alert-dismissible" role="alert">
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                                aria-hidden="true">&times;</span></button>
                                        <i class="fa fa-check-circle"></i> {{ session('success') }}
                                    </div>
                                    &nbsp;
                                @endif
                                <form action="{{ route('admin.finantial-report.report') }}" id="report-form" class="row"
                                    method="get">
                                    @csrf
                                    <div class="form-group  @error('year') has-error  @enderror col-md-4">
                                        <label for="year">Year</label>
                                        <select class="form-control  dropdown-selected" name="year" id="year">
                                            @foreach ($years as $year)
                                                <option
                                                    value="{{ $year }}"@if ($year == date('Y')) selected @endif>
                                                    {{ $year }}</option>
                                            @endforeach
                                        </select>
                                        @error('year')
                                            <span class="help-block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="">Level</label>
                                        <select class="form-control dropdown-selected" name="level" id="level">
                                            <option value="">Please Select Level</option>

                                        </select>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="">Session</label>
                                        <select class="form-control dropdown-selected" name="session" id="session">
                                            <option value=""> Select Session</option>
                                        </select>
                                    </div>

                                    <div class="clearfix"></div>
                                    <div class="form-group col-md-4">
                                        <label for="">Center</label>
                                        <select class="form-control dropdown-selected" name="center" id="center">
                                            <option value=""> Select Center</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="">Sponsor</label>
                                        <select class="form-control" name="sponsor" id="sponsor">
                                            <option value=""> Select sponsor</option>

                                        </select>
                                    </div>


                                    <div class="form-group  @error('report') has-error  @enderror col-md-4">
                                        <label for="">Report</label>
                                        <select class="form-control" name="report" id="report">
                                            <option value="1"> Sponsors Report</option>
                                            <option value="2">Totals Report</option>
                                        </select>
                                        @error('report')
                                            <span class="help-block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="clearfix"></div>

                                    <div class="form-group col-md-12">
                                        <button type="submit" class="btn btn-block btn-primary">Download</button>
                                    </div>
                                    <div class="clearfix"></div>

                                </form>

                                <table class="table" name="tablename" id="candidates">
                                    <thead>
                                        <tr>
                                            <th>Centre No</th>
                                            <th>Nationa Id</th>
                                            <th>Candidate No</th>
                                            <th>Candidate Surname</th>
                                            <th>Candidate Other Name</th>
                                            <th>Date Of Birth</th>
                                            <th>Gender</th>
                                            <th>Total Fee</th>
                                            <th>Paid Fee</th>

                                        </tr>
                                    </thead>
                                </table>
                                @push('scripts')
                                    <script>
                                        $(function() {
                                            var candidates = $('#candidates').DataTable({
                                                processing: true,
                                                serverSide: true,
                                                deferRender: true,
                                                "lengthMenu": [
                                                    [20, 50, 100, 200, 400, -1],
                                                    [20, 50, 100, 200, 400, "All"]
                                                ],
                                                ajax: {
                                                    url: "{{ route('admin.finantial-report.report') }}",
                                                    data: function(d) {
                                                        d.center = $("#center").val();
                                                        d.level = $("#level").val();
                                                        d.session = $("#session").val();
                                                        d.year = $("#year").val();
                                                        d.sponsor = $("#sponsor").val();
                                                    }
                                                },
                                                columns: [{
                                                        data: 'center_no',
                                                        name: 'center_candidate.center_no',
                                                        searchable: true
                                                    },

                                                    {
                                                        data: 'national_id',
                                                        name: 'center_candidate.national_id',
                                                        searchable: true
                                                    }, {
                                                        data: 'candidate_no',
                                                        name: 'candidates.candidate_no',
                                                        searchable: true
                                                    },
                                                    {
                                                        data: 'candidate_surname',
                                                        name: 'candidates.candidate_surname',
                                                        searchable: true
                                                    },
                                                    {
                                                        data: 'candidate_other_name',
                                                        name: 'candidates.candidate_other_name',
                                                        searchable: true
                                                    },
                                                    {
                                                        data: 'date_of_birth',
                                                        name: 'candidates.date_of_birth',
                                                        searchable: true
                                                    },
                                                    {
                                                        data: 'gender',
                                                        name: 'candidates.gender',
                                                        searchable: true
                                                    },

                                                    {
                                                        data: 'price',
                                                        name: 'price',
                                                        searchable: false
                                                    },

                                                    {
                                                        data: 'amount_paid',
                                                        name: 'amount_paid',
                                                        searchable: false
                                                    },




                                                ]

                                            });
                                            $("#candidates").css("width", "100%");
                                        });
                                        $(".dropdown-selected").each(function(index) {
                                            $(this).on("change", function(event) {
                                                var name = $(this).attr("name");
                                                var value = $(this).val();
                                                console.log(name);
                                                var inputData = $("#report-form").serialize();
                                                $.ajax({
                                                    type: "GET",
                                                    url: "{{ route('admin.finantial-report.index') }}",
                                                    data: inputData,
                                                    success: function(response) {
                                                        if (response) {

                                                            for (const key of Object.keys(response)) {

                                                                var formElement = key.slice(0, -1);
                                                                var selectOptions = response[key];
                                                                if (name == 'year') {
                                                                    $(`#${formElement}`).empty();
                                                                    $(`#${formElement}`).append(
                                                                        `<option value=''>Please Select ${formElement}</option>`
                                                                    );
                                                                }
                                                                if (!$.isEmptyObject(response[key])) {
                                                                    var selectOption = $(`#${formElement}`).val()
                                                                    if (selectOption == "") {
                                                                        $(`#${formElement}`).empty();
                                                                        $(`#${formElement}`).append(
                                                                            `<option value=''>Please Select ${formElement}</option>`
                                                                        );
                                                                        $.each(selectOptions, function(key, option) {
                                                                            $(`#${formElement}`).append(
                                                                                '<option value="' +
                                                                                option +
                                                                                '">' + option +
                                                                                '</option>');
                                                                        });
                                                                    }


                                                                }

                                                            }

                                                        }
                                                        $('#candidates').DataTable().ajax.reload();
                                                    }
                                                });

                                            });
                                        });
                                    </script>
                                @endpush
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
@endsection
