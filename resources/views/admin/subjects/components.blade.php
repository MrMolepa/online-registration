@extends('layouts.admin')

@section('content')
    <!-- ============================================================== -->
    <!-- Start Page Content -->
    <!-- MAIN -->
    <div class="main">
        <!-- MAIN CONTENT -->
        <div class="main-content">
            <div class="container-fluid">
                <h3 class="page-title">Components</h3>
                <div class="row">
                    <div class="col-md-12">
                        <!-- PANEL NO CONTROLS -->
                        <div class="panel">
                            <div class="panel-heading">
                                <h3 class="panel-title">Components</h3>
                            </div>
                            <div class="panel-body">
                                <div class="pull-right">
                                    <a href="" class="btn btn-info" data-toggle="modal"
                                        data-target="#add-component">
                                        + create
                                    </a>
                                </div>
                                <div class="clearfix"></div>
                                <div class="table-responsive">
                                    <table class="table" name="tablename"id="components">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Subject Code</th>
                                                <th>Component Code</th>
                                                <th>Component Name</th>
                                                <th>Created At</th>
                                                <th>Updated At</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>


                                    </table>
                                    @push('scripts')
                                        <script>
                                            $(function() {

                                                var components = $('#components').DataTable({
                                                    processing: true,
                                                    serverSide: true,
                                                    deferRender: true,
                                                    "lengthMenu": [
                                                        [20, 50, 100, 200, 400, -1],
                                                        [20, 50, 100, 200, 400, "All"]
                                                    ],
                                                    ajax: "{{ route('admin.components.index',['subject_code'=>$subject_code]) }}",
                                                    columns: [
                                                        {
                                                            data: 'id',
                                                            name: 'id',
                                                            searchable: true
                                                        },
                                                        {
                                                            data: 'subject_code',
                                                            name: 'subject_code',
                                                            searchable: true
                                                        },

                                                        {
                                                            data: 'component_code',
                                                            name: 'component_code',
                                                            searchable: true
                                                        },
                                                        {
                                                            data: 'component_name',
                                                            name: 'component_name',
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

                                                $("#components").css("width", "98.5%");





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
        <!-- ADD CANDIDATE MODAL -->
        <div class="modal fade bd-modal-md" id="add-component" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog ">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close resetform" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h3 class="modal-title">New component</h3>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('admin.components.store') }}" method="post" id="addcomponentForm">
                            <div>
                                @csrf
                            </div>
                            {{--  --}}
                            <div class="form-group">
                                <label class="control-label" for="subject_code">Subject Code</label>
                                <input type="text" name="subject_code" class="form-control" id="subject_code"
                                    value="{{$subject_code}}" readonly />
                            </div>
                            <div class="form-group  ">
                                <label for="component_code">Component Code</label>
                                <input type="text" class="form-control" name="component_code" id="component_code"
                                    value="" />
                            </div>
                            <div class="form-group">
                                <label class="control-label" for="component_name">component Name</label>
                                <input type="text" class="form-control" name="component_name" id="component_name"
                                    value="" />
                            </div>

                        </form>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="add-component" class="btn btn-primary"
                            id="save-component">Save</button>
                        <button type="button" class="btn btn-danger resetform" id="close"
                            data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>

        </div>
        <!--END ADD CANDIDATE MODEL -->
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





        //  Add component
        $(document).on('click', '#save-component', function(ev) {
            ev.preventDefault();
            var url = $('#addcomponentForm').attr('action');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });


            var inputData = $("#addcomponentForm").serialize();

            $.ajax({
                url: url,
                method: "POST",
                data: inputData,
                success: function(data) {
                    if ($.isEmptyObject(data.errors)) {
                        $('#add-component').modal('hide');
                        $('#addcomponentForm .help-block').remove();
                        $('#addcomponentForm .has-error').removeClass('has-error');
                        toastr.success(data.success);
                        $('#component').DataTable().ajax.reload();
                    } else {
                        printErrorMsg('#addcomponentForm', data.errors);
                    }


                }
            });


        });

        // Edit component
        $(document).on("click", "#components .editBtn", function() {
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
        $(document).on("click", "#components .saveBtn", function() {
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
                        $('#components').DataTable().ajax.reload();
                    } else {
                        printErrorMsg(`#${ID}`, data.errors);
                    }

                },
            });
        });


        // delete Candidate
        $(document).on('click', '#components .deleteBtn', function(ev) {
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
                            $('#components').DataTable().ajax.reload();
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
