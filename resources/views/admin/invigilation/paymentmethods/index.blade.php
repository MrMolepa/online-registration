@extends('layouts.admin')
@section('content')
    <!-- ============================================================== -->
    <!-- Start Page Content -->
    <!-- ============================================================== -->
    <div class="main">
        <!-- MAIN CONTENT -->
        <div class="main-content">
            <div class="container-fluid">
                <h3 class="page-title">Manage Invigilation Payment method</h3>
                <div class="row">
                    <div class="col-md-12">
                        <!-- PANEL NO CONTROLS -->
                        <div class="panel">
                            <div class="panel-heading">
                                <h3 class="panel-title">Payment method <b></b></h3>
                            </div>
                            <div class="panel-body">
                                {{--  --}}
                                <button type="button" class="btn btn-primary" data-toggle="modal"
                                    data-target="#add-paymentmethod-modal">
                                    + Add payment method
                                </button>

                                <table class="table table-striped"id="data-table-paymetmethods">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Method Name</th>
                                            <th>Description </th>
                                            <th>Account Number</th>
                                            <th>Branch Code</th>
                                            <th>Phone Number</th>
                                            <th>Tin Number</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>



                                <!-- Modal add payment method--->
                                <div class="modal fade" id="add-paymentmethod-modal" tabindex="-1" role="dialog"
                                    aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLabel">Add Payment Method</h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <form id="paymentmethod-add-form" method="post"
                                                    action="{{ route('admin.invigilations.paymentmethods.store') }}">
                                                    @csrf

                                                    <div class="form-group row center col-sm-12">
                                                        <label for="invigilation_type"
                                                            class="col-sm-4 col-form-label">Payment
                                                            Method</label>
                                                        <div class="col-sm-8">
                                                            <input type="text" class="form-control" name="name"
                                                                id="name" placeholder="Enter payment method name">
                                                        </div>
                                                    </div>

                                                    <div class="form-group row center col-sm-12">
                                                        <label for="description" class="col-sm-4 col-form-label">Payment
                                                            Description</label>
                                                        <div class="col-sm-8">
                                                            <input type="text" class="form-control" name="description"
                                                                id="description"
                                                                placeholder="Enter payment method description">
                                                        </div>
                                                    </div>

                                                    <div class="form-group row center col-sm-12">

                                                        <div class="form-check">
                                                            <label for="description"
                                                                class="col-sm-4 col-form-label">Branch</label>
                                                            <input class="form-check-input" type="checkbox" value="1"
                                                                name="is_branch" id="is_branch">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row center col-sm-12">

                                                        <div class="form-check">
                                                            <label for="description" class="col-sm-4 col-form-label">Account
                                                                Number</label>
                                                            <input class="form-check-input" type="checkbox" value="1"
                                                                name="is_account_number" id="is_account_number">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row center col-sm-12">

                                                        <div class="form-check">
                                                            <label for="description" class="col-sm-4 col-form-label">Phone
                                                                Number</label>
                                                            <input class="form-check-input" type="checkbox" value="1"
                                                                name="is_phone_number" id="is_phone_number">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row center col-sm-12">

                                                        <div class="form-check">
                                                            <label for="description" class="col-sm-4 col-form-label">Tin
                                                                Number</label>
                                                            <input class="form-check-input" type="checkbox" value="1"
                                                                name="is_tin_number" id="is_tin_number">
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-primary"
                                                    id="add-payment">Save</button>
                                                <button type="button" class="btn btn-danger"
                                                    data-dismiss="modal">Close</button>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <!-- Modal Edit invigilation type-->
                            <div class="modal fade" id="edit-paymentmethod-modal" tabindex="-1" role="dialog"
                                aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel">Update Payment Method</h5>
                                            <button type="button" class="close" data-dismiss="modal"
                                                aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <form id="edit-paymentmethod-form" method="post" action="">
                                                @csrf
                                                @method('PUT')
                                                <div class="form-group row center col-sm-12">
                                                    <label for="invigilation_type" class="col-sm-4 col-form-label">Payment
                                                        Method</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" class="form-control" name="name"
                                                            id="name" placeholder="Enter payment method name">
                                                    </div>
                                                </div>

                                                <div class="form-group row center col-sm-12">
                                                    <label for="description" class="col-sm-4 col-form-label">Payment
                                                        Description</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" class="form-control" name="description"
                                                            id="description"
                                                            placeholder="Enter payment method description">
                                                    </div>
                                                </div>

                                                <div class="form-group row center col-sm-12">

                                                    <div class="form-check">
                                                        <label for="description" class="col-sm-4 col-form-label">Branch
                                                            code</label>
                                                        <input class="form-check-input" type="checkbox" value="1"
                                                            name="is_branch" id="is_branch">

                                                    </div>
                                                </div>
                                                <div class="form-group row center col-sm-12">

                                                    <div class="form-check">
                                                        <label for="description" class="col-sm-4 col-form-label">Account
                                                            Number</label>
                                                        <input class="form-check-input" type="checkbox" value="1"
                                                            name="is_account_number" id="is_account_number">
                                                    </div>
                                                </div>
                                                <div class="form-group row center col-sm-12">

                                                    <div class="form-check">
                                                        <label for="description" class="col-sm-4 col-form-label">Phone
                                                            Number</label>
                                                        <input class="form-check-input" type="checkbox" value="1"
                                                            name="is_phone_number" id="is_phone_number">
                                                    </div>
                                                </div>
                                                <div class="form-group row center col-sm-12">

                                                    <div class="form-check">
                                                        <label for="description" class="col-sm-4 col-form-label">Tin
                                                            Number</label>
                                                        <input class="form-check-input" type="checkbox" value="1"
                                                            name="is_tin_number" id="is_tin_number">
                                                    </div>
                                                </div>
                                            </form>
                                            <div class="modal-footer">

                                                <button type="button" class="btn btn-primary"
                                                    id="update-type">update</button>
                                                <button type="button" class="btn btn-danger"
                                                    data-dismiss="modal">Close</button>
                                            </div>
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
                                        var table = $('#data-table-paymetmethods').DataTable({
                                            processing: true,
                                            serverSide: true,
                                            ajax: "{{ route('admin.invigilations.paymentmethods.index') }}",
                                            columns: [{
                                                    data: 'id',
                                                    name: 'id'
                                                },
                                                {
                                                    data: 'name',
                                                    name: 'name'
                                                },
                                                {
                                                    data: 'description',
                                                    name: 'description'
                                                },
                                                {
                                                    data: 'is_account_number',
                                                    name: 'is_account_number'
                                                },
                                                {
                                                    data: 'is_branch',
                                                    name: 'is_branch'
                                                },
                                                {
                                                    data: 'is_phone_number',
                                                    name: 'is_phone_number'
                                                },
                                                {
                                                    data: 'is_tin_number',
                                                    name: 'is_tin_number'
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
                                        $('#add-payment').on('click', function(event) {
                                            var addForm = $("#paymentmethod-add-form");
                                            var url = addForm.attr('action');
                                            $.ajax({
                                                url: url,
                                                type: 'POST',
                                                data: addForm.serialize(),
                                                success: function(data) {
                                                    if ($.isEmptyObject(data.errors)) {
                                                        $('#add-paymentmethod-modal').modal('hide');
                                                        toastr.success(data.success);
                                                        $('#paymentmethod-add-form')[0].reset();
                                                        $('#data-table-paymetmethods').DataTable().ajax.reload();
                                                    } else {
                                                        printErrorMsg('#add-paymentmethod-modal', data.errors);
                                                    }
                                                }

                                            });
                                        });
                                        //edit
                                        $(document).on('click', '.edit-paymentmethods', function() {

                                            var url = $(this).data("url");
                                            $.ajax({
                                                type: "GET",
                                                url: url,
                                                success: function(data) {
                                                    $('#edit-paymentmethod-modal').modal('show');
                                                    //Refresh table
                                                    table.draw();
                                                    var invigilation = data.invigilation;
                                                    var url = data.url;

                                                    var form = '#edit-paymentmethod-form';

                                                    $(`${form} input, ${form} select`).each(
                                                        function(index) {
                                                            var input = $(this);
                                                            console.log('Type: ' + input.attr('type') + 'Name: ' + input
                                                                .attr(
                                                                    'name') +
                                                                'Value: ' + input.val());
                                                            var name = input.attr('name');

                                                            $(`${form} #${name}`).val(invigilation[name]);
                                                            $("#edit-paymentmethod-form").attr('action', url);

                                                        }
                                                    );
                                                },
                                                error: function(data) {
                                                    console.log('Error:', data);
                                                }
                                            });
                                        });

                                        // Update
                                        $(document).on('click', '#update-type', function(e) {
                                            var editForm = $("#edit-paymentmethod-form");
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
                                                        $('#edit-paymentmethod-modal').modal('hide');
                                                        toastr.success(data.success);
                                                        $('#data-table-paymetmethods').DataTable().ajax.reload();
                                                    } else {
                                                        printErrorMsg('#edit-paymentmethod-modal', data.errors);
                                                    }


                                                }


                                            });
                                        });
                                        // Delete
                                        $(document).on('click', '.delete-paymentmethods', function() {

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
