@extends('layouts.admin')

@section('content')
    <!-- ============================================================== -->
    <!-- Start Page Content -->
    <!-- MAIN -->
    <div class="main">
        <!-- MAIN CONTENT -->
        <div class="main-content">
            <div class="container-fluid">
                <h3 class="page-title">Candidate Fee History</h3>
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel">
                            <div class="panel-heading">
                                <h3 class="panel-title">Filter</h3>
                            </div>

                            <div class="panel-body">
                                @if (session()->has('success'))
                                    <div class="alert alert-success alert-dismissible" role="alert">
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                                aria-hidden="true">&times;</span></button>
                                        <i class="fa fa-check-circle"></i> {{ session('success') }}
                                    </div>
                                    &nbsp;
                                @endif
                                <form action="" id="report-form" class="row" method="get">
                                    @csrf
                                    <div class="form-group  @error('year') has-error  @enderror col-md-4">
                                        <label for="year">Year</label>
                                        <select class="form-control  dropdown-selected" name="year" id="year">
                                            @foreach ($years as $year)
                                                <option
                                                    value="{{ $year }}"@if ($year == date('Y')) selected @endif>
                                                    {{ $year }}</option>
                                            @endforeach
                                        </select>
                                        @error('year')
                                            <span class="help-block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="level">Level</label>
                                        <select class="form-control dropdown-selected" name="level" id="level">
                                            <option value="">Please Select Level</option>

                                        </select>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="">Session</label>
                                        <select class="form-control dropdown-selected" name="session" id="session">
                                            <option value=""> Select Session</option>
                                        </select>
                                    </div>

                                    <div class="clearfix"></div>
                                    <div class="form-group col-md-6">
                                        <label for="">Center</label>
                                        <select class="form-control dropdown-selected" name="center" id="center">
                                            <option value=""> Select Center</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="">Sponsor</label>
                                        <select class="form-control" name="sponsor" id="sponsor">
                                            <option value=""> Select sponsor</option>

                                        </select>
                                    </div>




                                    <div class="clearfix"></div>

                                </form>

                                <table class="table" name="tablename" id="candidates-history">
                                    <thead>
                                        <tr>
                                            <th>Centre No</th>
                                            <th>Nationa Id</th>
                                            <th>Candidate No</th>
                                            <th>Candidate Surname</th>
                                            <th>Candidate Other Name</th>
                                            <th>Total Fee</th>
                                            <th>Paid Fee</th>
                                            <th>Action</th>

                                        </tr>
                                    </thead>
                                </table>
                                {{-- Modal collector --}}
                                <div class="modal fade bd-example-modal-lg" id="view-history-modal" tabindex="-1"
                                    role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLabel">Add Fee Details</h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="custom-tabs-line tabs-line-bottom text-center">
                                                <ul class="nav" role="tablist">
                                                    <li class="active">
                                                        <a href="#fee-invoice-tab" role="tab"
                                                            data-toggle="tab">Invoice</a>
                                                    </li>
                                                    <li>
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
                                                <div class="tab-pane fade in active" id="fee-invoice-tab">
                                                    <section class="main-pd-wrapper" style=" margin: 7px">
                                                        <div
                                                            style="

                                                                        margin: auto;
                                                                        line-height: 1.5;
                                                                        font-size: 14px;
                                                                        color: #4a4a4a;
                                                                    ">
                                                            <p
                                                                style="font-weight: bold; color: #000;  font-size: 18px; text-align: center;">
                                                                Examination Council of Lesotho
                                                            </p>
                                                            <p style="margin: 5px auto; text-align: center">
                                                                Government Office, 50 Constitution Rd,<br>
                                                                P.O. Box 507, Maseru 100
                                                            </p>
                                                            <hr>
                                                            <p class="candidate_no">
                                                                <b>Candidate No: </b> <span></span>
                                                            </p>
                                                            <p class="names">
                                                                <b>Student Names:</b> <span></span>
                                                            </p>
                                                            <p class="center_no">
                                                                <b>Center No: </b> <span></span>
                                                            </p>
                                                            <p class="total_subjects">
                                                                <b>Number Of Subject: </b> <span></span>
                                                            </p>
                                                            <p class="tatal_amount_paid">
                                                                <b>Amount Paid: </b> <span></span>
                                                            </p>
                                                            <p class="balance">
                                                                <b>Balance: </b> <span></span>
                                                            </p>


                                                            <hr
                                                                style="border: 1px dashed rgb(131, 131, 131); margin: 25px auto">
                                                        </div>

                                                        <div id="candidate-fee-details">

                                                        </div>


                                                        <table
                                                            style="width: 100%;
                                                                background: #f1f1f1;
                                                                border-radius: 4px;">
                                                            <thead>
                                                                <tr>
                                                                    <th>Sub-Total</th>
                                                                    <th style="text-align: center;"></th>
                                                                    <th>&nbsp;</th>
                                                                    <th style="text-align: right;" id="sub-total">
                                                                    </th>
                                                                </tr>
                                                            </thead>

                                                        </table>

                                                        <table
                                                            style="width: 100%;
                                                                    margin-top: 15px;
                                                                    border: 1px dashed #598abf;
                                                                    border-radius: 3px;">
                                                            <thead>
                                                                <tr>
                                                                    <td id="finename"></td>
                                                                    <td style="text-align: right;" id="finevalue">
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Total</td>
                                                                    <td style="text-align: right;" id="total">
                                                                    </td>
                                                                </tr>
                                                            </thead>

                                                        </table>
                                                    </section>
                                                </div>
                                                <div class="tab-pane" id="offline-tab">
                                                    <fieldset class="fieldset-border">
                                                        <legend class="fieldset-border">Add Exam Payment</legend>
                                                        <form id="add-payment-form" method="POST" action=""
                                                            enctype="multipart/form-data">
                                                            @csrf
                                                            <div class="row">
                                                                <input type="hidden" name="fee_group_id" value=" "
                                                                    class="form-control" id="fee_group_id">
                                                                <input type="hidden" name="candidate_id" value=" "
                                                                    class="form-control" id="candidate_id">


                                                                <div class="form-group  col-sm-6">
                                                                    <label class="control-label"
                                                                        for="reference_no">Reference</label>
                                                                    <input type="text" name="reference_no"
                                                                        class="form-control" id="reference_no">
                                                                </div>
                                                                <div class="form-group  col-sm-6">
                                                                    <label class="control-label" for="amount">Amount To
                                                                        Pay</label>
                                                                    <input type="text" name="amount"
                                                                        class="form-control" id="amount">
                                                                </div>
                                                                <div class="form-group col-sm-6">
                                                                    <label for="pay_via" class="col-form-label">Paid
                                                                        via</label>
                                                                    <select class="form-control" name="pay_via"
                                                                        id="pay_via">
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
                                                                    <input type="text" name="totalamount"
                                                                        class="form-control" id="totalamount" readonly>
                                                                </div>
                                                                <div class="clearfix"></div>
                                                                <div class="form-group  col-sm-6">
                                                                    <label class="control-label"
                                                                        for="fine">Fine</label>
                                                                    <input type="text" name="fine"
                                                                        class="form-control" id="fine">
                                                                </div>
                                                                <div class="form-group col-sm-6">
                                                                    <label for="status"
                                                                        class="col-sm-12 col-form-label">Status</label>
                                                                    <select class="form-control" name="status"
                                                                        id="status">
                                                                        <option value=""> select</option>
                                                                        <option value="1">Approve</option>
                                                                        <option value="0">Decline</option>
                                                                    </select>
                                                                </div>
                                                                <div class="clearfix"></div>
                                                                <div class="form-group col-sm-12">
                                                                    <label for="formFileLg" class="form-label">Proof Of
                                                                        Payment</label>
                                                                    <input type="file" name="attachment"
                                                                        class="form-control" id="attachment">
                                                                </div>
                                                                <div class="form-group  col-sm-12">
                                                                    <label class="control-label"
                                                                        for="remarks">Remarks</label>
                                                                    <textarea name="remarks" class="form-control" id="remarks"></textarea>
                                                                </div>
                                                                <div class="clearfix"></div>
                                                            </div>
                                                        </form>
                                                        <div class="modal-footer">
                                                            <button type="button" id="add-payment"
                                                                class="btn btn-primary">Add Payment</button>
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="tab-pane" id="payment-history-tab">
                                                    <table class="table" name="tablename" id="payment-history">
                                                        <thead>
                                                            <tr>
                                                                <th>Group Fee</th>
                                                                <th>Reference no</th>
                                                                <th>Amount</th>
                                                                <th>Fine</th>
                                                                <th>Created at</th>
                                                            </tr>
                                                        </thead>
                                                    </table>
                                                </div>
                                                <div class="modal-footer align-center">
                                                    <button type="button" class="btn btn-danger"
                                                        data-dismiss="modal">x</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @push('scripts')
                                    <script>
                                        $(function() {
                                            var candidates = $('#candidates-history').DataTable({
                                                processing: true,
                                                serverSide: true,
                                                scrollY: 500,
                                                scrollX: true,
                                                scrollCollapse: true,
                                                deferRender: true,
                                                "lengthMenu": [
                                                    [20, 50, 100, 200, 400, -1],
                                                    [20, 50, 100, 200, 400, "All"]
                                                ],
                                                ajax: {
                                                    url: "{{ route('admin.fees-stracture.fee-histories.index') }}",
                                                    data: function(d) {
                                                        d.center = $("#report-form #center").val();
                                                        d.level = $("#report-form  #level").val();
                                                        d.session = $("#report-form  #session").val();
                                                        d.year = $("#report-form  #year").val();
                                                        d.sponsor = $("#report-form  #sponsor").val();
                                                    }
                                                },
                                                columns: [{
                                                        data: 'center_no',
                                                        name: 'center_candidate.center_no',

                                                    },

                                                    {
                                                        data: 'national_id',
                                                        name: 'center_candidate.national_id',

                                                    }, {
                                                        data: 'candidate_no',
                                                        name: 'candidates.candidate_no',

                                                    },
                                                    {
                                                        data: 'candidate_surname',
                                                        name: 'candidates.candidate_surname',

                                                    },
                                                    {
                                                        data: 'candidate_other_name',
                                                        name: 'candidates.candidate_other_name',
                                                        searchable: true
                                                    },
                                                    {
                                                        data: 'price',
                                                        name: 'price',
                                                        searchable: false
                                                    },

                                                    {
                                                        data: 'amount_paid',
                                                        name: 'amount_paid',
                                                        searchable: false
                                                    },
                                                    {
                                                        data: 'action',
                                                        name: 'action'
                                                    },

                                                ]
                                            });
                                            $("#candidates-history").css("width", "100%");
                                            $("#year").trigger("change");
                                        });




                                        $('.dropdown-selected').on("change", function(event) {
                                            var name = $(this).attr("name");
                                            var value = $(this).val();
                                            var inputData = $("#report-form").serialize();
                                            $.ajax({
                                                type: "GET",
                                                url: "{{ route('admin.fees-stracture.fee-histories.index') }}",
                                                data: `${inputData}&filters=1`,
                                                success: function(response) {

                                                    console.log(response);
                                                    if (response) {
                                                        for (const key of Object.keys(response)) {


                                                            var formElement = key.slice(0, -1);
                                                            var selectOptions = response[key];
                                                            if (name == 'year') {
                                                                $(`#${formElement}`).empty();
                                                                $(`#${formElement}`).append(
                                                                    `<option value=''>Please Select ${formElement}</option>`
                                                                );
                                                            }



                                                            if (!$.isEmptyObject(response[key])) {
                                                                var selectOption = $(`#${formElement}`).val()
                                                                if (selectOption == "") {
                                                                    if (formElement == 'center') {
                                                                        $(`#${formElement}`).empty();
                                                                        $(`#${formElement}`).append(
                                                                            `<option value=''>Please Select ${formElement}</option>`
                                                                        );
                                                                        $.each(selectOptions, function(key, option) {
                                                                            $(`#${formElement}`).append(
                                                                                `<option value='${key}'>  ${key}  ${option}</option>`
                                                                            );
                                                                        });

                                                                    } else {

                                                                        $(`#${formElement}`).empty();
                                                                        $(`#${formElement}`).append(
                                                                            `<option value=''>Please Select ${formElement}</option>`
                                                                        );
                                                                        $.each(selectOptions, function(key, option) {
                                                                            $(`#${formElement}`).append(
                                                                                '<option value="' +
                                                                                option +
                                                                                '">' + option +
                                                                                '</option>');
                                                                        });

                                                                    }

                                                                }


                                                            }

                                                        }

                                                    }
                                                    $('#candidates-history').DataTable().ajax.reload();
                                                }
                                            });

                                        });

                                        //Show modal
                                        $(document).on('click', '.view-history', function() {
                                            var url = $(this).data("url");
                                            $.ajax({
                                                type: "GET",
                                                url: url,
                                                success: function(data) {
                                                    console.log(data);

                                                    var candidate_id = data.candidate.id
                                                    $('.candidate_no span').html(data.candidate.candidate_no);
                                                    $('.names span').html(data.candidate.candidate_other_name + ' ' + data.candidate
                                                        .candidate_surname);
                                                    $('.center_no span').html(data.candidate.center_no);
                                                    $('.total_subjects span').html(data.candidate.subject_number);
                                                    $('.tatal_amount_paid span').html('LSL' + (data.candidate.amount_paid).toFixed(2));
                                                    //
                                                    $('#candidate-fee-details').html(data.html);
                                                    $('#sub-total').html('LSL' + (data.sub_total).toFixed(2));
                                                    $('#total').html('LSL' + (data.total).toFixed(2));
                                                    $('.balance span').html('LSL' + (data.balance).toFixed(2));
                                                    $('#totalamount').val(data.balance);



                                                    //$('#fine').val(finevalue);
                                                    $('#candidate_id').val(data.candidate.id);
                                                    $('#fee_group_id').val(data.groupId);
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
                                                            url: "{{ route('admin.fees-stracture.fee-histories.index') }}",
                                                            data: function(d) {
                                                                d.payment_history = 'payment_history';
                                                                d.candidate_id = candidate_id;
                                                            },

                                                        },
                                                        columns: [
                                                            //feeGroup
                                                            {
                                                                data: 'feegroup.name',
                                                                name: 'feegroup.name',
                                                            },

                                                            {
                                                                data: 'reference_no',
                                                                name: 'fee_candidate_histories.reference_no',
                                                                searchable: true
                                                            },
                                                            {
                                                                data: 'amount',
                                                                name: 'fee_candidate_histories.amount',
                                                                searchable: true
                                                            },
                                                            {
                                                                data: 'fine',
                                                                name: 'fee_candidate_histories.fine',
                                                                searchable: true
                                                            },

                                                            {
                                                                data: 'created_at',
                                                                name: 'fee_candidate_histories.created_at',
                                                                searchable: true
                                                            },

                                                        ]
                                                    });
                                                    $("#payment-history").css("width", "100%");
                                                    $('#view-history-modal').modal('show');
                                                },
                                                error: function(xhr, error, code) {
                                                    console.log(xhr, code);
                                                }

                                            });
                                        });
                                        // Add
                                        $(document).on('click', '#add-payment', function(ev) {
                                            ev.preventDefault();
                                            var url = $('#add-payment-form').attr('action');
                                            $.ajaxSetup({
                                                headers: {
                                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                                }
                                            });

                                            //File data
                                            var formData = new FormData($('#add-payment-form')[0]);
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
                                                    // element.prop('disabled', false).html(caption);
                                                },
                                                complete: function(data) {
                                                    // element.prop('disabled', false).html(caption);
                                                }
                                            });

                                        });


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
    <!-- END MAIN -->
    <div class="clearfix"></div>
@endsection
