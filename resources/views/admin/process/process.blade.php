@extends('layouts.admin')

@section('content')
    <!-- ============================================================== -->
    <!-- Start Page Content -->
    <!-- MAIN -->
    <div class="main">
        <!-- MAIN CONTENT -->
        <div class="main-content">
            <div class="container-fluid">
                <h3 class="page-title">Proccess</h3>
                <div class="row">
                    <div class="col-md-12">
                        <!-- PANEL NO CONTROLS -->
                        <div class="panel">
                            <div class="panel-heading">
                                <h3 class="panel-title">Processes</h3>
                            </div>
                            <div class="panel-body">
                                <div class="custom-tabs-line tabs-line-bottom left-aligned">
                                    <ul class="nav" role="tablist">
                                        <li class="active"><a href="#process-tab"
                                                role="tab"data-toggle="tab">Processes</a></li>
                                        <li><a href="#state-types-tab" role="tab" data-toggle="tab">States </a>
                                        </li>
                                        <li><a href="#actions-tab" role="tab" data-toggle="tab">Actions</a></li>
                                        <li><a href="#activities-tab" role="tab" data-toggle="tab">Activities</a></li>
                                    </ul>
                                </div>
                                <div class="tab-content">
                                    <div class="tab-pane fade in active" id="process-tab">
                                        <div class="pull-right">
                                            <a href="" class="btn btn-info" data-toggle="modal"
                                                data-target="#add-process">
                                                + create
                                            </a>
                                        </div>
                                        <div class="clearfix"></div>
                                        <div class="table-responsive">
                                            <table class="table" name="tablename"id="process-datatables">
                                                <thead>
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>Process Name</th>
                                                        <th>Process Key</th>
                                                        <th>Description</th>
                                                        <th>Initial State</th>
                                                        <th>States</th>
                                                        <th>Transitions</th>
                                                        <th>P. Actions</th>
                                                        <th>Activities</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                            </table>
                                            @push('scripts')
                                                <script>
                                                    $(function() {
                                                        var processes = $('#process-datatables').DataTable({
                                                            processing: true,
                                                            serverSide: true,
                                                            deferRender: true,
                                                            "lengthMenu": [
                                                                [20, 50, 100, 200, 400, -1],
                                                                [20, 50, 100, 200, 400, "All"]
                                                            ],
                                                            "order": [
                                                                [1, "asc"]
                                                            ], // Order on init. # is the column, starting at 0
                                                            ajax: "{{ route('admin.processes.index') }}",
                                                            columns: [

                                                                {
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
                                                                    data: 'process_key',
                                                                    name: 'process_key',
                                                                },
                                                                {
                                                                    data: 'description',
                                                                    name: 'description',
                                                                },
                                                                {
                                                                    data: 'initial_state',
                                                                    name: 'initial_state',
                                                                },

                                                                {
                                                                    data: 'states',
                                                                    name: 'states',
                                                                },
                                                                {
                                                                    data: 'transition',
                                                                    name: 'transition',
                                                                },
                                                                {
                                                                    data: 'process_actions',
                                                                    name: 'process_actions',
                                                                },

                                                                {
                                                                    data: 'activities',
                                                                    name: 'activities',
                                                                },

                                                                {
                                                                    data: 'action',
                                                                    name: 'action',
                                                                    searchable: false,
                                                                    sortable: false
                                                                }
                                                            ]

                                                        });

                                                        $("#process-datatables").css("width", "98.5%");

                                                        $('#process-datatables tbody').attr('id', 'tablecontents');
                                                        //  Add Process
                                                        $(document).on('click', '#save-process', function(ev) {
                                                            ev.preventDefault();
                                                            var url = $('#addProcessForm').attr('action');
                                                            $.ajaxSetup({
                                                                headers: {
                                                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                                                }
                                                            });
                                                            var inputData = $('#addProcessForm').serialize();
                                                            $.ajax({
                                                                url: url,
                                                                method: "POST",
                                                                data: inputData,
                                                                success: function(data) {
                                                                    console.log(data);
                                                                    if ($.isEmptyObject(data.errors)) {
                                                                        $('#add-process').modal('hide');
                                                                        $('#addProcessForm .help-block').remove();
                                                                        $('#addSubjectForm .has-error').removeClass('has-error');
                                                                        toastr.success(data.success);
                                                                        $('#process-datatables').DataTable().ajax.reload();
                                                                    } else {
                                                                        printErrorMsg('#addProcessForm', data.errors);
                                                                    }


                                                                }
                                                            });


                                                        });

                                                        // Edit Process
                                                        $(document).on("click", "#process-datatables .editBtn", function() {
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
                                                            $(this).closest('tr').find('.dt-control').trigger('click');
                                                            $(this).closest('tr').next('table tr').find(".sessions-multiple").multiselect({
                                                                includeSelectAllOption: true,
                                                            });



                                                        });

                                                        // Update Process
                                                        $(document).on("click", "#process-datatables .saveBtn", function() {
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
                                                                        $('#process-datatables').DataTable().ajax.reload();
                                                                    } else {
                                                                        printErrorMsg(`#${ID}`, data.errors);
                                                                    }

                                                                },
                                                            });
                                                        });

                                                        // Delete Process
                                                        $(document).on('click', '#process-datatables .deleteBtn', function(ev) {
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
                                                                            $('#process-datatables').DataTable().ajax.reload();
                                                                        }
                                                                    }
                                                                });


                                                            } else {
                                                                return;
                                                            }

                                                        });



                                                    });
                                                </script>
                                            @endpush
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="state-types-tab">
                                        <div class="pull-right">
                                            <a href="" class="btn btn-info" data-toggle="modal"
                                                data-target="#add-option">
                                                + create
                                            </a>
                                        </div>
                                        <div class="clearfix"></div>
                                        <div class="table-responsive">
                                            <table class="table" name="tablename"id="state-types-datatables">
                                                <thead>
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>Name</th>
                                                        <th>Description</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                            </table>
                                            @push('scripts')
                                                <script>
                                                    $(function() {

                                                        var states_type = $('#state-types-datatables').DataTable({
                                                            processing: true,
                                                            serverSide: true,
                                                            deferRender: true,
                                                            "lengthMenu": [
                                                                [20, 50, 100, 200, 400, -1],
                                                                [20, 50, 100, 200, 400, "All"]
                                                            ],
                                                            ajax: "{{ route('admin.state-types.index') }}",
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
                                                                    data: 'description',
                                                                    name: 'description',
                                                                    searchable: true
                                                                },
                                                                {
                                                                    data: 'action',
                                                                    name: 'action',
                                                                    searchable: false,
                                                                    sortable: false

                                                                }

                                                            ]

                                                        });

                                                        $("#state-types-datatables").css("width", "98.5%");



                                                    });
                                                </script>
                                            @endpush
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="actions-tab">
                                        <div class="pull-right">
                                            <a href="" class="btn btn-info" data-toggle="modal"
                                                data-target="#add-option">
                                                + create
                                            </a>
                                        </div>
                                        <div class="clearfix"></div>
                                        <div class="table-responsive">
                                            <table class="table" name="tablename"id="action-types-datatables">
                                                <thead>
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>Name</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                            </table>
                                            @push('scripts')
                                                <script>
                                                    $(function() {

                                                        var states_type = $('#action-types-datatables').DataTable({
                                                            processing: true,
                                                            serverSide: true,
                                                            deferRender: true,
                                                            "lengthMenu": [
                                                                [20, 50, 100, 200, 400, -1],
                                                                [20, 50, 100, 200, 400, "All"]
                                                            ],
                                                            ajax: "{{ route('admin.action-types.index') }}",
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
                                                                    data: 'action',
                                                                    name: 'action',
                                                                    searchable: false,
                                                                    sortable: false

                                                                }

                                                            ]

                                                        });

                                                        $("#action-types-datatables").css("width", "98.5%");



                                                    });
                                                </script>
                                            @endpush
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="activities-tab">
                                        <div class="pull-right">
                                            <a href="" class="btn btn-info" data-toggle="modal"
                                                data-target="#add-option">
                                                + create
                                            </a>
                                        </div>
                                        <div class="clearfix"></div>
                                        <div class="table-responsive">
                                            <table class="table" name="tablename"id="activity_type-datatables">
                                                <thead>
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>Name</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                            </table>
                                            @push('scripts')
                                                <script>
                                                    $(function() {

                                                        var states_type = $('#activity_type-datatables').DataTable({
                                                            processing: true,
                                                            serverSide: true,
                                                            deferRender: true,
                                                            "lengthMenu": [
                                                                [20, 50, 100, 200, 400, -1],
                                                                [20, 50, 100, 200, 400, "All"]
                                                            ],
                                                            ajax: "{{ route('admin.activity-types.index') }}",
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
                                                                    data: 'action',
                                                                    name: 'action',
                                                                    searchable: false,
                                                                    sortable: false

                                                                }

                                                            ]

                                                        });

                                                        $("#activity_type-datatables").css("width", "98.5%");



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
        <!-- ADD PROCESS MODAL -->
        <div class="modal fade bd-modal-md" id="add-process" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close resetform" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h3 class="modal-title">New Process</h3>
                    </div>
                    <div class="clearfix"></div>
                    <div class="modal-body">
                        <form action="{{ route('admin.processes.store') }}" method="post" id="addProcessForm">
                            <div>
                                @csrf
                            </div>
                            <div class="form-group ">
                                <label class="control-label" for="name">Name</label>
                                <input type="text" class="form-control" name="name" id="name" value="" />
                            </div>

                            <div class="form-group">
                                <label class="control-label" for="name">Description</label>
                                <input type="text" class="form-control" name="description" id="description"
                                    value="" />
                            </div>
                            <div class="form-group">
                                <label class="control-label" for="name">Process Key</label>
                                <input type="text" class="form-control" name="process_key" id="process_key" value="" />
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="add-process" class="btn btn-primary"
                            id="save-process">Save</button>
                        <button type="button" class="btn btn-danger resetform" id="close"
                            data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>

        </div>
        <!--END ADD PROCESS MODEL -->
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
                        $(`${parent} [name='${key}']`).next().append(`<span class='help-block'>${value}</span>`);
                    } else {
                        $(`<span class='help-block'>${value}</span>`).insertAfter(`${parent} [name='${key}']`)
                    }


                }
            });
        }
        /****  Print errors End*******/
    </script>
@endsection
