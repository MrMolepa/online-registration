@extends('layouts.admin')

@section('content')
    <!-- ============================================================== -->
    <!-- Start Page Content -->
    <!-- MAIN -->
    <div class="main">
        <!-- MAIN CONTENT -->
        <div class="main-content">
            <div class="container-fluid">
                <h3 class="page-title">Transitions</h3>
                <div class="row">
                    <div class="col-md-12">
                        <!-- PANEL NO CONTROLS -->
                        <div class="panel">
                            <div class="panel-heading">
                                <h3 class="panel-title">Transitions</h3>
                            </div>
                            <div class="panel-body">
                                <div class="pull-right">
                                    <a href="" class="btn btn-info" data-toggle="modal"
                                        data-target="#add-transition">
                                        + create
                                    </a>
                                </div>
                                <div class="clearfix"></div>
                                <div class="table-responsive">
                                    <table class="table" name="tablename" id="transition-datatables">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Current state</th>
                                                <th>Next state</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>


                                    </table>
                                    @push('scripts')
                                        <script>
                                            $(function() {

                                                var transitions = $('#transition-datatables').DataTable({
                                                    processing: true,
                                                    serverSide: true,
                                                    deferRender: true,
                                                    "lengthMenu": [
                                                        [20, 50, 100, 200, 400, -1],
                                                        [20, 50, 100, 200, 400, "All"]
                                                    ],

                                                    ajax: {
                                                        url: "{{ route('admin.transitions.index') }}",
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
                                                            data: 'currentState',
                                                            name: 'currentState',
                                                            searchable: true
                                                        },
                                                        {
                                                            data: 'nextState',
                                                            name: 'nextState'

                                                        },
                                                        {
                                                            data: 'action',
                                                            name: 'action',
                                                            searchable: false,
                                                            sortable: false

                                                        }

                                                    ]

                                                });

                                                $("#transition-datatables").css("width", "98.5%");

                                                //  Add Transition
                                                $(document).on('click', '#save-transition', function(ev) {
                                                    ev.preventDefault();
                                                    var url = $('#addTransitionForm').attr('action');
                                                    $.ajaxSetup({
                                                        headers: {
                                                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                                        }
                                                    });
                                                    var inputData = $('#addTransitionForm').serialize();
                                                    $.ajax({
                                                        url: url,
                                                        method: "POST",
                                                        data: inputData,
                                                        success: function(data) {
                                                            console.log(data);
                                                            if ($.isEmptyObject(data.errors)) {
                                                                $('#add-transition').modal('hide');
                                                                $('#addTransitionForm .help-block').remove();
                                                                $('#addTransitionForm .has-error').removeClass('has-error');
                                                                toastr.success(data.success);
                                                                $('#transition-datatables').DataTable().ajax.reload();
                                                            } else {
                                                                printErrorMsg('#addTransitionForm', data.errors);
                                                            }


                                                        }
                                                    });


                                                });
                                                // Edit Transition
                                                $(document).on("click", "#transition-datatables .editBtn", function() {
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

                                                // Update Transition
                                                $(document).on("click", "#transition-datatables .saveBtn", function() {
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
                                                                toastr.success(data.success);
                                                                $('#transition-datatables').DataTable().ajax.reload();
                                                            } else {
                                                                printErrorMsg(`#${ID}`, data.errors);
                                                            }

                                                        },
                                                    });
                                                });

                                                // Delete Transition
                                                $(document).on('click', '#transition-datatables .deleteBtn', function(ev) {
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
                                                                    $('#transition-datatables').DataTable().ajax.reload();
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
        <div class="modal fade bd-modal-md" id="add-transition" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog ">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close resetform" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h3 class="modal-title">Transition</h3>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('admin.transitions.store') }}" method="post" id="addTransitionForm">
                            <div>
                                @csrf
                                <input type="hidden" name="process" value="{{ $process->id }}">
                            </div>


                            <div class="form-group">
                                <label for="current_state"> Current state</label>
                                <select name="current_state" class="form-control">
                                    <option value="">Select state</option>
                                    @foreach ($states as $state)
                                        <option value="{{ $state->id }}">{{ $state->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="next_state">Next state</label>
                                <select name="next_state" class="form-control">
                                    <option value="">Select state </option>
                                    @foreach ($states as $state)
                                        <option value="{{ $state->id }}">{{ $state->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </form>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="add-transition" class="btn btn-primary"
                            id="save-transition">Save</button>
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
