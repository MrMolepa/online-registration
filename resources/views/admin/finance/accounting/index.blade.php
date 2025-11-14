@extends('layouts.admin')
@section('content')
    <!-- ============================================================== -->
    <!-- Start Page Content -->
    <!-- ============================================================== -->
    <div class="main">
        <!-- MAIN CONTENT -->
        <div class="main-content">
            <div class="container-fluid">
                <h3 class="page-title">Manage Fee account</h3>
                <div class="row">
                    <div class="col-md-12">
                        <!-- PANEL NO CONTROLS -->
                        <div class="panel">
                            <div class="panel-body">

                                <div class="custom-tabs-line tabs-line-bottom left-aligned">
                                    <ul class="nav" role="tablist">
                                        <li class="active">
                                            <a href="#fee-account-tab" role="tab" data-toggle="tab">Account</a>
                                        </li>
                                        <li>
                                            <a href="#voucher-head-tab" role="tab" data-toggle="tab">Voucher Head</a>
                                        </li>

                                    </ul>
                                </div>
                                <div class="tab-content">
                                    <div class="tab-pane fade in active" id="fee-account-tab">
                                        <button type="button" class="btn btn-primary" data-toggle="modal"
                                            data-target="#add-account-modal">
                                            + Add Account
                                        </button>
                                        <table class="table table-striped" id="data-table-account">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Account Name</th>
                                                    <th>Description</th>
                                                    <th>Account Number</th>
                                                    <th>Opening Balance</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="tab-pane fade" id="voucher-head-tab">
                                        <button type="button" class="btn btn-primary" data-toggle="modal"
                                            data-target="#add-voucher-modal">
                                            + Add Voucher
                                        </button>
                                        <table class="table table-striped" id="data-table-voucher">
                                            <thead>
                                                <tr>

                                                    <th>ID</th>
                                                    <th>Account Name</th>
                                                    <th>Description</th>
                                                    <th>Type</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                            </div>



                        </div>
                        {{-- Add modal Account --}}
                        <div class="modal fade" id="add-account-modal" tabindex="-1" role="dialog"
                            aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-md" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Add Account</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <form id="add-account-form" method="post"
                                            action="{{ route('admin.accounting.accounts.store') }}">
                                            @csrf
                                            <fieldset class="row  fieldset-border">
                                                <legend class="fieldset-border">Account Setup</legend>
                                                <div class="row">
                                                    <div class="form-group col-lg-12">
                                                        <label for="name" class="form-label">Account Name</label>
                                                        <input type="text" class="form-control" name="name"
                                                            id="name" placeholder="Account Number">
                                                    </div>
                                                    <div class="form-group col-lg-12">
                                                        <label for="description" class="form-label">Account
                                                            Description</label>
                                                        <input type="text" class="form-control" name="description"
                                                            id="description" placeholder="Account Description">
                                                    </div>
                                                    <div class="form-group col-lg-12">
                                                        <label for="account_number" class="form-label">Account
                                                            Number</label>
                                                        <input type="text" class="form-control" name="account_number"
                                                            id="account_number" placeholder="Account Number">
                                                    </div>
                                                    <div class="form-group col-lg-12">
                                                        <label for="balance" class="form-label">Account
                                                            Description</label>
                                                        <input type="text" class="form-control" name="balance"
                                                            id="balance" placeholder="Opening Balance">
                                                    </div>


                                                    <div class="clearfix"></div>
                                                </div>
                                            </fieldset>
                                        </form>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-primary" id="add-account">Save</button>
                                            <button type="button" class="btn btn-danger"
                                                data-dismiss="modal">Close</button>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- End modal Account --}}
                        <div class="modal fade" id="edit-account-modal" tabindex="-1" role="dialog"
                            aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-md" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Edit Account</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <form id="edit-account-form" method="post" action="">
                                            @csrf
                                            @method('PUT')

                                            <fieldset class="row  fieldset-border">
                                                <legend class="fieldset-border">Account Setup</legend>
                                                <div class="row">
                                                    <div class="form-group col-lg-12">
                                                        <label for="name" class="form-label">Account
                                                            Name</label>
                                                        <input type="text" class="form-control" name="name"
                                                            id="name" placeholder="Account Number">
                                                    </div>
                                                    <div class="form-group col-lg-12">
                                                        <label for="description" class="form-label">Account
                                                            Description</label>
                                                        <input type="text" class="form-control" name="description"
                                                            id="description" placeholder="Account Description">
                                                    </div>
                                                    <div class="form-group col-lg-12">
                                                        <label for="account_number" class="form-label">Account
                                                            Number</label>
                                                        <input type="text" class="form-control" name="account_number"
                                                            id="account_number" placeholder="Account Number">
                                                    </div>
                                                    <div class="form-group col-lg-12">
                                                        <label for="balance" class="form-label">Account
                                                            Description</label>
                                                        <input type="text" class="form-control" name="balance"
                                                            id="balance" placeholder="Opening Balance">
                                                    </div>

                                                    <div class="clearfix"></div>
                                                </div>
                                                <div class="clearfix"></div>
                                    </div>
                                    </fieldset>
                                    </form>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-primary"
                                            id="update-account">Update</button>
                                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
                        {{-- Add modal Voucher Head --}}
                        <div class="modal fade" id="add-voucher-modal" tabindex="-1" role="dialog"
                            aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-md" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Add voucher</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <form id="add-voucher-form" method="post"
                                            action="{{ route('admin.accounting.vouchers.store') }}">
                                            @csrf
                                            <fieldset class="row  fieldset-border">
                                                <legend class="fieldset-border">voucher Setup</legend>
                                                <div class="row">
                                                    <div class="form-group col-lg-12">
                                                        <label for="name" class="form-label">voucher Name</label>
                                                        <input type="text" class="form-control" name="name"
                                                            id="name" placeholder="voucher Number">
                                                    </div>
                                                    <div class="form-group col-lg-12">
                                                        <label for="description" class="form-label">voucher
                                                            Description</label>
                                                        <input type="text" class="form-control" name="description"
                                                            id="description" placeholder="voucher Description">
                                                    </div>
                                                    <div class="form-group col-sm-12">
                                                        <label for="type" class="form-label">Voucher Head Type</label>
                                                        <select class="form-control" name="type" id="type">
                                                            <option value=""> select</option>
                                                            <option value="Income">
                                                                Income
                                                            </option>
                                                            <option value="Expense">
                                                                Expense
                                                            </option>
                                                        </select>
                                                    </div>


                                                    <div class="clearfix"></div>
                                                </div>
                                            </fieldset>
                                        </form>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-primary" id="add-voucher">Save</button>
                                            <button type="button" class="btn btn-danger"
                                                data-dismiss="modal">Close</button>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- End modal --}}
                        <div class="modal fade" id="edit-voucher-modal" tabindex="-1" role="dialog"
                            aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-md" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Edit voucher</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <form id="edit-voucher-form" method="post" action="">
                                            @csrf
                                            @method('PUT')
                                            <fieldset class="row  fieldset-border">
                                                <legend class="fieldset-border">voucher Setup</legend>
                                                <div class="row">
                                                    <div class="form-group col-lg-12">
                                                        <label for="name" class="form-label">voucher Name</label>
                                                        <input type="text" class="form-control" name="name"
                                                            id="name" placeholder="voucher Number">
                                                    </div>
                                                    <div class="form-group col-lg-12">
                                                        <label for="description" class="form-label">voucher
                                                            Description</label>
                                                        <input type="text" class="form-control" name="description"
                                                            id="description" placeholder="voucher Description">
                                                    </div>
                                                    <div class="form-group col-sm-12">
                                                        <label for="type" class="form-label">Voucher Head Type</label>
                                                        <select class="form-control" name="type" id="type">
                                                            <option value=""> select</option>
                                                            <option value="Income">
                                                                Income
                                                            </option>
                                                            <option value="Expense">
                                                                Expense
                                                            </option>
                                                        </select>
                                                    </div>


                                                    <div class="clearfix"></div>
                                                </div>
                                            </fieldset>
                                        </form>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-primary"
                                                id="update-voucher">Update</button>
                                            <button type="button" class="btn btn-danger"
                                                data-dismiss="modal">Close</button>
                                        </div>
                                        <div class="clearfix"></div>
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
                                //
                                $(document).ready(function() {
                                    $.ajaxSetup({
                                        headers: {
                                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                        }
                                    });
                                    //Account Script
                                    // datatable
                                    var table = $('#data-table-account').DataTable({
                                        processing: true,
                                        serverSide: true,
                                        ajax: "{{ route('admin.accounting.accounts.index') }}",
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
                                                data: 'account_number',
                                                name: 'account_number'
                                            },
                                            {
                                                data: 'balance',
                                                name: 'balance',
                                                render: $.fn.dataTable.render.number(',', '.', 2, 'LSL')
                                            },
                                            {
                                                data: 'action',
                                                name: 'action',
                                                orderable: false,
                                                searchable: false
                                            },

                                        ]
                                    });
                                    $("#data-table-account").css("width", "100%");
                                    //add
                                    $('#add-account').on('click', function(event) {
                                        var addForm = $("#add-account-form");
                                        var url = addForm.attr('action');
                                        $.ajax({
                                            url: url,
                                            type: 'POST',
                                            data: addForm.serialize(),
                                            success: function(data) {
                                                console.log(data)
                                                if ($.isEmptyObject(data.errors)) {
                                                    $('#add-account-modal').modal('hide');
                                                    toastr.success(data.success);
                                                    addForm[0].reset();
                                                    $('#data-table-account').DataTable().ajax.reload();

                                                } else {
                                                    printErrorMsg('#add-account-modal', data.errors);
                                                }
                                            }

                                        });
                                    })
                                    //edit
                                    $(document).on('click', '.edit-account', function() {

                                        var url = $(this).data("url");
                                        $.ajax({
                                            type: "GET",
                                            url: url,
                                            success: function(data) {
                                                $('#edit-account-modal').modal('show');
                                                var account = data.account;
                                                var url = data.url;
                                                var form = '#edit-account-form';
                                                $(`${form} input, ${form} select`).each(
                                                    function(index) {
                                                        var input = $(this);
                                                        console.log('Type: ' + input.attr('type') + 'Name: ' + input
                                                            .attr(
                                                                'name') +
                                                            'Value: ' + input.val());
                                                        var name = input.attr('name');


                                                        if (input.attr('type') == "checkbox") {
                                                            $(`${form} #${name}`).attr("checked", account[
                                                                name] == 1 ? true : false);
                                                        } else {
                                                            $(`${form} #${name}`).val(account[name]);
                                                        }
                                                        $("#edit-account-form").attr('action', url);

                                                    }
                                                );
                                            },
                                            error: function(data) {
                                                console.log('Error:', data);
                                            }
                                        });
                                    });
                                    // Update
                                    $(document).on('click', '#update-account', function(e) {
                                        var editForm = $("#edit-account-form");
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
                                                    $('#edit-account-modal').modal('hide');
                                                    toastr.success(data.success);
                                                    editForm[0].reset();
                                                    $('#data-table-account').DataTable().ajax.reload();
                                                } else {
                                                    printErrorMsg('#edit-account-modal', data.errors);
                                                }


                                            }
                                        });
                                    });
                                    // Delete
                                    $(document).on('click', '.delete-account', function() {
                                        var url = $(this).data("url");
                                        if (confirm("Are you sure you want to delete this charges !") == true) {
                                            $.ajax({
                                                type: "DELETE",
                                                url: url,
                                                success: function(data) {
                                                $('#data-table-account').DataTable().ajax.reload();
                                                },
                                                error: function(data) {
                                                    console.log('Error:', data);
                                                }
                                            });
                                        }
                                    });

                                    //Voucher Head script
                                    // datatable
                                    var table = $('#data-table-voucher').DataTable({
                                        processing: true,
                                        serverSide: true,
                                        ajax: "{{ route('admin.accounting.vouchers.index') }}",
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
                                                data: 'type',
                                                name: 'type'
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
                                    $('#add-voucher').on('click', function(event) {
                                        var addForm = $("#add-voucher-form");
                                        var url = addForm.attr('action');
                                        $.ajax({
                                            url: url,
                                            type: 'POST',
                                            data: addForm.serialize(),
                                            success: function(data) {
                                                console.log(data)
                                                if ($.isEmptyObject(data.errors)) {
                                                    $('#add-voucher-modal').modal('hide');
                                                    toastr.success(data.success);
                                                    addForm[0].reset();
                                                    $('#data-table-voucher').DataTable().ajax.reload();

                                                } else {
                                                    printErrorMsg('#add-voucher-modal', data.errors);
                                                }
                                            }

                                        });
                                    })
                                    //edit
                                    $(document).on('click', '.edit-voucher', function() {

                                        var url = $(this).data("url");
                                        $.ajax({
                                            type: "GET",
                                            url: url,
                                            success: function(data) {
                                                $('#edit-voucher-modal').modal('show');
                                                var voucher = data.voucher;
                                                var url = data.url;
                                                var form = '#edit-voucher-form';
                                                $(`${form} input, ${form} select`).each(
                                                    function(index) {
                                                        var input = $(this);
                                                        console.log('Type: ' + input.attr('type') + 'Name: ' + input
                                                            .attr(
                                                                'name') +
                                                            'Value: ' + input.val());
                                                        var name = input.attr('name');


                                                        if (input.attr('type') == "checkbox") {
                                                            $(`${form} #${name}`).attr("checked", voucher[
                                                                name] == 1 ? true : false);
                                                        } else {
                                                            $(`${form} #${name}`).val(voucher[name]);
                                                        }
                                                        $("#edit-voucher-form").attr('action', url);

                                                    }
                                                );
                                            },
                                            error: function(data) {
                                                console.log('Error:', data);
                                            }
                                        });
                                    });
                                    // Update
                                    $(document).on('click', '#update-voucher', function(e) {
                                        var editForm = $("#edit-voucher-form");
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
                                                    $('#edit-voucher-modal').modal('hide');
                                                    toastr.success(data.success);
                                                    editForm[0].reset();
                                                    $('#data-table-voucher').DataTable().ajax.reload();
                                                } else {
                                                    printErrorMsg('#edit-voucher-modal', data.errors);
                                                }


                                            }
                                        });
                                    });
                                    // Delete
                                    $(document).on('click', '.delete-voucher', function() {
                                        var url = $(this).data("url");
                                        if (confirm("Are you sure you want to delete this charges !") == true) {
                                            $.ajax({
                                                type: "DELETE",
                                                url: url,
                                                success: function(data) {
                                                    $('#data-table-voucher').DataTable().ajax.reload();
                                                },
                                                error: function(data) {
                                                    console.log('Error:', data);
                                                }
                                            });
                                        }
                                    });



                                    $("#data-table-voucher").css("width", "100%");
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
                    </div>
                </div>
                <!-- END PANEL NO CONTROLS -->
            </div>
        </div>

        <!-- ============================================================== -->
        <!-- End PAge Content -->
        <!-- ============================================================== -->
    @endsection
