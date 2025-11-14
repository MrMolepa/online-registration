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
                                <form action="{{ route('admin.candidate-registation.registration') }}" method="get">
                                    @csrf
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
                                        <label for="">By Center</label>
                                        <select class="form-control" name="center" id="center">
                                            <option value="">Please Select Center</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="">By Sponsor</label>
                                        <select class="form-control" name="sponsor" id="sponsor">
                                            <option value="">Please Select sponsor</option>
                                            @foreach ($sponsors as $sponsor)
                                                <option value="{{ $sponsor->sponser }}">{{ $sponsor->sponser }}</option>
                                            @endforeach
                                        </select>
                                    </div>
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
                                        <label for="filter">Show</label>
                                        <select class="form-control" name="filter" id="filter">
                                            <option value="10">10</option>
                                            <option value="50">50</option>
                                            <option value="100">100</option>
                                            <option value="500">500</option>
                                            <option value="1000">1000</option>
                                            <option value="5000">5000</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="subjects">Subjects</label>
                                        <select class="form-control" name="subjects" id="subjects">
                                            <option value="">Select Subject</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="subjects">Type</label>
                                        <select class="form-control" name="type" id="type">
                                            <option value="">Select Type</option>
                                            @foreach ($types as $type)
                                                <option value="{{ $type->type }}">{{ $type->type }}</option>
                                            @endforeach

                                        </select>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="">Export From</label>
                                        <input name="date_from" type="date" class="form-control">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="">File Format</label>
                                        <select class="form-control" name="file_format">
                                            <option value="txt">TXT</option>
                                            <option value="csv">CSV</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <button type="submit" class="btn btn-primary">Export Candidates</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <!-- PANEL NO CONTROLS -->
                        <div class="panel">
                            <div class="panel-heading">
                                <h3 class="panel-title">All Candidates</h3>
                            </div>
                            <div class="panel-body">
                                <div class="pull-left">
                                    <h4>Amendments List</h4>
                                    <div class="btn-group" role="group" aria-label="Basic example">
                                        <a href="{{ route('admin.candidate-registation.amendments', ['file_type' => 'CSV']) }}"
                                            class="btn btn-secondary">CSV</a>
                                        <a href="{{ route('admin.candidate-registation.amendments', ['file_type' => 'TXT']) }}"
                                            class="btn btn-secondary">TXT</a>
                                    </div>

                                </div>

                            </div>
                            <div class="clearfix"></div>
                            <!-- TABBED CONTENT -->
                            <div class="custom-tabs-line tabs-line-bottom left-aligned">
                                <ul class="nav" role="tablist">
                                    <li class="active"><a href="#tab-bottom-left1" role="tab"
                                            data-toggle="tab">Candidates per Center
                                        </a></li>
                                    <li><a href="#tab-bottom-left2" role="tab" data-toggle="tab">Registered
                                            Candidates</a></li>
                                </ul>
                            </div>
                            <div class="tab-content">
                                <div class="tab-pane fade in active" id="tab-bottom-left1">
                                    <div id="candidates-per-center" class="table-responsive">
                                        <div class="animated-wrapper">
                                            <div class="loader">Please wait...</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="tab-bottom-left2">
                                    <div id="candidates">
                                        <div class="table-responsive amendcandidates">
                                            <button type="button" data-toggle="modal" data-target="#add-candidate"
                                                class="btn btn-primary">+
                                                Candidate</button>
                                            <table class="table display compact" id="amend-datatable">
                                                <thead>
                                                    <tr>
                                                        <th>Center No</th>
                                                        <th>National ID</th>
                                                        <th>Candidate.No</th>
                                                        <th>Surname</th>
                                                        <th>Other Name </th>
                                                        <th>Type</th>
                                                        <th>Sponsor</th>
                                                        <th>No.subjects</th>
                                                        <th width="20%">Subjects</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>

                                            </table>
                                            @push('scripts')
                                                <script>
                                                    /*****  Display candidates*******/
                                                    toastr.options = {
                                                        "closeButton": true,
                                                        "debug": false,
                                                        "newestOnTop": false,
                                                        "progressBar": true,
                                                        "positionClass": "toast-top-right",
                                                        "preventDuplicates": false,
                                                        "onclick": null,
                                                        "showDuration": "300",
                                                        "hideDuration": "1000",
                                                        "timeOut": "5000",
                                                        "extendedTimeOut": "1000",
                                                        "showEasing": "swing",
                                                        "hideEasing": "linear",
                                                        "showMethod": "fadeIn",
                                                        "hideMethod": "fadeOut"
                                                    }
                                                    $(function() {

                                                        var table = $('#amend-datatable').DataTable({
                                                            processing: true,
                                                            serverSide: true,
                                                            responsive: true,
                                                            ajax: {
                                                                url: "{{ route('admin.candidate-registation.index') }}",
                                                                data: function(d) {
                                                                    d.center_no = $("#center").val(),
                                                                        d.filter = $("#filter").val(),
                                                                        d.level = $("#level").val(),
                                                                        d.session = $("#session").val(),
                                                                        d.year = $("#year").val(),
                                                                        d.sponsor = $("#sponsor").val(),
                                                                        d.type = $("#type").val(),
                                                                        d.subject = $("#subjects").val()
                                                                }
                                                            },
                                                            columns: [{
                                                                    data: 'center_no',
                                                                    name: 'center_candidate.center_no',
                                                                    searchable: true,
                                                                },
                                                                {
                                                                    data: 'national_id',
                                                                    name: 'center_candidate.national_id',
                                                                    searchable: true,
                                                                },
                                                                {
                                                                    data: 'candidate_no',
                                                                    name: 'candidates.candidate_no'
                                                                },
                                                                {
                                                                    data: 'candidate_surname',
                                                                    name: 'candidates.candidate_surname'

                                                                },
                                                                {
                                                                    data: 'candidate_other_name',
                                                                    name: 'candidates.candidate_other_name'
                                                                },
                                                                {
                                                                    data: 'type',
                                                                    name: 'center_candidate.type'
                                                                },
                                                                {
                                                                    data: 'sponser',
                                                                    name: 'center_candidate.sponser'
                                                                },
                                                                {
                                                                    data: 'subject_number',
                                                                    name: 'center_candidate.subject_number'
                                                                },
                                                                {
                                                                    data: 'subjects',
                                                                    name: 'subjects',
                                                                    orderable: false,
                                                                    searchable: false
                                                                },
                                                                {
                                                                    data: 'actions',
                                                                    name: 'actions',
                                                                    orderable: false,
                                                                    searchable: false
                                                                },


                                                            ]
                                                        });
                                                    });

                                                    $("#amend-datatable").css("width", "98.5%");
                                                    /*****  Display candidates end*******/

                                                    /********* ADD NEW Candidate ************/
                                                    $(document).on("click", "#save-candidate", function() {

                                                        //nav-tabs
                                                        var tabs = {}
                                                        $('#addCandidateForm ul.nav-tabs li').each(function(i) {
                                                            var href = $('a', this).attr("href");
                                                            var teb_id = $('a', this).data("id")
                                                            var tebData = $(`#addCandidateForm ${href}`).find('select, textarea, input').serialize();
                                                            tabs[teb_id] = tebData;
                                                        });
                                                        var i = 0;
                                                        $.ajaxSetup({
                                                            headers: {
                                                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                                                                    'content')
                                                            }
                                                        });
                                                        $.ajax({
                                                            url: "{{ route('admin.candidate-registation.store') }}",
                                                            cache: false,
                                                            beforeSend: function() {
                                                                // setting a timeout
                                                                $(".preloader").fadeIn();
                                                                i++;
                                                            },
                                                            method: "POST",
                                                            data: {
                                                                'tabs': tabs
                                                            },
                                                            success: function(data) {
                                                                console.log(data);
                                                                if ($.isEmptyObject(data.errors)) {
                                                                    $("#add-candidate").modal("hide");
                                                                    toastr.success(
                                                                        data.success
                                                                    );
                                                                    $('#amend-datatable').DataTable().ajax.reload();
                                                                } else {
                                                                    var parent = "#addCandidateForm";
                                                                    $(`${parent} input, ${parent} select, textarea`).each(function(index) {
                                                                        $(`${parent} .help-block`).remove();
                                                                        $(`${parent} .has-error`).removeClass('has-error');
                                                                    });
                                                                    $(`${parent} .required`).remove();
                                                                    $('#addCandidateForm ul.nav-tabs li').each(function(i) {
                                                                        var href = $('a', this).attr("href");
                                                                        var text = $('a', this).text();
                                                                        var teb_id = $('a', this).data("id");
                                                                        if (!$.isEmptyObject(data?.errors[teb_id]?.errors)) {
                                                                            $(`[data-id~="${teb_id}"]`, this).append(
                                                                                ' <small class="required">***</small>');
                                                                            $("small.required").css({
                                                                                "color": "red",
                                                                                'fontSize': '1.5em'
                                                                            });
                                                                            printErrorMsg(parent, data?.errors[teb_id]?.errors);
                                                                        }
                                                                    });
                                                                }

                                                            },
                                                            complete: function() {
                                                                i--;
                                                                if (i <= 0) {
                                                                    $(".preloader").fadeOut();
                                                                }
                                                                $("#save-candidate").prop('disabled', false);
                                                            },
                                                        });
                                                    });

                                                    /********* ADD NEW Candidate end************/



                                                    /*****  Edit Candidate subject and sponsor *******/
                                                    $(document).on("click", ".edit-candidate", function() {
                                                        var url = $(this).data("action");
                                                        var update_btn = $(this).attr("update");
                                                        // data-target=".edit-candidate"
                                                        var i = 0;

                                                        $.ajaxSetup({
                                                            headers: {
                                                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                                            }
                                                        });
                                                        $.ajax({
                                                            url: url,
                                                            cache: false,
                                                            method: "GET",
                                                            beforeSend: function() {
                                                                // setting a timeout
                                                                $(".preloader").fadeIn();
                                                                i++;
                                                            },
                                                            success: function(data) {

                                                                $(".select-readonly").remove();
                                                                var parent = "#edit-form";
                                                                $(`${parent} input, ${parent} select, textarea`).each(function(index) {
                                                                    $(`${parent} .help-block`).remove();
                                                                    $(`${parent} .has-error`).removeClass('has-error');
                                                                });
                                                                $(`${parent} .required`).remove();
                                                                var candidate = data.candidate;
                                                                var guardian = data.guardian === null ? {} : data.guardian;
                                                                var paid_fee = data.paid_fee === null ? {} : data.paid_fee;
                                                                var editable_fields = data.editable_fields;
                                                                var is_editable = data.editable;
                                                                $(".select-readonly").remove();
                                                                //Candidate
                                                                $(`form${parent} #candidate_registration input,form${parent} #candidate_registration select, form${parent} #candidate_registration textarea`)
                                                                    .each(
                                                                        function(index) {
                                                                            var input = $(this);
                                                                            var type = input.prop('type');
                                                                            var name = input.attr('name');
                                                                            var readonlySelects = [];

                                                                            if (type == "select-one") {
                                                                                $(`form${parent} #candidate_registration [name='${name}']`).val(
                                                                                    candidate[name])
                                                                                if (readonlySelects.indexOf(name) >= 0) {
                                                                                    setReadonly(`form${parent} [name='${name}']`);
                                                                                } else if (name == 'gender') {
                                                                                    if (is_editable) {
                                                                                        $(`.${name}-readonly`).remove(); //remove Div element
                                                                                        $(`form${parent} #candidate_registration [name='${name}']`)
                                                                                            .show()
                                                                                    } else {
                                                                                        setReadonly(
                                                                                            `form${parent} #candidate_registration [name='${name}']`
                                                                                        );
                                                                                    }

                                                                                }
                                                                            } else {
                                                                                if (is_editable && (editable_fields.indexOf(name) >= 0)) {
                                                                                    console.log(editable_fields);
                                                                                    if (name == 'national_id') {
                                                                                        $(`form${parent} #candidate_registration [name='${name}']`)
                                                                                            .val(candidate[name]);
                                                                                        $(`form${parent} #candidate_registration [name='${name}']`)
                                                                                            .removeAttr("readonly");

                                                                                    } else {
                                                                                        $(`form${parent} #candidate_registration [name='${name}']`)
                                                                                            .val(candidate[name]);
                                                                                        $(`form${parent} #candidate_registration [name='${name}']`)
                                                                                            .removeAttr("readonly");
                                                                                    }
                                                                                } else {
                                                                                    if (name == 'national_id') {
                                                                                        $(`form${parent} #candidate_registration [name='${name}']`)
                                                                                            .val(candidate[name]);
                                                                                        $(`form${parent} #candidate_registration [name='${name}']`)
                                                                                            .removeAttr("readonly");

                                                                                    } else {
                                                                                        $(`form${parent} #candidate_registration [name='${name}']`)
                                                                                            .attr("readonly",
                                                                                                "readonly")
                                                                                        $(`form${parent} #candidate_registration [name='${name}']`)
                                                                                            .val(candidate[name]);
                                                                                    }
                                                                                }

                                                                            }

                                                                        }
                                                                    );

                                                                //contact_address_Info
                                                                $(`form${parent} #contact_address_Info input,form${parent} #contact_address_Info select, form${parent} #contact_address_Info textarea`)
                                                                    .each(
                                                                        function(index) {
                                                                            var input = $(this);
                                                                            var type = input.prop('type');
                                                                            var name = input.attr('name');
                                                                            $(`form${parent} #contact_address_Info [name='${name}']`).val(
                                                                                candidate[name])

                                                                        }
                                                                    );
                                                                //guardian
                                                                $(`form${parent} #guardian input,form${parent} #guardian select, form${parent} #guardian textarea`)
                                                                    .each(
                                                                        function(index) {
                                                                            var input = $(this);
                                                                            var type = input.prop('type');
                                                                            var guardian_prifix_length = "guardian_".length;
                                                                            var name = input.attr('name').slice(guardian_prifix_length);
                                                                            $(`form${parent} #guardian [name='guardian_${name}']`)
                                                                                .val(guardian.hasOwnProperty(name) ? guardian[name] : '')

                                                                        }
                                                                    );


                                                                // Paid
                                                                $(`form${parent} [name='amount']`).val(paid_fee);
                                                                $(`form${parent} .center-subjects`).html(data[0].subjectsHTML);
                                                                $("#edit-form").attr('action', data.action);
                                                                $(".edit-candidate-modal").modal("show");
                                                            },
                                                            complete: function() {
                                                                i--;
                                                                if (i <= 0) {
                                                                    $(".preloader").fadeOut();
                                                                }
                                                            },
                                                        });
                                                    });
                                                    /*****  Edit Candidate subject and sponsor *******/

                                                    /*****  Update Candidate subject and sponsor*******/

                                                    $(document).on("click", "#update-candidate", function(ev) {
                                                        ev.preventDefault();
                                                        // var activeTab = url.substring(url.indexOf("#") + 1);

                                                        //nav-tabs
                                                        var tabs = {}
                                                        $('#edit-form ul.nav-tabs li').each(function(i) {
                                                            var href = $('a', this).attr("href");
                                                            var teb_id = $('a', this).data("id")
                                                            var tebData = $(`#edit-form ${href}`).find('select, textarea, input').serialize();
                                                            tabs[teb_id] = tebData;
                                                            console.log(tebData);
                                                        });
                                                        var action = $("#edit-form").attr('action');
                                                        $.ajaxSetup({
                                                            headers: {
                                                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                                            }
                                                        });
                                                        $.ajax({
                                                            url: action,
                                                            method: "PUT",
                                                            data: {
                                                                'tabs': tabs
                                                            },
                                                            success: function(data) {


                                                                if ($.isEmptyObject(data.errors)) {
                                                                    $('#edit-form').trigger("reset");
                                                                    $('#amend-datatable').DataTable().ajax.reload(null, false);
                                                                    $(".edit-candidate-modal").modal("hide");
                                                                    toastr.success(data.success);

                                                                } else {

                                                                    var parent = "#edit-form";
                                                                    $(`${parent} input, ${parent} select, textarea`).each(function(index) {
                                                                        $(`${parent} .help-block`).remove();
                                                                        $(`${parent} .has-error`).removeClass('has-error');
                                                                    });
                                                                    $(`${parent} .required`).remove();

                                                                    $('ul.nav-tabs li').each(function(i) {
                                                                        var href = $('a', this).attr("href");
                                                                        var text = $('a', this).text();
                                                                        var teb_id = $('a', this).data("id");
                                                                        if (!$.isEmptyObject(data?.errors[teb_id]?.errors)) {
                                                                            $(`[data-id~="${teb_id}"]`, this).append(
                                                                                ' <small class="required">***</small>');
                                                                            $("small.required").css({
                                                                                "color": "red",
                                                                                'fontSize': '1.5em'
                                                                            });
                                                                            printErrorMsg('#edit-form', data?.errors[teb_id]?.errors);
                                                                        }
                                                                    });






                                                                }
                                                            },
                                                        });
                                                    });
                                                    /*****  End Update Candidate subject and sponso *******/



                                                    // Add dynamic input function end
                                                    /*****  Delete Candidate start *******/
                                                    $(document).on("click", ".delete-candidate", function() {
                                                        var url = $(this).data("action");
                                                        if (confirm("Are You sure want to delete this candidate ?")) {
                                                            $.ajaxSetup({
                                                                headers: {
                                                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                                                }
                                                            });
                                                            $.ajax({
                                                                type: "DELETE",
                                                                url: url,
                                                                success: function(data) {
                                                                    $('#amend-datatable').DataTable().ajax.reload(null, false);
                                                                    toastr.success('Successfully deleted the record');
                                                                },
                                                            });
                                                        }

                                                    });
                                                    /*****  Delete Candidate End *******/
                                                </script>
                                            @endpush

                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="tab-bottom-left3">
                                        <div id="subject-per-center" class="table-responsive">
                                            <div class="animated-wrapper">
                                                <div class="loader">Please wait...</div>
                                            </div>
                                        </div>

                                    </div>



                                </div>
                                <!-- END TABBED CONTENT -->
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
@section('script')
    <script>
        /*-----------------------------------/
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    /*Diplay candidates
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    /*----------------------------------*/

        /********** Some Variable Initial Value **************/

        var level = $("#level").val();
        var session = $("#session").val();
        var center = $("#center").val();
        var sponsor = $("#sponsor").val();
        var year = $("#year").val();
        var subject = $("#subjects").val();
        var type = $("#type").val();
        var filter = $("#filter").val();

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
            center = $(this).val();
            year = $("#year").val();
            subject = $("#subjects").val();
            type = $("#type").val();
            filter = $("#filter").val();
            sponsor = $("#sponsor").val();


            candidate_per_center(
                level,
                session,
                center,
                filter,
                year,
                sponsor,
                subject,
                type,
                event = event
            );


            $('#amend-datatable').DataTable().ajax.reload(null, false);
        });
        /**********  Candidates Sorting End    **************/

        /**********  Candidates filter Start    **************/
        $("#session").on("change",
            function() {
                $('#center').html(`<option value="">
                                  Please Select Center
                              </option>`);
                level = $("#level").val();
                var session = $("#session").val();
                center = $("#center").val();
                year = $("#year").val();
                filter = $("#filter").val();
                sponsor = $("#sponsor").val();
                var subject = $("#subjects").val();
                var type = $("#type").val();


                candidate_per_center(
                    level,
                    session,
                    center,
                    filter,
                    year,
                    sponsor,
                    subject,
                    type,
                    event = null
                );
                $('#amend-datatable').DataTable().ajax.reload(null, false);

            });
        /**********  Candidates filter  End    **************/
        /**********  Candidates filter Start    **************/
        $("#filter").on("change", function() {
            level = $("#level").val();
            session = $("#session").val();
            center = $("#center").val();
            year = $("#year").val();
            subject = $("#subjects").val();
            type = $("#type").val();
            filter = $(this).val();
            sponsor = $("#sponsor").val();


            candidate_per_center(
                level,
                session,
                center,
                filter,
                year,
                sponsor,
                subject,
                type,
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
            center = $("#center").val();
            year = $("#year").val();
            filter = $("#filter").val();
            sponsor = $("#sponsor").val();
            type = $("#type").val();
            candidate_per_center(
                level,
                session,
                center,
                filter,
                year,
                sponsor,
                subject,
                type,
                event = null
            );
            $('#amend-datatable').DataTable().ajax.reload(null, false);
        });
        /**********  Candidates Main Search End   **************/

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
            center = $("#center").val();
            var year = $("#year").val();
            filter = $("#filter").val();
            sponsor = $("#sponsor").val();
            type = $("#type").val();
            filter = $(this).val();
            candidate_per_center(
                level,
                session,
                center,
                filter,
                year,
                sponsor,
                subject,
                type,
                event = null
            );
            $('#amend-datatable').DataTable().ajax.reload(null, false);
        });
        /**********  Candidates Main Search End   **************/


        /**********  Candidates Main Search Start    **************/
        $("#sponsor").on("change", function() {
            $('#center').html(`<option value="">
                                  Please Select Center
                              </option>
                              <option value="All">
                                  Please Select Center
                              </option>`);

            level = $("#level").val();
            session = $("#session").val();
            center = $("#center").val();
            year = $("#year").val();
            filter = $("#filter").val();
            sponsor = $(this).val();
            type = $("#type").val();
            subject = $("#subjects").val();
            candidate_per_center(
                level,
                session,
                center,
                filter,
                year,
                sponsor,
                subject,
                type,
                event = null
            );
            $('#amend-datatable').DataTable().ajax.reload(null, false);
        });
        /**********  Candidates Main Search End   **************/




        /**********  Candidates Main Search Start    **************/
        $("#type").on("change", function() {
            $('#center').html(`<option value="">
                                  Please Select Center
                              </option>
                              <option value="All">
                                  Please Select Center
                              </option>`);

            level = $("#level").val();
            session = $("#session").val();
            center = $("#center").val();
            year = $("#year").val();
            filter = $("#filter").val();
            sponsor = $("#sponsor").val();
            type = $(this).val();
            subject = $("#subjects").val();
            candidate_per_center(
                level,
                session,
                center,
                filter,
                year,
                sponsor,
                subject,
                type,
                event = null
            );
            $('#amend-datatable').DataTable().ajax.reload(null, false);
        });
        /**********  Candidates Main Search End   **************/

        /**********  Candidates Main Search Start    **************/
        $("#subjects").on("change", function(event) {
            $('#center').html(`<option value="">
                                  Please Select Center
                              </option>
                              <option value="All">
                                  Please Select Center
                              </option>`);

            level = $("#level").val();
            session = $("#session").val();
            center = $("#center").val();
            year = $("#year").val();
            filter = $("#filter").val();
            sponsor = $("#sponsor").val();
            type = $("#type").val();
            subject = $(this).val();
            candidate_per_center(
                level,
                session,
                center,
                filter,
                year,
                sponsor,
                subject,
                type,
                event = event
            );
            $('#amend-datatable').DataTable().ajax.reload(null, false);
        });
        /**********  Candidates Main Search End   **************/

        /*****  Subjects For Center *******/
        $(document).on("click", ".center-subjects  input", function() {
            if ($(this).prop("checked")) {
                var input_classes = $(this).attr("class").split(" ");
                var className;
                $.each(input_classes, function() {
                    if (this.toLowerCase().indexOf("subj_") >= 0) className = this;
                });
                $("." + className).prop("checked", false);
                $(this).prop("checked", true);
            } else {
                var input_classes = $(this).attr("class").split(" ");
                var className;
                $.each(input_classes, function() {
                    if (this.toLowerCase().indexOf("subj_") >= 0) className = this;
                });
                $("." + className).prop("checked", false);
                $(this).prop("checked", false);
            }

        });
        /*****  Retrieve Value When Page First Load  *******/

        candidate_per_center(
            level,
            session,
            center,
            filter,
            year,
            sponsor,
            subject,
            type,
        );
        /****  AJAX Main Function Who Perform All Tasks Start *******/
        function candidate_per_center(
            level,
            session,
            center,
            filter,
            year,
            sponsor,
            subject,
            type,
            event = null
        ) {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            var i = 0;
            $.ajax({
                url: "{{ route('admin.candidate-registation.registerdCandidates') }}",
                method: "POST",
                data: {
                    level: level,
                    session: session,
                    center: center,
                    filter: filter,
                    year: year,
                    sponsor: sponsor,
                    subject: subject,
                    type: type,
                },
                beforeSend: function() {
                    // setting a timeout
                    $(".animated-wrapper").fadeIn();
                    i++;
                },
                success: function(data) {
                    console.log(data);

                    $("#candidates-per-center").html(data.cendidate_per_center);

                    var centers = data.centers
                    var subjects = data.subjects


                    if (event == null) {
                        $('#subjects').html(`<option value="">
                                  Please Select subject
                              </option>`);
                        $('#center').html(`<option value="">
                                  Please Select Center
                              </option>`);

                        centers.forEach(center => {
                            $('#center').append($('<option>').val(center.center_no).text(center
                                .center_no +
                                ' - ' + center.center_name));
                        });


                        subjects.forEach(subject => {
                            $('#subjects').append($('<option>').val(subject.subject_code).text(subject
                                .subject_code +
                                ' - ' + subject.subject_name));
                        });

                    }


                },
                complete: function() {
                    i--;
                    if (i <= 0) {
                        $(".animated-wrapper").fadeOut();
                    }
                },

            });
        }


        function centerSubjects(form) {
            var centre_no = $(`#${form} #center_no`).find("option:selected").val();
            var level = $(`#${form}  #level`).find("option:selected").data("level");
            var session = $(`#${form} #session`).find("option:selected").data("session");

            console.log(centre_no);
            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
            });
            $.ajax({
                url: "{{ route('admin.candidates.center_subjects') }}",
                method: "POST",
                data: {
                    centre_no: centre_no,
                    level: level,
                    session: session
                },
                success: function(data) {
                    $(".center-subjects").html(data.subjectsHTML);
                    $("#save-candidate").prop('disabled', false);

                },
            });
        }
        /****  AJAX Main Function Who Perform All Tasks End *******/
        function setReadonly(selectElement) {

            $(`${selectElement}`).each(function() {
                var selectElement = $(this);
                var parent = selectElement.parent();
                var textValue = selectElement.find(":selected").text();
                if (!parent.length) {
                    parent = selectElement.parent();
                    textValue = selectElement.find(":selected").text();
                }
                var input = $("<input>");
                input.attr("id", selectElement.attr("id"));


                input.attr("type", "text");
                input.attr("value", textValue.trim());
                input.css({
                    background: "#eee",
                    opacity: 1
                });
                var classReadonly = selectElement.attr("id");
                input.addClass(`form-control select-readonly ${classReadonly}-readonly`);
                input.attr("readonly", true);
                parent.append(input);
                selectElement.hide();
            });
        }
        // /****  Print errors*******/
        function printErrorMsg(parent, msg) {

            $.each(msg, function(key, errors) {
                for (const error in errors) {
                    const value = errors[error];
                    $(`${parent} [name='${key}']`).parent().addClass('has-error');
                    $(`<span class='help-block'>${value}</span>`).insertAfter(`${parent} [name='${key}']`);
                    if (key == 'subjects' || key == 'subject') {
                        $(".subjects-errors").find('span').css({
                            "color": "#ff0000"
                        }).html(`<strong>${value}</strong>`);
                    }

                }
            });
        }
        /****  Print errors End*******/
    </script>

    <!--ADD CANDIDATE MODEL -->
    <div class="modal fade bd-modal-md" id="add-candidate" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close resetform" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h3 class="modal-title"> Add Candidate </h3>
                </div>
                <div class="modal-body">
                    <form action="" method="post" id="addCandidateForm">

                        <ul class="nav nav-tabs nav-justified nav-inline">
                            <li class="active"><a href="#candidate_registration_new" data-id='1'
                                    data-toggle="tab">Candidate
                                    Registration</a></li>
                            <li><a href="#contact_address_Info_new" data-id='2' data-toggle="tab">Contact & Address
                                    Info</a></li>
                            <li><a href="#guardian_new" data-id='3' data-toggle="tab">Next of Kin</a></li>

                        </ul>
                        <div class="tab-content">
                            @csrf
                            <div class="tab-pane active" id="candidate_registration_new">

                                <div class="form-group col-md-4">
                                    <label for="level" class="control-label">Registration
                                        Level</label>
                                    <select name="level" class="form-control" id="level">
                                        <option value=" ">Select Registration
                                            Level</option>
                                        @foreach ($levels as $level)
                                            <option value="{{ $level->level }}" data-level="{{ $level->id }}">
                                                {{ $level->level }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group  col-md-4">
                                    <label for="center_no" class="control-label">Center</label>
                                    <select name="center_no" class="form-control" id="center_no">
                                        @foreach ($centers as $center)
                                            <option value="{{ $center->center_no }}">
                                                {{ $center->center_no }}-{{ $center->center_name }}</option>
                                        @endforeach
                                    </select>


                                </div>
                                <div class="form-group col-md-4">
                                    <label for="session" class="control-label">Registration
                                        Session</label>
                                    <select class="form-control" name="session" id="session">
                                        @foreach ($sessions as $session)
                                            <option value="{{ $session->session }}" data-session="{{ $session->id }}">
                                                {{ $session->description }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="candidate_no" class="control-label">Candidate Number</label>
                                    <input type="text" class="form-control " placeholder="Enter Candidate Number"
                                        name="candidate_no" id="candidate_no">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="national_id" class="control-label">National Id</label>
                                    <input type="text" class="form-control" placeholder="Enter National ID"
                                        name="national_id" id="national_id">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="candidate_surname" class="control-label">Surname</label>
                                    <input type="text" class="form-control" placeholder="Enter Candidate Surname"
                                        name="candidate_surname" id="candidate_surname">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="candidate_other_name" class="control-label">Other name</label>
                                    <input type="text" class="form-control " placeholder="Enter Other_name"
                                        name="candidate_other_name" id="candidate_other_name">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="date_of_birth" class="control-label">Date of birth</label>
                                    <input type="date" class="form-control " y placeholder="Enter date of birth"
                                        name="date_of_birth" id="date_of_birth">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="gender" class="control-label">Gender</label>
                                    <select name="gender" class="form-control" id="gender">
                                        <option value=" ">Please Select Gender</option>
                                        <option value="M">Male</option>
                                        <option value="F">Female</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="special_need" class="control-label">Special need</label>
                                    <select name="special_need" class="form-control" id="special_need">
                                        <option value=" ">Please Special need</option>
                                        @foreach ($specialNeeds as $specialNeed)
                                            <option value="{{ $specialNeed->id }}">{{ $specialNeed->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="sponser" class="control-label">Sponsor</label>
                                    <select name="sponser" class="form-control" id="sponsor">
                                        <option value=" ">Please Select sponsor</option>
                                        @foreach ($sponsors as $sponsor)
                                            <option value="{{ $sponsor->sponser }}">{{ $sponsor->sponser }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="type" class="control-label">Type</label>
                                    <select name="type" class="form-control" id="type">
                                        <option value="">Please Select type</option>
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-12  center-subjects">
                                </div>
                                <div class="form-row text-center clearfix subjects-errors"><span></span>
                                </div>

                            </div>
                            <div class="tab-pane" id="contact_address_Info_new">
                                <fieldset class="row  fieldset-border">
                                    <legend class="fieldset-border">Contacts</legend>
                                    <div class="form-group col-md-12">
                                        <label for="phone_number">Phone Number</label>
                                        <input type="text" class="form-control" name="phone_number" id="phone_number"
                                            value=" ">

                                    </div>
                                    <div class="form-group col-md-12">
                                        <label for="email">Email</label>
                                        <input type="text" class="form-control" name="email" id="email"
                                            value="">

                                    </div>
                                </fieldset>
                                <fieldset class="row  fieldset-border">
                                    <legend class="fieldset-border">Address</legend>
                                    <div class="form-group col-md-6">
                                        <label for="postal_address">Postal
                                            Address </label>
                                        <input type="text" class="form-control" id="postal_address"
                                            name="postal_address">

                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="physical_address">Physical
                                            Address</label>
                                        <input type="text" class="form-control" id="physical_address"
                                            name="physical_address">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="village">Village</label>
                                        <input type="text" class="form-control" id="village" name="village">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="candidate_district">District</label>
                                        <select class="form-control" name="district" id="district">
                                            @foreach ($districts as $district)
                                                <option value="{{ $district->district }}">
                                                    {{ $district->district }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                </fieldset>
                            </div>
                            <div class="tab-pane" id="guardian_new">
                                <fieldset class="row  fieldset-border">
                                    <legend class="fieldset-border">Personal Information</legend>
                                    <div class="form-group col-md-12">
                                        <label for="guardian_type">Relationship
                                            Between</label>
                                        <select name="guardian_type" class="form-control" id="guardian_type">
                                            <option value="">Please select
                                                relationship</option>
                                            @foreach ($guardian_types as $guardian_type)
                                                <option value="{{ $guardian_type->id }}">
                                                    {{ $guardian_type->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label for="guardian_national_id">National Id</label>
                                        <input type="text" class="form-control" value="{{ time() }}"
                                            id="guardian_national_id" name="guardian_national_id"
                                            placeholder="national id">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="guardian_name">Other
                                            Names</label>
                                        <input type="text" class="form-control form-control-sm" id="guardian_name"
                                            name="guardian_name" placeholder="Name">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="guardian_surname">Surname</label>
                                        <input type="text" class="form-control" id="guardian_surname"
                                            name="guardian_surname" placeholder="Surname">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="guardian_email">Email</label>
                                        <input type="text" class="form-control form-control-sm" id="guardian_email"
                                            name="guardian_email" placeholder="Email">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="guardian_phone">Phone
                                            Number</label>
                                        <input type="text" class="form-control form-control-sm"
                                            id="guardian_phone_number" name="guardian_phone_number"
                                            placeholder="Phone Number">
                                    </div>
                                </fieldset>

                                <fieldset class="row  fieldset-border">
                                    <legend class="fieldset-border">Address</legend>
                                    <div class="form-group col-md-6">
                                        <label for="guardian_postal_address">Postal
                                            Address </label>
                                        <input type="text" class="form-control form-control-sm"
                                            id="guardian_postal_address" name="guardian_postal_address"
                                            placeholder="P.O.Box 2398">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="guardian_physical_address">Physical
                                            Address</label>
                                        <input type="text" class="form-control form-control-sm"
                                            id="guardian_physical_address" name="guardian_physical_address"
                                            placeholder="Qoaling">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="guardian_village">Village</label>
                                        <input type="text" class="form-control form-control-sm" id="guardian_village"
                                            name="guardian_village" placeholder="Ha Seoli">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="guardian_district">District</label>
                                        <select class="form-control form-control-sm" name="guardian_district"
                                            id="guardian_district">
                                            <option value="">Please Select
                                                District</option>
                                            @foreach ($districts as $district)
                                                <option value="{{ $district->district }}">
                                                    {{ $district->district }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </fieldset>
                            </div>

                        </div>


                    </form>

                </div>
                <div class="modal-footer">
                    <button type="submit" name="add_user" class="btn btn-primary" disabled
                        id="save-candidate">Save</button>
                    <button type="button" class="btn btn-danger resetform" id="adduser_close"
                        data-dismiss="modal">Close</button>

                </div>
            </div>
        </div>

    </div>
    <!--END ADD CANDIDATE MODEL -->


    <!-- UPDATE MODEL CANDIDATE -->
    <div class="modal fade edit-candidate-modal" id="update_form" tabindex="-1" role="dialog"
        aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h5 class="modal-title"> Update Candidate</h5>
                </div>
                <div class="modal-body">
                    <form action="" method="post" id="edit-form">
                        <div class="row">
                            @method('PUT')
                            @csrf
                            <ul class="nav nav-tabs nav-justified nav-inline">
                                <li class="active"><a href="#candidate_registration" data-id='1'
                                        data-toggle="tab">Candidate
                                        Registration</a></li>
                                <li><a href="#contact_address_Info" data-id='2' data-toggle="tab">Contact & Address
                                        Info</a></li>
                                <li><a href="#guardian" data-id='3' data-toggle="tab">Next of Kin</a></li>
                                <li><a href="#exam_fees" data-id='4' data-toggle="tab">Exams Fees</a></li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane active" id="candidate_registration">
                                    <div class="form-group col-md-4">
                                        <label for="level" class="control-label">Registration
                                            Level</label>
                                        <select name="level" class="form-control" id="level">
                                            <option value=" ">Select Registration
                                                Level</option>
                                            @foreach ($levels as $level)
                                                <option value="{{ $level->level }}" data-level="{{ $level->id }}">
                                                    {{ $level->level }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group  col-md-4">
                                        <label for="center_no" class="control-label">Center</label>
                                        <select name="center_no" class="form-control" id="center_no">
                                            @foreach ($centers as $center)
                                                <option value="{{ $center->center_no }}">
                                                    {{ $center->center_no }}-{{ $center->center_name }}</option>
                                            @endforeach
                                        </select>
                                        <span class="text-danger error-text center_no_error"></span>

                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="session" class="control-label">Registration
                                            Session</label>
                                        <select class="form-control" name="session" id="session">
                                            @foreach ($sessions as $session)
                                                <option value="{{ $session->session }}"
                                                    data-session="{{ $session->id }}">
                                                    {{ $session->description }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="candidate_no" class="control-label">Candidate Number</label>
                                        <input type="text" class="form-control " readonly
                                            placeholder="Enter Candidate Number" name="candidate_no" id="candidate_no">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="national_id" class="control-label">National Id</label>
                                        <input type="text" class="form-control" readonly
                                            placeholder="Enter National ID" name="national_id" id="national_id">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="candidate_surname" class="control-label">Surname</label>
                                        <input type="text" class="form-control" readonly
                                            placeholder="Enter Candidate Surname" name="candidate_surname"
                                            id="candidate_surname">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="candidate_other_name" class="control-label">Other name</label>
                                        <input type="text" class="form-control " readonly
                                            placeholder="Enter Other_name" name="candidate_other_name"
                                            id="candidate_other_name">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="date_of_birth" class="control-label">Date of birth</label>
                                        <input type="date" class="form-control " readonly
                                            placeholder="Enter date of birth" name="date_of_birth" id="date_of_birth">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="gender" class="control-label">Gender</label>
                                        <select name="gender" class="form-control" id="gender">
                                            <option value=" ">Please Select Gender</option>
                                            <option value="M">Male</option>
                                            <option value="F">Female</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="special_need" class="control-label">Special need</label>
                                        <select name="special_need" class="form-control" id="special_need">
                                            <option value=" ">Please Special need</option>
                                            @foreach ($specialNeeds as $specialNeed)
                                                <option value="{{ $specialNeed->id }}">{{ $specialNeed->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="sponsor" class="control-label">Sponsor</label>
                                        <select name="sponser" class="form-control" id="sponsor">
                                            <option value=" ">Please Select sponsor</option>
                                            @foreach ($sponsors as $sponsor)
                                                <option value="{{ $sponsor->sponser }}">{{ $sponsor->sponser }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="type" class="control-label">Type</label>
                                        <select name="type" class="form-control" id="type">
                                            <option value="">Please Select type</option>
                                            <option value="1">1</option>
                                            <option value="2">2</option>
                                            <option value="3">3</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-12  center-subjects">
                                    </div>
                                    <div class="form-row text-center clearfix subjects-errors"><span></span>
                                    </div>

                                </div>
                                <div class="tab-pane" id="contact_address_Info">
                                    <fieldset class="row  fieldset-border">
                                        <legend class="fieldset-border">Contacts</legend>
                                        <div class="form-group col-md-12">
                                            <label for="phone_number">Phone Number</label>
                                            <input type="text" class="form-control" name="phone_number"
                                                id="phone_number" value=" ">

                                        </div>
                                        <div class="form-group col-md-12">
                                            <label for="email">Email</label>
                                            <input type="text" class="form-control" name="email" id="email"
                                                value="">

                                        </div>
                                    </fieldset>
                                    <fieldset class="row  fieldset-border">
                                        <legend class="fieldset-border">Address</legend>
                                        <div class="form-group col-md-6">
                                            <label for="postal_address">Postal
                                                Address </label>
                                            <input type="text" class="form-control" id="postal_address"
                                                name="postal_address">

                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="physical_address">Physical
                                                Address</label>
                                            <input type="text" class="form-control" id="physical_address"
                                                name="physical_address">
                                        </div>

                                        <div class="form-group col-md-6">
                                            <label for="village">Village</label>
                                            <input type="text" class="form-control" id="village" name="village">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="candidate_district">District</label>
                                            <select class="form-control" name="district" id="district">
                                                @foreach ($districts as $district)
                                                    <option value="{{ $district->district }}">
                                                        {{ $district->district }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                    </fieldset>
                                </div>
                                <div class="tab-pane" id="guardian">
                                    <fieldset class="row  fieldset-border">
                                        <legend class="fieldset-border">Personal Information</legend>
                                        <div class="form-group col-md-12">
                                            <label for="guardian_type">Relationship
                                                Between</label>
                                            <select name="guardian_type" class="form-control" id="guardian_type">
                                                <option value="">Please select
                                                    relationship</option>
                                                @foreach ($guardian_types as $guardian_type)
                                                    <option value="{{ $guardian_type->id }}">
                                                        {{ $guardian_type->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-md-12">
                                            <label for="guardian_national_id">National Id</label>
                                            <input type="text" class="form-control" id="guardian_national_id"
                                                name="guardian_national_id" placeholder="national id">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="guardian_name">Other
                                                Names</label>
                                            <input type="text" class="form-control form-control-sm" id="guardian_name"
                                                name="guardian_name" placeholder="Name">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="guardian_surname">Surname</label>
                                            <input type="text" class="form-control" id="guardian_surname"
                                                name="guardian_surname" placeholder="Surname">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="guardian_email">Email</label>
                                            <input type="text" class="form-control form-control-sm"
                                                id="guardian_email" name="guardian_email" placeholder="Email">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="guardian_phone">Phone
                                                Number</label>
                                            <input type="text" class="form-control form-control-sm"
                                                id="guardian_phone_number" name="guardian_phone_number"
                                                placeholder="Phone Number">
                                        </div>
                                    </fieldset>

                                    <fieldset class="row  fieldset-border">
                                        <legend class="fieldset-border">Address</legend>
                                        <div class="form-group col-md-6">
                                            <label for="guardian_postal_address">Postal
                                                Address </label>
                                            <input type="text" class="form-control form-control-sm"
                                                id="guardian_postal_address" name="guardian_postal_address"
                                                placeholder="P.O.Box 2398">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="guardian_physical_address">Physical
                                                Address</label>
                                            <input type="text" class="form-control form-control-sm"
                                                id="guardian_physical_address" name="guardian_physical_address"
                                                placeholder="Qoaling">
                                        </div>

                                        <div class="form-group col-md-6">
                                            <label for="guardian_village">Village</label>
                                            <input type="text" class="form-control form-control-sm"
                                                id="guardian_village" name="guardian_village" placeholder="Ha Seoli">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="guardian_district">District</label>
                                            <select class="form-control form-control-sm" name="guardian_district"
                                                id="guardian_district">
                                                <option value="">Please Select
                                                    District</option>
                                                @foreach ($districts as $district)
                                                    <option value="{{ $district->district }}">
                                                        {{ $district->district }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </fieldset>
                                </div>
                                <div class="tab-pane" id="exam_fees">
                                    <fieldset class="row  fieldset-border">
                                        <legend class="fieldset-border">Fee</legend>
                                        <div class="form-group col-md-12">
                                            <label for="amount">Paid Fee</label>
                                            <input type="text" class="form-control" id="amount" name="amount"
                                                readonly>
                                        </div>
                                    </fieldset>
                                </div>
                            </div>
                        </div>
                        <div class="row">

                        </div>
                    </form>
                </div>

                <div class="modal-footer">
                    <button type="submit" name="save_updates" class="btn btn-primary"
                        id="update-candidate">Update</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>

                </div>

            </div>
        </div>
    </div>
    <!-- UPDATE MODEL CANDIDATE END -->
@endsection
