@extends('layouts.admin')

@section('content')
    <!-- ============================================================== -->
    <!-- Start Page Content -->
    <!-- ============================================================== -->
    <div class="main">
        <!-- MAIN CONTENT -->
        <div class="main-content">
            <div class="container-fluid">
                <h3 class="page-title">Other Charges ({{ $center->center_no }})</h3>
                <div class="row">
                    <div class="col-md-12">
                        <!-- PANEL NO CONTROLS -->
                        <div class="panel">
                            <div class="panel-heading">
                                <h3 class="panel-title"> Other Charges ({{ $center->center_no }} {{ $center->center_name }})
                                </h3>
                            </div>
                            <div class="panel-body">
                                <div class="pull-right">
                                    <a href="" class="btn btn-info" data-toggle="modal"
                                        data-target="#add-charge-modal">+ Create
                                    </a>
                                </div>

                                <div class="clearfix"></div>
                                <br>
                                <div class="table-responsive">
                                    <table class="table" name="tablename" id="center-other-charges">
                                        <thead>
                                            <tr>
                                                <th>Id</th>
                                                <th>Center No</th>
                                                <th>Centre Name</th>
                                                <th>Comments</th>
                                                <th>Charge</th>
                                                <th>Added By</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>


                                    </table>
                                    @push('scripts')
                                        <script>
                                            $(function() {

                                                var center_other_charges = $('#center-other-charges').DataTable({
                                                    processing: true,
                                                    serverSide: true,

                                                    deferRender: true,
                                                    "lengthMenu": [
                                                        [20, 50, 100, 200, 400, -1],
                                                        [20, 50, 100, 200, 400, "All"]
                                                    ],
                                                    ajax: "{{ route('admin.center-charges.index', ['center_no' => $center->center_no]) }}",
                                                    columns: [{
                                                            data: 'id',
                                                            name: 'id',
                                                            searchable: true
                                                        },
                                                        {
                                                            data: 'center_id',
                                                            name: 'center_id',
                                                            searchable: true
                                                        },
                                                        {
                                                            data: 'center.center_name',
                                                            name: 'center.center_name',
                                                            searchable: true
                                                        },
                                                        {
                                                            data: 'comments',
                                                            name: 'comments ',
                                                            searchable: true
                                                        },
                                                        {
                                                            data: 'charge',
                                                            name: 'charge',
                                                            searchable: true
                                                        },
                                                        {
                                                            data: 'user.email',
                                                            name: 'user.email',
                                                            searchable: true
                                                        },

                                                        {
                                                            data: 'actions',
                                                            name: 'actions',
                                                            searchable: false,
                                                            sortable: false

                                                        }

                                                    ]

                                                });



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

            <!-- ADD Charge MODAL -->
            <div class="modal fade bd-modal-md" id="add-charge-modal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog ">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close resetform" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h3 class="modal-title">New Candidate</h3>
                        </div>
                        <div class="modal-body">
                            <form action="{{ route('admin.center-charges.store') }}" id="storeChargeForm" method="post">
                                <div>
                                    @csrf
                                </div>
                                <div class="form-group ">
                                    <label for="center_no">Center no</label>
                                    <input type="text" class="form-control" name="center_no"
                                        value="{{ $center->center_no }}" readonly id="center_no">

                                </div>
                                <div class="form-group ">
                                    <label for="center_no">center_name</label>
                                    <input type="text" class="form-control" name="center_name"
                                        value="{{ $center->center_name }} " readonly id="center_name">
                                </div>

                                <div class="form-group ">
                                    <label for="charge">Charge Fee</label>
                                    <input type="text" value="" class="form-control" name="charge" id="charge">
                                </div>
                                <div class="form-group ">
                                    <label for="comments">Comments</label>
                                    <textarea name="comments" id="" class="form-control" cols="30" rows="10"></textarea>

                                </div>
                            </form>

                        </div>
                        <div class="modal-footer">
                            <button type="submit" name="save-charge" class="btn btn-primary" id="save-charge">Save</button>
                            <button type="button" class="btn btn-danger resetform" id="close"
                                data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>

            </div>
            <!--END ADD Charge Charge MODEL -->
            <!-- ADD UPDATEMODAL -->
            <div class="modal fade bd-modal-md" id="update-charge-modal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog ">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close resetform" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h3 class="modal-title">Update Charge</h3>
                        </div>
                        <div class="modal-body">
                            <form action="" id="updateFormCharge" method="post">
                                <div>
                                    @csrf
                                </div>
                                <div class="form-group ">
                                    <label for="center_no">Center no</label>
                                    <input type="text" class="form-control" name="center_no"
                                        value="{{ $center->center_no }}" readonly id="center_no">

                                </div>
                                <div class="form-group ">
                                    <label for="center_no">center_name</label>
                                    <input type="text" class="form-control" name="center_name"
                                        value="{{ $center->center_name }} " readonly id="center_name">
                                </div>

                                <div class="form-group ">
                                    <label for="charge">Charge Fee</label>
                                    <input type="text" value="" class="form-control" name="charge"
                                        id="charge">

                                </div>
                                <div class="form-group ">
                                    <label for="comments">Comments</label>
                                    <textarea name="comments" id="comments" class="form-control" cols="30" rows="10"></textarea>

                                </div>
                            </form>

                        </div>
                        <div class="modal-footer">
                            <button type="submit" name="update-charge" class="btn btn-primary"
                                id="update-charge">Save</button>
                            <button type="button" class="btn btn-danger resetform" id="close"
                                data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>

            </div>
            <!--END UPDATE Charge MODEL -->
            <!-- END MAIN CONTENT -->
        </div>
        <!-- END MAIN CONTENT -->
    </div>
    <!-- ============================================================== -->
    <!-- End PAge Content -->
    <!-- ============================================================== -->
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

        //  Add Charge
        $(document).on('click', '#save-charge', function(ev) {
            ev.preventDefault();
            var url = $('#storeChargeForm').attr('action');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });


            var inputData = $("#storeChargeForm").serialize();

            $.ajax({
                url: url,
                method: "POST",
                data: inputData,
                success: function(data) {
                    if ($.isEmptyObject(data.errors)) {
                        $('#add-charge-modal').modal('hide');
                        $('#storeChargeForm .help-block').remove();
                        $('#storeChargeForm .has-error').removeClass('has-error');
                        toastr.success(data.success);
                        $('#center-other-charges').DataTable().ajax.reload();
                    } else {
                        printErrorMsg('#storeChargeForm', data.errors);
                    }


                }
            });


        });

        // Edit charge
        $(document).on("click", "#center-other-charges .editBtn", function(ev) {
            ev.preventDefault();
            var actionUrl = $(this).data('url');

            console.log(actionUrl);

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "GET",
                url: actionUrl,
                success: function(data) {
                    console.log(data);
                    $("#updateFormCharge #comments").val(data.centerOtherCharge.comments)
                    $("#updateFormCharge #charge").val(data.centerOtherCharge.charge)
                    $("#updateFormCharge").attr('action', data.url)
                    $('#update-charge-modal').modal('show');


                },
            });
        });
        // Update Charge
        $(document).on("click", "#update-charge", function() {
            var actionUrl = $('#updateFormCharge').attr('action');
            var inputData = $("#updateFormCharge").serialize();


            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "PUT",
                url: actionUrl,
                data: inputData,
                success: function(data) {

                    if ($.isEmptyObject(data.errors)) {
                        toastr.success("You have successfully Saved Changes");
                        $('#center-other-charges').DataTable().ajax.reload();
                        $('#update-charge-modal').modal('hide');
                    } else {
                        printErrorMsg('#updateFormCharge', data.errors);
                    }

                },
            });
        });


        // delete Candidate
        $(document).on('click', '#center-other-charges .deleteBtn', function(ev) {
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
                            $('#center-other-charges').DataTable().ajax.reload();
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
        function resetErrorMsg(parent) {
            $(`${parent} input, ${parent} select, textarea`).each(function(index) {
                $(`${parent} .help-block`).remove();
                $(`${parent} .has-error`).removeClass('has-error');
                // console.log(input.attr('type') + 'Name: ' + input.attr('name') + '  Value: ' + input.val());
            });

        }
    </script>
@endsection
