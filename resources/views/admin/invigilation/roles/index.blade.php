@extends('layouts.admin')
@section('content')
    <div class="main">
        <!-- MAIN CONTENT -->
        <div class="main-content">
            <div class="container-fluid">
                <h3 class="page-title">Manage Roles allocation</h3>
                <div class="row">
                    <div class="col-md-12">
                        <!-- PANEL NO CONTROLS -->
                        <div class="panel">
                            <div class="panel-heading">
                                <h3 class="panel-title">Roles allocation<b></b></h3>
                            </div>

                            <div class="panel-body">

                                <button type="button" class="btn btn-primary" data-toggle="modal"
                                    data-target="#add-role-modal">
                                    + Roles allocation
                                </button>
                                <table class="table table-striped" id="data-table-invigilation">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Invigilation Type</th>
                                            <th>Candidate Range</th>
                                            <th>Number of Invigilator</th>
                                            <th>Amount</th>
                                            <th>Session paid</th>
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











    <!-- Modal add invigilation roles-->
    <div class="modal fade" id="add-role-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Invigilation Roles</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="role-add-form" method="post" action="{{ route('admin.invigilations.roles.store') }}">
                        @csrf
                        <div class="form-group row">
                            <label for="invigilation_type" class="col-sm-12 col-form-label">Invigilation Type</label>
                            <div class="col-sm-12">
                                <select class="form-control" name="invigilation_type_id">
                                    <option value="">Select</option>

                                    @foreach ($invigilatortypes as $invigilatortype)
                                        <option value="{{ $invigilatortype->id }}">
                                            {{ $invigilatortype->name }}-{{ $invigilatortype->invigilation_catergories->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="range" class="col-sm-12 col-form-label">Candidate
                                Range</label>
                            <div class="col-sm-12">
                                <select class="form-control col-sm-12" name="invigilation_candidate_id">
                                    <option value="">Select</option>

                                    @foreach ($invigilatorCandidates as $invigilatorCandidate)
                                        <option value="{{ $invigilatorCandidate->id }}">
                                            {{ $invigilatorCandidate->range_start }} -
                                            {{ $invigilatorCandidate->range_end }}</option>
                                    @endforeach


                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="invigilator_number" class="col-sm-12 col-form-label">Number of
                                Invigilator</label>
                            <div class="col-sm-12">
                                <input type="number" class="form-control" name="invigilator_number" id="invigilator_number"
                                    placeholder="0">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="invigilator_paymentamount_id" class="col-sm-12 col-form-label">Amount</label>
                            <div class="col-sm-12">
                                <select class="form-control" name="invigilator_paymentamount_id">
                                    <option value="">Select</option>

                                    @foreach ($invigilatorPaymentamounts as $invigilatorPaymentamount)
                                        <option value="{{ $invigilatorPaymentamount->id }}">
                                            M {{ $invigilatorPaymentamount->amount }}.00</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-check form-group row">
                            <label for="description" class="col-sm-4 col-form-label">Is payment based on sessions</label>
                            <input class="form-check-input" type="checkbox" value="1" name="is_sessions"
                                id="is_sessions">
                        </div>


                    </form>
                </div>
                <div class="modal-footer">

                    <button type="button" class="btn btn-primary" id="add-role">Save</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Edit invigilation roles-->
    <div class="modal fade" id="roles-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Update Invigilator roles</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="role-edit-form" method="POST" action="">
                        @csrf
                        @method('PUT')
                        <div class="form-group row">
                            <label for="invigilation_type" class="col-sm-12 col-form-label">Invigilation Type</label>
                            <div class="col-sm-12">
                                <select class="form-control" name="invigilation_type_id" id="invigilation_type_id">
                                    <option selected></option>

                                    @foreach ($invigilatortypes as $invigilatortype)
                                        <option value="{{ $invigilatortype->id }}">
                                            {{ $invigilatortype->name }}-{{ $invigilatortype->invigilation_catergories->name }}
                                        </option>
                                    @endforeach


                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="range" class="col-sm-12 col-form-label">Candidate
                                Range</label>
                            <div class="col-sm-12">
                                <select class="form-control" name="invigilation_candidate_id"
                                    id="invigilation_candidate_id">
                                    <option selected></option>

                                    @foreach ($invigilatorCandidates as $invigilatorCandidate)
                                        <option value="{{ $invigilatorCandidate->id }}">
                                            {{ $invigilatorCandidate->range_start }} -
                                            {{ $invigilatorCandidate->range_end }}</option>
                                    @endforeach


                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="invigilator_number" class="col-sm-12 col-form-label">Number of
                                Invigilator</label>
                            <div class="col-sm-12">
                                <input type="number" class="form-control" name="invigilator_number"
                                    id="invigilator_number" placeholder="Number of invigilators">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="invigilation_type" class="col-sm-12 col-form-label">Amount</label>
                            <div class="col-sm-12">
                                <select class="form-control" name="invigilator_paymentamount_id"
                                    id="invigilator_paymentamount_id">
                                    <option value="">Select</option>

                                    @foreach ($invigilatorPaymentamounts as $invigilatorPaymentamount)
                                        <option value="{{ $invigilatorPaymentamount->id }}">
                                            M {{ $invigilatorPaymentamount->amount }}.00</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-check form-group row">
                            <label for="description" class="col-sm-4 col-form-label">Is payment based on sessions</label>
                            <input class="form-check-input" type="checkbox" value="1" name="is_sessions"
                                id="is_sessions">
                        </div>

                    </form>
                </div>
                <div class="modal-footer">

                    <button type="button" class="btn btn-primary" id="update-role">Update</button>
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
                    ajax: "{{ route('admin.invigilations.roles.index') }}",
                    columns: [{
                            data: 'id',
                            name: 'id'

                        },
                        {
                            data: 'invigilation_type',
                            name: 'invigilation_type'
                        },
                        {
                            data: 'candidate_range',
                            name: 'candidate_range'
                        },
                        {
                            data: 'invigilator_number',
                            name: 'invigilator_number'
                        },
                        {
                            data: 'invigilator_paymentamount.amount',
                            name: 'invigilator_paymentamount.amount'
                        },
                        {
                            data: 'is_sessions',
                            name: 'is_sessions'
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
                $('#add-role').on('click', function(event) {
                    var addForm = $("#role-add-form");
                    var url = addForm.attr('action');
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: addForm.serialize(),
                        success: function(data) {
                            if ($.isEmptyObject(data.errors)) {
                                $('#add-role-modal').modal('hide');
                                toastr.success(data.success);
                                $('#data-table-invigilation').DataTable().ajax.reload();
                            } else {
                                printErrorMsg('#add-role-modal', data.errors);
                            }
                        }

                    });
                });

                //edit
                $(document).on('click', '.edit-role', function() {

                    var url = $(this).data("url");
                    $.ajax({
                        type: "GET",
                        url: url,
                        success: function(data) {
                            $('#roles-modal').modal('show');
                            //Refresh table
                            table.draw();
                            var invigilation = data.invigilation;
                            var url = data.url;

                            var form = '#role-edit-form';

                            $(`${form} input, ${form} select`).each(
                                function(index) {
                                    var input = $(this);
                                    console.log('Type: ' + input.attr('type') + 'Name: ' + input
                                        .attr(
                                            'name') +
                                        'Value: ' + input.val());
                                    var name = input.attr('name');
                                    $("#role-edit-form").attr('action', url);
                                    if (input.attr('type') == "checkbox") {
                                        $(`${form} #${name}`).attr("checked", invigilation[
                                            name] == 1 ? true : false);
                                    } else {
                                        $(`${form} #${name}`).val(invigilation[name]);
                                    }

                                }
                            );






                            // $(`${form} input, ${form} select`).each(
                            //     function(index) {
                            //         var input = $(this);
                            //         console.log('Type: ' + input.attr('type') + 'Name: ' + input
                            //             .attr('name') +
                            //             'Value: ' + input.val());
                            //         var name = input.attr('name');

                            //         $(`${form} #${name}`).val(invigilation[name]);
                            //         $("#role-edit-form").attr('action', url);

                            //     }
                            // );
                        },
                        error: function(data) {
                            console.log('Error:', data);
                        }
                    });
                });

                // Update
                $(document).on('click', '#update-role', function(e) {
                    var editForm = $("#role-edit-form");
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
                                $('#roles-modal').modal('hide');
                                toastr.success(data.success);
                                $('#responseMessage').DataTable().ajax.reload();
                            } else {
                                printErrorMsg('#roles-modal', data.errors);
                            }


                        }


                    });
                });
                // Delete
                $(document).on('click', '.delete-role', function() {

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
@endsection
