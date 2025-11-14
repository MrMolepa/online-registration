@extends('layouts.admin')

@section('content')
    <!-- ============================================================== -->
    <!-- Start Page Content -->
    <!-- ============================================================== -->
    <div class="main">
        <!-- MAIN CONTENT -->
        <div class="main-content">
            <div class="container-fluid">
                <h3 class="page-title">Sponsors</h3>
                <div class="row">
                    <div class="col-md-12">
                        <!-- PANEL NO CONTROLS -->
                        <div class="panel">
                            <div class="panel-heading">
                                <h3 class="panel-title">Sponsor</h3>
                            </div>
                            <div class="panel-body">

                                <div class="custom-tabs-line tabs-line-bottom left-aligned">
                                    <ul class="nav" role="tablist">
                                        <li class="active"><a href="#sponsors" role="tab" data-toggle="tab">Sponsors</a>
                                        </li>
                                        <li><a href="#sponsors-fees" role="tab" data-toggle="tab">Sponsors Fees
                                                Collections </a></li>
                                    </ul>
                                </div>

                                <div class="tab-content">
                                    <div class="tab-pane fade in active" id="sponsors">
                                        <button type="button" data-toggle="modal" data-target="#add-sponsor-modal"
                                            class="btn btn-primary"> +
                                            Sponsor</button>
                                        <table class="table" name="tablename" id="funders-datatable">
                                            <thead>
                                                <tr>
                                                    <th>Sponsor</th>
                                                    <th>Name</th>
                                                    <th>Description</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>

                                            @push('scripts')
                                                <script>
                                                    $(function() {

                                                        var table_funders_datatable = $('#funders-datatable').DataTable({
                                                            processing: true,
                                                            serverSide: true,
                                                            deferRender: true,
                                                            "lengthMenu": [
                                                                [20, 50, 100, 200, 400, -1],
                                                                [20, 50, 100, 200, 400, "All"]
                                                            ],

                                                            ajax: {
                                                                url: "{{ route('admin.sponsors.index') }}",

                                                            },
                                                            columns: [

                                                                {
                                                                    data: 'sponsor',
                                                                    name: 'sponsor',
                                                                    searchable: true
                                                                },
                                                                {
                                                                    data: 'name',
                                                                    name: 'name',
                                                                },
                                                                {
                                                                    data: 'description',
                                                                    name: 'description',

                                                                },

                                                                {
                                                                    data: 'status',
                                                                    name: 'status',
                                                                    searchable: true

                                                                },



                                                                {
                                                                    data: 'actions',
                                                                    name: 'actions',
                                                                },

                                                            ]

                                                        });
                                                        $("#funders-datatable").css("width", "99%");
                                                    });
                                                </script>
                                            @endpush

                                        </table>
                                    </div>
                                    <div class="tab-pane fade in" id="sponsors-fees">
                                        <fieldset>
                                            <legend>Filter</legend>
                                            <div class="pull-right col-md-4">
                                                <div class="input-group">
                                                    <span class="input-group-btn">
                                                        <button class="btn secondary" type="button">Year</button>
                                                    </span>
                                                    <select class="form-control status-dropdown" id="year">
                                                        @foreach ($years as $year)
                                                            <option
                                                                value="{{ $year }}"@if ($year == date('Y')) selected @endif>
                                                                {{ $year }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="clearfix"></div>
                                        </fieldset>
                                        <div class="table-responsive">
                                            <table class="table" name="tablename" id="sponsor-datatable">
                                                <thead>
                                                    <tr>
                                                        <th>Level</th>
                                                        <th>Session</th>
                                                        <th>Sponsor</th>
                                                        <th>Short Name</th>
                                                        <th>Name</th>
                                                        <th>Total Candidates</th>
                                                        <th>Approvals</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>

                                                @push('scripts')
                                                    <script>
                                                        $(function() {
                                                            var table_school_fee = $('#sponsor-datatable').DataTable({
                                                                processing: true,
                                                                serverSide: true,
                                                                deferRender: true,
                                                                "lengthMenu": [
                                                                    [20, 50, 100, 200, 400, -1],
                                                                    [20, 50, 100, 200, 400, "All"]
                                                                ],

                                                                ajax: {
                                                                    url: "{{ route('admin.sponsors.getSponsorAllCollection') }}",
                                                                    data: function(d) {
                                                                        d.year = $("#year").val()
                                                                    }
                                                                },
                                                                columns: [{
                                                                        data: 'level',
                                                                        name: 'level',
                                                                        searchable: true
                                                                    },
                                                                    {
                                                                        data: 'session',
                                                                        name: 'session',
                                                                        searchable: true
                                                                    },
                                                                    {
                                                                        data: 'sponsor',
                                                                        name: 'sponsor',
                                                                        searchable: true
                                                                    },
                                                                    {
                                                                        data: 'description',
                                                                        name: 'description',

                                                                    },
                                                                    {
                                                                        data: 'name',
                                                                        name: 'name',
                                                                        searchable: true

                                                                    },

                                                                    {
                                                                        data: 'candidates',
                                                                        name: 'candidates',

                                                                    },

                                                                    {
                                                                        data: 'approvals',
                                                                        name: 'approvals',

                                                                    },
                                                                    {
                                                                        data: 'actions',
                                                                        name: 'actions',
                                                                    },

                                                                ]

                                                            });
                                                            $("#sponsor-datatable").css("width", "99%");
                                                        });
                                                    </script>
                                                @endpush

                                            </table>
                                        </div>
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
    </div>
    <!--END APPROVE SPONSOR  MODAL -->

    <!--PAYMENT MODAL -->
    <div class="modal fade bd-example-modal-lg" id="view-history-modal" tabindex="-1" role="dialog"
        aria-labelledby="LargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h5 class="modal-title">Add Fee Details</h5>
                </div>
                <div class="custom-tabs-line tabs-line-bottom text-center">
                    <ul class="nav" role="tablist">
                        <li class="active">
                            <a href="#payment-history-tab" role="tab" data-toggle="tab">
                                Payment Histories</a>
                        </li>

                        <li>
                            <a href="#offline-tab" role="tab" data-toggle="tab">Collect
                                Payment</a>
                        </li>

                    </ul>
                </div>
                <div class="tab-content">
                    <div class="tab-pane fade in active" id="payment-history-tab">
                        <table class="table" name="tablename" id="payment-history">
                            <thead>
                                <tr>
                                    <th>Collect by</th>
                                    <th>Amount</th>
                                    <th>Fine</th>
                                    <th>Created at</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                        </table>
                    </div>

                    <div class="tab-pane " id="offline-tab">
                        <fieldset class="fieldset-border">
                            <legend class="fieldset-border">Sponsor Payment</legend>
                            <form id="add-payment-form" method="POST"
                                action="{{ route('admin.sponsors.storeSponsorCollection') }}" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <input type="hidden" name="financial_year" value=" " class="form-control"
                                        id="financial_year">
                                    <input type="hidden" name="level" value="" class="form-control" id="level">
                                    <input type="hidden" name="session" value="" class="form-control" id="session">
                                    <input type="hidden" name="sponsor" value="" class="form-control"
                                        id="sponsor">
                                    <div class="form-group  col-sm-6">
                                        <label class="control-label" for="reference_no">Reference</label>
                                        <input type="text" name="reference_no" class="form-control" id="reference_no"
                                            readonly>
                                    </div>
                                    <div class="form-group  col-sm-6">
                                        <label class="control-label" for="amount">Amount To
                                            Pay</label>
                                        <input type="text" name="amount" class="form-control" id="amount">
                                    </div>
                                    <div class="form-group col-sm-6">
                                        <label for="pay_via" class="col-form-label">Paid
                                            via</label>
                                        <select class="form-control" name="pay_via" id="pay_via">
                                            <option value=""> select</option>
                                            @foreach ($feepaymentmethods as $feepaymentmethod)
                                                <option value="{{ $feepaymentmethod->id }}">
                                                    {{ $feepaymentmethod->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group  col-sm-6">
                                        <label class="control-label" for="totalamount">Total
                                            Amount</label>
                                        <input type="text" name="totalamount" class="form-control" id="totalamount"
                                            readonly>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="form-group  col-sm-6">
                                        <label class="control-label" for="fine">Fine</label>
                                        <input type="text" name="fine" class="form-control" id="fine">
                                    </div>
                                    <div class="form-group col-sm-6">
                                        <label for="status" class="col-sm-12 col-form-label">Status</label>
                                        <select class="form-control" name="status" id="status">
                                            <option value=""> select</option>
                                            <option value="1">Approve</option>
                                            <option value="0">Decline</option>
                                        </select>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="form-group col-sm-12">
                                        <label for="formFileLg" class="form-label">Proof Of
                                            Payment</label>
                                        <input type="file" name="attachment" class="form-control" id="attachment">
                                    </div>
                                    <div class="form-group  col-sm-12">
                                        <label class="control-label" for="remarks">Remarks</label>
                                        <textarea name="remarks" class="form-control" id="remarks"></textarea>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                            </form>
                            <div class="modal-footer">
                                <button type="button" id="add-payment" class="btn btn-primary">Add Payment</button>
                            </div>
                        </fieldset>
                    </div>

                    <div class="modal-footer align-center">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">x</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END PAYMENT MODAL -->

    <!-- ADD  SPONSOR  MODAL -->
    <div class="modal fade bd-modal-md" id="add-sponsor-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog ">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close resetform" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h3 class="modal-title">Add Sponsor </h3>
                </div>
                <div class="modal-body">
                    <form action="{{ route('admin.sponsors.store') }}" method="post" id="addSponsorForm">
                        <div>
                            @csrf
                        </div>
                        <div class="form-group col-md-12">
                            <label class="control-label" for="sponsor">Sponsor</label>
                            <input type="text" name="sponsor" value="" class="form-control" id="sponsor">
                        </div>
                        <div class="form-group col-md-12">
                            <label class="control-label" for="name">Name</label>
                            <input type="text" name="name" value="" class="form-control" id="name">
                        </div>
                        <div class="form-group col-md-12">
                            <label class="control-label" for="description">Description</label>
                            <input type="text" name="description" value="" class="form-control"
                                id="description">
                        </div>
                        <div class="form-group col-md-12">
                            <label class="control-label" for="status">Status</label>
                            <div>
                                <select class='form-control' name='status' id="status">
                                    <option value=''>Select status</option>
                                    <option value='1'>Active</option>
                                    <option value='0'>Inactive</option>
                                </select>
                            </div>
                        </div>
                    </form>
                    <div class="clearfix"></div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="save-sponsor">Save</button>
                    <button type="button" class="btn btn-danger resetform" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>

    </div>
    <!--END ADD  SPONSOR  MODAL -->

    <!--  UPDATE   MODAL -->
    <div class="modal fade bd-modal-md" id="edit-sponsor-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog ">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close resetform" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h3 class="modal-title">Update Sponsor </h3>
                </div>
                <div class="modal-body">
                    <form action="" method="post" id="editSponsorForm">
                        <div>
                            @csrf
                            @method('PUT')
                        </div>

                        <div class="form-group col-md-12">
                            <label class="control-label" for="sponsor">Sponsor</label>
                            <input type="text" name="sponsor" value="" class="form-control" id="sponsor"
                                readonly>
                        </div>
                        <div class="form-group col-md-12">
                            <label class="control-label" for="name">Name</label>
                            <input type="text" name="name" value="" class="form-control" id="name">
                        </div>
                        <div class="form-group col-md-12">
                            <label class="control-label" for="description">Description</label>
                            <input type="text" name="description" value="" class="form-control"
                                id="description">
                        </div>
                        <div class="form-group col-md-12">
                            <label class="control-label" for="status">Status</label>
                            <div>
                                <select class='form-control' name='status' id="status">
                                    <option value=''>Select status</option>
                                    <option value='1'>Active</option>
                                    <option value='0'>Inactive</option>
                                </select>
                            </div>
                        </div>
                    </form>
                    <div class="clearfix"></div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="update-sponsor">Update</button>
                    <button type="button" class="btn btn-danger resetform" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!--  APPROVE   MODAL -->
    <div class="modal fade bd-modal-md" id="approve-sponsor-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog ">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close resetform" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h3 class="modal-title">Approve Sponsor </h3>
                </div>
                <div class="modal-body">
                    <form action="" method="post" id="approveSponsorForm">
                        <div>
                            @csrf
                            @method('PUT')
                        </div>
                        <div class="form-group col-md-12">
                            <label for="status" class=" col-form-label">Status</label>
                            <select class="form-control" name="status" id="status">
                                <option value=""> select</option>
                                <option value="1">Approve</option>
                                <option value="0">Decline</option>
                            </select>
                        </div>

                        <div class="form-group  col-md-12">
                            <label class="control-label" for="amount">Amount To
                                Pay</label>
                            <input type="text" name="amount" class="form-control" id="amount">
                        </div>
                        <div class="form-group  col-sm-12">
                            <label class="control-label" for="remarks">Remarks</label>
                            <textarea name="remarks" class="form-control" id="remarks"></textarea>
                        </div>


                    </form>
                    <div class="clearfix"></div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="update-collection">Update</button>
                    <button type="button" class="btn btn-danger resetform" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>

    </div>
    <!--END APPROVE SPONSOR  MODAL -->








    <!-- END MAIN CONTENT -->

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


        /**** ADD SPONSOR*******/
        $(document).on('click', '#save-sponsor', function(ev) {
            ev.preventDefault();
            var url = $('#addSponsorForm').attr('action');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            var $btn = $(this);
            // Disable the button
            $btn.prop('disabled', true);
            // Add loading text (optional)
            $btn.html('<i class="fa fa-spinner fa-spin"></i> Processing...');
            var inputData = $("#addSponsorForm").serialize();
            $.ajax({
                url: url,
                method: "POST",
                data: inputData,
                success: function(data) {
                    if ($.isEmptyObject(data.errors)) {
                        $('#add-sponsor-modal').modal('hide');
                        $('#addSponsorForm .help-block').remove();
                        $('#addSponsorForm .has-error').removeClass('has-error');
                        toastr.success(data.success);
                        $('#funders-datatable').DataTable().ajax.reload();
                    } else {
                        printErrorMsg('#addSponsorForm', data.errors);
                    }


                },
                complete: function(data) {
                    // Re-enable button and restore original text when request is complete
                    $btn.prop('disabled', false);
                    $btn.text('Save');
                },
                error: function(xhr, error, code) {
                    console.log(xhr, code);
                }
            });


        });
        /**** ADD SPONSOR END*******/

        //**** EDIT SPONSOR*******/
        $(document).on('click', '.edit-sponsor', function(ev) {
            ev.preventDefault()
            var url = $(this).data("url");
            $.ajax({
                type: "GET",
                url: url,
                success: function(data) {

                    $('#edit-sponsor-modal').modal('show');
                    //Refresh table

                    var sponsor = data.sponsor;
                    var url = data.url;

                    var form = '#editSponsorForm';

                    $(`${form} input, ${form} select ,${form} textarea`).each(
                        function(index) {

                            var input = $(this);
                            console.log('Type: ' + input.attr('type') + 'Name: ' + input
                                .attr(
                                    'name') +
                                'Value: ' + input.val());
                            var name = input.attr('name');
                            $(`${form} #${name}`).val(sponsor[name]);

                        }
                    );
                    $(form).attr('action', url);
                },
                error: function(data) {
                    console.log('Error:', data);
                }
            });
        });
        //**** EDIT SPONSOR END*******/



        //**** UPDATE SPONSOR*******/
        $(document).on('click', '#update-sponsor', function(ev) {
            ev.preventDefault()

            var editForm = $("#editSponsorForm");
            var url = editForm.attr('action');
            $.ajax({
                url: url,
                type: "POST",
                data: editForm.serializeArray(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(data) {
                    console.log(data);
                    if ($.isEmptyObject(data.errors)) {
                        $('#funders-datatable').DataTable().ajax.reload();
                        $('#edit-sponsor-modal').modal('hide');
                        toastr.success(data.success);
                    } else {
                        printErrorMsg("#editSponsorForm", data.errors);
                    }


                }
            });
        });
        //**** UPDATE SPONSOR END*******/

        //**** DELETE SPONSOR *******/
        $(document).on('click', '.delete-sponsor', function(ev) {
            ev.preventDefault()
            var url = $(this).data("url");


            if (confirm('Are you sure you want to delete this records?')) {
                $.ajax({
                    type: "DELETE",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: url,
                    success: function(data) {
                        if ($.isEmptyObject(data.error)) {
                            toastr.success(data.success);
                            $('#funders-datatable').DataTable().ajax.reload();
                        } else {
                            toastr.error(data.error);
                        }
                    },
                    error: function(data) {
                        console.log('Error:', data);
                    }
                });
            } else {
                return
            }

        });
        //**** DELETE SPONSOR END*******/


        /**** FILTER BY YEAR*******/
        $("#year").on("change", function(event) {
            $('#sponsor-datatable').DataTable().ajax.reload();
        });
        /**** FILTER BY YEAR END*******/

        /**** SHOW MODAL PAYMENT*******/
        $(document).on('click', '.collected-fee', function() {
            var url = $(this).data("url");
            var $btn = $(this);
            // Disable the button
            $btn.prop('disabled', true);
            // Add loading text (optional)
            $btn.html('<i class="fa fa-spinner fa-spin"></i> Loading...');
            $.ajax({
                type: "GET",
                url: url,
                success: function(data) {
                    var sponsor = data.sponsor;
                    var url = data.url;
                    $('#totalamount').val(sponsor.grand_total);
                    $('#session').val(sponsor.session);
                    $('#level').val(sponsor.level);
                    $('#financial_year').val(sponsor.financial_year);
                    $('#reference_no').val(sponsor.reference_no);
                    $('#sponsor').val(sponsor.sponsor);
                    var payment_histories = $('#payment-history').DataTable({
                        processing: true,
                        serverSide: true,
                        deferRender: true,
                        destroy: true,
                        "lengthMenu": [
                            [20, 50, 100, 200, 400, -1],
                            [20, 50, 100, 200, 400, "All"]
                        ],
                        ajax: {
                            url: "{{ route('admin.sponsors.getSponsorAllCollection') }}",
                            data: function(d) {
                                d.payment_history = 'payment_history';
                                d.level = sponsor.level;
                                d.financial_year = sponsor.financial_year;
                                d.session = sponsor.session;
                                d.sponsor = sponsor.sponsor;
                            },

                        },
                        columns: [
                            //feeGroup
                            {
                                data: 'collect_by',
                                name: 'funder_payment_histories.collect_by',
                            },


                            {
                                data: 'amount',
                                name: 'funder_payment_histories.amount',
                                searchable: true
                            },
                            {
                                data: 'fine',
                                name: 'funder_payment_histories.fine',
                                searchable: true
                            },
                            {
                                data: 'created_at',
                                name: 'funder_payment_histories.created_at',
                                searchable: true
                            },


                            {
                                data: 'actions',
                                name: 'actions',
                                searchable: false
                            },





                        ]
                    });
                    $("#payment-history").css("width", "100%");
                    $('#view-history-modal').modal('show');
                },
                complete: function(data) {
                    // Re-enable button and restore original text when request is complete
                    $btn.prop('disabled', false);
                    $btn.text('collect');
                },
                error: function(xhr, error, code) {
                    console.log(xhr, code);
                }

            });
        });
        /**** SHOW MODAL PAYMENT END*******/

        /**** SAVE PAYMENT*******/
        $(document).on('click', '#add-payment', function(ev) {
            ev.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var url = $('#add-payment-form').attr('action');
            var $btn = $(this);
            // Disable the button
            $btn.prop('disabled', true);
            // Add loading text (optional)
            $btn.html('<i class="fa fa-spinner fa-spin"></i> Processing...');

            //File data
            var formData = new FormData($('#add-payment-form')[0]);

            $.ajax({
                url: url,
                method: "POST",
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
                success: function(data) {
                    console.log(data);
                    if ($.isEmptyObject(data.errors)) {
                        $('#view-history-modal').modal('hide');
                        toastr.success(data.success);
                        $('#candidates-history').DataTable().ajax.reload();
                    } else {
                        printErrorMsg('#add-payment-form', data.errors);
                    }
                },
                error: function(error) {
                    console.log(error);
                },
                complete: function(data) {
                    // Re-enable button and restore original text when request is complete
                    $btn.prop('disabled', false);
                    $btn.text('Submit');
                }
            });

        });
        /**** SAVE PAYMENT END*******/


        $(document).on('click', '.edit-collection', function() {
            var url = $(this).data("url");

            // Disable the button
            $.ajax({
                type: "GET",
                url: url,
                success: function(data) {
                    var funderPaymentHistory = data.funderPaymentHistory;
                    var url = data.url;

                    var form = '#approveSponsorForm';

                    $(`${form} input, ${form} select ,${form} textarea`).each(
                        function(index) {

                            var input = $(this);
                            console.log('Type: ' + input.attr('type') + 'Name: ' + input
                                .attr(
                                    'name') +
                                'Value: ' + input.val());
                            var name = input.attr('name');
                            $(`${form} #${name}`).val(funderPaymentHistory[name]);

                        }
                    );
                    $(form).attr('action', url);
                    $('#approve-sponsor-modal').modal('show');
                },
                complete: function(data) {
                    // Re-enable button and restore original text when request is complete

                },
                error: function(xhr, error, code) {
                    console.log(xhr, code);
                }

            });
        });

        /**** UPDATE PAYMENT*******/
        $(document).on('click', '#update-collection', function(ev) {
            ev.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var url = $('#approveSponsorForm').attr('action');
            var $btn = $(this);
            // Disable the button
            $btn.prop('disabled', true);
            // Add loading text (optional)
            $btn.html('<i class="fa fa-spinner fa-spin"></i> Processing...');

            var inputData = $("#approveSponsorForm").serialize();


            $.ajax({
                url: url,
                method: "POST",
                data: inputData,
                success: function(data) {
                    console.log(data);
                    if ($.isEmptyObject(data.errors)) {
                        toastr.success(data.success);
                        $('#payment-history').DataTable().ajax.reload();
                        $('#approve-sponsor-modal').modal('hide');
                    } else {
                        printErrorMsg('#approveSponsorForm', data.errors);
                    }
                },
                error: function(error) {
                    console.log(error);
                },
                complete: function(data) {
                    // Re-enable button and restore original text when request is complete
                    $btn.prop('disabled', false);
                    $btn.text('Update');
                }
            });

        });
        /**** UPDATE PAYMENT END*******/


        /****  Print errors*******/
        function printErrorMsg(parent, msg) {
            $(`${parent} input, ${parent} select, ${parent} textarea`).each(function(index) {
                $(`${parent} .help-block`).remove();
                $(`${parent} .has-error`).removeClass('has-error');
                // console.log(input.attr('type') + 'Name: ' + input.attr('name') + '  Value: ' + input.val());
            });
            $.each(msg, function(key, errors) {
                for (const error in errors) {
                    const value = errors[error];
                    $(`${parent} [name='${key}']`).parent().addClass('has-error');
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
