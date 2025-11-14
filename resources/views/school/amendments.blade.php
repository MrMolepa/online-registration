@extends('layouts.school')

@section('content')
    <div id="page-wrapper">
        <div class="header">
            <h1 class="page-header">
                Amend Candidates
                <!--<small>Welcome John Doe</small>-->
            </h1>

            <ol class="breadcrumb">
                <li><a href="javascript:void();">Home</a></li>
                <li class="active"><a href="javascript:void();">Amend Entries</a></li>
            </ol>
        </div>

        <div id="page-inner" class="amend_candidates">
            <div class="row">
                <div class="col-md-12">
                    <!-- Advanced Tables -->
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Amend Entries
                        </div>
                        <div class="panel-body">

                            <div class="form-group">
                                <label for="level_filter">Filter By Level</label>
                                <select id="level_filter" name="level_filter" class="form-control">
                                    <option value=" ">Please Select Level</option>
                                    @foreach ($levels as $level)
                                        <option value="{{ $level->level }}">
                                            {{ $level->level }}</option>
                                    @endforeach
                                </select>

                            </div>
                            <div>

                                @if (is_activate($center->level))
                                    @permission('amendments-create')
                                        <button type="button" data-toggle="modal" data-target="#add-candidate"
                                            class="btn  btn-primary">+
                                            Candidate</button>
                                        <button type="button" data-toggle="modal" data-target=".import-csv-registration-modal"
                                            class=" btn btn-primary mx-4">+
                                            Candidates by CSV</button>
                                    @endpermission
                                    @permission('amendments-delete')
                                        <button class="btn btn-danger pull-right btn-delete-Selected">Delete bulk</button>
                                    @endpermission
                                @endif
                                <div class="clearfix"></div>
                            </div>
                            <br />
                            <div class="table-responsive amendcandidates">
                                <table class="table display compact" id="amend-datatable">
                                    <thead>
                                        <tr>
                                            <th><label><input type="checkbox" id="select-all"
                                                        name="select-all-candidates"></label></th>
                                            <th>National ID</th>
                                            <th>Candidate.No</th>
                                            <th>Surname</th>
                                            <th>Other Name </th>
                                            <th>Type</th>
                                            <th>Sponsor</th>
                                            <th>No.subjects</th>
                                            <th width="40%">Subjects</th>
                                            <th>Action</th>
                                            {{--  --}}
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
                                                dom: 'lBfrtip',
                                                deferRender: true,
                                                "lengthMenu": [
                                                    [10, 50, 100, 200, 1000, -1],
                                                    [10, 50, 100, 200, 1000, "All"]
                                                ],
                                                buttons: [{
                                                        extend: 'csv',
                                                        text: '<i class="far fa-file-excel"></i>',
                                                        className: 'btn btn-primary'
                                                    },
                                                    {
                                                        extend: 'pdf',
                                                        text: '<i class="fas fa-file-pdf"></i>',
                                                        className: 'btn btn-primary',
                                                        columns: [1, 2, 3, 4, 5, 6, 7, 8, 9]
                                                    },
                                                    {
                                                        extend: 'print',
                                                        text: '<i class="fas fa-print"></i>',
                                                        className: 'buttons-print',
                                                        columns: [1, 2, 3, 4, 5, 6, 7, 8, 9]
                                                    },
                                                    {
                                                        extend: 'colvis',
                                                        text: '<i class="fas fa-eye"></i>',
                                                        className: 'btn btn-view',
                                                        columns: [1, 2, 3, 4, 5, 6, 7, 8, 9]
                                                    }
                                                ],
                                                columnDefs: [{

                                                    'targets': [0, 8],
                                                    /* column index */

                                                    'orderable': false,
                                                    /* true or false */

                                                }],

                                                ajax: {
                                                    url: "{{ route('center.candidates.fatchAmendments') }}",
                                                    data: function(d) {
                                                        d.level_filter = $('#level_filter').val()
                                                    }
                                                },

                                                columns: [{
                                                        data: 'checkbox',
                                                        name: 'checkbox',

                                                        searchable: false
                                                    },
                                                    {
                                                        data: 'national_id',
                                                        name: 'center_candidate.national_id'
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
                                        /*****  Display candidates end*******/


                                        $(document).on("change", "#level_filter", function() {
                                            $('#amend-datatable').DataTable().ajax.reload(null, false);
                                        });

                                        $(document).on("dblclick", "input:radio", function() {
                                            if (this.checked) {
                                                this.checked = false;
                                            } else {
                                                this.checked = true;

                                            }
                                        });

                                        $('.modal').on('hidden.bs.modal', function(e) {
                                            $('form').trigger("reset");
                                        });






                                        /********** Search  Candidate Number And display info End**************/

                                        /********* ADD NEW Candidate ************/
                                        $(document).on("click", "#save-candidate", function() {
                                            var i = 0;
                                            $.ajaxSetup({
                                                headers: {
                                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                                                        'content')
                                                }
                                            });
                                            $.ajax({
                                                url: "{{ route('center.candidates.store') }}",
                                                cache: false,
                                                beforeSend: function() {
                                                    // setting a timeout
                                                    $(".preloader").fadeIn();
                                                    i++;
                                                },
                                                method: "POST",
                                                data: $("#addCandidateForm").serialize(),
                                                success: function(data) {
                                                    console.log(data);
                                                    if ($.isEmptyObject(data.errors)) {
                                                        $("#add-candidate").modal("hide");
                                                        toastr.success(
                                                            data.success
                                                        );
                                                        $('#amend-datatable').DataTable().ajax.reload();
                                                    } else {
                                                        printErrorMsg("#addCandidateForm", data.errors)
                                                    }

                                                },
                                                complete: function() {
                                                    i--;
                                                    if (i <= 0) {
                                                        $(".preloader").fadeOut();
                                                    }
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
                                                    console.log(data);

                                                    $(".select-readonly").remove();
                                                    var parent = "#edit-form";
                                                    var candidate = data.candidate;
                                                    var editable_fields = data.editable_fields;
                                                    var is_editable = data.editable;
                                                    $(`form${parent} input,form${parent} select, form${parent}  textarea`).each(
                                                        function(index) {
                                                            var input = $(this);
                                                            var type = input.prop('type');
                                                            var name = input.attr('name');
                                                            var readonlySelects = ['session', 'level'];
                                                            if (type == "select-one") {
                                                                $(`form${parent} [name='${name}']`).val(candidate[name])
                                                                if (readonlySelects.indexOf(name) >= 0) {
                                                                    setReadonly(`form${parent} [name='${name}']`);
                                                                } else if (name == 'gender') {
                                                                    if (is_editable) {
                                                                        $(`.${name}-readonly`).remove(); //remove Div element
                                                                        $(`form${parent} [name='${name}']`).show()
                                                                    } else {
                                                                        setReadonly(`form${parent} [name='${name}']`);
                                                                    }

                                                                }



                                                            } else {
                                                                if (is_editable && (editable_fields.indexOf(name) >= 0)) {
                                                                    console.log(editable_fields);
                                                                    if (name == 'national_id') {
                                                                        $(`form${parent} [name='${name}']`).val(candidate[name]);
                                                                        $(`form${parent} [name='${name}']`).removeAttr("readonly");

                                                                    } else {
                                                                        $(`form${parent} [name='${name}']`).val(candidate[name]);
                                                                        $(`form${parent} [name='${name}']`).removeAttr("readonly");
                                                                    }
                                                                } else {
                                                                    if (name == 'national_id') {
                                                                        $(`form${parent} [name='${name}']`).val(candidate[name]);
                                                                        $(`form${parent} [name='${name}']`).removeAttr("readonly");

                                                                    } else {
                                                                        $(`form${parent} [name='${name}']`).attr("readonly",
                                                                            "readonly")
                                                                        $(`form${parent} [name='${name}']`).val(candidate[name]);
                                                                    }
                                                                }


                                                            }

                                                        }
                                                    );
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
                                        $("#update-candidate").click(function(ev) {
                                            ev.preventDefault();
                                            var action = $("#edit-form").attr('action');
                                            $.ajaxSetup({
                                                headers: {
                                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                                }
                                            });
                                            $.ajax({
                                                url: action,
                                                method: "PUT",
                                                data: $("#edit-form").serialize(),
                                                success: function(data) {
                                                    console.log(data);
                                                    if ($.isEmptyObject(data.errors)) {
                                                        $('#edit-form').trigger("reset");
                                                        $('#amend-datatable').DataTable().ajax.reload(null, false);
                                                        $(".edit-candidate-modal").modal("hide");
                                                        toastr.success(data.success);

                                                    } else {
                                                        printErrorMsg('#edit-form', data.errors);
                                                    }
                                                },
                                            });
                                        });
                                        /*****  End Update Candidate subject and sponso *******/

                                        /*****  Show Candidate  *******/
                                        $(document).on("click", ".show-candidate", function() {
                                            $.ajaxSetup({
                                                headers: {
                                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                                }
                                            });
                                            var action = $(this).data("action");
                                            var i = 0;
                                            $.ajax({
                                                url: action,
                                                method: "GET",
                                                beforeSend: function() {
                                                    // setting a timeout
                                                    $(".preloader").fadeIn();
                                                    i++;
                                                },
                                                success: function(data) {

                                                    var parent = "#show-candidate";
                                                    var candidate = data.candidate === null ? {} : data.candidate;
                                                    var guardian = data.guardian === null ? {} : data.guardian;
                                                    var subjects = data.subjects === null ? {} : data.subjects;
                                                    var paid_fee = data.paid_fee === null ? {} : data.paid_fee;
                                                    $(".select-readonly").remove();
                                                    $(`form${parent} #candidate-information input,form${parent} #candidate-information select, form${parent} #candidate-information textarea`)
                                                        .each(
                                                            function(index) {
                                                                var input = $(this);
                                                                var type = input.prop('type');
                                                                var name = input.attr('name');
                                                                var readonlySelects = ['gender', 'session', 'level', 'sponser', 'type',
                                                                    'district'
                                                                ];
                                                                console.log(type);
                                                                if (type == "select-one") {
                                                                    $(`form${parent} [name='${name}']`).val(candidate[name])
                                                                    if (readonlySelects.indexOf(name) >= 0) {
                                                                        setReadonly(`form${parent} [name='${name}']`);
                                                                    }

                                                                } else {
                                                                    $(`form${parent} [name='${name}']`).val(candidate[name])

                                                                }

                                                            }
                                                        );
                                                    //guardian
                                                    $(`form${parent} #candidate-guardian input,form${parent} #candidate-guardian select, form${parent} #candidate-guardian textarea`)
                                                        .each(
                                                            function(index) {
                                                                var input = $(this);
                                                                var type = input.prop('type');
                                                                var guardian_prifix_length = "guardian_".length;
                                                                var name = input.attr('name').slice(guardian_prifix_length);
                                                                var readonlySelects = ['guardian_type', 'guardian_district'];
                                                                if (type == "select-one") {
                                                                    $(`form${parent} [name='guardian_${name}']`).val(guardian
                                                                        .hasOwnProperty(name) ? guardian[name] : '');
                                                                    if (name == "type") {
                                                                        var type = `guardian_${name}`
                                                                        $(`form${parent} [name='guardian_${name}']`).val(
                                                                            `${guardian[type]}`)
                                                                    }
                                                                    if (readonlySelects.indexOf(name) >= 0) {
                                                                        setReadonly(
                                                                            `form${parent} #candidate-guardian [name='guardian_${name}']`
                                                                        );
                                                                    }

                                                                } else {
                                                                    $(`form${parent} #candidate-guardian [name='guardian_${name}']`)
                                                                        .val(guardian.hasOwnProperty(name) ? guardian[name] : '')

                                                                }

                                                            }
                                                        );
                                                    //Subjects
                                                    $(`form${parent} #candidate-subjects ul`).html("");
                                                    $.each(subjects, function(key, subject) {
                                                        var doubleSubjectOption = ['0178', '0181'];
                                                        var option = doubleSubjectOption.includes(subject.subject_code) ?
                                                            subject.description : '';
                                                        $(`form${parent} #candidate-subjects ul`).append(`
                                                             <li class = "list-group-item"> ${subject.subject_code }  : ${subject.subject_name}  ${option}</li>
                                                            `);

                                                    });
                                                    // Paid
                                                    $(`form${parent} [name='amount']`).val(paid_fee);
                                                    $(".show-candidate-modal").modal("show");
                                                },
                                                complete: function() {
                                                    i--;
                                                    if (i <= 0) {
                                                        $(".preloader").fadeOut();
                                                    }
                                                },
                                            });
                                        });
                                        /*****Show Update Candidate Endr *******/

                                        /*****  Delete Candidate start *******/
                                        $(document).on("click", ".delete-candidate", function() {
                                            var id = $(this).data("id");
                                            if (confirm("are You sure delete this candidate ?")) {
                                                $.ajaxSetup({
                                                    headers: {
                                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                                    }
                                                });
                                                $.ajax({
                                                    type: "DELETE",
                                                    url: "/center/delete-candidate/" + id,
                                                    success: function(data) {

                                                        $('#amend-datatable').DataTable().ajax.reload(null, false);
                                                        toastr.success('You clicked Success toast');

                                                    },
                                                });
                                            }

                                        });
                                        /*****  Delete Candidate End *******/

                                        /*****  Delete Candidate start *******/
                                        $(document).on("click", ".btn-delete-Selected", function() {
                                            if (confirm("are You sure delete this candidate ?")) {
                                                var candidateNo = [];
                                                $("[name='candidates[]']:checked").each(function(i) {
                                                    candidateNo[i] = $(this).val();
                                                });

                                                if (candidateNo.length === 0) {
                                                    alert("Please select atleast one candidate");
                                                } else {
                                                    $.ajaxSetup({
                                                        headers: {
                                                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                                        }
                                                    });
                                                    $.ajax({
                                                        type: "POST",
                                                        url: "{{ route('center.candidates.deleteCandidates') }}",
                                                        data: {
                                                            candidateNumbers: candidateNo
                                                        },
                                                        success: function(data) {
                                                            $('#amend-datatable').DataTable().ajax.reload(null, false);
                                                            toastr.success("successfully deleted " + candidateNo.length +
                                                                " candidates");

                                                        },
                                                    });
                                                }
                                            }

                                        });
                                        /*****  Delete Candidate End *******/

                                        /*****  Edit Candidate  DBO and gender *******/
                                        $(document).on("click", ".edit-DOBGender", function() {
                                            $.ajaxSetup({
                                                headers: {
                                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                                }
                                            });
                                            var action = $(this).data("action");
                                            $('#edit-form-DOBgender').trigger("reset");
                                            var i = 0;


                                            $.ajax({
                                                url: action,
                                                method: "GET",
                                                beforeSend: function() {
                                                    // setting a timeout
                                                    $(".preloader").fadeIn();
                                                    i++;
                                                },
                                                success: function(data) {
                                                    console.log(data);
                                                    // var data = JSON.parse(response);

                                                    $('#edit-form-DOBgender  input[name="candidate_number"]').val(data.candidate
                                                        .candidate_no);
                                                    $('#edit-form-DOBgender  input[name="candidate_surname"]').val(
                                                        data.candidate.candidate_surname
                                                    );
                                                    $('#edit-form-DOBgender input[name="candidate_other_name"]').val(
                                                        data.candidate.candidate_other_name
                                                    );

                                                    $('#edit-form-DOBgender input[name="date_of_birth"]').val(
                                                        data.candidate.date_of_birth
                                                    );
                                                    $('#edit-form-DOBgender input[type="radio"][name="gender"][value="' + data.candidate
                                                        .gender +
                                                        '"]').prop("checked", true);

                                                    $("#edit-form-DOBgender").attr('action', data.action);
                                                    $(".edit-DOB-gender-modal").modal("show");
                                                },
                                                complete: function() {
                                                    i--;
                                                    if (i <= 0) {
                                                        $(".preloader").fadeOut();
                                                    }
                                                },
                                            });
                                        });
                                        /***** End Update Candidate  DBO and gender *******/
                                        /*****  Save Updates Candidate  DBO and gender *******/
                                        $(document).on("click", "#save-DOBgender", function() {
                                            var action = $("#edit-form-DOBgender").attr('action')
                                            var i = 0;
                                            $.ajaxSetup({
                                                headers: {
                                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                                }
                                            });
                                            $.ajax({
                                                url: action,
                                                method: "PUT",
                                                data: $("#edit-form-DOBgender").serialize(),
                                                beforeSend: function() {
                                                    // setting a timeout
                                                    $(".preloader").fadeIn();
                                                    i++;
                                                },
                                                success: function(data) {
                                                    console.log(data);
                                                    if (data.status) {
                                                        toastr.success("Successfully Update Date of birth and Gender");
                                                        $(".edit-DOB-gender-modal").modal("hide");
                                                    }
                                                },
                                                complete: function() {
                                                    i--;
                                                    if (i <= 0) {
                                                        $(".preloader").fadeOut();
                                                    }
                                                },
                                            });
                                        });
                                        /***** End Update Candidate  DBO and gender *******/
                                    </script>
                                @endpush
                            </div>
                        </div>
                        <!--End Advanced Tables -->
                    </div>
                </div>
                <!-- /. ROW  -->
            </div>
            <!-- /. PAGE INNER  -->
        </div>
        <!-- /. PAGE WRAPPER  -->

    </div>

    <!-- /. WRAPPER  -->
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
                        @method('PUT')
                        @csrf
                        <div class="row">
                            <div class="form-group col-md-6">
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
                            <div class="form-group col-md-6">
                                <label for="session" class="control-label">Registration
                                    Session</label>
                                <select class="form-control" name="session" id="session">
                                    @foreach ($sessions as $session)
                                        <option value="{{ $session->session }}" data-session="{{ $session->id }}">
                                            {{ $session->description }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="candidate_no" class="control-label">Candidate Number</label>
                                <input type="text" class="form-control " readonly placeholder="Enter Candidate Number"
                                    name="candidate_no" id="candidate_no">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="national_id" class="control-label">National Id</label>
                                <input type="text" class="form-control" readonly placeholder="Enter National ID"
                                    name="national_id" id="national_id">
                            </div>

                            <div class="form-group col-md-6">
                                <label for="candidate_surname" class="control-label">Surname</label>
                                <input type="text" class="form-control" readonly placeholder="Enter Candidate Surname"
                                    name="candidate_surname" id="candidate_surname">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="candidate_other_name" class="control-label">Other name</label>
                                <input type="text" class="form-control " readonly placeholder="Enter Other_name"
                                    name="candidate_other_name" id="candidate_other_name">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="date_of_birth" class="control-label">Date of birth</label>
                                <input type="date" class="form-control " readonly placeholder="Enter date of birth"
                                    name="date_of_birth" id="date_of_birth">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="gender" class="control-label">Gender</label>
                                <select name="gender" class="form-control" id="gender">
                                    <option value=" ">Please Select Gender</option>
                                    <option value="M">Male</option>
                                    <option value="F">Female</option>
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="type" class="control-label">Type</label>
                                <select name="type" class="form-control" id="type">
                                    <option value=" ">Please Select type</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="sponsor" class="control-label">Sponsor</label>
                                <select name="sponser" class="form-control" id="sponsor">
                                    <option value=" ">Please Select sponsor</option>
                                    @foreach ($sponsors as $sponsor)
                                    <option value="{{ $sponsor->sponsor }}">
                                        {{ $sponsor->sponsor }}</option>
                                       @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-12  center-subjects">

                            </div>
                            <div class="form-row text-center clearfix subjects-errors"><span></span></div>
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

    <!-- SHOW MODEL CANDIDATE -->
    <div class="modal fade show-candidate-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h5 class="modal-title"> Candidate Information </h5>
                </div>
                <div class="modal-body">
                    <form action="" method="post" id="show-candidate">
                        <div class="tabbable-panel">
                            <div class="tabbable-line">
                                <ul class="nav nav-tabs">
                                    <li class="active">
                                        <a href="#candidate-subjects" data-toggle="tab">Subjects</a>
                                    </li>
                                </ul>
                                <div class="tab-content">

                                    <div class="tab-pane p-3 active " id="candidate-subjects">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <fieldset class="row  fieldset-border">
                                                    <legend class="fieldset-border">Subjects</legend>
                                                    <ul class="list-group">
                                                    </ul>
                                                </fieldset>

                                            </div>
                                            <div class="col-md-6">
                                                <fieldset class="row  fieldset-border">
                                                    <legend class="fieldset-border">Examination fee</legend>
                                                    <div class="form-group col-md-12">
                                                        <label for="amount">Paid amount</label>
                                                        <input type="text" class="form-control" id="amount"
                                                            name="amount" readonly>
                                                    </div>
                                                </fieldset>
                                            </div>
                                        </div>


                                    </div>


                                </div>
                            </div>
                        </div>
                    </form>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </div>

            </div>
        </div>
    </div>
    <!-- SHOW  CANDIDATE MODAL  END-->

    <!-- ADD MODEL CANDIDATE -->
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
                        @csrf

                        <div class="row">
                            <div class="form-group col-md-6">
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
                            <div class="form-group col-md-6">
                                <label for="session" class="control-label">Registration
                                    Session</label>
                                <select class="form-control" name="session" id="session">
                                    @foreach ($sessions as $session)
                                        <option value="{{ $session->session }}" data-session="{{ $session->id }}">
                                            {{ $session->description }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="candidate_no" class="control-label">Candidate Number</label>
                                <input type="text" class="form-control " placeholder="Enter Candidate Number"
                                    name="candidate_no" id="candidate_no">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="national_id" class="control-label">National Id</label>
                                <input type="text" class="form-control " placeholder="Enter National ID"
                                    name="national_id" id="national_id">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="candidate_surname" class="control-label">Surname</label>
                                <input type="text" class="form-control" placeholder="Enter Candidate Surname"
                                    name="candidate_surname" id="candidate_surname">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="candidate_other_name" class="control-label">Other name</label>
                                <input type="text" class="form-control " placeholder="Enter Other_name"
                                    name="candidate_other_name" id="candidate_other_name">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="date_of_birth" class="control-label">Date of birth</label>
                                <input type="date" class="form-control " placeholder="Enter date of birth"
                                    name="date_of_birth" id="date_of_birth">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="gender" class="control-label">Gender</label>
                                <select name="gender" class="form-control" id="gender">
                                    <option value=" ">Please Select Gender</option>
                                    <option value="M">Male</option>
                                    <option value="F">Female</option>
                                </select>
                            </div>

                            @switch($center->level)
                                @case('G7ELT')
                                    @php
                                        $national_id = time();
                                    @endphp
                                    <input type="hidden" name="guardian_national_id" value="{{ $national_id }}">
                                    <div class="form-group col-md-6">
                                        <label for="guardian_surname" class="control-label">Guardian surname</label>
                                        <input type="text" class="form-control " placeholder="Enter guardian surname"
                                            name="guardian_surname" id="guardian_surname">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="guardian_name" class="control-label">Guardian name</label>
                                        <input type="text" class="form-control " placeholder="Enter guardian name"
                                            name="guardian_name" id="guardian_name">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="guardian_phone_number" class="control-label">Guardian phone number</label>
                                        <input type="text" class="form-control " placeholder="Enter guardian phone number"
                                            name="guardian_phone_number" id="guardian_phone_number">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="guardian_village " class="control-label">Guardian village </label>
                                        <input type="text" class="form-control " placeholder="Enter guardian village "
                                            name="guardian_village" id="guardian_village ">
                                    </div>
                                @break

                                @case('LGCSE')
                                @break

                                @default
                            @endswitch

                            <div class="form-group col-md-6">
                                <label for="type" class="control-label">Type</label>
                                <select name="type" class="form-control" id="type">
                                    <option value=" ">Please Select type</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="sponsor" class="control-label">Sponsor</label>
                                <select name="sponsor" class="form-control" id="sponsor">
                                    <option value=" ">Please Select sponsor</option>
                                    @foreach ($sponsors as $sponsor)
                                        <option value="{{ $sponsor->sponsor }}">
                                            {{ $sponsor->sponsor }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-12  center-subjects">

                            </div>
                            <div class="form-row text-center clearfix subjects-errors"><span></span></div>
                        </div>

                    </form>

                </div>
                <div class="modal-footer">
                    <button type="submit" name="add_user" class="btn btn-primary" disabled
                        id="save-candidate">Add</button>
                    <button type="button" class="btn btn-danger resetform" id="adduser_close"
                        data-dismiss="modal">Close</button>

                </div>
            </div>
        </div>

    </div>
    <!--END ADD CANDIDATE MODEL -->

    <!-- import-csv-registration-modal -->
    <div class="modal fade import-csv-registration-modal" tabindex="-1" role="dialog"
        aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="width:1000px;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h5 class="modal-title"> Register candidates by CSV </h5>
                </div>
                <div class="modal-body">
                    <!-- Candidate registration template section -->
                    <div id="accordion" class="py-5">

                        <div class="card border-0 wow fadeInUp" style="visibility: visible; animation-name: fadeInUp">
                            <div class="card-header p-0 border-0" id="heading-241">
                                <button class="btn btn-link accordion-title border-0 collapsed" data-toggle="collapse"
                                    data-target="#collapse-241" aria-expanded="false" aria-controls="#collapse-241">
                                    * Candidates' Registration template
                                </button>
                            </div>
                            <div id="collapse-241" class="collapse" aria-labelledby="heading-241"
                                data-parent="#accordion">
                                <div class="card-body accordion-body">

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="panel panel-default">
                                                <div class="panel-heading">
                                                    * Candidates' Registration template
                                                </div>
                                                <div class="panel-body">
                                                    <p>Uploading a comma-separated (CSV) values spreadsheet is a way to add
                                                        or register
                                                        bulk candidates at once.
                                                    </p>
                                                    <p>To get started, download the CSV template. Enter all the candidates
                                                        details you
                                                        want to add or register, and save the file. Then select the level,
                                                        and upload the CSV file.
                                                    </p>
                                                    <a href=" {{ asset("school/assets/download/$center->level-Sample.csv") }}"
                                                        download class="btn template_download"><i
                                                            class="fa fa-download"></i>
                                                        Download</a>
                                                    <hr>
                                                    <div class="row">
                                                        @foreach ($subjects as $subject)
                                                            <div class="col-md-4">
                                                                <div class="row">
                                                                    <div class="col-md-12 d-flex justify-content-start">
                                                                        <span class="sub_code text-left">
                                                                            <b>{{ str_pad($subject->subject_code, 4, '0', STR_PAD_LEFT) }}</b>
                                                                        </span><span
                                                                            class="text-left">{{ $subject->subject_name }}</span>

                                                                    </div>
                                                                </div>

                                                            </div>
                                                        @endforeach

                                                    </div>

                                                </div>
                                            </div>
                                            <!-- end Candidate registration template card -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card border-0 wow fadeInUp" style="visibility: visible; animation-name: fadeInUp">
                            <div class="card-header p-0 border-0" id="heading-242">
                                <button class="btn btn-link accordion-title border-0 collapsed" data-toggle="collapse"
                                    data-target="#collapse-242" aria-expanded="false" aria-controls="#collapse-242">
                                    * Register candidates by CSV
                                </button>
                            </div>
                            <div id="collapse-242" class="collapse" aria-labelledby="heading-242"
                                data-parent="#accordion">
                                <div class="card-body accordion-body">
                                    <!-- List of subjects available and upload section -->
                                    <div class="row">
                                        <div class="col-md-5">
                                            <!-- Advanced Tables -->
                                            <div class="panel panel-default">
                                                <div class="panel-heading">
                                                    * Register candidates by CSV
                                                </div>
                                                <div class="panel-body">
                                                    <div class="row">
                                                        <!-- Candidate upload registration csv file section-->
                                                        <!-- List of subjects available -->

                                                        <div class="col-md-12">
                                                            <div id="uploadArea" class="upload-area">
                                                                <!-- Header -->
                                                                <form method="POST" id="import-form"
                                                                    action="{{ route('center.registration.importCandidatate') }}"
                                                                    enctype="multipart/form-data" accept-charset="utf-8">
                                                                    <div>
                                                                        @csrf
                                                                    </div>

                                                                    <div class="upload-area__header">
                                                                        <h1 class="upload-area__title">Upload file</h1>
                                                                        <p class="upload-area__paragraph">
                                                                            File should be a
                                                                            <strong class="upload-area__tooltip">
                                                                                CSV or TXT
                                                                                <span
                                                                                    class="upload-area__tooltip-data"></span>
                                                                                <!-- Data Will be Comes From Js -->
                                                                            </strong>
                                                                        </p>
                                                                        <div class="form-group">
                                                                            <label for="session"
                                                                                class="control-label">Registration
                                                                                Session</label>
                                                                            <select class="form-control" name="session"
                                                                                id="session">
                                                                                @foreach ($sessions as $session)
                                                                                    <option
                                                                                        value="{{ $session->session }}"
                                                                                        data-session="{{ $session->id }}">
                                                                                        {{ $session->description }}
                                                                                    </option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>

                                                                        <div class="form-group ">
                                                                            <label for="level"
                                                                                class="control-label">Registration
                                                                                Level</label>
                                                                            <select name="level" class="form-control"
                                                                                id="level">
                                                                                <option value=" ">Select Registration
                                                                                    Level</option>

                                                                                @foreach ($levels as $level)
                                                                                    <option value="{{ $level->level }}">
                                                                                        {{ $level->level }}</option>
                                                                                @endforeach



                                                                            </select>

                                                                        </div>
                                                                    </div>
                                                                    <!-- End Header -->

                                                                    <!-- Drop Zoon -->
                                                                    <div id="dropZoon"
                                                                        class="upload-area__drop-zoon drop-zoon">
                                                                        <span class="drop-zoon__icon">
                                                                            <i class="far fa-file-excel"></i>
                                                                        </span>
                                                                        <p class="drop-zoon__paragraph">Drop your file here
                                                                            or Click to
                                                                            browse</p>
                                                                        <span id="loadingText"
                                                                            class="drop-zoon__loading-text">Please
                                                                            Wait</span>
                                                                        <div alt="Preview Image" id="previewImage"
                                                                            class="drop-zoon__preview-image"
                                                                            draggable="false">
                                                                        </div>
                                                                        <div class="form-group ">
                                                                            <input type="file" name="fileup"
                                                                                id="fileInput"
                                                                                class="drop-zoon__file-input">
                                                                        </div>

                                                                    </div>
                                                                    <!-- End Drop Zoon -->

                                                                    <!-- File Details -->
                                                                    <div id="fileDetails"
                                                                        class="upload-area__file-details file-details">
                                                                        <h3 class="file-details__title">Uploaded File</h3>
                                                                        <div id="uploadedFile" class="uploaded-file">
                                                                            <div class="uploaded-file__icon-container">
                                                                                <i
                                                                                    class="far fa-file-excel uploaded-file__icon"></i>
                                                                                <span
                                                                                    class="uploaded-file__icon-text"></span>
                                                                                <!-- Data Will be Comes From Js -->
                                                                            </div>



                                                                            <div id="uploadedFileInfo"
                                                                                class="uploaded-file__info">
                                                                                <span class="uploaded-file__name">Project
                                                                                    1</span>
                                                                                <span
                                                                                    class="uploaded-file__counter">0%</span>
                                                                            </div>

                                                                        </div>

                                                                    </div>
                                                                    <div class="import-progress">
                                                                        <p>Importing
                                                                            <span class="progress-status">0/100</span>
                                                                        </p>
                                                                        <progress class="progress progress-bar"
                                                                            max="100" value="5"></progress>
                                                                    </div>

                                                                    <input type="submit" value="Submit"
                                                                        name="uploadfile" class="btn btn-primary"
                                                                        id="submit-btn">
                                                                    <button type="button" class="btn btn-default"
                                                                        disabled="disabled" id="fakebtn">Submit <i
                                                                            class="fa fa-minus-circle"></i></button>
                                                                    <!-- End File Details -->
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-7">
                                            <!-- Advanced Tables -->
                                            <div class="panel panel-default">
                                                <div class="panel-heading">
                                                    * Total Candidates <span class="total-candidate"></span>
                                                </div>
                                                <div class="panel-body">
                                                    <div class="alert alert-success alert-candidate-count">
                                                        <button type="button" class="close" data-dismiss="alert"
                                                            aria-label="Close">
                                                            <i class="material-icons">close</i>
                                                        </button>
                                                        <span><b>
                                                                <a href="{{ route('center.candidates.index') }}"><i
                                                                        class="fas fa-check-double"></i>&nbsp;
                                                                    Registered candidates <span class="registered"></span>
                                                                </a>

                                                            </b>
                                                            <br>
                                                            <i class="fas fa-times"></i>&nbsp;Unregistered
                                                            <b>
                                                                candidates <span class="unregistered"></span>
                                                            </b>
                                                        </span>
                                                    </div>

                                                    <div class="alert alert-danger alert-candidate-errors">
                                                        <button type="button" class="close" data-dismiss="alert"
                                                            aria-label="Close">
                                                            <i class="material-icons">close</i>
                                                        </button>
                                                        <div class="table-responsive candidate-error">
                                                            <table class="table">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Row</th>
                                                                        <th colspan="8">Messages</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>

                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    </div>

                </div>
            @endsection
            @section('script')
                <script>
                    // Select Upload-Area
                    const uploadArea = document.querySelector('#uploadArea')

                    // Select Drop-Zoon Area
                    const dropZoon = document.querySelector('#dropZoon');

                    // Loading Text
                    const loadingText = document.querySelector('#loadingText');

                    // Slect File Input
                    const fileInput = document.querySelector('#fileInput');

                    // Select Preview Image
                    const previewImage = document.querySelector('#previewImage');

                    // File-Details Area
                    const fileDetails = document.querySelector('#fileDetails');

                    // Uploaded File
                    const uploadedFile = document.querySelector('#uploadedFile');

                    // Uploaded File Info
                    const uploadedFileInfo = document.querySelector('#uploadedFileInfo');

                    // Uploaded File  Name
                    const uploadedFileName = document.querySelector('.uploaded-file__name');

                    // Uploaded File Icon
                    const uploadedFileIconText = document.querySelector('.uploaded-file__icon-text');

                    // Uploaded File Counter
                    const uploadedFileCounter = document.querySelector('.uploaded-file__counter');

                    // ToolTip Data
                    const toolTipData = document.querySelector('.upload-area__tooltip-data');

                    // Images Types
                    const imagesTypes = [
                        "txt",
                        "csv"
                    ];

                    // Append Images Types Array Inisde Tooltip Data
                    toolTipData.innerHTML = [...imagesTypes].join(', .');

                    // When (drop-zoon) has (dragover) Event
                    dropZoon.addEventListener('dragover', function(event) {
                        // Prevent Default Behavior
                        event.preventDefault();

                        // Add Class (drop-zoon--over) On (drop-zoon)
                        dropZoon.classList.add('drop-zoon--over');
                    });

                    // When (drop-zoon) has (dragleave) Event
                    dropZoon.addEventListener('dragleave', function(event) {
                        // Remove Class (drop-zoon--over) from (drop-zoon)
                        dropZoon.classList.remove('drop-zoon--over');
                    });

                    // When (drop-zoon) has (drop) Event
                    dropZoon.addEventListener('drop', function(event) {
                        // Prevent Default Behavior
                        event.preventDefault();

                        // Remove Class (drop-zoon--over) from (drop-zoon)
                        dropZoon.classList.remove('drop-zoon--over');

                        // Select The Dropped File
                        const file = event.dataTransfer.files[0];

                        // Call Function uploadFile(), And Send To Her The Dropped File :)
                        uploadFile(file);
                    });

                    // When (drop-zoon) has (click) Event
                    dropZoon.addEventListener('click', function(event) {
                        // Click The (fileInput)
                        fileInput.click();
                    });

                    // When (fileInput) has (change) Event
                    fileInput.addEventListener('change', function(event) {
                        // Select The Chosen File
                        const file = event.target.files[0];

                        // Call Function uploadFile(), And Send To Her The Chosen File :)
                        uploadFile(file);
                    });

                    // Upload File Function
                    function uploadFile(file) {
                        // FileReader()
                        const fileReader = new FileReader();
                        // File Type
                        const fileType = file.type;
                        // File Size
                        const fileSize = file.size;

                        // If File Is Passed from the (File Validation) Function
                        if (fileValidate(fileType, fileSize)) {
                            // Add Class (drop-zoon--Uploaded) on (drop-zoon)
                            dropZoon.classList.add('drop-zoon--Uploaded');

                            // Show Loading-text
                            loadingText.style.display = "block";
                            // Hide Preview Image
                            previewImage.style.display = 'none';

                            // Remove Class (uploaded-file--open) From (uploadedFile)
                            uploadedFile.classList.remove('uploaded-file--open');
                            // Remove Class (uploaded-file__info--active) from (uploadedFileInfo)
                            uploadedFileInfo.classList.remove('uploaded-file__info--active');

                            // After File Reader Loaded
                            fileReader.addEventListener('load', function() {
                                // After Half Second
                                setTimeout(function() {
                                    // Add Class (upload-area--open) On (uploadArea)
                                    uploadArea.classList.add('upload-area--open');

                                    // Hide Loading-text (please-wait) Element
                                    loadingText.style.display = "none";
                                    // Show Preview Image
                                    previewImage.style.display = 'block';

                                    // Add Class (file-details--open) On (fileDetails)
                                    fileDetails.classList.add('file-details--open');
                                    // Add Class (uploaded-file--open) On (uploadedFile)
                                    uploadedFile.classList.add('uploaded-file--open');
                                    // Add Class (uploaded-file__info--active) On (uploadedFileInfo)
                                    uploadedFileInfo.classList.add('uploaded-file__info--active');
                                }, 500); // 0.5s

                                $("#submit-btn").show();
                                $(".import-progress").show();
                                $("#fakebtn").hide();



                                // Add The (fileReader) Result Inside (previewImage) Source
                                // previewImage.setAttribute('src', fileReader.result);

                                // Add File Name Inside Uploaded File Name
                                uploadedFileName.innerHTML = file.name;

                                // Call Function progressMove();
                                progressMove();
                            });

                            // Read (file) As Data Url
                            fileReader.readAsDataURL(file);
                        } else { // Else
                            $("#submit-btn").hide();
                            $("#fakebtn").show();
                            this; // (this) Represent The fileValidate(fileType, fileSize) Function

                        };
                    };

                    // Progress Counter Increase Function
                    function progressMove() {
                        // Counter Start
                        let counter = 0;

                        // After 600ms
                        setTimeout(() => {
                            // Every 100ms
                            let counterIncrease = setInterval(() => {
                                // If (counter) is equle 100
                                if (counter === 100) {
                                    // Stop (Counter Increase)
                                    clearInterval(counterIncrease);
                                } else { // Else
                                    // plus 10 on counter
                                    counter = counter + 10;
                                    // add (counter) vlaue inisde (uploadedFileCounter)
                                    uploadedFileCounter.innerHTML = `${counter}%`
                                }
                            }, 100);
                        }, 600);
                    };


                    // Simple File Validate Function
                    function fileValidate(fileType, fileSize) {
                        // File Type Validation
                        // If The Uploaded File Type Is 'jpeg'
                        if (fileType === 'text/csv') {
                            // Add Inisde (uploadedFileIconText) The (jpg) Value
                            uploadedFileIconText.innerHTML = 'csv';
                        } else { // else
                            return alert('Please make sure to upload An CSV File Type');
                        };

                        // If The Uploaded File Is An Image
                        if (fileSize !== 0) {
                            // Check, If File Size Is 2MB or Less
                            if (fileSize <= 8000 * 1000 * 1000) { // 2MB :)
                                return true;
                            } else { // Else File Size
                                return alert('Please Your File Should be 32 Megabytes or Less');
                            };
                        } else { // Else File Type
                            return alert('Please make sure to upload An CSV File Type');
                        };
                    };




                    $(document).on("submit", "#import-form", function(e) {
                        var i = 0;
                        var caption = $('#submit-btn').val();
                        $('.progress-bar').val(4);
                        $('.progress-status').html("0/100")
                        e.preventDefault(); //form will not submitted
                        $.ajaxSetup({
                            headers: {
                                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                                    "content"
                                ),
                            },
                        });

                        $.ajax({
                            url: $('#import-form').attr('action'),
                            method: "POST",
                            data: new FormData(this),
                            contentType: false, // The content type used when sending data to the server.
                            cache: false, // To unable request pages to be cached
                            processData: false,
                            beforeSend: function() {
                                // setting a timeout
                                $('#submit-btn').prop('disabled', true).val("Processing.....");
                                i++;
                                var percentComplete = Math.round((i / 100) * 100);
                                $('.progress-bar').val(percentComplete);
                                $('.progress-status').html(`${percentComplete}/100`)
                            },

                            success: function(data) {
                                console.log(data);
                                $('#submit-btn').prop('disabled', false).val(caption);
                                $(".candidate-error table tbody > tr").remove();
                                $(".alert-candidate-count").hide();
                                $(".alert-candidate-errors").hide();
                                if (!$.isEmptyObject(data.errors)) {
                                    $(".registered").html(data.candidatesNumbers.registerCandidate);
                                    $(".unregistered").html(data.candidatesNumbers.unregisterCandidate);
                                    $(".total-candidate").html(data.candidatesNumbers.totalCandidates);
                                    $(".alert-candidate-count").show();
                                    $(".alert-candidate-errors").show();
                                    var tr = "";
                                    $.each(data.errors, function(i, data) {
                                        $.each(data.messages, function(i, error) {
                                            tr =
                                                `<tr><td>${data.row}</td><td><span class="text-danger"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> ${error}</span></td></tr>`;
                                            $(".candidate-error table tbody").append(tr);
                                        });

                                    });
                                } else if (!$.isEmptyObject(data.error)) {
                                    printErrorMsg("#import-form", data.error)
                                } else {
                                    $(".alert-candidate-count").show();
                                    $(".registered").html(data.candidatesNumbers.registerCandidate);
                                    $(".unregistered").html(data.candidatesNumbers.unregisterCandidate);
                                    $(".total-candidate").html(data.candidatesNumbers.totalCandidates);
                                }
                                $('#amend-datatable').DataTable().ajax.reload();
                            },
                            complete: function() {
                                i--;
                                if (i <= 0) {

                                    $('#submit-btn').prop('disabled', false).val(caption);
                                }
                            },


                        })
                    });












                    //

                    //

                    // $(document).on("submit", "#import-form", function(e) {
                    //     var i = 0;

                    //     $('.progress-bar').val(0);
                    //     $('.progress-status').html("0/100")




                    //     // var percent = $('.percent');
                    //     // var status = $('#status');

                    //     e.preventDefault(); //form will not submitted
                    //     $.ajaxSetup({
                    //         headers: {
                    //             "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                    //                 "content"
                    //             ),
                    //         },
                    //     });
                    //     var caption = $('#submit-btn').val();
                    //     $.ajax({
                    //         xhr: function() {
                    //             var xhr = new window.XMLHttpRequest();
                    //             xhr.upload.addEventListener("progress", function(evt) {
                    //                 if (evt.lengthComputable) {
                    //                     var percentComplete = (evt.loaded / evt.total) * 100;
                    //                      // Place upload progress bar visibility code here
                    //                      $('.progress-bar').val(percentComplete);
                    //                      $('.progress-status').html(`${percentComplete}/100`)

                    //                 }
                    //             }, false);

                    //             return xhr;
                    //         },
                    //         url: $('#import-form').attr('action'),
                    //         method: "POST",
                    //         data: new FormData(this),
                    //         contentType: false, // The content type used when sending data to the server.
                    //         cache: false, // To unable request pages to be cached
                    //         processData: false,
                    //         beforeSend: function() {
                    //             // setting a timeout
                    //             $('#submit-btn').prop('disabled', true).val("Processing.....");
                    //             i++;
                    //         },

                    //         success: function(data) {
                    //             console.log(data);
                    // $('#submit-btn').prop('disabled', false).val(caption);
                    // $(".candidate-error table tbody > tr").remove();
                    // $(".alert-candidate-count").hide();
                    // $(".alert-candidate-errors").hide();
                    // if (!$.isEmptyObject(data.errors)) {
                    //     $(".registered").html(data.candidatesNumbers.registerCandidate);
                    //     $(".unregistered").html(data.candidatesNumbers.unregisterCandidate);
                    //     $(".total-candidate").html(data.candidatesNumbers.totalCandidates);
                    //     $(".alert-candidate-count").show();
                    //     $(".alert-candidate-errors").show();
                    //     var tr = "";
                    //     $.each(data.errors, function(i, data) {
                    //         $.each(data.messages, function(i, error) {
                    //             tr =
                    //                 `<tr><td>${data.row}</td><td><span class="text-danger"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> ${error}</span></td></tr>`;
                    //             $(".candidate-error table tbody").append(tr);
                    //         });

                    //     });
                    // } else if (!$.isEmptyObject(data.error)) {
                    //     printErrorMsg("#import-form", data.error)
                    // } else {
                    //     $(".alert-candidate-count").show();
                    //     $(".registered").html(data.candidatesNumbers.registerCandidate);
                    //     $(".unregistered").html(data.candidatesNumbers.unregisterCandidate);
                    //     $(".total-candidate").html(data.candidatesNumbers.totalCandidates);
                    // }
                    //             $('#amend-datatable').DataTable().ajax.reload();
                    //         },
                    //         complete: function() {
                    //             i--;
                    //             if (i <= 0) {
                    //                 // $("body").removeClass("data-loaded");
                    //                 $('#submit-btn').prop('disabled', false).val(caption);
                    //             }
                    //         },


                    //     })
                    // });

                    /****  Print errors*******/
                    function printErrorMsg(parent, msg) {
                        $(`${parent} input, ${parent} select, textarea`).each(function(index) {
                            $(`${parent} .help-block`).remove();
                            $(`${parent} .has-error`).removeClass('has-error');

                        });
                        $.each(msg, function(key, errors) {
                            for (const error in errors) {
                                const value = errors[error];
                                $(`[name='${key}']`).parent().addClass('has-error');
                                $(`<span class='help-block'>${value}</span>`).insertAfter(`${parent} [name='${key}']`)
                                if (key == 'subjects' || key == 'subject') {
                                    $(".subjects-errors").find('span').css({
                                        "color": "#ff0000"
                                    }).html(`<strong>${value}</strong>`);
                                }

                            }
                        });
                    }
                    /****  Print errors End*******/


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

                    /*-----------------------------------/
                    /*Diplay candidates
                    /*----------------------------------*/

                    /********** Some Variable Initial Value **************/

                    var candidates_filter = $("#candidates_filter").val();
                    var candidates_sort = $("#candidates_sort").val();
                    var search_txt = "";

                    /**********  Candidates Sorting Start    **************/
                    $("#candidates_sort").on("change", function() {
                        var candidates_sort = $(this).val();
                        load_candidates(
                            candidates_filter,
                            candidates_sort,
                            search_txt
                        );


                    });
                    /**********  Candidates Sorting End    **************/

                    /**********  Candidates filter Start    **************/
                    $("#candidates_filter").on("change", function() {
                        var candidates_filter = $(this).val();
                        load_candidates(
                            candidates_filter,
                            candidates_sort,
                            search_txt
                        );

                    });
                    /**********  Candidates filter  End    **************/

                    /**********  Candidates Main Search Start    **************/
                    $("#search_txt").keyup(function() {
                        var search = $(this).val();

                        load_candidates(
                            candidates_filter,
                            candidates_sort,
                            search
                        );
                    });
                    /**********  Candidates Main Search End   **************/

                    /*****  Retrieve Value When Page First Load  *******/

                    load_candidates(
                        candidates_filter,
                        candidates_sort,
                        search_txt
                    );

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

                    $(document).on("change", "#addCandidateForm #level", function() {
                        centerSubjects();
                    });

                    function centerSubjects() {
                        var centre_no = '{{ auth()->user()->center_no }}';
                        var level = $("#addCandidateForm #level").find("option:selected").data("level");
                        var session = $("#addCandidateForm #session").find("option:selected").data("session");
                        $.ajaxSetup({
                            headers: {
                                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                            },
                        });
                        $.ajax({
                            url: "{{ route('center.candidates.center_subjects') }}",
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

                    /*****  Subjects For Center *******/


                    /****  AJAX Main Function Who Perform All Tasks Start *******/
                    function load_candidates(
                        candidates_filter,
                        candidates_sort,
                        search_txt
                    ) {

                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        });
                        $.ajax({
                            url: '{{ route('center.registered') }}',
                            method: "POST",
                            data: {
                                candidates_filter: candidates_filter,
                                candidates_sort: candidates_sort,
                                search_txt: search_txt,
                            },
                            success: function(data) {
                                $(".candidateInfo").html(data.table);
                                $(".candidateInfoPrivate").html(data.private_table);
                                $(".candidateInfoGrade").html(data.table_grade_11);
                            },
                        });
                    }
                    /****  AJAX Main Function Who Perform All Tasks End *******/
                </script>
            @endsection
