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
                                                    value="BANK" />
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
                                    <li class="active"><a href="#invoice" role="tab" data-toggle="tab">Candidates
                                            Payments</a>
                                    </li>
                                    <li><a href="#payments" role="tab" data-toggle="tab">Services Payments</a></li>
                                </ul>
                            </div>
                            <div class="tab-content">
                                <div class="tab-pane fade in active" id="invoice">
                                    <table class="table" name="tablename" id="invoices-datatable">
                                        <thead>
                                            <tr>
                                                <th>Centre Number</th>
                                                <th>Candidate No</th>
                                                <th>First Name</th>
                                                <th>Last Name</th>
                                                <th>Fee Group</th>
                                                <th>Pay via</th>
                                                <th>Reference No</th>
                                                <th>Fine</th>
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
                                                            data: 'center_no',
                                                            name: 'center_candidate.center_no',
                                                            searchable: true,


                                                        },
                                                        {
                                                            data: 'candidate_no',
                                                            name: 'center_candidate.candidate_no',
                                                            searchable: true,

                                                        },
                                                        {
                                                            data: 'candidate_other_name',
                                                            name: 'candidates.candidate_other_name',
                                                            searchable: true,
                                                        },
                                                        {
                                                            data: 'candidate_surname',
                                                            name: 'candidates.candidate_surname',
                                                            searchable: true,
                                                        },
                                                        {
                                                            data: 'fee_group',
                                                            name: 'fee_groups.name',
                                                            searchable: false,

                                                        },

                                                        {
                                                            data: 'name',
                                                            name: 'fee_payment_method.name',
                                                            searchable: false,
                                                        },

                                                        {
                                                            data: 'reference_no',
                                                            name: 'fee_candidate_histories.reference_no',
                                                        },

                                                        {
                                                            data: 'fine',
                                                            name: 'fee_candidate_histories.fine',
                                                        },
                                                        {
                                                            data: 'amount',
                                                            name: 'fee_candidate_histories.amount',
                                                        },

                                                        {
                                                            data: 'created_at',
                                                            name: 'fee_candidate_histories.created_at',
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
                                                    table_school_fee.column(6).search(paymeny_methods.join('|'), true, false, true).draw();
                                                });


                                                $("#year").on("change", function(event) {
                                                    $('#invoices-datatable').DataTable().ajax.reload();
                                                    $("#payments-datatable").DataTable().ajax.reload();

                                                });

                                            });
                                        </script>
                                    @endpush
                                </div>
                                <div class="tab-pane fade" id="payments">
                                    <table class="table" name="tablename" id="payments-datatable">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>First Name</th>
                                                <th>Last name</th>
                                                <th>Pay via</th>
                                                <th>Reference No</th>
                                                <th>Fine</th>
                                                <th>Amount</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>

                                    </table>
                                    @push('scripts')
                                        <script>
                                            $(function() {
                                                var services_payments = $('#payments-datatable').DataTable({
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

                                                    ajax: {
                                                        url: "{{ route('admin.payment-history.payments') }}",
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
                                                            data: 'first_name',
                                                            name: 'first_name',
                                                        },
                                                        {
                                                            data: 'last_name',
                                                            name: 'last_name',
                                                        },
                                                        {
                                                            data: 'payment_method',
                                                            name: 'payment_method',
                                                        },
                                                        {
                                                            data: 'reference_no',
                                                            name: 'reference_no',
                                                        },
                                                        {
                                                            data: 'fine',
                                                            name: 'fine',
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
                                                $('.filter-checkbox').on('change', function(e) {
                                                    var paymeny_methods = []
                                                    $.each($('.filter-checkbox'), function(i, elem) {
                                                        if ($(elem).prop('checked')) {
                                                            paymeny_methods.push($(this).val())
                                                        }
                                                    })
                                                    services_payments.column(4).search(paymeny_methods.join('|'), true, false, true).draw();
                                                });

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
