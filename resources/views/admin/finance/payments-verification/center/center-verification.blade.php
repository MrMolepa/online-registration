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
                                    <div class="pull-left">
                                        <button class="btn btn-primary" data-toggle="modal"
                                            data-target="#add-balanceBD-modal" type="button">
                                            + Balance b/d
                                        </button>
                                    </div>
                                    <div class="clearfix"></div>
                                </fieldset>
                                <div class="table-responsive">
                                    <table class="table display compact" id="center">
                                        <thead>
                                            <tr>
                                                <th>Upload Date</th>
                                                <th>Center No</th>
                                                <th>confirmation Reciept</th>
                                                <th>Amount Paid</th>
                                                <th>Checked By</th>
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
                                                var center = $('#center').DataTable({
                                                    processing: true,
                                                    serverSide: true,

                                                    deferRender: true,
                                                    "lengthMenu": [
                                                        [20, 50, 100, 200, 400, -1],
                                                        [20, 50, 100, 200, 400, "All"]
                                                    ],

                                                    ajax: {
                                                        url: "{{ route('admin.payments-verification.center', $center->center_no) }}",
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
                                                            data: 'confirmation_slip',
                                                            name: 'confirmation_slip',
                                                            orderable: false,
                                                            searchable: false
                                                        },
                                                        {
                                                            data: 'amount_paid',
                                                            name: 'amount_paid',
                                                            searchable: true

                                                        },
                                                        {
                                                            data: 'email',
                                                            name: 'email',
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
                                                $("#center").css("width", "99.5%");
                                                getCenterCharges();


                                            });

                                            function getCenterCharges() {
                                                $.ajaxSetup({
                                                    headers: {
                                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                                    }
                                                });
                                                $.ajax({
                                                    url: "{{ route('admin.payments-verification.centercharges', $center->center_no) }}",
                                                    method: "POST",
                                                    data: {
                                                        year: $("#year").val()
                                                    },
                                                    success: function(data) {
                                                        console.log(data);

                                                        $('#center-charges').html(data.html);
                                                    }
                                                });
                                            }

                                            $("#year").on("change", function(event) {
                                                $('#center').DataTable().ajax.reload();
                                                getCenterCharges();
                                            });
                                        </script>
                                    @endpush

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

            <!-- ADD Charge MODAL -->
            <div class="modal fade bd-modal-md" id="add-balanceBD-modal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog ">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close resetform" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h3 class="modal-title">Balance Brought Forward</h3>
                        </div>
                        <div class="modal-body">
                            <form action="{{ route('admin.payments-verification.balanceBroughtForward') }}"
                                id="balanceBroughtForwardForm" method="post">
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
                                    <label for="bank_reference">Bank_reference</label>
                                    <input type="text" value="Bal b/f -{{ date('Y') }}-{{ $center->center_no }}"
                                        readonly class="form-control" name="bank_reference" id="bank_reference">
                                </div>
                                <div class="form-group ">
                                    <label class="control-label" for="financial_year">Financial Year</label>
                                    <select class="form-control" name="financial_year" id="financial_year">
                                        @foreach ($years as $year)
                                            <option value="{{ $year }}">{{ $year }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group ">
                                    <label for="charge">Amount Paid</label>
                                    <input type="text" value="" class="form-control" name="amount_paid"
                                        id="amount_paid">
                                </div>



                            </form>

                        </div>
                        <div class="modal-footer">
                            <button type="submit" name="save-balance-brought-forward" class="btn btn-primary"
                                id="save-balance-brought-forward">Save</button>
                            <button type="button" class="btn btn-danger resetform" id="close"
                                data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>

            </div>
            <!--END ADD Charge Charg e MODEL -->

            <!-- UPDATE CONFIRMATION  MODAL -->
            <div class="modal fade bd-modal-md" id="update-confirmation" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog ">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close resetform" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h3 class="modal-title"> Center Proof of Payments </h3>
                        </div>
                        <div class="modal-body">
                            <form action="" method="post" id="centerConfimationForm">
                                <div>
                                    @csrf
                                </div>
                                <div class="form-group ">
                                    <label class="control-label" for="center_no">Centre Number</label>
                                    <input type="text" readonly class="form-control" name="center_no" id="center_no"
                                        value=" ">
                                </div>
                                <div class="form-group ">
                                    <label class="control-label" for="center_name">Centre Name</label>
                                    <input type="text" readonly name="center_name" class="form-control"
                                        id="center_name">
                                </div>

                                <div class="form-group ">
                                    <label class="control-label" for="amount_paid">Amount paid </label>
                                    <input type="text" name="amount_paid" class="form-control" id="amount_paid">
                                </div>

                                <div class="form-group ">
                                    <label class="control-label" for="financial_year">Financial Year</label>
                                    <select class="form-control" name="financial_year" id="financial_year">
                                        @foreach ($years as $year)
                                            <option value="{{ $year }}">{{ $year }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group ">
                                    <label class="control-label" for="bank_ref">bank_ref</label>
                                    <input type="text" name="bank_ref" class="form-control" id="bank_ref">
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Confirmation</label>
                                    <label class="fancy-radio">
                                        <input name="confirmation" value="0" type="radio">
                                        <span><i></i>Not Checked</span>
                                    </label>
                                    <label class="fancy-radio">
                                        <input name="confirmation" value="1" type="radio" />
                                        <span><i></i>Not Valid</span>
                                    </label>
                                    <label class="fancy-radio">
                                        <input name="confirmation" value="2" type="radio" />
                                        <span><i></i>Valid</span>
                                    </label>
                                </div>

                            </form>
                            <div class="clearfix"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary" id="save-confirmation">Save</button>
                            <button type="button" class="btn btn-danger resetform" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>

            </div>
            <!--END UPDATE CONFIRMATION  MODAL -->
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






        $(document).on("click", "#save-balance-brought-forward", function() {
            actionUrl = $('#balanceBroughtForwardForm').attr('action');
            var inputData = $('#balanceBroughtForwardForm').serialize();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                url: actionUrl,
                dataType: "json",
                data: inputData,
                success: function(data) {

                    if ($.isEmptyObject(data.errors)) {
                        toastr.success(data.success);
                        $('#center').DataTable().ajax.reload();
                        $("#add-balanceBD-modal").modal('hide');
                    } else {
                        printErrorMsg(`#balanceBroughtForwardForm`, data.errors);
                    }

                },
            });
        });
        // edit candidate
        $(document).on("click", "#center .editBtn", function(ev) {
            ev.preventDefault()
            var actionUrl = $(this).data('url');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "GET",
                url: actionUrl,
                success: function(data) {
                    var object = data.confimation;
                    for (const property in object) {
                        console.log(`${property}: ${object[property]}`);
                        $(`#centerConfimationForm [name='${property}']`).val(object[property]);

                        if (property == 'center') {
                            console.log(object[property].center_no);
                            $(`#centerConfimationForm [name='center_no']`).val(object[property]
                                .center_no);
                            $(`#centerConfimationForm [name='center_name']`).val(object[property]
                                .center_name);
                        }
                        if (property == 'checked_status') {
                            $("input[name=confirmation][value=" + object[property] + "]").prop(
                                'checked', true);
                        }


                    }
                    $("#centerConfimationForm").attr('action', data.url);
                    $("#update-confirmation").modal('show');



                },
            });
        });
        // update changes candidate
        $(document).on("click", "#save-confirmation", function() {
            actionUrl = $('#centerConfimationForm').attr('action');
            var inputData = $('#centerConfimationForm').serialize();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "PUT",
                url: actionUrl,
                dataType: "json",
                data: inputData,
                success: function(data) {
                    console.log(data);
                    if ($.isEmptyObject(data.errors)) {
                        toastr.success(data.success);
                        $('#center').DataTable().ajax.reload();
                        $("#update-confirmation").modal('hide');
                    } else {

                        printErrorMsg(`#centerConfimationForm`, data.errors);
                    }

                },
            });
        });



        // delete confirmation
        $(document).on('click', '#center  .deleteBtn', function(ev) {
            ev.preventDefault();
            var url = $(this).data('url');
            if (confirm("Are you sure you want to delete this!") == true) {
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
                            $('#center').DataTable().ajax.reload();
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
    </script>
@endsection
