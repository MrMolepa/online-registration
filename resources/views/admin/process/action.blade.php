@extends('layouts.admin')

@section('content')
    <!-- ============================================================== -->
    <!-- Start Page Content -->
    <!-- MAIN -->
    <div class="main">
        <!-- MAIN CONTENT -->
        <div class="main-content">
            <div class="container-fluid">
                <h3 class="page-title">Actions</h3>
                <div class="row">
                    <div class="col-md-12">
                        <!-- PANEL NO CONTROLS -->
                        <div class="panel">
                            <div class="panel-heading">
                                <h3 class="panel-title">Actions {{ $process->name }}</h3>
                            </div>
                            <div class="panel-body">


                                <div class="custom-tabs-line tabs-line-bottom left-aligned">
                                    <ul class="nav" role="tablist">
                                        <li class="active"><a href="#actions-tab"
                                                role="tab"data-toggle="tab">Actions</a></li>
                                        <li><a href="#action-order-tab" role="tab" data-toggle="tab">Action Order </a>
                                        </li>


                                    </ul>
                                </div>
                                <div class="tab-content">
                                    <div class="tab-pane fade in active" id="actions-tab">

                                        <div class="pull-right">
                                            <a href="" class="btn btn-info" data-toggle="modal"
                                                data-target="#add-actions">
                                                + create
                                            </a>
                                        </div>
                                        <div class="clearfix"></div>
                                        <div class="table-responsive">
                                            <table class="table" name="tablename" id="actions-datatables">
                                                <thead>
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>Action type</th>
                                                        <th>Name</th>
                                                        <th>Description</th>
                                                        <th>Transitions</th>
                                                        <th>Users</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>


                                            </table>
                                            @push('scripts')
                                                <script>
                                                    $(function() {

                                                        var actions = $('#actions-datatables').DataTable({
                                                            processing: true,
                                                            serverSide: true,
                                                            deferRender: true,
                                                            "lengthMenu": [
                                                                [20, 50, 100, 200, 400, -1],
                                                                [20, 50, 100, 200, 400, "All"]
                                                            ],

                                                            ajax: {
                                                                url: "{{ route('admin.actions.index') }}",
                                                                data: function(d) {
                                                                    d.process_id = "{{ $process->id }}";
                                                                }
                                                            },
                                                            error: function(xhr, error, code) {
                                                                console.log(xhr, code);
                                                            },
                                                            columns: [{
                                                                    data: 'id',
                                                                    name: 'id',
                                                                    searchable: true
                                                                },
                                                                {
                                                                    data: 'actionType',
                                                                    name: 'actionType',
                                                                    searchable: true
                                                                },
                                                                {
                                                                    data: 'name',
                                                                    name: 'name',

                                                                },
                                                                {
                                                                    data: 'description',
                                                                    name: 'description'
                                                                },
                                                                {
                                                                    data: 'transition',
                                                                    name: 'transition'
                                                                },
                                                                {
                                                                    data: 'users',
                                                                    name: 'users'
                                                                },
                                                                {
                                                                    data: 'action',
                                                                    name: 'action',
                                                                    searchable: false,
                                                                    sortable: false
                                                                }
                                                            ]

                                                        });

                                                        $("#actions-datatables").css("width", "98.5%");



                                                        //  Add Action
                                                        $(document).on('click', '#save-actions', function(ev) {
                                                            ev.preventDefault();
                                                            var url = $('#addActionForm').attr('action');
                                                            $.ajaxSetup({
                                                                headers: {
                                                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                                                }
                                                            });
                                                            var inputData = $('#addActionForm').serialize();
                                                            $.ajax({
                                                                url: url,
                                                                method: "POST",
                                                                data: inputData,
                                                                success: function(data) {
                                                                    console.log(data);
                                                                    if ($.isEmptyObject(data.errors)) {
                                                                        $('#add-actions').modal('hide');
                                                                        $('#addActionForm .help-block').remove();
                                                                        $('#addActionForm .has-error').removeClass('has-error');
                                                                        toastr.success(data.success);
                                                                        $('#actions-datatables').DataTable().ajax.reload();
                                                                    } else {
                                                                        printErrorMsg('#addActionForm', data.errors);
                                                                    }


                                                                }
                                                            });


                                                        });

                                                        // Edit Action
                                                        $(document).on("click", "#actions-datatables  .editBtn", function() {
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

                                                            $(this).closest("tr").find(".users-multiple").multiselect({
                                                                includeSelectAllOption: true,
                                                            });
                                                            $(this).closest("tr").find(".transition-multiple").multiselect({
                                                                includeSelectAllOption: true,
                                                            });







                                                        });

                                                        // Update Action
                                                        $(document).on("click", "#actions-datatables .saveBtn", function() {
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
                                                                        $('#actions-datatables').DataTable().ajax.reload();
                                                                    } else {
                                                                        printErrorMsg(`#${ID}`, data.errors);
                                                                    }

                                                                },
                                                            });
                                                        });

                                                        // Delete Action
                                                        $(document).on('click', '#actions-datatables .deleteBtn', function(ev) {
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
                                                                            $('#actions-datatables').DataTable().ajax.reload();
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
                                    <div class="tab-pane fade" id="action-order-tab">
                                        <div class="table-responsive">
                                            <table class="table" name="tablename" id="actions-order-datatables">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Description</th>
                                                        <th>Users</th>
                                                    </tr>
                                                </thead>
                                            </table>
                                            @push('scripts')
                                                <script>
                                                    $(function() {

                                                        var actions_order = $('#actions-order-datatables').DataTable({
                                                            processing: true,
                                                            serverSide: true,
                                                            deferRender: true,
                                                            "lengthMenu": [
                                                                [20, 50, 100, 200, 400, -1],
                                                                [20, 50, 100, 200, 400, "All"]
                                                            ],

                                                            ajax: {
                                                                url: "{{ route('admin.actions.order') }}",
                                                                data: function(d) {
                                                                    d.process_id = "{{ $process->id }}";
                                                                }
                                                            },
                                                            error: function(xhr, error, code) {
                                                                console.log(xhr, code);
                                                            },
                                                            columns: [{
                                                                    data: 'order',
                                                                    name: 'order',
                                                                    searchable: false
                                                                },
                                                                {
                                                                    data: 'description',
                                                                    name: 'description'
                                                                },

                                                                {
                                                                    data: 'users',
                                                                    name: 'users'
                                                                },

                                                            ]

                                                        });
                                                        $("#actions-order-datatables").css("width", "98.5%");

                                                        $("#actions-order-datatables tbody").sortable({
                                                            items: "tr",
                                                            cursor: 'move',
                                                            opacity: 0.6,
                                                            update: function() {
                                                                sendOrderToServer();
                                                            }
                                                        });


                                                        function sendOrderToServer() {

                                                            var orders = [];
                                                            var token = $('meta[name="csrf-token"]').attr('content');
                                                            $('#actions-order-datatables tbody tr').each(function(index, element) {
                                                                orders.push({
                                                                    id: $(this).attr('data-id'),
                                                                    position: index + 1
                                                                });
                                                                console.log(orders);
                                                            });
                                                            $.ajax({
                                                                type: "get",
                                                                dataType: "json",
                                                                url: "{{ route('admin.actions.order') }}",
                                                                data: {
                                                                    orders : orders,
                                                                    _token: token,
                                                                    process_id :"{{ $process->id }}"
                                                                },
                                                                success: function(response) {
                                                                    if (response.status == "success") {
                                                                        console.log(response);
                                                                        $('#actions-order-datatables').DataTable().ajax.reload();
                                                                    } else {
                                                                        console.log(response)
                                                                    }
                                                                }
                                                            });
                                                        }



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


        <!-- ADD ACTION MODAL -->
        <div class="modal fade bd-modal-md" id="add-actions" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog ">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close resetform" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h3 class="modal-title">Action</h3>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('admin.actions.store') }}" method="post" id="addActionForm">
                            <div>
                                @csrf
                                <input type="hidden" name="process" value="{{ $process->id }}">
                            </div>
                            <div class="form-group">
                                <label for="action_type">Action Type</label>
                                <select name="action_type" class="form-control">
                                    <option value="">Select actions type</option>
                                    @foreach ($action_types as $action_type)
                                        <option value="{{ $action_type->id }}">{{ $action_type->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" name="name" id="name" value="" />
                            </div>
                            <div class="form-group">
                                <label class="control-label" for="description">Description</label>
                                <input type="text" class="form-control" name="description" id="description"
                                    value="" />
                            </div>
                            <div class="form-group">
                                <label class="control-label"> Transitions</label>
                                @foreach ($transitions as $transition)
                                    <label class="fancy-checkbox">
                                        <input type="checkbox" value="{{ $transition->id }}" name="transition_actions[]">
                                        <span>{{ $transition->selectCurrentState->name }} To
                                            {{ $transition->selectNextState->name }} </span>
                                    </label>
                                @endforeach

                            </div>
                            <div class="form-group">
                                <label class="control-label" for="users">Users</label>
                                <div>
                                    <select name="users[]" multiple="multiple" id="users"
                                        class="form-control users-multiple">
                                        <option value="">Please select user</option>
                                        <optgroup label="Admin User">
                                            @foreach ($adminUsers as $adminUser)
                                                <option value="{{ $adminUser->id }};{{ $adminUser->type }}"
                                                    data-badge="">
                                                    {{ $adminUser->email }}</option>
                                            @endforeach
                                        </optgroup>
                                        <optgroup label="Sponser User">
                                            @foreach ($sponporUsers as $sponporUser)
                                                <option value="{{ $sponporUser->id }};{{ $sponporUser->type }}"
                                                    data-badge="">
                                                    {{ $sponporUser->email }}</option>
                                            @endforeach
                                        </optgroup>
                                        <optgroup label="Roles">
                                            @foreach ($roles as $role)
                                                <option value="{{ $role->id }};{{ $role->type }}" data-badge="">
                                                    {{ $role->display_name }}</option>
                                            @endforeach
                                        </optgroup>
                                    </select>
                                </div>

                            </div>
                        </form>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="add-action" class="btn btn-primary" id="save-actions">Save</button>
                        <button type="button" class="btn btn-danger resetform" id="close"
                            data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>

        </div>
        <!--END ADD ACTION  MODEL -->




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


        $("#addActionForm .users-multiple").multiselect({
            includeSelectAllOption: true,
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
