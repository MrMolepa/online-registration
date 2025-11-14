@extends('layouts.admin')
@section('content')
    <!-- ============================================================== -->
    <!-- Start Page Content -->
    <!-- ============================================================== -->
    <div class="main">
        <!-- MAIN CONTENT -->
        <div class="main-content">
            <div class="container-fluid">
                <h3 class="page-title">Manage candidates range</h3>
                <div class="row">
                    <div class="col-md-12">
                        <!-- PANEL NO CONTROLS -->
                        <div class="panel">
                            <div class="panel-heading">
                                <h3 class="panel-title">Candidates range <b></b></h3>
                            </div>
                            <div class="panel-body">
                                {{--  --}}
                                <button type="button" class="btn btn-primary" data-toggle="modal"
                                    data-target="#add-range-modal">
                                    + Add range
                                </button>
                                <div class="col-sm-12">
                                    <table class="table table-striped"id="data-table-invigilation">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Candidate Range from</th>
                                                <th>Candidate Range to</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- END PANEL NO CONTROLS -->
                    </div>

                </div>


            </div>
        </div>
        <!-- END MAIN CONTENT -->
    </div>
    <!-- Modal add Candidate range-->
    <div class="modal fade" id="add-range-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Candidates ranges</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="range-add-form" method="post"
                        action="{{ route('admin.invigilations.candidatesrange.store') }}">
                        @csrf

                            <div class="form-group">
                                <label for="range from" class=" col-form-label">Candidate Range from</label>

                                    <input type="number" class="form-control" name="range_start" id="range_start"
                                        placeholder=" ">
                                          </div>
                            <div class="form-group ">
                                <label for="range to" class="">Candidate Range to</label>
                                    <input type="number" class="form-control" name="range_end" id="range_end"
                                        placeholder=" ">
                        </div>

                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="add-range">Save</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Edit candidate range -->
    <div class="modal fade" id="range-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Update Candidates range</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="range-edit-form" method="POST" action="">
                        @csrf
                        @method('PUT')
                            <div class="form-group">
                                <label for="candidate_from" class="col-form-label">Candidate Range
                                    from</label>
                                    <input type="number" class="form-control" name="range_start" id="range_start"
                                        placeholder=" ">
                            </div>
                            <div class="form-group">
                                <label for="candidate_to" class="col-form-label">Candidate Range
                                    to</label>
                                    <input type="number" class="form-control" name="range_end" id="range_end"
                                        placeholder=" ">
                            </div>
                    </form>
                </div>
                <div class="modal-footer">

                    <button type="button" class="btn btn-primary" id="update-range">Update</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
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
                    ajax: "{{ route('admin.invigilations.candidatesrange.index') }}",
                    columns: [{
                            data: 'id',
                            name: 'id'
                        },
                        {
                            data: 'range_start',
                            name: 'range_start'
                        },
                        {
                            data: 'range_end',
                            name: 'range_end'
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
                $('#add-range').on('click', function(event) {
                    var addForm = $("#range-add-form");
                    var url = addForm.attr('action');
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: addForm.serialize(),
                        success: function(data) {
                            if ($.isEmptyObject(data.errors)) {
                                $('#add-range-modal').modal('hide');
                                toastr.success(data.success);
                                $('#data-table-invigilation').DataTable().ajax.reload();
                            } else {
                                printErrorMsg('#add-range-modal', data.errors);
                            }
                        }
                    });
                });

                //edit
                $(document).on('click', '.edit-range', function() {
                    var url = $(this).data("url");
                    $.ajax({
                        type: "GET",
                        url: url,
                        success: function(data) {
                            $('#range-modal').modal('show');
                            //Refresh table
                            table.draw();
                            var invigilation = data.invigilation;
                            var url = data.url;

                            var form = '#range-edit-form';

                            $(`${form} input, ${form} select`).each(
                                function(index) {
                                    var input = $(this);
                                    console.log('Type: ' + input.attr('type') + 'Name: ' + input
                                        .attr(
                                            'name') +
                                        'Value: ' + input.val());
                                    var name = input.attr('name');

                                    $(`${form} #${name}`).val(invigilation[name]);
                                    $("#range-edit-form").attr('action', url);

                                }
                            );
                        },
                        error: function(data) {
                            console.log('Error:', data);
                        }
                    });
                });

                // Update
                $(document).on('click', '#update-range', function(e) {
                    var editForm = $("#range-edit-form");
                    var url = editForm.attr('action');

                    $.ajax({
                        type: "POST",
                        data: editForm.serializeArray(),
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        url: url,
                        success: function(data) {
                            if ($.isEmptyObject(data.errors)) {
                                $('#range-modal').modal('hide');
                                toastr.success(data.success);
                                $('#responseMessage').DataTable().ajax.reload();
                            } else {
                                printErrorMsg('#range-modal', data.errors);
                            }


                        }
                    });
                });






                // Delete
                $(document).on('click', '.delete-range', function() {

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
            });
        </script>
    @endpush
@endsection
