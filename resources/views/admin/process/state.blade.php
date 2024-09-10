@extends('layouts.admin')

@section('content')
    <!-- ============================================================== -->
    <!-- Start Page Content -->
    <!-- MAIN -->
    <div class="main">
        <!-- MAIN CONTENT -->
        <div class="main-content">
            <div class="container-fluid">
                <h3 class="page-title">States</h3>
                <div class="row">
                    <div class="col-md-12">
                        <!-- PANEL NO CONTROLS -->
                        <div class="panel">
                            <div class="panel-heading">
                                <h3 class="panel-title">{{ $process->name }}</h3>
                            </div>
                            <div class="panel-body">
                                <div class="pull-right">
                                    <a href="" class="btn btn-info" data-toggle="modal" data-target="#add-state">
                                        + create
                                    </a>
                                </div>
                                <div class="clearfix"></div>
                                <div class="table-responsive">
                                    <table class="table" name="tablename" id="state-datatables">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>State type</th>
                                                <th>Name</th>
                                                <th>Description</th>
                                                <th>Transitions</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>


                                    </table>
                                    @push('scripts')
                                        <script>
                                            $(function() {

                                                var state = $('#state-datatables').DataTable({
                                                    processing: true,
                                                    serverSide: true,
                                                    deferRender: true,
                                                    "lengthMenu": [
                                                        [20, 50, 100, 200, 400, -1],
                                                        [20, 50, 100, 200, 400, "All"]
                                                    ],

                                                    ajax: {
                                                        url: "{{ route('admin.states.index') }}",
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
                                                            data: 'stateType',
                                                            name: 'stateType',
                                                            searchable: true
                                                        },
                                                        {
                                                            data: 'name',
                                                            name: 'name',
                                                            searchable: true
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
                                                            data: 'action',
                                                            name: 'action',
                                                            searchable: false,
                                                            sortable: false

                                                        }

                                                    ]

                                                });

                                                $("#state-datatables").css("width", "98.5%");



                                                //  Add State
                                                $(document).on('click', '#save-state', function(ev) {
                                                    ev.preventDefault();
                                                    var url = $('#addStateForm').attr('action');
                                                    $.ajaxSetup({
                                                        headers: {
                                                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                                        }
                                                    });
                                                    var inputData = $('#addStateForm').serialize();
                                                    $.ajax({
                                                        url: url,
                                                        method: "POST",
                                                        data: inputData,
                                                        success: function(data) {
                                                            console.log(data);
                                                            if ($.isEmptyObject(data.errors)) {
                                                                $('#add-state').modal('hide');
                                                                $('#addStateForm .help-block').remove();
                                                                $('#addStateForm .has-error').removeClass('has-error');
                                                                toastr.success(data.success);
                                                                $('#state-datatables').DataTable().ajax.reload();
                                                            } else {
                                                                printErrorMsg('#addStateForm', data.errors);
                                                            }


                                                        }
                                                    });


                                                });

                                                // Edit State
                                                $(document).on("click", "#state-datatables .editBtn", function() {
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



                                                });

                                                // Update State
                                                $(document).on("click", "#state-datatables .saveBtn", function() {
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
                                                            if ($.isEmptyObject(data.errors)) {
                                                                trObj.find(".editInput").hide();
                                                                trObj.find(".saveBtn").hide();
                                                                trObj.find(".editSpan").show();
                                                                trObj.find(".editBtn").show();
                                                                toastr.success(data.success);
                                                                $('#state-datatables').DataTable().ajax.reload();
                                                            } else {
                                                                printErrorMsg(`#${ID}`, data.errors);
                                                            }

                                                        },
                                                    });
                                                });

                                                // Delete  State
                                                $(document).on('click', '#state-datatables .deleteBtn', function(ev) {
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
                                                                    $('#state-datatables').DataTable().ajax.reload();
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
                        </div>
                        <!-- END PANEL NO CONTROLS -->
                    </div>

                </div>


            </div>
        </div>
        <!-- END MAIN CONTENT -->


        <!-- ADD STATE MODAL -->
        <div class="modal fade bd-modal-md" id="add-state" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog ">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close resetform" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h3 class="modal-title">State</h3>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('admin.states.store') }}" method="post" id="addStateForm">
                            <div>
                                @csrf
                                <input type="hidden" name="process" value="{{ $process->id }}">
                            </div>

                            <div class="form-group">
                                <label for="state_type">State Type</label>
                                <select name="state_type" class="form-control">
                                    <option value="">Select state type</option>
                                    @foreach ($state_types as $state_type)
                                        <option value="{{ $state_type->id }}">{{ $state_type->name }}</option>
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

                        </form>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="add-stae" class="btn btn-primary" id="save-state">Save</button>
                        <button type="button" class="btn btn-danger resetform" id="close"
                            data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>

        </div>
        <!--END ADD STATE MODEL -->




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

        ;

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
