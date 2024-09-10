@extends('layouts.admin')

@section('content')
    <!-- ============================================================== -->
    <!-- Start Page Content -->
    <!-- MAIN -->
    <div class="main">
        <!-- MAIN CONTENT -->
        <div class="main-content">
            <div class="container-fluid">
                <h3 class="page-title">All Candidates</h3>
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel">
                            <div class="panel-heading">
                                <h3 class="panel-title">Filter Registered Center</h3>
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
                                <form action="{{ route('admin.candidates.entries.export') }}" method="POST">
                                    @csrf
                                    <div class="form-group col-md-3">
                                        <label for="year">Year</label>
                                        <select class="form-control status-dropdown" name="year" id="year">
                                            @foreach ($years as $year)
                                                <option
                                                    value="{{ $year }}"@if ($year == date('Y')) selected @endif>
                                                    {{ $year }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="">By Level</label>
                                        <select class="form-control" name="level" id="level">
                                            <option value="">Please Select Level</option>
                                            @foreach ($levels as $level)
                                                <option value="{{ $level->level }}">{{ $level->level }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="">By Session</label>
                                        <select class="form-control" name="session" id="session">
                                            <option value="">Please Select Session</option>
                                            @foreach ($sessions as $session)
                                                <option value="{{ $session->session }}">{{ $session->session }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="year">Option</label>
                                        <select class="form-control status-dropdown" name="option" id="option">
                                            <option value="">Select Option</option>
                                            @foreach ($options as $option)
                                                <option value="{{ $option->option_code }}">{{ $option->option_code }}
                                                </option>
                                            @endforeach

                                        </select>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="">By Center</label>
                                        <select multiple class="form-control" name="center[]" id="center">
                                            <option value="">Please Select Center</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="">By Subjects</label>
                                        <select multiple class="form-control" name="subject[]" id="subject">
                                        </select>
                                    </div>

                                    <div class="form-group col-md-12">
                                        <button type="submit" class="btn  btn-block btn-primary">Export Candidates</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <!-- PANEL NO CONTROLS -->
                        <div class="panel">
                            <div class="panel-heading">
                                <h3 class="panel-title">Total Entries</h3>
                            </div>
                            <div class="panel-body">
                                <div class="tab-pane fade in active" id="tab-bottom-left1">
                                    <div id="candidates-per-center" class="table-responsive">
                                        <div class="animated-wrapper">
                                            <div class="loader">Please wait...</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- TABBED CONTENT -->


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
    @section('script')
        <script>
            /*-----------------------------------/
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            /*Diplay candidates
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            /*----------------------------------*/

            /********** Some Variable Initial Value **************/

            var level = $("#level").val();
            var session = $("#session").val();
            var center = $("#center").val() || []
            var subject = $("#subject").val() || [];
            var year = $("#year").val();
            var option = $("#option").val();

            /**********  Center_no,level,session Change get Subjects,   **************/
            $(document).on("change",
                "#edit-form  #center_no, #addCandidateForm #center_no,#edit-form  #level, #addCandidateForm #level,#edit-form  #session, #addCandidateForm #session",
                function() {
                    var form = $(this).closest("form").attr('id');
                    centerSubjects(form)
                });
            /**********  Center_no,level,session Change get Subjects,   **************/


            /**********  Candidates Sorting Start    **************/
            $("#center").on("change", function(event) {
                level = $("#level").val();
                session = $("#session").val();
                center = $(this).val() || [];
                year = $("#year").val();
                subject = $("#subject").val() || [];
                option = $("#option").val();
                candidate_per_center(
                    level,
                    session,
                    center,
                    subject,
                    year,
                    option ,
                    event = event
                );



            });
            /**********  Candidates Sorting End    **************/

            /**********  Candidates Sorting Start    **************/
            $("#subject").on("change", function(event) {
                level = $("#level").val();
                session = $("#session").val();
                center = $("#center").val() || []
                subject = $(this).val() || [];
                year = $("#year").val();
                sponsor = $("#sponsor").val();
                option = $("#option").val();


                candidate_per_center(
                    level,
                    session,
                    center,
                    subject,
                    year,
                    option,
                    event = event
                );



            });
            /**********  Candidates Sorting End    **************/

            /**********  Candidates filter Start    **************/
            $("#session").on("change",

                function() {
                    $('#center').html(`<option value="">
                                  Please Select Center
                              </option>`);
                    level = $("#level").val();
                    session = $(this).val();
                    center = $("#center").val() || []
                    subject = $("#subject").val() || [];
                    year = $("#year").val();
                    option = $("#option").val();





                    candidate_per_center(
                        level,
                        session,
                        center,
                        subject,
                        year,
                        option,
                        event = null
                    );
                    $('#amend-datatable').DataTable().ajax.reload(null, false);

                });
            /**********  Candidates filter  End    **************/

            /**********  Candidates Main Search Start    **************/
            $("#level").on("change", function() {
                $('#center').html(`<option value="">
                                  Please Select Center
                              </option>
                              <option value="All">
                                  Please Select Center
                              </option>`);
                level = $(this).val();
                session = $("#session").val();

                year = $("#year").val();

                center = $("#center").val() || []
                subject = $("#subject").val() || [];
                option = $("#option").val();


                candidate_per_center(
                    level,
                    session,
                    center,
                    subject,
                    year,
                    option,
                    event = null
                );
                $('#amend-datatable').DataTable().ajax.reload(null, false);
            });
            /**********  Candidates Main Search End   **************/

            $("#option").on("change", function() {

                level = $("#level").val();
                session = $("#session").val();

                year = $("#year").val();

                center = $("#center").val() || []
                subject = $("#subject").val() || [];
                option = $(this).val();


                candidate_per_center(
                    level,
                    session,
                    center,
                    subject,
                    year,
                    option,
                    event = null
                );
                $('#amend-datatable').DataTable().ajax.reload(null, false);
            });

            /**********  Candidates Main Search Start    **************/
            $("#year").on("change", function() {
                $('#center').html(`<option value="">
                                  Please Select Center
                              </option>
                              <option value="All">
                                  Please Select Center
                              </option>`);

                level = $("#level").val();
                session = $("#session").val();
                center = $("#center").val() || []
                subject = $("#subject").val() || [];
                year = $(this).val();
                option = $("#option").val();

                candidate_per_center(
                    level,
                    session,
                    center,
                    subject,
                    year,
                    option,
                    event = null
                );
                $('#amend-datatable').DataTable().ajax.reload(null, false);
            });
            /**********  Candidates Main Search End   **************/

            /*****  Retrieve Value When Page First Load  *******/

            candidate_per_center(
                level,
                session,
                center,
                subject,
                year,
                option
            );
            /****  AJAX Main Function Who Perform All Tasks Start *******/
            function candidate_per_center(
                level,
                session,
                center,
                subject,
                year,
                option,
                event = null
            ) {

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                var i = 0;
                $.ajax({
                    url: "{{ route('admin.candidates.entries.index') }}",
                    method: "GET",
                    data: {
                        level: level,
                        session: session,
                        center: center,
                        subject: subject,
                        year: year,
                        option: option
                    },
                    beforeSend: function() {
                        // setting a timeout
                        $(".animated-wrapper").fadeIn();
                        i++;
                    },
                    success: function(data) {
                        console.log(data);

                        $("#candidates-per-center").html(data.cendidate_per_center);
                    },
                    complete: function() {
                        i--;
                        if (i <= 0) {
                            $(".animated-wrapper").fadeOut();
                        }
                    },

                });
            }




            $("#subject").select2({
                placeholder: "Select the subject",
                ajax: {
                    url: "{{ route('admin.candidates.entries.autocompleteSearchSubject') }}",
                    method: "GET",
                    data: function(params) {
                        var level = $("#level").find("option:selected").val();
                        var session = $("#session").find("option:selected").val();
                        var year = $("#year").find("option:selected").val();
                        var query = {
                            search: params.term,
                            level: level,
                            session: session,
                            subject: $(this).val(),
                            year: year,
                        };
                        return query;
                    },
                    dataType: "json",
                    delay: 100,
                    processResults: function(data) {
                        console.log(data);
                        return {
                            results: $.map(data, function(item) {
                                return {
                                    text: `${item.subject_name}(${ item.subject_code})`,
                                    id: item.subject_code,
                                };
                            }),
                        };
                    },
                    cache: true,
                    error: function(jqXHR, status, error) {
                        console.log(error + ": " + jqXHR.responseText);
                        return {
                            results: []
                        }; // Return dataset to load after error
                    }
                },
                width: '100%',
                containerCss: {
                    "display": "block"
                }
            })

            $("#center").select2({
                placeholder: "Select the Center",
                ajax: {
                    url: "{{ route('admin.candidates.entries.autocompleteSearchCenter') }}",
                    method: "GET",
                    data: function(params) {
                        var level = $("#level").find("option:selected").val();
                        var session = $("#session").find("option:selected").val();
                        var year = $("#year").find("option:selected").val();
                        var query = {
                            search: params.term,
                            level: level,
                            session: session,
                            year: year,
                        };
                        return query;
                    },
                    dataType: "json",
                    delay: 100,
                    processResults: function(data) {
                        console.log(data);
                        return {
                            results: $.map(data, function(item) {
                                return {
                                    text: `${item.center_name}(${item.center_no})`,
                                    id: item.center_no,
                                };
                            }),
                        };
                    },
                    cache: true,
                    error: function(jqXHR, status, error) {
                        console.log(error + ": " + jqXHR.responseText);
                        return {
                            results: []
                        }; // Return dataset to load after error
                    }
                },
                width: '100%',
                containerCss: {
                    "display": "block"
                }
            })
        </script>
    @endsection
