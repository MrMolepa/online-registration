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
                                        <button type="button" class="close" data-dismiss="alert"
                                            aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                        <i class="fa fa-check-circle"></i> {{ session('success') }}
                                    </div>
                                    &nbsp;
                                @endif
                                <form action="{{ route('admin.candidates.exportCandidates') }}" method="get">
                                    @csrf
                                    <div class="form-group col-md-4">
                                        <label for="">By Level</label>
                                        <select class="form-control" name="level" id="level">
                                            <option value="">Please Select Level</option>
                                            <option value="JC">JC</option>
                                            <option value="LGCSE">LGCSE</option>
                                            <option value="LGCSEGR">LGCSE (Grade 11)</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="">By Session</label>
                                        <select class="form-control" name="session" id="session">
                                            <option value="">Please Select Session</option>
                                            <option value="June">June</option>
                                            <option value="November">November</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="">By Center</label>
                                        <select class="form-control" name="center" id="center">
                                            <option value="">Please Select Center</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="">From</label>
                                        <input name="date_from" type="date"  class="form-control">
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
                                <div class="pull-left">
                                    <h3 class="panel-title">All Candidates</h3>
                                    {{-- <div class="dropdown">
                                        <button class="btn btn-primary dropdown-toggle" type="button"
                                            data-toggle="dropdown"> Reports
                                            <span class="caret"></span></button>
                                        <ul class="dropdown-menu">
                                            <li><a href="#">Registered Candidate per Session</a></li>
                                            <li><a href="{{route('admin.candidates.exportentries')}}">Entries</a></li>
                                        </ul>
                                    </div> --}}

                                </div>


                                <div class="form-group pull-right">
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
                                <div class="clearfix"></div>
                            </div>
                            <div class="panel-body">
                                <div class="pull-left">
                                    <h4>Amendments List</h4>
                                    <div class="btn-group" role="group" aria-label="Basic example">
                                        <a href="{{ route('admin.candidates.exportAmendments', ['file_type' => 'CSV']) }}"
                                            class="btn btn-secondary">CSV</a>
                                        <a href="{{ route('admin.candidates.exportAmendments', ['file_type' => 'TXT']) }}"
                                            class="btn btn-secondary">TXT</a>


                                    </div>

                                </div>
                                <div class="pull-right">
                                    <h4>New Candidates</h4>
                                    <a href="{{ route('admin.candidates.create') }}" class="btn btn-info">+ NEW
                                        CANDIDATE</a>
                                    <a href="{{ route('admin.candidates.import') }}" class="btn btn-info">IMPORT
                                        CANDIDATES</a>
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
                                    <li><a href="#tab-bottom-left3" role="tab" data-toggle="tab">Subjects per School
                                        </a></li>
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
                                                        <th>Candidate.No</th>
                                                        <th>Surname</th>
                                                        <th>Other Name </th>
                                                        <th>Type</th>
                                                        <th>Sponsor</th>
                                                        <th>No.subjects</th>
                                                        <th width="40%">Subjects</th>
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
                                                                url: "{{ route('admin.candidates.index') }}",
                                                                data: function(d) {
                                                                    d.center_no = $("#center").val(),
                                                                        d.filter = $("#filter").val(),
                                                                        d.level = $("#level").val(),
                                                                        d.session = $("#session").val()
                                                                }
                                                            },
                                                            columns: [{
                                                                    data: 'center_no',
                                                                    name: 'center_candidate.center_no',
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

                                                    /*****  Update Candidate *******/
                                                    $(document).on("click", "#save", function(ev) {
                                                        ev.preventDefault();
                                                        $.ajaxSetup({
                                                            headers: {
                                                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                                            }
                                                        });
                                                        $.ajax({
                                                            url: "{{ route('admin.candidates.updateCandidate') }}",
                                                            method: "POST",
                                                            data: $("#edit-form").serialize() + "&" + this.name + "=" + this.value,
                                                            success: function(data) {
                                                                console.log(data);
                                                                $('.error-text').text('');
                                                                if ($.isEmptyObject(data.error)) {
                                                                    $('#edit-form').trigger("reset");
                                                                    $('#amend-datatable').DataTable().ajax.reload(null, false);
                                                                    $(".edit-candidate").modal("hide");
                                                                    toastr.success(data.msg);

                                                                } else {
                                                                    printErrorMsg(data.error);
                                                                }
                                                            },
                                                        });
                                                    });
                                                    /*****  End Update Candidate subject and sponso *******/

                                                    /*****  Edit Candidate subject and sponsor *******/
                                                    $(document).on("click", ".updateCandidate", function() {
                                                        //  $("form")[0].reset();
                                                        update($(this));
                                                    });
                                                    $(document).on("dblclick", "input:radio", function() {
                                                        if (this.checked) {
                                                            this.checked = false;
                                                        } else {
                                                            this.checked = true;

                                                        }
                                                    });

                                                    function update(element) {
                                                        var centre_no = $(element).data("centre");
                                                        var id = $(element).data("id");
                                                        var update_btn = $(element).attr("update");

                                                        // data-target=".edit-candidate"
                                                        var i = 0;

                                                        $("#subjects").html(" ");

                                                        $.ajaxSetup({
                                                            headers: {
                                                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                                            }
                                                        });
                                                        $.ajax({
                                                            url: "{{ route('admin.candidates.editCandidate') }}",
                                                            cache: false,
                                                            method: "post",
                                                            beforeSend: function() {
                                                                // setting a timeout
                                                                $(".preloader").fadeIn();
                                                                i++;
                                                            },

                                                            data: {
                                                                candidate_no: id

                                                            },
                                                            success: function(data) {
                                                                var data = JSON.parse(JSON.stringify(data));
                                                                console.log(data );

                                                                var all_subjects = data.all_subjects;

                                                                var subjects = (data.joined_result.subjects === null) ? [] : data.joined_result.subjects
                                                                    .split(",");

                                                                var flag = 0;
                                                                var maths_optionA = 0;
                                                                var physics_optionA = 0;
                                                                var maths_optionB = 0;
                                                                var physics_optionB = 0;
                                                                //  loop to all subjects
                                                                $.each(all_subjects, function(index_all_sujects, value) {
                                                                    // loop to all students subject
                                                                    $.each(subjects, function(index, subject_value) {
                                                                        var code = subject_value.split(" ");
                                                                        //    loop and split array code and option

                                                                        if (code[0] == parseInt(value.subject_code)) {
                                                                            flag = 1;

                                                                            if (code[1] == "B" && value.subject_code == "0178") {
                                                                                maths_optionB = 1;
                                                                            } else if (code[1] == "B" && value.subject_code == "0181") {
                                                                                physics_optionB = 1;
                                                                            } else if (code[1] == "A" && value.subject_code == "0178") {
                                                                                maths_optionA = 1;
                                                                            } else if (code[1] == "A" && value.subject_code == "0181") {
                                                                                physics_optionA = 1;
                                                                            }
                                                                        }
                                                                    });
                                                                    if (flag == 1) {
                                                                        if (maths_optionB == 1) {
                                                                            addAllInputs(
                                                                                "subjects",
                                                                                value.subject_name + " Core",
                                                                                value.subject_name,
                                                                                "radio",
                                                                                value.subject_code + " A",
                                                                                " "
                                                                            );
                                                                            addAllInputs(
                                                                                "subjects",
                                                                                value.subject_name + " Extended",
                                                                                value.subject_name,
                                                                                "radio",
                                                                                value.subject_code + " B",
                                                                                "checked"
                                                                            );
                                                                            maths_optionB = 0;
                                                                        } else if (physics_optionB == 1) {
                                                                            addAllInputs(
                                                                                "subjects",
                                                                                value.subject_name + " Core",
                                                                                value.subject_name,
                                                                                "radio",
                                                                                value.subject_code + " A",
                                                                                " "
                                                                            );
                                                                            addAllInputs(
                                                                                "subjects",
                                                                                value.subject_name + " Extended",
                                                                                value.subject_name,
                                                                                "radio",
                                                                                value.subject_code + " B",
                                                                                "checked"
                                                                            );
                                                                            physics_optionB = 0;
                                                                        } else if (maths_optionA == 1) {
                                                                            addAllInputs(
                                                                                "subjects",
                                                                                value.subject_name + " Extended",
                                                                                value.subject_name,
                                                                                "radio",
                                                                                value.subject_code + " B",
                                                                                " "
                                                                            );
                                                                            addAllInputs(
                                                                                "subjects",
                                                                                value.subject_name + " Core",
                                                                                value.subject_name,
                                                                                "radio",
                                                                                value.subject_code + " A",
                                                                                "checked"
                                                                            );
                                                                            maths_optionA = 0;
                                                                        } else if (physics_optionA == 1) {
                                                                            addAllInputs(
                                                                                "subjects",
                                                                                value.subject_name + " Extended",
                                                                                value.subject_name,
                                                                                "radio",
                                                                                value.subject_code + " B",
                                                                                " "
                                                                            );
                                                                            addAllInputs(
                                                                                "subjects",
                                                                                value.subject_name + " Core",
                                                                                value.subject_name,
                                                                                "radio",
                                                                                value.subject_code + " A",
                                                                                "checked"
                                                                            );
                                                                            physics_optionA = 0;
                                                                        } else {
                                                                            addAllInputs(
                                                                                "subjects",
                                                                                value.subject_name,
                                                                                "Subjects[]",
                                                                                "checkbox",
                                                                                value.subject_code + " A",
                                                                                "checked"
                                                                            );
                                                                        }

                                                                        flag = 0;
                                                                    } else {
                                                                        if (value.subject_code == 178) {
                                                                            addAllInputs(
                                                                                "subjects",
                                                                                value.subject_name + " Extended",
                                                                                value.subject_name,
                                                                                "radio",
                                                                                value.subject_code + " B",
                                                                                " "
                                                                            );
                                                                            addAllInputs(
                                                                                "subjects",
                                                                                value.subject_name + " Core",
                                                                                value.subject_name,
                                                                                "radio",
                                                                                value.subject_code + " A",
                                                                                " "
                                                                            );
                                                                        } else if (value.subject_code == 181) {
                                                                            addAllInputs(
                                                                                "subjects",
                                                                                value.subject_name + " Extended",
                                                                                value.subject_name,
                                                                                "radio",
                                                                                value.subject_code + " B",
                                                                                " "
                                                                            );
                                                                            addAllInputs(
                                                                                "subjects",
                                                                                value.subject_name + " Core",
                                                                                value.subject_name,
                                                                                "radio",
                                                                                value.subject_code + " A",
                                                                                " "
                                                                            );
                                                                        } else {
                                                                            addAllInputs(
                                                                                "subjects",
                                                                                value.subject_name,
                                                                                "Subjects[]",
                                                                                "checkbox",
                                                                                value.subject_code + " A",
                                                                                " "
                                                                            );
                                                                        }
                                                                    }
                                                                });

                                                                $('input[name="candidate_number"]').val(id);
                                                                $('input[name="surname"]').val(data.joined_result.candidate_surname);
                                                                $('input[name="other_name"]').val(
                                                                    data.joined_result.candidate_other_name
                                                                );
                                                                $('input[name="gender"]').val(data.joined_result.gender);
                                                                $('input[name="date_of_birth"]').val(data.joined_result.date_of_birth);

                                                                $('#edit-form #type option[value="' + data.joined_result.type + '"]').attr("selected", "selected");
                                                                $("#edit-form #type").val(data.joined_result.type).change();
                                                                 
                                                                 
                                                                $('#edit-form #sponser option[value="' + data.joined_result.sponser +'"]').attr("selected", "selected");
                                                                $("#edit-form #sponser").val(data.joined_result.sponser).change();
                                                                
                                                                
                                                                $('#edit-form #level option[value="' + data.joined_result.level +'"]' ).attr("selected", "selected");
                                                                $("#edit-form #level").val(data.joined_result.level).change();
                                                                  

                                                                $('#edit-form #session option[value="' +data.joined_result.session + '"]').attr("selected", "selected");
                                                                $("#edit-form #session").val(data.joined_result.session).change();

                                                                $('#edit-form #center_no option[value="' +data.joined_result.center_no +'"]').attr("selected", "selected");
                                                                $("#edit-form #center_no").val(data.joined_result.center_no).change();

                                                                $(".edit-candidate").modal("show");
                                                            },
                                                            complete: function() {
                                                                i--;
                                                                if (i <= 0) {
                                                                    $(".preloader").fadeOut();
                                                                }
                                                            },
                                                        });
                                                    }
                                                    /*****  Edit Candidate subject and sponsor *******/
                                                    // Add dynamic input function start
                                                    function addAllInputs(
                                                        divName,
                                                        label,
                                                        name = "",
                                                        inputType,
                                                        value,
                                                        checked = null
                                                    ) {
                                                        var newdiv = document.createElement("div");
                                                        newdiv.className += "col-md-4";

                                                        switch (inputType) {
                                                            case "text":
                                                                newdiv.innerHTML = `
                                                                <div class="form-group">
                                                                <input type="text" name="${name}" value="${value}"  id="${value}" >
                                                                <label for="${value}">${label} </label>
                                                                </div>
                                                                `;

                                                                break;
                                                            case "radio":
                                                                newdiv.innerHTML = ` 
                                                                <div class="form-group">
                                                                <input type="radio" name="${name}" ${checked} value="${value}" id="${value}">
                                                                <label for="${value}">${label} </label>
                                                                </div>
                                                                
                                                                `;
                                                                break;
                                                            case "checkbox":
                                                                newdiv.innerHTML = `
                                                        
                                                            <div class="form-group">
                                                            <input type="checkbox" name="${name}" ${checked} value="${value}" id="${value}">
                                                            <label for="${value}">${label} </label>
                                                            </div>
                                                        
                                                            `;
                                                                break;
                                                            case "textarea":
                                                                newdiv.innerHTML = ` 
                                                            <div class="form-group">
                                                            <input type="textarea" name="${name}"  value="${value}">
                                                            <label>${label} </label>
                                                            </div>
                                                            `;

                                                                break;
                                                            default:
                                                                newdiv.innerHTML = " ";

                                                                break;
                                                        }
                                                        document.getElementById(divName).appendChild(newdiv);
                                                    }

                                                    /****  Print errors*******/
                                                    function printErrorMsg(msg) {
                                                        $.each(msg, function(key, value) {
                                                            $('.' + key + '_error').text(value);
                                                            $("input[name='" + key + "']").addClass("is-valid");
                                                        });
                                                    }
                                                    /****  Print errors End*******/


                                                    // Add dynamic input function end
                                                    /*****  Delete Candidate start *******/
                                                    $(document).on("click", ".delete-candidate", function() {
                                                        var id = $(this).data("id");
                                                        if (confirm("Are You sure want to delete this candidate ?")) {
                                                            $.ajaxSetup({
                                                                headers: {
                                                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                                                }
                                                            });
                                                            $.ajax({
                                                                type: "DELETE",
                                                                url: "/admin/delete-candidate/" + id,
                                                                success: function(data) {

                                                                    $('#amend-datatable').DataTable().ajax.reload(null, false);
                                                                    toastr.success('You clicked Success toast');

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
        var filter = $("#filter").val();


        /**********  Candidates Sorting Start    **************/
        $("#center").on("change", function(event) {
            level = $("#level").val();
            session = $("#session").val();
            center = $("#center").val();
            filter = $("#filter").val();


            candidate_per_center(
                level,
                session,
                center,
                filter,
                event
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
                filter = $("#filter").val();

                candidate_per_center(
                    level,
                    session,
                    center,
                    filter
                );
                $('#amend-datatable').DataTable().ajax.reload(null, false);

            });
        /**********  Candidates filter  End    **************/
        /**********  Candidates filter Start    **************/
        $("#filter").on("change", function() {
            level = $("#level").val();
            session = $("#session").val();
            center = $("#center").val();
            var filter = $("#filter").val();

            candidate_per_center(
                level,
                session,
                center,
                filter
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
            var level = $("#level").val();
            session = $("#session").val();
            center = $("#center").val();
            filter = $("#filter").val();


            candidate_per_center(
                level,
                session,
                center,
                filter
            );
            $('#amend-datatable').DataTable().ajax.reload(null, false);
        });
        /**********  Candidates Main Search End   **************/

        /********** Search Candidate Number And display info **************/

        $(document).on("keyup", "#candidate_No", function() {
            var search = $(this).val();
            $("#add-candidate .errors").html("");
            if (search.length >= 9) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: "{{ route('admin.candidates.getCandidateinfo') }}",
                    method: "POST",
                    data: {
                        candidateNo: search
                    },
                    success: function(data) {

                        if (data.status == 1) {
                            $(".candidateinfo").html(data.output);
                            $("#save-candidate").prop('disabled', false);
                        } else {
                            $(".candidateinfo").html(data.output);
                            $("#save-candidate").prop('disabled', false);
                        }

                    },
                });
            } else {
                $(".candidateinfo").html("");
                $("#save-candidate").prop('disabled', true);
            }
        });
        /********** Search  Candidate Number And display info End**************/



        /********* ADD NEW Candidate ************/
        $(document).on("click", "#save-candidate", function() {
            var i = 0;
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('admin.candidates.addCandidate') }}",
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
                    $("#add-candidate .subjects-error ul").html("");

                    if ($.isEmptyObject(data.errors)) {
                        if (data.status == 1) {
                            $("#add-candidate .subjects-error ul").html("");
                            // $("#addCandidateForm").trigger("reset");
                            $("form")[0].reset();
                            $("#add-candidate").modal("hide");
                            $("#addCandidateForm .candidateinfo").html("");
                            //  Reload dataTable
                            $('#amend-datatable').DataTable().ajax.reload(null, false);
                            toastr.success('You have Successfully added candidate');
                        }
                    } else {
                        printErrorMsg('#addCandidateForm', data.errors)


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


        /*****  Retrieve Value When Page First Load  *******/

        candidate_per_center(
            level,
            session,
            center,
            filter
        );



        /****  AJAX Main Function Who Perform All Tasks Start *******/
        function candidate_per_center(
            level,
            session,
            center,
            filter,
            event = null
        ) {

            var i = 0;
            $.ajax({
                // http://127.0.0.1:8000/api/admin/candidates/registered
                // https://register.examscouncil.org.ls
                url: 'https://register.examscouncil.org.ls/api/admin/candidates/registered',
                method: "POST",
                data: {
                    level: level,
                    session: session,
                    center: center,
                    filter: filter
                },
                beforeSend: function() {
                    // setting a timeout
                    $(".animated-wrapper").fadeIn();
                    i++;
                },
                success: function(data) {
                    $("#candidates-per-center").html(data.cendidate_per_center);
                    // $("#subject-per-center").html(data.subjects_per_centers);
                    // $("#candidates").html(data.candidates);


                    // console.log(data.centers);

                    var centers = data.centers


                    if (event == null) {
                        console.log(event);
                        $('#center').html(`<option value="">
                                  Please Select Center
                              </option>`);

                        centers.forEach(center => {
                            $('#center').append($('<option>').val(center.center_no).text(center
                                .center_no +
                                ' - ' + center.center_name));

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
        /****  AJAX Main Function Who Perform All Tasks End *******/

        $(document).on("change", "#physical_science_core", function() {
            if ($(this).prop("checked")) {
                if ($("#physical_science_extended").prop("checked")) {
                    $("#physical_science_extended").prop("checked", false);
                    $(this).prop("checked", true);
                } else {
                    $(this).prop("checked", true);
                }
            } else {
                $(this).prop("checked", false);
            }
        });

        $(document).on("change", "#physical_science_extended", function() {
            if ($(this).prop("checked")) {
                if ($("#physical_science_core").prop("checked")) {
                    $("#physical_science_core").prop("checked", false);
                    $(this).prop("checked", true);
                } else {
                    $(this).prop("checked", true);
                }
            } else {
                $(this).prop("checked", false);
            }
        });

        $(document).on("change", "#maths_core", function() {
            if ($(this).prop("checked")) {
                if ($("#maths_extended").prop("checked")) {
                    $("#maths_extended").prop("checked", false);
                    $(this).prop("checked", true);
                } else {
                    $(this).prop("checked", true);
                }
            } else {
                $(this).prop("checked", false);
            }
        });

        $(document).on("change", "#maths_extended", function() {
            if ($(this).prop("checked")) {
                if ($("#maths_core").prop("checked")) {
                    $("#maths_core").prop("checked", false);

                    $(this).prop("checked", true);
                } else {
                    $(this).prop("checked", true);
                }
            } else {
                $(this).prop("checked", false);
            }
        });


        // /****  Print errors*******/
        function printErrorMsg(parent, msg) {
            $(`${parent} input, ${parent} select, textarea`).each(function(index) {
                var input = $(this);
                var inputName = input.attr('name');
                $("[name='" + inputName + "']").parent().removeClass('has-error')
                $("[name='" + inputName + "']").siblings('span').html('');
                // console.log(input.attr('type') + 'Name: ' + input.attr('name') + '  Value: ' + input.val());
            });
            $.each(msg, function(key, errors) {
                for (const error in errors) {
                    const value = errors[error];
                    $("[name='" + key + "']").parent().addClass('has-error');
                    $("[name='" + key + "']").next().html(`<strong>${value}</strong>`);

                    if (key == 'subjects') {
                        $(".subjects-errors").addClass('has-error');
                        $(".subjects-errors").find('span').html(`<strong>${value}</strong>`);
                    }
                }

            });
        }

        /****  Print errors End*******/
        /****  Print errors End*******/
    </script>
    <!-- update Model -->
    <div class="modal fade edit-candidate" id="update_form" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h5 class="modal-title"> Amend Candidate </h5>
                </div>
                <div class="modal-body">
                    <form action="" method="post" id="edit-form">
                        @csrf
                        <div class="modal_content_pop_up">
                            <div class="row">

                                <input type="hidden" name="candidate_number" value="">
                                <span class="text-danger error-text candidate_number_error"></span>

                                <div class="form-group col-md-6">
                                    <label for="surname">Surname</label>
                                    <input type="text" readonly="readonly" class="form-control" name="surname"
                                        id="surname" value="">
                                    <span class="text-danger error-text surname_error"></span>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="other_name">Other name</label>
                                    <input type="text" readonly="readonly" class="form-control" name="other_name"
                                        id="other_name" value="">
                                    <span class="text-danger error-text other_name_error"></span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="date_of_birth">Date of birth</label>
                                    <input type="date" readonly="readonly" class="form-control" name="date_of_birth"
                                        id="date_of_birth" value=" ">
                                    <span class="text-danger error-text date_of_birth_error"></span>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="gender">Gender</label>
                                    <input type="text" readonly="readonly" class="form-control" name="gender" id="gender"
                                        value="">
                                    <span class="text-danger error-text date_of_birth_error"></span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="type">Type</label>
                                    <select name="type" class="form-control" id="type">
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="sponser">Sponsor</label>
                                    <select name="sponser" class="form-control" id="sponser">
                                        <option value="P">P</option>
                                        <option value="M">M</option>
                                        <option value="N">N</option>
                                        <option value="O">O</option>
                                    </select>
                                    <span class="text-danger error-text sponser_error"></span>

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
                                <div class="form-group   col-md-4">
                                    <label for="level" class="control-label">Level</label>
                                    <select name="level" class="form-control" id="level">
                                        <option value="LGCSE">LGCSE</option>
                                        <option value="LGCSEGR">LGCSE (Grade 11)</option>
                                    </select>

                                </div>
                                <div class="form-group   col-md-4">
                                    <label for="session" class="control-label">Session</label>
                                    <select name="session" class="form-control" id="session">
                                        <option value="June">June</option>
                                        <option value="November">November</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12 form-group align-items-center justify-content-center">
                                <h4>Subject List</h4>
                                <div class="text-danger error-text MATHEMATICS_error"></div>
                                <div class="text-danger error-text PHYSICAL_SCIENCE_error"></div>
                                <div class="text-danger error-text Subjects_error"></div>
                            </div>
                            <div class="row  form-group d-flex justify-content-center" id="subjects">

                            </div>

                        </div>
                    </form>
                </div>

                <div class="modal-footer">

                    <button type="submit" name="save_updates" class="btn btn-primary" id="save">Update</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>

                </div>

            </div>
        </div>
    </div>
    <!-- update Model Date of Birth and Gender -->
    <!-- ADD CANDIDATE MODAL -->
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
                        <div>
                            @csrf
                        </div>
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label for="candidate_No" class="control-label">Candidate number</label>
                                <input type="text" class="form-control " placeholder="Enter Candidate Number"
                                    name="candidate_No" id="candidate_No" placeholder="*Candidate number">
                                <span class="help-block"></span>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="level" class="control-label">Level</label>
                                <select name="level" class="form-control" id="level">
                                    <option value="">Select level</option>
                                    <option value="LGCSE">LGCSE</option>
                                    <option value="LGCSEGR">LGCSE (Grade 11)</option>
                                </select>
                                <span class="help-block"></span>

                            </div>
                            <div class="form-group  col-md-4">
                                <label for="center_no" class="control-label">Center</label>
                                <select name="center_no" class="form-control" id="center_no">
                                    <option value="">
                                        Select Center</option>
                                    @foreach ($centers as $center)
                                        <option value="{{ $center->center_no }}">
                                            {{ $center->center_no }}-{{ $center->center_name }}</option>
                                    @endforeach
                                </select>
                                <span class="help-block"></span>

                            </div>
                        </div>
                        <div class="candidateinfo">
                        </div>
                    </form>

                </div>
                <div class="modal-footer">
                    <button type="submit" name="add_user" class="btn btn-primary" disabled id="save-candidate">Add</button>
                    <button type="button" class="btn btn-danger resetform" id="adduser_close"
                        data-dismiss="modal">Close</button>

                </div>
            </div>
        </div>

    </div>
    <!--END ADD CANDIDATE MODEL -->
@endsection
