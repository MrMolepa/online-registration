@extends('layouts.admin')
@section('content')
    <!-- ============================================================== -->
    <!-- Start Page Content -->
    <!-- ============================================================== -->
    <div class="main">
        <!-- MAIN CONTENT -->
        <div class="main-content">
            <div class="container-fluid">
                <h3 class="page-title">Manage Invigilation types</h3>
                <div class="row">
                    <div class="col-md-12">
                        <!-- PANEL NO CONTROLS -->
                        <div class="panel">
                            <div class="panel-heading">
                                <h3 class="panel-title">Invigilation types <b></b></h3>
                            </div>
                            <div class="panel-body">
                                {{--  --}}
                                <button type="button" class="btn btn-primary" data-toggle="modal"
                                    data-target="#add-type-modal">
                                    + Add type
                                </button>

                                <table class="table table-striped"id="data-table-invigilation">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Invigilator Type</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>



                                <!-- Modal add invigilation type-->
                                <div class="modal fade" id="add-type-modal" tabindex="-1" role="dialog"
                                    aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLabel">Add Invigilator Type</h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <form id="type-add-form" method="post"
                                                    action="{{ route('admin.invigilations.types.store') }}">
                                                    @csrf
                                                    <div class="form-group row">

                                                        <div class="form-group row center col-sm-12">
                                                            <label for="invigilation_type"
                                                                class="col-sm-4 col-form-label">Invigilator
                                                                Type</label>
                                                            <div class="col-sm-8">
                                                                <input type="text" class="form-control" name="name"
                                                                    id="name" placeholder=" ">
                                                            </div>
                                                        </div>
                                                    </div>

                                                </form>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-primary" id="add-type">Save</button>
                                                <button type="button" class="btn btn-danger"
                                                    data-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Modal Edit invigilation type-->
                                <div class="modal fade" id="type-modal" tabindex="-1" role="dialog"
                                    aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLabel">Update Invigilator Type</h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <form id="type-edit-form" method="post" action="">
                                                    @csrf
                                                    <div class="form-group row">

                                                        <div class="form-group row col-sm-12">
                                                            <label for="invigilation_type"
                                                                class="col-sm-4 col-form-label">Invigilator
                                                                Type</label>
                                                            <div class="col-sm-8">
                                                                <input type="text" class="form-control" name="name"
                                                                    id="name" placeholder="Invigilation Type">
                                                            </div>
                                                        </div>
                                                    </div>

                                                </form>
                                            </div>
                                            <div class="modal-footer">

                                                <button type="button" class="btn btn-primary"
                                                    id="update-type">update</button>
                                                <button type="button" class="btn btn-danger"
                                                    data-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @push('scripts')
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
                                        // data tables
                                        $(document).ready(function() {
                                            $.ajaxSetup({
                                                headers: {
                                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                                }
                                            });
                                            // datatable
                                            var table = $('#data-table-invigilation').DataTable({
                                                processing: true,
                                                serverSide: true,
                                                ajax: "{{ route('admin.invigilations.types.index') }}",
                                                columns: [{
                                                        data: 'id',
                                                        name: 'id'
                                                    },
                                                    {
                                                        data: 'name',
                                                        name: 'name'
                                                    },

                                                    {
                                                        data: 'action',
                                                        name: 'action',
                                                        orderable: false,
                                                        searchable: false
                                                    },

                                                ]
                                            });

                                            //add
                                            $('#add-type').on('click', function(event) {
                                                var addForm = $("#type-add-form");
                                                var url = addForm.attr('action');
                                                $.ajax({
                                                    url: url,
                                                    type: 'POST',
                                                    data: addForm.serialize(),
                                                    success: function(data) {
                                                        if ($.isEmptyObject(data.errors)) {

                                                            $('#add-type-modal').modal('hide');

                                                            toastr.success(data.success);

                                                            $('#type-add-form').DataTable().ajax.reload();

                                                        } else {
                                                            printErrorMsg('#add-type-modal', data.errors);
                                                        }
                                                    }

                                                });
                                            });

                                            //edit
                                            $(document).on('click', '.edit-type', function() {
                                                var url = $(this).data("url");
                                                $.ajax({
                                                    type: "GET",
                                                    url: url,
                                                    success: function(data) {

                                                        $("#type-edit-form").attr('action', url);

                                                        $('#type-modal').modal('show');
                                                        var invigilation = data.invigilation;
                                                        var url = data.url;

                                                        $("#type-edit-form #name").val(invigilation.name);
                                                        $("#type-edit-form").attr('action', url);
                                                        // table.draw();
                                                    },
                                                    error: function(data) {
                                                        console.log('Error:', data);
                                                    }
                                                });
                                            });

                                            // Update
                                            $(document).on('click', '#update-type', function(e) {
                                                var editForm = $("#type-edit-form");
                                                var url = editForm.attr('action');

                                                $.ajax({
                                                    type: "PUT",
                                                    data: editForm.serializeArray(),
                                                    url: url,

                                                    success: function(data) {
                                                        if ($.isEmptyObject(data.errors)) {
                                                            $('#type-modal').modal('hide');
                                                            toastr.success(data.success);

                                                            $('#data-table-invigilation').DataTable().ajax.reload();

                                                        } else {
                                                            printErrorMsg('#add-type-modal', data.errors);
                                                        }


                                                    }

                                                });
                                            });

                                            // Delete
                                            $(document).on('click', '.delete-type', function() {

                                                var url = $(this).data("url");


                                                $.ajax({
                                                    type: "DELETE",
                                                    url: url,
                                                    success: function(data) {
                                                        //Refresh table
                                                        table.draw();
                                                    },
                                                    error: function(data) {
                                                        console.log('Error:', data);
                                                    }
                                                });
                                            });


                                            /****  Print errors*******/
                                            function printErrorMsg(parent, msg) {
                                                $(`${parent} input, ${parent} select, textarea`).each(function(index) {
                                                    $(`${parent} .invalid-feedback`).remove();
                                                    $(`${parent} .is-invalid`).removeClass('is-invalid');
                                                    // console.log(input.attr('type') + 'Name: ' + input.attr('name') + '  Value: ' + input.val());
                                                });
                                                $.each(msg, function(key, errors) {
                                                    for (const error in errors) {
                                                        const value = errors[error];
                                                        $(`[name='${key}']`).addClass('is-invalid');
                                                        $(`<span class='invalid-feedback'>${value}</span>`).insertAfter(
                                                            `${parent} [name='${key}']`)

                                                    }
                                                });
                                            }
                                            /****  Print errors End*******/



                                        });
                                    </script>
                                @endpush

                            </div>
                        </div>
                        <!-- END PANEL NO CONTROLS -->
                    </div>

                </div>


            </div>
        </div>
        <!-- END MAIN CONTENT -->
    </div>




    <!-- ============================================================== -->
    <!-- End PAge Content -->
    <!-- ============================================================== -->
@endsection
