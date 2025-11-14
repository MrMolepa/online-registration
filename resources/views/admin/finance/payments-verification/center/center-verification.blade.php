@extends('layouts.admin')

@section('content')
    <!-- ============================================================== -->
    <!-- Start Page Content -->
    <!-- ============================================================== -->
    <div class="main">
        <!-- MAIN CONTENT -->
        <div class="main-content">
            <div class="container-fluid">
                <h3 class="page-title">Payments Verification({{ $center->center_no }})</h3>
                <div class="row">
                    <div class="col-md-9">
                        <!-- PANEL NO CONTROLS -->
                        <div class="panel">
                            <div class="panel-heading">
                                <h3 class="panel-title"> Payments Verification({{ $center->center_no }}
                                    {{ $center->center_name }})</h3>
                            </div>

                            <div class="panel-body">
                                <fieldset>
                                    <legend>Filter confirmation ({{ $center->center_name }})</legend>
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
                                    <div class="pull-left  col-md-4">
                                        <div class="input-group">
                                            <span class="input-group-btn">
                                                <button class="btn secondary" type="button">Session</button>
                                            </span>
                                            <select class="form-control status-dropdown" id="session">
                                                @foreach ($sessions as $session)
                                                    <option value="{{ $session }}">
                                                        {{ $session }}</option>
                                                @endforeach
                                            </select>

                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                </fieldset>
                                <div class="custom-tabs-line tabs-line-bottom left-aligned">
                                    <ul class="nav" role="tablist">
                                        <li class="active"><a href="#payemnts" role="tab" data-toggle="tab">
                                                Payments
                                            </a></li>
                                        <li><a href="#other-chargers" role="tab" data-toggle="tab">Other Charges</a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="tab-content">
                                    <div class="tab-pane fade in active" id="payemnts">
                                        <div class="pull-right">
                                            <a href="" class="btn btn-info" data-toggle="modal"
                                                data-target="#add-payment-modal">+ Create
                                            </a>
                                        </div>
                                        <div class="clearfix"></div>
                                        <div class="table-responsive">
                                            <table class="table display compact" id="payment-datatable">
                                                <thead>
                                                    <tr>
                                                        <th>Upload Date</th>
                                                        <th>Center No</th>
                                                        <th>Attachment</th>
                                                        <th>Amount</th>
                                                        <th>Collected By</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>

                                            </table>
                                            @push('scripts')
                                                <script>
                                                    toastr.options = {
                                                        "closeButton": true,
                                                        "debug": false,
                                                        "newestOnTop": false,
                                                        "progressBar": true,
                                                        "positionClass": "toast-top-right",
                                                        "preventDuplicates": false,
                                                        "onclick": null,
                                                        "showDuration": "300",
                                                        "hideDuration": "1000",
                                                        "timeOut": "5000",
                                                        "extendedTimeOut": "1000",
                                                        "showEasing": "swing",
                                                        "hideEasing": "linear",
                                                        "showMethod": "fadeIn",
                                                        "hideMethod": "fadeOut"
                                                    }
                                                    $(function() {
                                                        var center = $('#payment-datatable').DataTable({
                                                            processing: true,
                                                            serverSide: true,
                                                            deferRender: true,
                                                            "lengthMenu": [
                                                                [20, 50, 100, 200, 400, -1],
                                                                [20, 50, 100, 200, 400, "All"]
                                                            ],

                                                            ajax: {
                                                                url: "{{ route('admin.centre-collection.fees.center', $center->center_no) }}",
                                                                data: function(d) {
                                                                    d.year = $("#year").val()
                                                                }
                                                            },


                                                            columns: [{
                                                                    data: 'status',
                                                                    name: 'status',
                                                                    searchable: true
                                                                },
                                                                {
                                                                    data: 'center_no',
                                                                    name: 'center_no',
                                                                    searchable: true
                                                                },

                                                                {
                                                                    data: 'attachment',
                                                                    name: 'attachment',
                                                                    orderable: false,
                                                                    searchable: false
                                                                },
                                                                {
                                                                    data: 'amount',
                                                                    name: 'amount',
                                                                    searchable: true

                                                                },
                                                                {
                                                                    data: 'collect_by',
                                                                    name: 'collect_by',
                                                                    searchable: true

                                                                },
                                                                {
                                                                    data: 'actions',
                                                                    name: 'actions',
                                                                    orderable: false,
                                                                    searchable: false
                                                                },




                                                            ]


                                                        });
                                                        $("#payment-datatable").css("width", "99.5%");
                                                        getCenterCharges();


                                                    });

                                                    function getCenterCharges() {
                                                        $.ajaxSetup({
                                                            headers: {
                                                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                                            }
                                                        });
                                                        $.ajax({
                                                            url: "{{ route('admin.centre-collection.fees.center', $center->center_no) }}",
                                                            method: "GET",
                                                            data: {
                                                                year: $("#year").val(),
                                                                invoices: 'total'
                                                            },
                                                            success: function(data) {
                                                                $('#center-charges').html(data.html);


                                                            }
                                                        });
                                                    }

                                                    $("#year").on("change", function(event) {
                                                        $('#payment-datatable').DataTable().ajax.reload();
                                                        $('#center-other-charges').DataTable().ajax.reload();
                                                        getCenterCharges();
                                                    });
                                                </script>
                                            @endpush
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="other-chargers">
                                        <div class="pull-right">
                                            <a href="" class="btn btn-info" data-toggle="modal"
                                                data-target="#add-charge-modal">+ Create
                                            </a>
                                        </div>
                                        <div class="clearfix"></div>
                                        <div class="table-responsive">
                                            <table class="table" name="tablename" id="center-other-charges">
                                                <thead>
                                                    <tr>
                                                        <th>Center No</th>
                                                        <th>Centre Name</th>
                                                        <th>Remarks</th>
                                                        <th>Amount</th>
                                                        <th>Collected By</th>
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

                                                            ajax: {
                                                                url: "{{ route('admin.centre-collection.center-charges.index', ['center_no' => $center->center_no]) }}",
                                                                data: function(d) {
                                                                    d.year = $("#year").val()

                                                                }
                                                            },
                                                            columns: [{
                                                                    data: 'center_no',
                                                                    name: 'center_no',
                                                                    searchable: true
                                                                },
                                                                {
                                                                    data: 'center.center_name',
                                                                    name: 'center.center_name',
                                                                    searchable: true
                                                                },
                                                                {
                                                                    data: 'remarks',
                                                                    name: 'remarks ',
                                                                    searchable: true
                                                                },
                                                                {
                                                                    data: 'amount',
                                                                    name: 'amount',
                                                                    searchable: true
                                                                },
                                                                {
                                                                    data: 'collected_by',
                                                                    name: 'collected_by',
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
                                                        $("#center-other-charges").css("width", "98.5%");



                                                    });
                                                </script>
                                            @endpush
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <!-- END PANEL NO CONTROLS -->
                        </div>


                    </div>
                    <div class="col-md-3">
                        <!-- PANEL NO CONTROLS -->
                        <div class="panel">
                            <div class="panel-heading">
                                <h3 class="panel-title"> Amounts </h3>
                            </div>
                            <div class="panel-body">
                                <div class="table-responsive" id="center-charges">




                                </div>
                            </div>
                            <!-- END PANEL NO CONTROLS -->
                        </div>


                    </div>

                </div>
            </div>
            <!-- END MAIN CONTENT -->
            <!--ADD PAYMENT MODEL -->
            <div class="modal fade bd-modal-md" id="add-payment-modal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog ">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close resetform" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h3 class="modal-title">Add payemnt</h3>
                        </div>
                        <div class="modal-body">
                            <form action="{{ route('admin.centre-collection.fees.store') }}" id="addPaymentForm"
                                method="post">
                                <div>
                                    @csrf
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="center_no">Center no</label>
                                    <input type="text" class="form-control" name="center_no"
                                        value="{{ $center->center_no }}" readonly id="center_no">

                                </div>


                                <div class="form-group  col-md-6">
                                    <label for="financial_year">Financial year </label>
                                    <select class="form-control status-dropdown" name="financial_year" id="financial_year">
                                        <option value="">Select Financial year</option>
                                        @foreach ($years as $year)
                                            <option
                                                value="{{ $year }}"@if ($year == date('Y')) selected @endif>
                                                {{ $year }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="session">Session </label>
                                    <select class="form-control status-dropdown" name="session" id="session">
                                        <option value="">Select Session</option>
                                        @foreach ($sessions as $session)
                                            <option value="{{ $session }}">
                                                {{ $session }}</option>
                                        @endforeach
                                    </select>
                                </div>


                                <div class="form-group  col-md-6">
                                    <label for="amount">Amount</label>
                                    <input type="text" value="" class="form-control" name="amount"
                                        id="amount">
                                </div>

                                <div class="form-group  col-md-6">
                                    <label for="attachment" class="form-label">Proof Of
                                        Payment</label>
                                    <input type="file" name="attachment" class="form-control" id="attachment">
                                </div>

                                <div class="form-group  col-md-6">
                                    <label for="reference_no" class="form-label">Reference number</label>
                                    <input type="text" name="reference_no" class="form-control" id="reference_no">
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="status">Session </label>
                                    <select class="form-control status-dropdown" name="status" id="status">
                                        <option value="">Select status</option>
                                        <option value="2">Approve</option>
                                        <option value="1">Decline</option>
                                    </select>
                                </div>


                                <div class="form-group col-md-12">
                                    <label for="remarks">Remarks</label>
                                    <textarea name="remarks" id="remarks" class="form-control" cols="30" rows="3"></textarea>
                                </div>
                                <div class="clearfix"></div>
                            </form>

                        </div>
                        <div class="modal-footer">
                            <button type="submit" name="save-payment" class="btn btn-primary"
                                id="save-payment">Save</button>
                            <button type="button" class="btn btn-danger resetform" id="close"
                                data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>

            </div>
            <!--END ADD PAYMENT  MODEL -->

            <!--UPDATE PAYMENT MODEL -->
            <div class="modal fade bd-modal-md" id="edit-payment-modal" tabindex="-1" role="dialog"
                aria-hidden="true">
                <div class="modal-dialog ">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close resetform" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h3 class="modal-title">Edit payemnt</h3>
                        </div>
                        <div class="modal-body">
                            <form action="{{ route('admin.centre-collection.fees.store') }}" id="editPaymentForm"
                                method="post">
                                <div>
                                    @csrf
                                    @method('PUT')
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="center_no">Center no</label>
                                    <input type="text" class="form-control" name="center_no"
                                        value="{{ $center->center_no }}" readonly id="center_no">

                                </div>


                                <div class="form-group  col-md-6">
                                    <label for="financial_year">Financial year </label>
                                    <select class="form-control status-dropdown" name="financial_year"
                                        id="financial_year">
                                        <option value="">Select Financial year</option>
                                        @foreach ($years as $year)
                                            <option
                                                value="{{ $year }}"@if ($year == date('Y')) selected @endif>
                                                {{ $year }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="session">Session </label>
                                    <select class="form-control status-dropdown" name="session" id="session">
                                        <option value="">Select Session</option>
                                        @foreach ($sessions as $session)
                                            <option value="{{ $session }}">
                                                {{ $session }}</option>
                                        @endforeach
                                    </select>
                                </div>


                                <div class="form-group  col-md-6">
                                    <label for="amount">Amount</label>
                                    <input type="text" value="" class="form-control" name="amount"
                                        id="amount">
                                </div>

                                <div class="form-group  col-md-6">
                                    <label for="attachment" class="form-label">Proof Of
                                        Payment</label>
                                    <input type="file" name="attachment" class="form-control" id="attachment">
                                </div>

                                <div class="form-group  col-md-6">
                                    <label for="reference_no" class="form-label">Reference number</label>
                                    <input type="text" name="reference_no" class="form-control" id="reference_no">
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="status">Session </label>
                                    <select class="form-control status-dropdown" name="status" id="status">
                                        <option value="">Select status</option>
                                        <option value="2">Approve</option>
                                        <option value="1">Decline</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="remarks">Remarks</label>
                                    <textarea name="remarks" id="remarks" class="form-control" cols="30" rows="3"></textarea>
                                </div>
                                <div class="clearfix"></div>
                            </form>

                        </div>
                        <div class="modal-footer">
                            <button type="submit" name="update-payment" class="btn btn-primary"
                                id="update-payment">Save</button>
                            <button type="button" class="btn btn-danger resetform" id="close"
                                data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>

            </div>
            <!--END UPDATE PAYMENT  MODEL -->


            <!--ADD Charge MODEL -->
            <div class="modal fade bd-modal-md" id="add-charge-modal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog ">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close resetform" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h3 class="modal-title">Other Charges</h3>
                        </div>
                        <div class="modal-body">
                            <form action="{{ route('admin.centre-collection.center-charges.store') }}" id="addChargeForm"
                                method="post">
                                <div>
                                    @csrf
                                </div>
                                <div class="form-group ">
                                    <label for="center_no">Center no</label>
                                    <input type="text" class="form-control" name="center_no"
                                        value="{{ $center->center_no }}" readonly id="center_no">

                                </div>


                                <div class="form-group ">
                                    <label for="financial_year">Financial year </label>
                                    <select class="form-control status-dropdown" name="financial_year"
                                        id="financial_year">
                                        <option value="">Select Financial year</option>
                                        @foreach ($years as $year)
                                            <option
                                                value="{{ $year }}"@if ($year == date('Y')) selected @endif>
                                                {{ $year }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group ">
                                    <label for="session">Session </label>
                                    <select class="form-control status-dropdown" name="session" id="session">
                                        <option value="">Select Session</option>
                                        @foreach ($sessions as $session)
                                            <option value="{{ $session }}">
                                                {{ $session }}</option>
                                        @endforeach
                                    </select>
                                </div>


                                <div class="form-group ">
                                    <label for="amount">Amount</label>
                                    <input type="text" value="" class="form-control" name="amount"
                                        id="amount">
                                </div>
                                <div class="form-group">
                                    <label for="remarks">Remarks</label>
                                    <textarea name="remarks" id="remarks" class="form-control" cols="30" rows="3"></textarea>
                                </div>
                            </form>

                        </div>
                        <div class="modal-footer">
                            <button type="submit" name="save-charge" class="btn btn-primary"
                                id="save-charge">Save</button>
                            <button type="button" class="btn btn-danger resetform" id="close"
                                data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>

            </div>
            <!--END ADD Charge MODEL -->
            <!--UPDATE Charge MODEL -->
            <div class="modal fade bd-modal-md" id="edit-charge-modal" tabindex="-1" role="dialog"
                aria-hidden="true">
                <div class="modal-dialog ">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close resetform" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h3 class="modal-title">Edit Other Charges</h3>
                        </div>
                        <div class="modal-body">
                            <form action="" id="editChargeForm" method="post">
                                <div>
                                    @csrf
                                </div>
                                <div class="form-group ">
                                    <label for="center_no">Center no</label>
                                    <input type="text" class="form-control" name="center_no"
                                        value="{{ $center->center_no }}" readonly id="center_no">

                                </div>


                                <div class="form-group ">
                                    <label for="financial_year">Financial year </label>
                                    <select class="form-control status-dropdown" name="financial_year"
                                        id="financial_year">
                                        <option value="">Select financial year</option>
                                        @foreach ($years as $year)
                                            <option
                                                value="{{ $year }}"@if ($year == date('Y')) selected @endif>
                                                {{ $year }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group ">
                                    <label for="session">Session </label>
                                    <select class="form-control status-dropdown" name="session" id="session">
                                        <option value="">Select Session</option>
                                        @foreach ($sessions as $session)
                                            <option value="{{ $session }}">
                                                {{ $session }}</option>
                                        @endforeach
                                    </select>
                                </div>


                                <div class="form-group ">
                                    <label for="amount">Amount</label>
                                    <input type="text" value="" class="form-control" name="amount"
                                        id="amount">
                                </div>
                                <div class="form-group">
                                    <label for="remarks">Remarks</label>
                                    <textarea name="remarks" id="remarks" class="form-control" cols="30" rows="3"></textarea>
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

        /****  PAYMENTS*******/
        //  Add Payment
        $(document).on('click', '#save-payment', function(ev) {
            ev.preventDefault();
            var url = $('#addPaymentForm').attr('action');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            //File data
            var formData = new FormData($('#addPaymentForm')[0]);
            // var caption = element.html();
            $.ajax({
                url: url,
                method: "POST",
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
                beforeSend: function() {
                    // element.prop('disabled', true).html("Processing.....");
                },
                success: function(data) {
                   
                    if ($.isEmptyObject(data.errors)) {
                        $('#add-payment-modal').modal('hide');
                        toastr.success(data.success);
                        $('#payment-datatable').DataTable().ajax.reload();
                    } else {
                        printErrorMsg('#addPaymentForm', data.errors);
                    }

                },
                error: function(error) {
                    console.log(error);
                    // element.prop('disabled', false).html(caption);
                },
                complete: function(data) {
                    // element.prop('disabled', false).html(caption);
                }
            });

        });

        // Edit Payment
        $(document).on('click', '#payment-datatable .edit-payment', function() {

            var url = $(this).data("url");
            $.ajax({
                type: "GET",
                url: url,
                success: function(data) {
                    $('#edit-payment-modal').modal('show');
                    var payment = data.payment;
                    console.log(payment)
                    var url = data.url;
                    var form = '#editPaymentForm';
                    $(`${form} input:not([type=file]), ${form} select, ${form} textarea`).each(
                        function(index) {
                            var input = $(this);

                            var name = input.attr('name');


                            if (input.attr('type') == "checkbox") {
                                $(`${form} #${name}`).attr("checked", charge[
                                    name] == 1 ? true : false);
                            } else {
                                $(`${form} #${name}`).val(payment[name]);
                            }
                            $(form).attr('action', url);

                        }
                    );
                },
                error: function(data) {
                    console.log('Error:', data);
                }
            });
        });

        // Update Charge
        $(document).on("click", "#update-payment", function() {
            //File data
            var formData = new FormData($('#editPaymentForm')[0]);

            var form = '#editPaymentForm';
            var url = $(form).attr('action');

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: url,
                method: "POST",
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
                beforeSend: function() {
                    // element.prop('disabled', true).html("Processing.....");
                },
                success: function(data) {
                    console.log(data);
                    if ($.isEmptyObject(data.errors)) {
                        $('#edit-payment-modal').modal('hide');
                        toastr.success(data.success);
                        $('#payment-datatable').DataTable().ajax.reload();
                    } else {
                        printErrorMsg(form, data.errors);
                    }

                },
            });
        });

        // Delete payment
        $(document).on('click', '#payment-datatable .delete-payment', function(ev) {
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
                            $('#payment-datatable').DataTable().ajax.reload();
                        }



                    }
                });


            } else {
                return;
            }

        });


        /**** END PAYMENTS*******/

        /****  OTHER CHARGE*******/
        //  Add Charge
        $(document).on('click', '#save-charge', function(ev) {
            ev.preventDefault();
            var url = $('#addChargeForm').attr('action');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: url,
                method: "POST",
                data: $("#addChargeForm").serialize(),
                success: function(data) {
                    if ($.isEmptyObject(data.errors)) {
                        $('#add-charge-modal').modal('hide');
                        $('#addChargeForm .help-block').remove();
                        $('#addChargeForm .has-error').removeClass('has-error');
                        toastr.success(data.success);
                        $('#center-other-charges').DataTable().ajax.reload();
                    } else {
                        printErrorMsg('#addChargeForm', data.errors);
                    }


                }
            });


        });
        // Edit charge
        $(document).on('click', '#center-other-charges .edit-charge', function() {

            var url = $(this).data("url");
            $.ajax({
                type: "GET",
                url: url,
                success: function(data) {
                    $('#edit-charge-modal').modal('show');
                    var charge = data.charge;
                    var url = data.url;
                    var form = '#editChargeForm';
                    $(`${form} input, ${form} select, ${form} textarea`).each(
                        function(index) {
                            var input = $(this);
                            console.log('Type: ' + input.attr('type') + 'Name: ' + input
                                .attr(
                                    'name') +
                                'Value: ' + input.val());
                            var name = input.attr('name');


                            if (input.attr('type') == "checkbox") {
                                $(`${form} #${name}`).attr("checked", charge[
                                    name] == 1 ? true : false);
                            } else {
                                $(`${form} #${name}`).val(charge[name]);
                            }
                            $(form).attr('action', url);

                        }
                    );
                },
                error: function(data) {
                    console.log('Error:', data);
                }
            });
        });
        // Update Charge
        $(document).on("click", "#update-charge", function() {

            var form = '#editChargeForm';
            var url = $(form).attr('action');
            console.log(url);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "PUT",
                url: url,
                data: $('#editChargeForm').serialize(),
                success: function(data) {
                    if ($.isEmptyObject(data.errors)) {
                        toastr.success("You have successfully Saved Changes");
                        $('#center-other-charges').DataTable().ajax.reload();
                        $('#edit-charge-modal').modal('hide');
                    } else {
                        printErrorMsg(form, data.errors);
                    }

                },
            });
        });
        // Delete Charge
        $(document).on('click', '#center-other-charges .delete-charge', function(ev) {
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
        /****  EDD OTHER CHARGE*******/







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
    </script>
@endsection
