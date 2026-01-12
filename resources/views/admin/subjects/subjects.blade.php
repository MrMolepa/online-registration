@extends('layouts.admin')

@section('content')
    <!-- ============================================================== -->
    <!-- Start Page Content -->
    <!-- MAIN -->
    <div class="main">
        <!-- MAIN CONTENT -->
        <div class="main-content">
            <div class="container-fluid">
                <h3 class="page-title">All Subjects</h3>
                <div class="row">
                    <div class="col-md-12">
                        <!-- PANEL NO CONTROLS -->
                        <div class="panel">
                            <div class="panel-heading">
                                <h3 class="panel-title">All Subjects</h3>
                            </div>
                            <div class="panel-body">

                                @if (session()->has('success'))
                                    <div class="alert alert-success alert-dismissible" role="alert">
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                                aria-hidden="true">&times;</span></button>
                                        <strong>Success! </strong> {{ session('success') }}
                                    </div>
                                @endif
                                @if (session()->has('error'))
                                    <div class="alert alert-danger alert-dismissible" role="alert">
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                                aria-hidden="true">&times;</span></button>
                                        <strong>Error! </strong> {{ session('error') }}
                                    </div>
                                @endif
                                <div class="custom-tabs-line tabs-line-bottom left-aligned">
                                    <ul class="nav" role="tablist">
                                        <li class="active"><a href="#subject-tab"
                                                role="tab"data-toggle="tab">Subjects</a></li>
                                        <li><a href="#options-tab" role="tab" data-toggle="tab">Options</a></li>
                                        <li><a href="#level-tab" role="tab" data-toggle="tab">Levels</a></li>
                                        <li><a href="#discipline-tab" role="tab" data-toggle="tab">Disciplines</a></li>
                                        <li><a href="#session-tab" role="tab" data-toggle="tab">Sessions</a></li>
                                    </ul>
                                </div>
                                <div class="tab-content">
                                    <div class="tab-pane fade in active" id="subject-tab">
                                        <fieldset>
                                            <legend>Filter</legend>
                                            <div class="row">
                                                <div class="form-group col-md-8">
                                                    <a href="" class="btn btn-info" data-toggle="modal"
                                                        data-target="#add-subject">
                                                        + create
                                                    </a>

                                                </div>
                                                <div class="col-md-4">
                                                    <div class="input-group" id="filters">
                                                        <span class="input-group-btn">
                                                            <button class="btn secondary" type="button">Level</button>
                                                        </span>
                                                        <select id="level" name="level"
                                                            class="form-control status-dropdown">
                                                            <option value="">Please Select Level</option>
                                                            @foreach ($levels as $level)
                                                                <option value="{{ $level->id }}"> {{ $level->level }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                            </div>
                                        </fieldset>
                                        <div class="table-responsive">
                                            <table class="table" name="tablename"id="subjects">
                                                <thead>
                                                    <tr>
                                                        <th></th>
                                                        <th>Subect Name</th>
                                                        <th>Short Name</th>
                                                        <th>Level</th>
                                                        <th>Discipline</th>
                                                        <th>Practical Fee </th>
                                                        <th>Delf Fee </th>
                                                        <th>Sync to </th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>


                                            </table>
                                            @push('scripts')
                                                <script>
                                                    $(function() {

                                                        var subjects = $('#subjects').DataTable({
                                                            processing: true,
                                                            serverSide: true,
                                                            // scrollY: 500,
                                                            // scrollX: true,
                                                            // scrollCollapse: true,
                                                            // deferRender: true,
                                                            "lengthMenu": [
                                                                [100, 250, 500, -1],
                                                                [100, 250, 500, "All"]
                                                            ],
                                                            ajax: {
                                                                url: "{{ route('admin.subjects.index') }}",
                                                                data: function(d) {
                                                                    d.level = $("#filters #level").val()
                                                                }
                                                            },
                                                            columns: [

                                                                {
                                                                    "className": 'dt-control',
                                                                    data: 'subject_code',
                                                                    name: 'subject_code',
                                                                    "orderable": false,
                                                                    "defaultContent": '',
                                                                    searchable: false

                                                                },
                                                                {
                                                                    data: 'subject_name',
                                                                    name: 'subject_name',
                                                                    searchable: true
                                                                },
                                                                {
                                                                    data: 'short_name',
                                                                    name: 'short_name',
                                                                },

                                                                {
                                                                    data: 'level',
                                                                    name: 'level',
                                                                },
                                                                {
                                                                    data: 'discipline',
                                                                    name: 'discipline',
                                                                },

                                                                {
                                                                    data: 'is_practical',
                                                                    name: 'is_practical',
                                                                },
                                                                {
                                                                    data: 'is_delf',
                                                                    name: 'is_delf',
                                                                },
                                                                {
                                                                    data: 'sync_timetable',
                                                                    name: 'sync_timetable',
                                                                },


                                                                {
                                                                    data: 'action',
                                                                    name: 'action',
                                                                    searchable: false,
                                                                    sortable: false

                                                                }
                                                            ]
                                                        });
                                                        $("#subjects").css("width", "98.5%");
                                                        // Add event listener for opening and closing details
                                                        $('#subjects').on('click', 'td.dt-control', function() {
                                                            var tr = $(this).closest('tr');
                                                            var row = subjects.row(tr);

                                                            if (row.child.isShown()) {
                                                                // This row is already open - close it
                                                                row.child.hide();
                                                                tr.removeClass('shown');
                                                            } else {
                                                                // Open this row
                                                                row.child(format(row.data())).show();
                                                                tr.addClass('shown');
                                                            }
                                                        });




                                                        $(document).on('change', '#filters #level', function(ev) {
                                                            $('#subjects').DataTable().ajax.reload();
                                                            console.log('ok');
                                                        });


                                                        /* Formatting function for row details - modify as you need */
                                                        function format(d) {



                                                            return '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">' +

                                                                '<tr>' +
                                                                '<td>' + d.session + '</td>' +
                                                                '</tr>' +
                                                                '<tr>' +
                                                                '<td>' + d.options + '</td>' +
                                                                '</tr>' +
                                                                '<tr>' +
                                                                '<td>' + d.components + '</td>' +
                                                                '</tr>' +


                                                                '</table>';
                                                        }





                                                    });
                                                </script>
                                            @endpush
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="level-tab">
                                        <div class="pull-right">
                                            <a href="" class="btn btn-info" data-toggle="modal"
                                                data-target="#add-level">
                                                + create
                                            </a>
                                        </div>

                                        <div class="clearfix"></div>
                                        <div class="table-responsive">
                                            <table class="table" name="tablename"id="levels-datatable">
                                                <thead>
                                                    <tr>
                                                        <th>id</th>
                                                        <th>Level</th>
                                                        <th>Description</th>
                                                        <th>Status</th>
                                                        <th>Private registration</th>
                                                        <th>Created At</th>
                                                        <th>Updated At</th>
                                                        <th>action</th>
                                                    </tr>
                                                </thead>
                                            </table>
                                            @push('scripts')
                                                <script>
                                                    $(function() {
                                                        var levels = $('#levels-datatable').DataTable({
                                                            processing: true,
                                                            serverSide: true,
                                                            deferRender: true,
                                                            "lengthMenu": [
                                                                [20, 50, 100, 200, 400, -1],
                                                                [20, 50, 100, 200, 400, "All"]
                                                            ],
                                                            ajax: "{{ route('admin.levels.index') }}",
                                                            columns: [{
                                                                    data: 'id',
                                                                    name: 'id',
                                                                    searchable: true
                                                                },
                                                                {
                                                                    data: 'level',
                                                                    name: 'level',
                                                                    searchable: true
                                                                },
                                                                {
                                                                    data: 'description',
                                                                    name: 'description',


                                                                },

                                                                {
                                                                    data: 'is_active',
                                                                    name: 'is_active',

                                                                },

                                                                {
                                                                    data: 'private_registration',
                                                                    name: 'private_registration',

                                                                },


                                                                {
                                                                    data: 'created_at',
                                                                    name: 'created_at',

                                                                },
                                                                {
                                                                    data: 'updated_at',
                                                                    name: 'updated_at',

                                                                },
                                                                {
                                                                    data: 'action',
                                                                    name: 'action',
                                                                    searchable: false,
                                                                    sortable: false

                                                                }

                                                            ]

                                                        });
                                                        $("#levels-datatable").css("width", "98.5%");






                                                        // TOASTER AND NOTIFICATION SETUP
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
                                                        //  Add Level
                                                        $(document).on('click', '#save-level', function(ev) {
                                                            ev.preventDefault();
                                                            var url = $('#addLevelForm').attr('action');
                                                            $.ajaxSetup({
                                                                headers: {
                                                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                                                }
                                                            });


                                                            var inputData = $("#addLevelForm").serialize();

                                                            $.ajax({
                                                                url: url,
                                                                method: "POST",
                                                                data: inputData,
                                                                success: function(data) {
                                                                    if ($.isEmptyObject(data.errors)) {
                                                                        $('#add-level').modal('hide');
                                                                        $('#addLevelForm .help-block').remove();
                                                                        $('#addLevelForm .has-error').removeClass('has-error');
                                                                        toastr.success(data.success);
                                                                        $('#levels-datatable').DataTable().ajax.reload();
                                                                    } else {
                                                                        printErrorMsg('#addLevelForm', data.errors);
                                                                    }


                                                                }
                                                            });


                                                        });
                                                        // Edit Level
                                                        $(document).on("click", "#levels-datatable .editBtn", function() {
                                                            //hide edit span

                                                            $(this).closest("tr").find(".editSpan").hide();

                                                            //show edit input
                                                            $(this).closest("tr").find(".editInput").show();

                                                            //hide edit button
                                                            $(this).closest("tr").find(".editBtn").hide();

                                                            //show edit button
                                                            $(this).closest("tr").find(".saveBtn").show();

                                                            $(this).closest("tr").find(".sessions-multiple").multiselect({
                                                                includeSelectAllOption: true,
                                                            });


                                                        });
                                                        // Update changes candidate
                                                        $(document).on("click", "#levels-datatable .saveBtn", function() {
                                                            actionUrl = $(this).data('url');
                                                            var trObj = $(this).closest("tr");
                                                            var ID = $(this).closest("tr").attr("id");
                                                            var inputData = $(this).closest("tr").find(".editInput").serialize();
                                                            $.ajaxSetup({
                                                                headers: {
                                                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                                                }
                                                            });
                                                            $.ajax({
                                                                type: "PUT",
                                                                url: actionUrl,
                                                                dataType: "json",
                                                                data: inputData,
                                                                success: function(data) {
                                                                    console.log(data);

                                                                    if ($.isEmptyObject(data.errors)) {
                                                                        trObj.find(".editInput").hide();
                                                                        trObj.find(".saveBtn").hide();
                                                                        trObj.find(".editSpan").show();
                                                                        trObj.find(".editBtn").show();
                                                                        toastr.success("You have successfully Saved Changes");
                                                                        $('#levels-datatable').DataTable().ajax.reload();
                                                                    } else {
                                                                        printErrorMsg(`#${ID}`, data.errors);
                                                                    }

                                                                },
                                                            });
                                                        });
                                                        // delete Candidate
                                                        $(document).on('click', '#levels-datatable .deleteBtn', function(ev) {
                                                            ev.preventDefault();
                                                            var url = $(this).data('url');
                                                            if (confirm("Are you sure you want to delete this candidates!") == true) {
                                                                $.ajaxSetup({
                                                                    headers: {
                                                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                                                    }
                                                                });

                                                                $.ajax({
                                                                    url: url,
                                                                    method: "DELETE",
                                                                    success: function(data) {
                                                                        if (data.success) {
                                                                            toastr.success(data.success);
                                                                            $('#levels-datatable').DataTable().ajax.reload();
                                                                        }



                                                                    }
                                                                });


                                                            } else {
                                                                return;
                                                            }

                                                        });
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





                                                    });
                                                </script>
                                            @endpush
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="options-tab">
                                        <div class="pull-right">
                                            <a href="" class="btn btn-info" data-toggle="modal"
                                                data-target="#add-option">
                                                + create
                                            </a>
                                        </div>
                                        <div class="clearfix"></div>
                                        <div class="table-responsive">
                                            <table class="table" name="tablename"id="options-datatable">
                                                <thead>
                                                    <tr>
                                                        <th>Option Code</th>
                                                        <th>Alternative Option</th>
                                                        <th>Description</th>
                                                        <th>Created At</th>
                                                        <th>Updated At</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                            </table>
                                            @push('scripts')
                                                <script>
                                                    $(function() {
                                                        var levels = $('#options-datatable').DataTable({
                                                            processing: true,
                                                            serverSide: true,
                                                            deferRender: true,
                                                            "lengthMenu": [
                                                                [20, 50, 100, 200, 400, -1],
                                                                [20, 50, 100, 200, 400, "All"]
                                                            ],
                                                            ajax: "{{ route('admin.options.index') }}",
                                                            columns: [{
                                                                    data: 'option_code',
                                                                    name: 'option_code',
                                                                    searchable: true
                                                                },
                                                                {
                                                                    data: 'alternative_option_code',
                                                                    name: 'alternative_option_code',
                                                                    searchable: true
                                                                },



                                                                {
                                                                    data: 'description',
                                                                    name: 'description',


                                                                },
                                                                {
                                                                    data: 'created_at',
                                                                    name: 'created_at',

                                                                },
                                                                {
                                                                    data: 'updated_at',
                                                                    name: 'updated_at',

                                                                },
                                                                {
                                                                    data: 'action',
                                                                    name: 'action',
                                                                    searchable: false,
                                                                    sortable: false

                                                                }

                                                            ]

                                                        });
                                                        $("#options-datatable").css("width", "98.5%");
                                                        // TOASTER AND NOTIFICATION SETUP
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
                                                        //  Add Option
                                                        $(document).on('click', '#save-option', function(ev) {
                                                            ev.preventDefault();
                                                            var url = $('#addOptionForm').attr('action');

                                                            console.log(url);
                                                            $.ajaxSetup({
                                                                headers: {
                                                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                                                }
                                                            });


                                                            var inputData = $("#addOptionForm").serialize();

                                                            $.ajax({
                                                                url: url,
                                                                method: "POST",
                                                                data: inputData,
                                                                success: function(data) {
                                                                    console.log(data);
                                                                    if ($.isEmptyObject(data.errors)) {
                                                                        $('#add-option').modal('hide');
                                                                        $('#addOptionForm .help-block').remove();
                                                                        $('#addOptionForm .has-error').removeClass('has-error');
                                                                        toastr.success(data.success);
                                                                        $('#options-datatable').DataTable().ajax.reload();
                                                                    } else {
                                                                        printErrorMsg('#addOptionForm', data.errors);
                                                                    }

                                                                }
                                                            });


                                                        });
                                                        // Edit Level
                                                        $(document).on("click", "#options-datatable .editBtn", function() {
                                                            //hide edit span

                                                            $(this).closest("tr").find(".editSpan").hide();

                                                            //show edit input
                                                            $(this).closest("tr").find(".editInput").show();

                                                            //hide edit button
                                                            $(this).closest("tr").find(".editBtn").hide();

                                                            //show edit button
                                                            $(this).closest("tr").find(".saveBtn").show();

                                                            $(this).closest("tr").find(".sessions-multiple").multiselect({
                                                                includeSelectAllOption: true,
                                                            });


                                                        });
                                                        // Update changes candidate
                                                        $(document).on("click", "#options-datatable .saveBtn", function() {
                                                            actionUrl = $(this).data('url');
                                                            var trObj = $(this).closest("tr");
                                                            var ID = $(this).closest("tr").attr("id");
                                                            var inputData = $(this).closest("tr").find(".editInput").serialize();
                                                            $.ajaxSetup({
                                                                headers: {
                                                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                                                }
                                                            });
                                                            $.ajax({
                                                                type: "PUT",
                                                                url: actionUrl,
                                                                dataType: "json",
                                                                data: inputData,
                                                                success: function(data) {
                                                                    console.log(data);

                                                                    if ($.isEmptyObject(data.errors)) {
                                                                        trObj.find(".editInput").hide();
                                                                        trObj.find(".saveBtn").hide();
                                                                        trObj.find(".editSpan").show();
                                                                        trObj.find(".editBtn").show();
                                                                        toastr.success("You have successfully Saved Changes");
                                                                        $('#options-datatable').DataTable().ajax.reload();
                                                                    } else {
                                                                        printErrorMsg(`#${ID}`, data.errors);
                                                                    }

                                                                },
                                                            });
                                                        });
                                                        // delete Candidate
                                                        $(document).on('click', '#options-datatable .deleteBtn', function(ev) {
                                                            ev.preventDefault();
                                                            var url = $(this).data('url');
                                                            if (confirm("Are you sure you want to delete this candidates!") == true) {
                                                                $.ajaxSetup({
                                                                    headers: {
                                                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                                                    }
                                                                });

                                                                $.ajax({
                                                                    url: url,
                                                                    method: "DELETE",
                                                                    success: function(data) {
                                                                        if (data.success) {
                                                                            toastr.success(data.success);
                                                                            $('#ptions-datatable').DataTable().ajax.reload();
                                                                        }


                                                                    }
                                                                });


                                                            } else {
                                                                return;
                                                            }

                                                        });
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





                                                    });
                                                </script>
                                            @endpush
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="discipline-tab">
                                        <div class="pull-right">
                                            <a href="" class="btn btn-info" data-toggle="modal"
                                                data-target="#add-discipline">
                                                + create
                                            </a>
                                        </div>
                                        <div class="clearfix"></div>
                                        <div class="table-responsive">
                                            <table class="table" name="tablename"id="disciplines-datatable">
                                                <thead>
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>Name</th>
                                                        <th>Display Name</th>
                                                        <th>Created At</th>
                                                        <th>Updated At</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                            </table>
                                            @push('scripts')
                                                <script>
                                                    $(function() {
                                                        var levels = $('#disciplines-datatable').DataTable({
                                                            processing: true,
                                                            serverSide: true,
                                                            deferRender: true,
                                                            "lengthMenu": [
                                                                [20, 50, 100, 200, 400, -1],
                                                                [20, 50, 100, 200, 400, "All"]
                                                            ],
                                                            ajax: "{{ route('admin.disciplines.index') }}",
                                                            columns: [{
                                                                    data: 'id',
                                                                    name: 'id',
                                                                    searchable: true
                                                                },
                                                                {
                                                                    data: 'name',
                                                                    name: 'name',
                                                                    searchable: true
                                                                },
                                                                {
                                                                    data: 'display_name',
                                                                    name: 'display_name',
                                                                },


                                                                {
                                                                    data: 'created_at',
                                                                    name: 'created_at',

                                                                },
                                                                {
                                                                    data: 'updated_at',
                                                                    name: 'updated_at',

                                                                },
                                                                {
                                                                    data: 'action',
                                                                    name: 'action',
                                                                    searchable: false,
                                                                    sortable: false

                                                                }

                                                            ]

                                                        });
                                                        $("#disciplines-datatable").css("width", "98.5%");
                                                        // TOASTER AND NOTIFICATION SETUP
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
                                                        //  Add Discipline
                                                        $(document).on('click', '#save-discipline', function(ev) {
                                                            ev.preventDefault();
                                                            var url = $('#addDisciplineForm').attr('action');
                                                            $.ajaxSetup({
                                                                headers: {
                                                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                                                }
                                                            });


                                                            var inputData = $("#addDisciplineForm").serialize();

                                                            $.ajax({
                                                                url: url,
                                                                method: "POST",
                                                                data: inputData,
                                                                success: function(data) {
                                                                    if ($.isEmptyObject(data.errors)) {
                                                                        $('#add-discipline').modal('hide');
                                                                        $('#addDisciplineForm .help-block').remove();
                                                                        $('#addDisciplineForm .has-error').removeClass('has-error');
                                                                        toastr.success(data.success);
                                                                        $('#disciplines-datatable').DataTable().ajax.reload();
                                                                    } else {
                                                                        printErrorMsg('#addDisciplineForm', data.errors);
                                                                    }


                                                                }
                                                            });


                                                        });
                                                        // Edit Subject
                                                        $(document).on("click", "#disciplines-datatable .editBtn", function() {
                                                            //hide edit span

                                                            $(this).closest("tr").find(".editSpan").hide();

                                                            //show edit input
                                                            $(this).closest("tr").find(".editInput").show();

                                                            //hide edit button
                                                            $(this).closest("tr").find(".editBtn").hide();

                                                            //show edit button
                                                            $(this).closest("tr").find(".saveBtn").show();



                                                        });
                                                        // Update changes candidate
                                                        $(document).on("click", "#disciplines-datatable .saveBtn", function() {
                                                            actionUrl = $(this).data('url');
                                                            var trObj = $(this).closest("tr");
                                                            var ID = $(this).closest("tr").attr("id");
                                                            var inputData = $(this).closest("tr").find(".editInput").serialize();
                                                            $.ajaxSetup({
                                                                headers: {
                                                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                                                }
                                                            });
                                                            $.ajax({
                                                                type: "PUT",
                                                                url: actionUrl,
                                                                dataType: "json",
                                                                data: inputData,
                                                                success: function(data) {
                                                                    console.log(data);

                                                                    if ($.isEmptyObject(data.errors)) {
                                                                        trObj.find(".editInput").hide();
                                                                        trObj.find(".saveBtn").hide();
                                                                        trObj.find(".editSpan").show();
                                                                        trObj.find(".editBtn").show();
                                                                        toastr.success("You have successfully Saved Changes");
                                                                        $('#disciplines-datatable').DataTable().ajax.reload();
                                                                    } else {
                                                                        printErrorMsg(`#${ID}`, data.errors);
                                                                    }

                                                                },
                                                            });
                                                        });
                                                        // delete Candidate
                                                        $(document).on('click', '#disciplines-datatable .deleteBtn', function(ev) {
                                                            ev.preventDefault();
                                                            var url = $(this).data('url');
                                                            if (confirm("Are you sure you want to delete this records!") == true) {
                                                                $.ajaxSetup({
                                                                    headers: {
                                                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                                                    }
                                                                });

                                                                $.ajax({
                                                                    url: url,
                                                                    method: "DELETE",
                                                                    success: function(data) {
                                                                        if (data.success) {
                                                                            toastr.success(data.success);
                                                                            $('#disciplines-datatable').DataTable().ajax.reload();
                                                                        }



                                                                    }
                                                                });


                                                            } else {
                                                                return;
                                                            }

                                                        });
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


                                                    });
                                                </script>
                                            @endpush
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="session-tab">
                                        <div class="pull-right">
                                            <a href="" class="btn btn-info" data-toggle="modal"
                                                data-target="#add-session">
                                                + create
                                            </a>
                                        </div>
                                        <div class="clearfix"></div>
                                        <div class="table-responsive">
                                            <table class="table" name="tablename"id="sessions-datatable">
                                                <thead>
                                                    <tr>
                                                        <th>id</th>
                                                        <th>Session</th>
                                                        <th>Description</th>
                                                        <th>Financial Year</th>
                                                        <th>Closing Date</th>
                                                        <th>Status</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>


                                            </table>
                                            @push('scripts')
                                                <script>
                                                    $(function() {
                                                        // TOASTER AND NOTIFICATION SETUP
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
                                                        var session = $('#sessions-datatable').DataTable({
                                                            processing: true,
                                                            serverSide: true,
                                                            deferRender: true,
                                                            "lengthMenu": [
                                                                [20, 50, 100, 200, 400, -1],
                                                                [20, 50, 100, 200, 400, "All"]
                                                            ],
                                                            ajax: "{{ route('admin.sessions.index') }}",
                                                            columns: [{
                                                                    data: 'id',
                                                                    name: 'id',
                                                                    searchable: true
                                                                },
                                                                {
                                                                    data: 'session',
                                                                    name: 'session',
                                                                    searchable: true
                                                                },
                                                                {
                                                                    data: 'description',
                                                                    name: 'description',


                                                                },

                                                                {
                                                                    data: 'financial_year',
                                                                    name: 'financial_year',

                                                                },
                                                                {
                                                                    data: 'financial_closing_date',
                                                                    name: 'financial_closing_date',

                                                                },
                                                                {
                                                                    data: 'is_active',
                                                                    name: 'is_active',
                                                                },

                                                                {
                                                                    data: 'action',
                                                                    name: 'action',
                                                                    searchable: false,
                                                                    sortable: false

                                                                }

                                                            ]

                                                        });
                                                        $("#sessions-datatable").css("width", "98.5%");
                                                        //  Add Subject
                                                        $(document).on('click', '#save-session', function(ev) {
                                                            ev.preventDefault();
                                                            var url = $('#addSessionForm').attr('action');
                                                            $.ajaxSetup({
                                                                headers: {
                                                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                                                }
                                                            });


                                                            var inputData = $("#addSessionForm").serialize();

                                                            $.ajax({
                                                                url: url,
                                                                method: "POST",
                                                                data: inputData,
                                                                success: function(data) {
                                                                    if ($.isEmptyObject(data.errors)) {
                                                                        $('#add-session').modal('hide');
                                                                        $('#addSessionForm .help-block').remove();
                                                                        $('#aaddSessionForm .has-error').removeClass('has-error');
                                                                        toastr.success(data.success);
                                                                        $('#sessions-datatable').DataTable().ajax.reload();
                                                                    } else {
                                                                        printErrorMsg('#addSessionForm', data.errors);
                                                                    }


                                                                }
                                                            });


                                                        });
                                                        // Edit Sessions
                                                        $(document).on("click", "#sessions-datatable .editBtn", function() {
                                                            //hide edit span

                                                            $(this).closest("tr").find(".editSpan").hide();

                                                            //show edit input
                                                            $(this).closest("tr").find(".editInput").show();

                                                            //hide edit button
                                                            $(this).closest("tr").find(".editBtn").hide();

                                                            //show edit button
                                                            $(this).closest("tr").find(".saveBtn").show();

                                                            $(this).closest("tr").find(".sessions-multiple").multiselect({
                                                                includeSelectAllOption: true,
                                                            });


                                                        });
                                                        // Update changes candidate
                                                        $(document).on("click", "#sessions-datatable .saveBtn", function() {
                                                            actionUrl = $(this).data('url');
                                                            var trObj = $(this).closest("tr");
                                                            var ID = $(this).closest("tr").attr("id");
                                                            var inputData = $(this).closest("tr").find(".editInput").serialize();
                                                            $.ajaxSetup({
                                                                headers: {
                                                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                                                }
                                                            });
                                                            $.ajax({
                                                                type: "PUT",
                                                                url: actionUrl,
                                                                dataType: "json",
                                                                data: inputData,
                                                                success: function(data) {
                                                                    console.log(data);

                                                                    if ($.isEmptyObject(data.errors)) {
                                                                        trObj.find(".editInput").hide();
                                                                        trObj.find(".saveBtn").hide();
                                                                        trObj.find(".editSpan").show();
                                                                        trObj.find(".editBtn").show();
                                                                        toastr.success("You have successfully Saved Changes");
                                                                        $('#sessions-datatable').DataTable().ajax.reload();
                                                                    } else {
                                                                        printErrorMsg(`#${ID}`, data.errors);
                                                                    }

                                                                },
                                                            });
                                                        });
                                                        // delete Candidate
                                                        $(document).on('click', '#sessions-datatable .deleteBtn', function(ev) {
                                                            ev.preventDefault();
                                                            var url = $(this).data('url');
                                                            if (confirm("Are you sure you want to delete this sesseion!") == true) {
                                                                $.ajaxSetup({
                                                                    headers: {
                                                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                                                    }
                                                                });

                                                                $.ajax({
                                                                    url: url,
                                                                    method: "DELETE",
                                                                    success: function(data) {
                                                                        if (data.success) {
                                                                            toastr.success(data.success);
                                                                            $('#sessions-datatable').DataTable().ajax.reload();
                                                                        } else {
                                                                            toastr.error(data.error);
                                                                        }



                                                                    }
                                                                });


                                                            } else {
                                                                return;
                                                            }

                                                        });


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






                                                    });
                                                </script>
                                            @endpush
                                        </div>
                                    </div>
                                    <!-- END TABBED CONTENT -->
                                </div>
                            </div>
                        </div>
                        <!-- END PANEL NO CONTROLS -->
                    </div>

                </div>


            </div>
        </div>
        <!-- END MAIN CONTENT -->
        <!-- ADD SUBJECT MODAL -->
        <div class="modal fade bd-modal-md" id="add-subject" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog ">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close resetform" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h3 class="modal-title">New Subject</h3>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('admin.subjects.store') }}" method="post" id="addSubjectForm">
                            <div>
                                @csrf
                            </div>
                            <div class="form-group  ">
                                <label for="subject_code">Subject Code</label>
                                <input type="text" class="form-control" name="subject_code" id="subject_code"
                                    value="" />
                            </div>
                            <div class="form-group">
                                <label class="control-label" for="subject_name">Subject Name</label>
                                <input type="text" class="form-control" name="subject_name" id="subject_name"
                                    value="" />
                            </div>
                            <div class="form-group">
                                <label class="control-label" for="short_name">Short Name</label>
                                <input type="text" name="short_name" class="form-control" id="short_name"
                                    value="" />
                            </div>


                            <div class="form-group">
                                <label for="level" class="control-label">Level</label>
                                <select id="level" name="level" class="form-control">
                                    <option value="">Please Select Level</option>
                                    @foreach ($levels as $level)
                                        <option value="{{ $level->id }}"> {{ $level->level }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="discipline" class="control-label">Discipline</label>
                                <select id="discipline" name="discipline" class="form-control">
                                    <option value="">Please Select discipline</option>
                                    @foreach ($disciplines as $discipline)
                                        <option value="{{ $discipline->id }}"> {{ $discipline->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="control-label" for="session">session</label>
                                <div>
                                    <select class='sessions-multiple form-control' name='sessions[]' id="session"
                                        multiple='multiple'>
                                        @foreach ($sessions as $session)
                                            <option value='{{ $session->id }}'>
                                                {{ $session->session }}-{{ $session->financial_year }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label" for="option">Options</label>
                                <div>
                                    <select class='options-multiple form-control' name='options[]' id="option"
                                        multiple='multiple'>
                                        @foreach ($options as $option)
                                            <option value='{{ $option->option_code }}'>
                                                {{ $option->description }}({{ $option->option_code }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>



                        </form>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="add-subject" class="btn btn-primary"
                            id="save-subject">Save</button>
                        <button type="button" class="btn btn-danger resetform" id="close"
                            data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>

        </div>
        <!--END ADD SUBJECT MODEL -->

        <!-- ADD OPTION MODAL -->
        <div class="modal fade bd-modal-md" id="add-option" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog ">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close resetform" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h3 class="modal-title">New Option</h3>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('admin.options.store') }}" method="post" id="addOptionForm">
                            <div>
                                @csrf
                            </div>
                            <div class="form-group">
                                <label class="control-label" for="option_code">Option Code</label>
                                <input type="text" class="form-control" name="option_code" id="option_code"
                                    value="" />
                            </div>
                            <div class="form-group">
                                <label class="control-label" for="option_code">Alternative option</label>
                                <input type="text" class="form-control" name="alternative_option_code"
                                    id="alternative_option_code" value="" />
                            </div>
                            <div class="form-group">
                                <label class="control-label" for="description">Description</label>
                                <input type="text" name="description" class="form-control" id="description"
                                    value="" />
                            </div>
                        </form>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="add-option" class="btn btn-primary" id="save-option">Save</button>
                        <button type="button" class="btn btn-danger resetform" id="close"
                            data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>

        </div>
        <!--END ADD LEVEL MODEL -->
        <!-- ADD LEVEL MODAL -->
        <div class="modal fade bd-modal-md" id="add-level" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog ">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close resetform" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h3 class="modal-title">New Level</h3>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('admin.levels.store') }}" method="post" id="addLevelForm">
                            <div>
                                @csrf
                            </div>
                            <div class="form-group">
                                <label class="control-label" for="level">Level</label>
                                <input type="text" class="form-control" name="level" id="level"
                                    value="" />
                            </div>
                            <div class="form-group">
                                <label class="control-label" for="description">Description</label>
                                <input type="text" name="description" class="form-control" id="description"
                                    value="" />
                            </div>

                            <div class="form-group">
                                <label class="control-label" for="status">Status</label>
                                <div>
                                    <select class='form-control' name='is_active' id="status">
                                        <option value=''>Please Select Status</option>
                                        <option value='1'>Enabled</option>
                                        <option value='0'>Disabled</option>

                                    </select>
                                </div>
                            </div>



                        </form>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="add-subject" class="btn btn-primary" id="save-level">Save</button>
                        <button type="button" class="btn btn-danger resetform" id="close"
                            data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>

        </div>
        <!--END ADD LEVEL MODEL -->
        <!-- ADD Disciplines MODAL -->
        <div class="modal fade bd-modal-md" id="add-discipline" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog ">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close resetform" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h3 class="modal-title">New discipline</h3>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('admin.disciplines.store') }}" method="post" id="addDisciplineForm">
                            <div>
                                @csrf
                            </div>
                            <div class="form-group  ">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" name="name" id="name"
                                    value="" />
                            </div>
                            <div class="form-group">
                                <label class="control-label" for="display_name">Display name</label>
                                <input type="text" class="form-control" name="display_name" id="display_name"
                                    value="" />
                            </div>
                            <div class="form-group">
                                <label for="level_id" class="control-label">Level</label>
                                <select id="level_id" name="level_id" class="form-control">
                                    <option value="">Please Select Level</option>
                                    @foreach ($levels as $level)
                                        <option value="{{ $level->id }}"> {{ $level->level }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </form>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="add-discipline" class="btn btn-primary"
                            id="save-discipline">Save</button>
                        <button type="button" class="btn btn-danger resetform" id="close"
                            data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>

        </div>
        <!--END ADD Disciplines MODEL -->
        <!-- ADD SESSION  MODAL -->
        <div class="modal fade bd-modal-md" id="add-session" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog ">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close resetform" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h3 class="modal-title">New Session</h3>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('admin.sessions.store') }}" method="post" id="addSessionForm">
                            <div>
                                @csrf
                            </div>
                            <div class="form-group">
                                <label class="control-label" for="exams_month">Exams Months</label>
                                <div>
                                    <select class='form-control' name='exams_month' id="exams_month">
                                        <option value=''>Please Select</option>

                                        @for ($i = 0; $i < 13; $i++)
                                            <option value='{{ $i }}'>
                                                {{ date('F', strtotime('01.' . $i . '.2025')) }}/{{ date('F', strtotime('01.' . ($i + 1) . '.2001')) }}
                                            </option>
                                        @endfor

                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="financial_year">Financial Year </label>
                                <input type="text" class="form-control" name="financial_year" id="financial_year"
                                    readonly value="{{ date('Y') . '-' . (date('Y') + 1) }}" />
                            </div>

                            <div class="form-group">
                                <label for="financial_closing">Financial Closing Date</label>
                                <input type="date" class="form-control" name="financial_closing"
                                    id="financial_closing" value="" />
                            </div>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="is_active" id="is_active"
                                    value="1" />
                                <label class="form-check-label" for="is_active">Activate</label>
                            </div>
                            <fieldset class="form-group">
                                <span>Copy Previous</span>
                                <div class="form-check">
                                    <input class="form-check-input" checked type="checkbox" name="previous-copy[]"
                                        id="previous-subjects" value="1">
                                    <label class="form-check-label" checked for="previous-subjects">Subjects</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" checked type="checkbox" name="previous-copy[]"
                                        id="previous-fees" value="2">
                                    <label class="form-check-label" for="previous-fees">Fees Strature</label>
                                </div>

                            </fieldset>
                        </form>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="add-session" class="btn btn-primary"
                            id="save-session">Save</button>
                        <button type="button" class="btn btn-danger resetform" id="close"
                            data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>

        </div>
        <!--END ADD Disciplines MODEL -->
    </div>
    <!-- END MAIN -->
    <div class="clearfix"></div>
