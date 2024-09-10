@extends('layouts.admin')

@section('content')
    <!-- ============================================================== -->
    <!-- Start Page Content -->
    <!-- MAIN -->
    <div class="main">
        <!-- MAIN CONTENT -->
        <div class="main-content">
            <div class="container-fluid">
                <h3 class="page-title">Activities </h3>
                <div class="row">
                    <div class="col-md-12">
                        <!-- PANEL NO CONTROLS -->
                        <div class="panel">
                            <div class="panel-heading">
                                <h3 class="panel-title">Activities for {{ $process->name }}</h3>
                            </div>
                            <div class="panel-body">
                                <div class="pull-right">
                                    <a href="" class="btn btn-info" data-toggle="modal"
                                        data-target="#add-activities">
                                        + create
                                    </a>
                                </div>
                                <div class="clearfix"></div>
                                <div class="table-responsive">
                                    <table class="table" name="tablename" id="activities-datatables">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Activity type</th>
                                                <th>Name</th>
                                                <th>Description</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>


                                    </table>
                                    @push('scripts')
                                        <script>
                                            $(function() {

                                                var activities = $('#activities-datatables').DataTable({
                                                    processing: true,
                                                    serverSide: true,
                                                    deferRender: true,
                                                    "lengthMenu": [
                                                        [20, 50, 100, 200, 400, -1],
                                                        [20, 50, 100, 200, 400, "All"]
                                                    ],

                                                    ajax: {
                                                        url: "{{ route('admin.activities.index') }}",
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
                                                            data: 'activityType',
                                                            name: 'activityType',

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
                                                            data: 'action',
                                                            name: 'action',
                                                            searchable: false,
                                                            sortable: false

                                                        }

                                                    ]

                                                });

                                                $("#activities-datatables").css("width", "98.5%");

                                                //  Add Activity
                                                $(document).on('click', '#save-activity', function(ev) {
                                                    ev.preventDefault();
                                                    var url = $('#addActivityForm').attr('action');
                                                    $.ajaxSetup({
                                                        headers: {
                                                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                                        }
                                                    });
                                                    var inputData = $('#addActivityForm').serialize();
                                                    $.ajax({
                                                        url: url,
                                                        method: "POST",
                                                        data: inputData,
                                                        success: function(data) {
                                                            console.log(data);
                                                            if ($.isEmptyObject(data.errors)) {
                                                                $('#add-activities').modal('hide');
                                                                $('#addActivityForm .help-block').remove();
                                                                $('#addActivityForm .has-error').removeClass('has-error');
                                                                toastr.success(data.success);
                                                                $('#activities-datatables').DataTable().ajax.reload();
                                                            } else {
                                                                printErrorMsg('#addActivityForm', data.errors);
                                                            }


                                                        }
                                                    });


                                                });

                                                // Edit Activity
                                                $(document).on("click", "#activities-datatables .editBtn", function() {
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

                                                // Update Activity
                                                $(document).on("click", "#activities-datatables .saveBtn", function() {
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
                                                                $('#activities-datatables').DataTable().ajax.reload();
                                                            } else {
                                                                printErrorMsg(`#${ID}`, data.errors);
                                                            }

                                                        },
                                                    });
                                                });

                                                // Delete Activity
                                                $(document).on('click', '#activities-datatables.deleteBtn', function(ev) {
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
                                                                    $('#activities-datatables').DataTable().ajax.reload();
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
        <div class="modal fade bd-modal-md" id="add-activities" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog ">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close resetform" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h3 class="modal-title">Activity</h3>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('admin.activities.store') }}" method="post" id="addActivityForm">
                            <div>
                                @csrf
                                <input type="hidden" name="process" value="{{ $process->id }}">
                            </div>

                            <div class="form-group">
                                <label for="activity_type">Activity type</label>
                                <select name="activity_type" class="form-control">
                                    <option value="">Select activity type</option>
                                    @foreach ($activity_types as $activity_type)
                                        <option value="{{ $activity_type->id }}">{{ $activity_type->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="control-label" for="name">Name</label>
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
                                        <input type="checkbox" value="{{ $transition->id }}" name="transition_activity[]">
                                        <span>{{ $transition->selectCurrentState->name }} To
                                            {{ $transition->selectNextState->name }} </span>
                                    </label>
                                @endforeach
                            </div>

                        </form>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="add-activity" class="btn btn-primary" id="save-activity">Save</button>
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
