@extends('layouts.admin')

@section('content')
    <!-- ============================================================== -->
    <!-- Start Page Content -->
    <!-- ============================================================== -->
    <div class="main">
        <!-- MAIN CONTENT -->
        <div class="main-content">
            <div class="container-fluid">
                <h3 class="page-title">Payment History</h3>
                <div class="row">
                    <div class="col-md-12">
                        <!-- PANEL NO CONTROLS -->
                        <div class="panel">
                            <div class="panel-body">
                                <fieldset>
                                    <legend>Filter History</legend>
                                    <div class="row">
                                        <div class="form-group col-md-8">
                                            <div class="filter-wrapper">
                                                <input type="checkbox" name="payment_method[]" class="filter-checkbox"
                                                    value="LITE" />
                                                LITE
                                                <input type="checkbox" name="payment_method[]" class="filter-checkbox"
                                                    value="MPESA" />
                                                M-pesa
                                                <input type="checkbox" name="payment_method[]" class="filter-checkbox"
                                                    value="BANK-SLIP" />
                                                Bank slip
                                            </div>

                                        </div>
                                        <div class="col-md-4">
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

                                    </div>
                                </fieldset>

                            </div>
                            <!-- END PANEL NO CONTROLS -->
                        </div>

                    </div>
                    <div class="panel">
                        <div class="panel-body">
                            <div class="custom-tabs-line tabs-line-bottom left-aligned">
                                <ul class="nav" role="tablist">
                                    <li class="active"><a href="#invoice" role="tab" data-toggle="tab">Invoices</a>
                                    </li>
                                    <li><a href="#payments" role="tab" data-toggle="tab">Payments</a></li>
                                </ul>
                            </div>
                            <div class="tab-content">
                                <div class="tab-pane fade in active" id="invoice">


                                    <table class="table" name="tablename" id="invoices-datatable">
                                        <thead>
                                            <tr>
                                                <th>Invoice ID</th>
                                                <th>Client ID</th>
                                                <th>First Name</th>
                                                <th>Last Name</th>
                                                <th>Reference NO</th>
                                                <th>Amount</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>

                                    </table>
                                    @push('scripts')
                                        <script>
                                            $(function() {
                                                var table_school_fee = $('#invoices-datatable').DataTable({
                                                    processing: true,
                                                    serverSide: true,
                                                    deferRender: true,
                                                    responsive: true,
                                                    "lengthMenu": [
                                                        [20, 50, 100, 200, 400, -1],
                                                        [20, 50, 100, 200, 400, "All"]
                                                    ],
                                                    dom: 'lBfrtip',
                                                    buttons: [{
                                                            extend: 'copyHtml5',
                                                            text: '<i class="far fa-copy"></i>',
                                                            titleAttr: 'Copy',
                                                            className: 'buttons-pdf'
                                                        },
                                                        {
                                                            extend: 'excelHtml5',
                                                            text: '<i class="far fa-file-excel"></i>',
                                                            titleAttr: 'Excel',
                                                            className: 'buttons-pdf'
                                                        },
                                                        {
                                                            extend: 'csvHtml5',
                                                            text: '<i class="fas fa-file-excel"></i>',
                                                            titleAttr: 'CSV',
                                                            className: 'buttons-pdf'
                                                        },
                                                        {
                                                            extend: 'pdfHtml5',
                                                            text: '<i class="far fa-file-pdf"></i>',
                                                            titleAttr: 'PDF',
                                                            className: 'buttons-pdf'
                                                        }
                                                    ],


                                                    ajax: {
                                                        url: "{{ route('admin.payment-history.index') }}",
                                                        data: function(d) {
                                                            d.year = $("#year").val()
                                                            d.payment_method = $('.filter-checkbox:checked').val();
                                                        }
                                                    },
                                                    columns: [{
                                                            data: 'id',
                                                            name: 'id',

                                                        },
                                                        {
                                                            data: 'client_id',
                                                            name: 'client_id',


                                                        },
                                                        {
                                                            data: 'candidate_other_name',
                                                            name: 'candidate_other_name',


                                                        },
                                                        {
                                                            data: 'candidate_surname',
                                                            name: 'candidate_surname',


                                                        },
                                                        {
                                                            data: 'reference_no',
                                                            name: 'reference_no',
                                                        },
                                                        {
                                                            data: 'amount',
                                                            name: 'amount',
                                                        },
                                                        {
                                                            data: 'created_at',
                                                            name: 'created_at',
                                                        }

                                                    ]

                                                });


                                                $("#invoices-datatable").css("width", "98.5%");


                                                $('.filter-checkbox').on('change', function(e) {
                                                    var paymeny_methods = []
                                                    $.each($('.filter-checkbox'), function(i, elem) {
                                                        if ($(elem).prop('checked')) {
                                                            paymeny_methods.push($(this).val())
                                                        }
                                                    })
                                                    table_school_fee.column(4).search(paymeny_methods.join('|'), true, false, true).draw();
                                                });


                                                $("#year").on("change", function(event) {
                                                    $('#invoices-datatable').DataTable().ajax.reload();
                                                });

                                            });
                                        </script>
                                    @endpush
                                </div>
                                <div class="tab-pane fade" id="payments">
                                    <table class="table" name="tablename" id="payments-datatable">
                                        <thead>
                                            <tr>
                                                <th>Payment ID</th>
                                                <th>Invoice ID</th>
                                                <th>Reference NO</th>
                                                <th>Amount</th>
                                                <th>Payment Date</th>
                                            </tr>
                                        </thead>

                                    </table>
                                    @push('scripts')
                                        <script>
                                            $(function() {
                                                var table_school_fee = $('#payments-datatable').DataTable({
                                                    processing: true,
                                                    serverSide: true,
                                                    deferRender: true,
                                                    responsive: true,
                                                    "lengthMenu": [
                                                        [20, 50, 100, 200, 400, -1],
                                                        [20, 50, 100, 200, 400, "All"]
                                                    ],
                                                    dom: 'lBfrtip',
                                                    buttons: [{
                                                            extend: 'copyHtml5',
                                                            text: '<i class="far fa-copy"></i>',
                                                            titleAttr: 'Copy'
                                                        },
                                                        {
                                                            extend: 'excelHtml5',
                                                            text: '<i class="far fa-file-excel"></i>',
                                                            titleAttr: 'Excel'
                                                        },
                                                        {
                                                            extend: 'csvHtml5',
                                                            text: '<i class="fas fa-file-excel"></i>',
                                                            titleAttr: 'CSV'
                                                        },
                                                        {
                                                            extend: 'pdfHtml5',
                                                            text: '<i class="far fa-file-pdf"></i>',
                                                            titleAttr: 'PDF'
                                                        }
                                                    ],
                                                    ajax: "{{ route('admin.payment-history.payments') }}",
                                                    columns: [{
                                                            data: 'id',
                                                            name: 'id',

                                                        },
                                                        {
                                                            data: 'invoice_id',
                                                            name: 'invoice_id',
                                                        },
                                                        {
                                                            data: 'reference_no',
                                                            name: 'reference_no',
                                                        },
                                                        {
                                                            data: 'amount',
                                                            name: 'amount',
                                                        },
                                                        {
                                                            data: 'created_at',
                                                            name: 'created_at',
                                                        }

                                                    ]

                                                });

                                                $("#payments-datatable").css("width", "99.5%");

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
    </div>
    <!-- END MAIN CONTENT -->
    </div>
    <!-- ============================================================== -->
    <!-- End PAge Content -->
    <!-- ============================================================== -->
@endsection
