@extends('layouts.admin')
@section('content')
    <div class="main">
        <!-- MAIN CONTENT -->
        <div class="main-content">
            <div class="container-fluid">
                <h3 class="page-title">Manage Invigilator sessions</h3>
                <div class="row">
                    <div class="col-md-12">
                        {{-- Filtering Registered Centres --}}
                        <div class="panel">
                            <div class="panel-heading">
                                <h3 class="panel-title">Time Sheet</h3>
                            </div>
                            <div class="panel-body">

                                <div class="pull-left">
                                    <button class="btn btn-primary ml-4" data-toggle="modal"
                                        data-target="#addTimesheetModal">+Timesheet</button>
                                    <button type="button" class="btn btn-primary" data-toggle="modal"
                                        data-target="#uploadTimesheetModal">
                                        + Upload CSV
                                    </button>
                                    <button type="button" class="btn btn-primary" data-toggle="modal"
                                        data-target="#add-proprietor-modal">
                                        + Proprietor
                                    </button>
                                </div>
                                <div class="clearfix"></div>
                                {{-- TIMESHEET TABLE --}}
                                <div class="timesheet-table">
                                    <table class="table table-hover" id="data-table-timesheet">
                                        <thead class="thead-light">
                                            <tr>
                                                <th></th>
                                                <th>Center No</th>
                                                <th>National Id</th>
                                                <th>Email</th>
                                                <th>Other Names</th>
                                                <th>Surname</th>
                                                <th>Action</th>
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
        <!-- ADD TIMESHEET MAIN CONTENT -->
        <div class="modal fade" id="addTimesheetModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Add Invigilator Session</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="addTimesheetForm" action="{{ route('admin.invigilations.timesheet.store') }}"
                            method="POST">
                            @csrf
                            <div class="row">
                                <div class="form-group col-md-12">
                                    <label for="profile_id" class="col-form-label">Invigilator</label>
                                    <select class="form-control profile-Invigilator" name="profile_id" id="profile_id">
                                        <option value="">Select</option>
                                        @foreach ($invigilation_profiles as $invigilation_profile)
                                            <option value="{{ $invigilation_profile->id }}">
                                                {{ $invigilation_profile->other_names }}
                                                {{ $invigilation_profile->surname }} -
                                                {{ $invigilation_profile->center_no }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-md-12">
                                    <label for="options">Subjects</label>
                                    <div id="options-checkboxes">
                                        <!-- Checkbox options will be loaded here -->
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">

                        <button type="button" class="btn btn-primary" id="add-session">Save</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- END ADD TIMESHEET MAIN CONTENT -->
        <!-- UPLOAD TIMESHEET MAIN CONTENT -->
        <div class="modal fade" id="uploadTimesheetModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Upload Invigilator Session</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form id="importForm" enctype="multipart/form-data" accept-charset="utf-8">
                        <div class="modal-body">
                            <p>Uploading a comma-separated (CSV) values spreadsheet is a way to add=
                                bulk sessions at once.
                            </p>
                            <p>To get started, download the CSV template. Enter all the sessions
                                details you
                                want to add and save the file.
                                and upload the CSV file.
                            </p>
                            <a href="" download class="btn template_download"><i class="fa fa-download"></i>
                                Download</a>

                            <hr>
                            <div id="alert-container">
                            </div>
                            @csrf
                            <div class="form-group  @error('upload') has-error @enderror">
                                <label class="control-label" for="upload">Upload File</label>
                                <input type="file" name="upload" class="form-control" id="upload">
                                @error('upload')
                                    <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary" id="upload-session">update</button>
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- ADD  Proprietor MAIN CONTENT -->
        <div class="modal fade" id="add-proprietor-modal" tabindex="-1" role="dialog"
            aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel"> Proprietor</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="add-proprietor-form" method="post"
                            action="{{ route('admin.invigilations.proprietors.store') }}">
                            @csrf
                            <div class="form-group col-md-6">
                                <label for="session" class="col-form-label">Session</label>
                                <select class="form-control dropdown-selected" name="session" id="session">
                                    <option value="">Select</option>
                                    @foreach ($years as $year)
                                        <option value="{{ $year->id }}">
                                            {{ $year->session }} {{ $year->financial_year }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="level" class="col-form-label">Level</label>
                                <select class="form-control dropdown-selected" name="level" id="level">
                                    <option value="">Select</option>
                                    @foreach ($levels as $level)
                                        <option value="{{ $level->level }}">
                                            {{ $level->level }}
                                        </option>
                                    @endforeach
                                </select>

                            </div>

                            <div class="form-group col-md-12">
                                <label for="center_no" class="col-form-label">Center</label>
                                <select class="form-control" name="center_no" id="center_no">
                                    <option value="">Select</option>
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="proprietor_source" class="col-form-label">proprietor
                                    Source</label>
                                <select class="form-control" name="proprietor_source" id="proprietor_source">
                                    <option value="">Select</option>
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="proprietor_target" class="col-form-label">proprietor
                                    Target </label>
                                <select class="form-control" name="proprietor_target" id="proprietor_target">
                                    <option value="">Select</option>
                                </select>
                            </div>
                            <div class="form-group col-md-6" id="timetables">
                            </div>
                            <div class="clearfix"></div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id="add-proprietor">Save</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- END UPLOAD TIMESHEET MAIN CONTENT -->
        <!-- UPDATE TIMESHEET MAIN CONTENT -->
        <div class="modal fade" id="editTimesheetModal" tabindex="-1" role="dialog"
            aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Update Invigilator Session </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="" id="editTimesheetForm" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="form-group col-md-12">
                                    <label for="profile_id" class="col-form-label">Invigilator</label>
                                    <select class="form-control profile-select" name="profile_id" id="profile_id">
                                        <option value="">Select</option>
                                        @foreach ($invigilation_profiles as $invigilation_profile)
                                            <option value="{{ $invigilation_profile->id }}">
                                                {{ $invigilation_profile->other_names }}
                                                {{ $invigilation_profile->surname }} -
                                                {{ $invigilation_profile->center_no }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-md-12">
                                    <label for="options">Subjects</label>
                                    <div id="invigilator-sessions">
                                    </div>
                                </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id="update-session">update</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- END UPDATE TIMESHEET MAIN CONTENT -->
    </div>
    <!-- END MAIN CONTENT -->

    @push('scripts')
        <script type="text/javascript">
            toastr.options = {
                closeButton: true,
                newestOnTop: false,
                progressBar: true,
                positionClass: "toast-top-center",
                preventDuplicates: false,
                onclick: null,
                showDuration: "3000",
                hideDuration: "8000",
                timeOut: "10000",
                extendedTimeOut: "8000",
                showEasing: "swing",
                hideEasing: "linear",
                showMethod: "fadeIn",
                hideMethod: "fadeOut",
            };

            $(document).ready(function() {


                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                var table = $('#data-table-timesheet').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('admin.invigilations.timesheet.index') }}",
                    columns: [

                        {
                            className: 'dt-control',
                            data: null,
                            name: 'session',
                            "defaultContent": '',
                            searchable: false,
                            orderable: false,
                        },

                        {
                            data: 'center_no',
                            name: 'center_no'
                        },
                        {
                            data: 'national_id',
                            name: 'national_id'
                        },

                        {
                            data: 'email',
                            name: 'email'
                        },
                        {
                            data: 'other_names',
                            name: 'other_names'
                        },
                        {
                            data: 'surname',
                            name: 'surname'
                        },

                        {
                            data: 'actions',
                            name: 'actions',
                            orderable: false,
                            searchable: false
                        },
                    ],
                    errors(data, ) {

                    }
                });


                function format(d) {
                    console.log(d);
                    var invigilator_sessions = JSON.parse(d.invigilator_sessions);
                    var sessions = "";
                    $.each(invigilator_sessions, function(i, invigilator_session) {
                        sessions += `<tr>
                            <td>${invigilator_session.actions}</td>
                             <td>${invigilator_session.subject_code}${invigilator_session.paper_no}</td>
                              <td>${invigilator_session.date_time}</td>
                            </tr>`;

                    });

                    return (
                        `<table class='table mb-0'>
                                <tr class='table-primary'>
                                <th></th>
                                    <th>Subject</th>
                                    <th>Exam Date</th>
                                </tr>
                                ${ sessions}
                            </table>`
                    );
                }
                $("#data-table-timesheet tbody").on("click", "td.dt-control", function() {
                    var tr = $(this).closest("tr");
                    var row = table.row(tr);

                    if (row.child.isShown()) {
                        row.child.hide();
                        tr.removeClass("shown");
                    } else {
                        row.child(format(row.data()), "p-0").show();
                        tr.addClass("shown");
                    }
                });






                // Add Timesheet
                $(document).on('click', '#add-session', function(ev) {
                    event.preventDefault();
                    var addtime = $("#addTimesheetForm");
                    var url = addtime.attr('action');
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: addtime.serialize(),
                        success: function(response) {
                            console.log(response);
                            if ($.isEmptyObject(response.errors)) {
                                toastr.success(response.success);
                                $('#data-table-timesheet').DataTable().ajax.reload();
                                $('#addTimesheetModal').modal('hide');
                            } else {
                                printErrorMsg('#addTimesheetForm', response.errors);
                            }
                        },
                        error: function(xhr) {
                            var errors = xhr.responseJSON.errors;
                            var errorHtml = '';
                            $.each(errors, function(key, value) {
                                errorHtml += '<p>' + value[0] + '</p>';
                            });
                            $('#errorMessages').html(errorHtml).show();
                            toastr.error("There were errors in your submission.");
                        }
                    });
                });
                // Edit Timesheet
                $(document).on('click', '.edit-session', function(ev) {
                    ev.preventDefault();
                    var url = $(this).data("url");
                    $.ajax({
                        type: "GET",
                        url: url,
                        success: function(data) {
                            var sessions = data.sessions
                            $('#editTimesheetModal').modal('show');
                            var updateUrl = data.url;
                            var parent = '#editTimesheetForm';
                            $(`${parent}`).attr('action', updateUrl);
                            $("#invigilator-sessions").html(sessions)
                            $(`${parent} #profile_id`).val(data.invigilator.id);
                        },
                        error: function(xhr, status, error) {
                            toastr.error("There were errors in your submission.");
                            console.log('Error:', error);

                        }
                    });
                });
                // Update Timesheet
                $(document).on('click', '#update-session', function(ev) {
                    ev.preventDefault();
                    var editForm = $("#editTimesheetForm");
                    var url = editForm.attr('action');
                    $.ajax({
                        type: "POST",
                        data: editForm.serializeArray(),
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        url: url,
                        success: function(response) {
                            console.log(response);
                            if ($.isEmptyObject(response.errors)) {
                                toastr.success(response.success);
                                $('#data-table-timesheet').DataTable().ajax.reload();
                                $('#editTimesheetModal').modal('hide');
                            } else {
                                printErrorMsg('#editTimesheetForm', response.errors);
                            }
                        },
                        error: function(xhr, status, error) {
                            toastr.error(
                                "There were errors in your submission.");
                            console.log('error:', error);
                        }
                    });
                });
                // Delete Timesheet
                $(document).on('click', '.delete-session', function() {
                    if (confirm("Are you sure you are delete the records?")) {
                        var url = $(this).data("url");
                        $.ajax({
                            type: "DELETE",
                            url: url,
                            success: function(response) {
                                console.log(response);
                                $('#data-table-timesheet').DataTable().ajax.reload();
                             toastr.success(response.success);

                            },
                            error: function(data) {
                                console.log('Error:', data);
                            }
                        });
                    } else {
                        return
                    }
                });

                // Add Timesheet
                $('.dropdown-selected').on("change", function(event) {
                    var name = $(this).attr("name");
                    var value = $(this).val();
                    var inputData = $("#add-proprietor-form").serialize();
                    $.ajax({
                        type: "GET",
                        url: "{{ route('admin.invigilations.proprietors.index') }}",
                        data: `${inputData}&center_filter=1`,
                        success: function(response) {
                            var formElement = 'center_no';
                            var centers = response.centers;



                            $(`#${formElement}`).empty();
                            $(`#${formElement}`).append(
                                `<option value=''>Please Select ${formElement}</option>`
                            );
                            $.each(centers, function(key, center) {
                                $(`#${formElement}`).append(
                                    `<option value='${center.center_no}'>  ${center.center_no}-${center.center_name}</option>`
                                );
                            });


                        }
                    });

                });
                $('#center_no').on("change", function(event) {
                    var name = $(this).attr("name");
                    var value = $(this).val();
                    var inputData = $("#add-proprietor-form").serialize();
                    $.ajax({
                        type: "GET",
                        url: "{{ route('admin.invigilations.proprietors.index') }}",
                        data: `${inputData}&center_sessions=1`,
                        success: function(response) {
                            var invigilators = response.invigilators;
                            var timetables = response.timetables;



                            console.log(response)

                            //proprietor_target
                            //proprietor_source
                            $('#timetables').html(timetables);
                            $(`#proprietor_target`).empty();
                            $(`#proprietor_target`).append(
                                `<option value=''>Please Select proprietor target</option>`
                            );
                            $.each(invigilators, function(key, invigilator) {
                                $(`#proprietor_target`).append(
                                    `<option value='${invigilator.id}'>  ${invigilator.surname} ${invigilator.other_names}</option>`
                                );
                            });

                            $(`#proprietor_source`).empty();
                            $(`#proprietor_source`).append(
                                `<option value=''>Please Select proprietor source</option>`
                            );
                            $.each(invigilators, function(key, invigilator) {
                                $(`#proprietor_source`).append(
                                    `<option value='${invigilator.id}'>  ${invigilator.surname} ${invigilator.other_names}</option>`
                                );
                            });



                        }
                    });

                });
                $(document).on("click", "#timesheet-proprietor  input", function() {
                    if ($(this).prop("checked")) {
                        var className = $(this).attr("class");
                        $("." + className).prop("checked", false);
                        $(this).prop("checked", true);
                    } else {
                        var className = $(this).attr("class");
                        $("." + className).prop("checked", false);
                        $(this).prop("checked", false);
                    }
                    var timesheet_source = $('input[name="timesheet_source[]"]:checked').length;
                    var timesheet_target = $('input[name="timesheet_target[]"]:checked').length;
                    $('.source-total').html(timesheet_source)
                    $('.target-total').html(timesheet_target);
                });

                //add-proprietor

                $(document).on('click', '#add-proprietor', function(ev) {
                    event.preventDefault();
                    var url = $("#add-proprietor-form").attr('action');
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: $("#add-proprietor-form").serialize(),
                        success: function(response) {
                            console.log(response);
                            if ($.isEmptyObject(response.errors)) {
                                toastr.success(response.success);
                                $('#add-proprietor-modal').modal('hide');
                                $('#data-table-timesheet').DataTable().ajax.reload();
                            } else {
                                printErrorMsg('#add-proprietor-form', response.errors);
                            }
                        },
                    });
                });
                $('.profile-Invigilator').on("change", function(event) {
                    var profile_id = $(this).val();
                    if (profile_id != "") {
                        $.ajax({
                            url: "{{ route('admin.invigilations.timesheet.getsubjects') }}",
                            data: {
                                profile_id: profile_id
                            },
                            method: "GET",
                            success: function(response) {
                             $(`#options-checkboxes`).html(response.sessions)
                            },
                            error: function() {
                                // $('#options-checkboxes').append('<p>Error loading subjects.</p>');
                            }
                        });

                    }


                })


















                function getsubjects(profile_id) {

                }

                /****  Print errors*******/
                function printErrorMsg(parent, msg) {
                    $(`${parent} input, ${parent} select, textarea`).each(function(index) {
                        $(`${parent} .help-block`).remove();
                        $(`${parent} .has-error`).removeClass('has-error');
                        // console.log(input.attr('type') + 'Name: ' + input.attr('name') + '  Value: ' + input.val());
                    });
                    $.each(msg, function(key, errors) {
                        for (const error in errors) {
                            const value = errors[error];

                            $(`[name='${key}']`).parent().addClass('has-error');
                            if (key == "gender") {
                                $(`${parent} [name='${key}']`).next().append(
                                    `<span class='help-block'>${value}</span>`);
                            } else {
                                $(`<span class='help-block'>${value}</span>`).insertAfter(
                                    `${parent} [name='${key}']`)
                            }


                        }
                    });
                }
                /****  Print errors End*******/
                /***** Import *********/
                $('#importForm').on('submit', function(event) {
                    event.preventDefault();
                    let formData = new FormData(this);
                    $.ajax({
                        url: '{{ route('admin.invigilations.import') }}',
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function(response) {

                            console.log(response);
                            $('#alert-container').empty();
                            if (response.status === 'error') {
                                let errorMessages = '';
                                $.each(response.errors, function(index, error) {
                                    errorMessages += `
                                    <tr>
                                        <td>${error.row.join(', ')}</td>
                                        <td>${error.messages.join(', ')}</td>
                                    </tr>
                                `;
                                });
                                $('#alert-container').html(`
                                <div class="overflow-scroll" style="overflow-y: scroll;overflow-x: hidden;height: 30rem;">
                                    <div class="alert alert-danger alert-dismissible" role="alert">
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                        <i class="fa fa-times-circle"></i>
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Row</th>
                                                    <th>Message</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                ${errorMessages}
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            `);
                            } else {
                                $('#alert-container').append(`
                                <div class="alert alert-success alert-dismissible" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    <i class="fa fa-check-circle"></i>
                                    <div>
                                        Total Invigilator Timesheets: ${response.totalCandidates} <br>
                                        Inserted Invigilator Timesheets: ${response.insertedCandidates} <br>
                                        Not Inserted Invigilator Timesheets: ${response.totalCandidates - response.insertedCandidates}
                                    </div>
                                </div>
                            `);
                                $('#data-table-timesheet').DataTable().ajax.reload();
                                $('#importForm')[0].reset();
                            }
                        },
                        error: function(xhr) {
                            // toastr.error("There were errors in your submission.");
                            console.log(xhr.responseText);
                        }
                    });
                });
            });
        </script>
    @endpush
@endsection