@endsection
@section('script')
    <script>
        // TOASTER AND NOTIFICATION SETUP
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


        $("#addSubjectForm .sessions-multiple").multiselect({
            includeSelectAllOption: true,
        });

        $("#addSubjectForm  .options-multiple").multiselect({
            includeSelectAllOption: true,
        });




        //  Add Subject
        $(document).on('click', '#save-subject', function(ev) {
            ev.preventDefault();
            var url = $('#addSubjectForm').attr('action');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            var inputData = $("#addSubjectForm").serialize();
            $.ajax({
                url: url,
                method: "POST",
                data: inputData,
                success: function(data) {
                    console.log(data);
                    if ($.isEmptyObject(data.errors)) {
                        $('#add-subject').modal('hide');
                        $('#addSubjectForm .help-block').remove();
                        $('#addSubjectForm .has-error').removeClass('has-error');
                        $('#subjects').DataTable().ajax.reload();
                        toastr.success(data.success);
                    } else {
                        printErrorMsg('#addSubjectForm', data.errors);
                    }


                }
            });


        });

        // Edit Subject
        $(document).on("click", "#subjects .editBtn", function() {
            //hide edit span
            $(this).closest("tr").find(".editSpan").hide();
            $(this).closest('tr').next('td.dt-control tr').find(".editSpan").hide();


            //show edit input
            $(this).closest("tr").find(".editInput").show();
            $(this).closest('tr').next('td.dt-control tr').find(".editInput").show();



            //hide edit button
            $(this).closest("tr").find(".editBtn").hide();

            //show edit button
            $(this).closest("tr").find(".saveBtn").show();

            // $(this).closest("tr").find(".sessions-multiple").multiselect({
            //     includeSelectAllOption: true,
            // });


            $(this).closest('tr').find('.dt-control').trigger('click');
            $(this).closest('tr').next('table tr').find(".sessions-multiple").multiselect({
                includeSelectAllOption: true,
            });

            $(this).closest('tr').next('table tr').find(".options-multiple").multiselect({
                includeSelectAllOption: true,
            });


        });

        // Update changes candidate
        $(document).on("click", "#subjects .saveBtn", function() {
            actionUrl = $(this).data('url');
            var trObj = $(this).closest("tr");
            var ID = $(this).closest("tr").attr("id");
            var inputData = $(this).closest("tr").find(".editInput").serialize();
            var sessionsData = $(this).closest("tr").next('table tr').find(".editInput").serialize();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "PUT",
                url: actionUrl,
                dataType: "json",
                data: inputData + "&" + sessionsData,
                success: function(data) {
                    console.log(data);

                    if ($.isEmptyObject(data.errors)) {
                        trObj.find(".editInput").hide();
                        trObj.find(".saveBtn").hide();
                        trObj.find(".editSpan").show();
                        trObj.find(".editBtn").show();
                        toastr.success("You have successfully Saved Changes");
                        $('#subjects').DataTable().ajax.reload();
                    } else {
                        printErrorMsg(`#${ID}`, data.errors);
                    }

                },
            });
        });


        // Sync To Timetable
        $(document).on("click", "#subjects .sync-timetable", function() {
            actionUrl = $(this).data('url');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "GET",
                url: actionUrl,
                success: function(data) {
                    toastr.success(data.success);
                },
            });
        });


        // delete Candidate
        $(document).on('click', '#subjects .deleteBtn', function(ev) {
            ev.preventDefault();
            var url = $(this).data('url');
            if (confirm("Are you sure you want to delete this candidates!") == true) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    url: url,
                    method: "DELETE",
                    success: function(data) {
                        if (data.success) {
                            toastr.success(data.success);
                            $('#subjects').DataTable().ajax.reload();
                        }



                    }
                });


            } else {
                return;
            }

        });


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
                    if (key == "sessions") {
                        $(".help-block").css({
                            'color': "#e3342f"
                        })
                        $(`<span class='help-block'>${value}</span>`).insertAfter(`${parent} [name='${key}[]']`)
                    } else if (key == "options") {
                        $(".help-block").css({
                            'color': "#e3342f"
                        });
                        $(`<span class='help-block'>${value}</span>`).insertAfter(`${parent} [name='${key}[]']`)
                    } else {
                        $(`<span class='help-block'>${value}</span>`).insertAfter(`${parent} [name='${key}']`)
                    }


                }
            });
        }
        /****  Print errors End*******/
    </script>
@endsection
