@extends('layouts.admin')

@section('content')
    <!-- ============================================================== -->
    <!-- Start Page Content -->
    <!-- ============================================================== -->
    <div class="main">
        <!-- MAIN CONTENT -->
        <div class="main-content">
            <div class="container-fluid">
                <h3 class="page-title">Fee Charges</h3>
                <div class="row">
                    <div class="col-md-12">
                        <!-- PANEL NO CONTROLS -->
                        <div class="panel">
                            <div class="panel-heading">
                                <h3 class="panel-title">Fee Charges</h3>
                            </div>
                            <div class="panel-body">

                                <div class="custom-tabs-line tabs-line-bottom left-aligned">
                                    <ul class="nav" role="tablist">
                                        <li class="active"><a href="#fee-charge-tab" role="tab" data-toggle="tab">Fee
                                                Charges</a></li>
                                        <li>
                                            <a href="#late-fee-charge-tab" role="tab" data-toggle="tab">Late Fee
                                                Charges</a>
                                        </li>

                                    </ul>
                                </div>
                                <div class="tab-content">
                                    <div class="tab-pane fade in active" id="fee-charge-tab">
                                        <button type="button" data-toggle="modal" data-target="#add-fee"
                                            class="btn btn-primary"> +
                                            Fee</button>
                                        <div class="table-responsive">
                                            <table class="table" name="tablename" id="fees">
                                                <thead>
                                                    <tr>
                                                        <th>Display Name</th>
                                                        <th>Subject Fee</th>
                                                        <th>Level</th>
                                                        <th>Session</th>
                                                        <th>Registration fee</th>
                                                        <th>Local fee</th>
                                                        <th>Practical fee</th>
                                                        <th>Bank_charge</th>
                                                        <th>Delf fee</th>
                                                        <th>financial_year</th>
                                                        <th>action</th>
                                                    </tr>
                                                </thead>


                                            </table>
                                            @push('scripts')
                                                <script>
                                                    $(function() {

                                                        var fees = $('#fees').DataTable({
                                                            processing: true,
                                                            serverSide: true,

                                                            deferRender: true,
                                                            "lengthMenu": [
                                                                [20, 50, 100, 200, 400, -1],
                                                                [20, 50, 100, 200, 400, "All"]
                                                            ],
                                                            ajax: "{{ route('admin.fees.index') }}",
                                                            columns: [{
                                                                    data: 'candidate_type',
                                                                    name: 'candidate_type',
                                                                    searchable: true
                                                                },
                                                                {
                                                                    data: 'subject_fee',
                                                                    name: 'subject_fee',
                                                                    searchable: true
                                                                },
                                                                {
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
                                                                    data: 'registration_fee',
                                                                    name: 'registration_fee',
                                                                    searchable: true
                                                                },
                                                                {
                                                                    data: 'local_fee',
                                                                    name: 'local_fee',


                                                                },
                                                                {
                                                                    data: 'practical_subject_fee',
                                                                    name: 'practical_subject_fee',
                                                                },
                                                                {
                                                                    data: 'bank_charge',
                                                                    name: 'bank_charge',

                                                                },
                                                                {
                                                                    data: 'delf_fee',
                                                                    name: 'delf_fee',
                                                                },
                                                                {
                                                                    data: 'financial_year',
                                                                    name: 'financial_year',

                                                                },
                                                                {
                                                                    data: 'action',
                                                                    name: 'action',
                                                                    searchable: false,
                                                                    sortable: false

                                                                }

                                                            ]

                                                        });

                                                        $("#fees").css("width", "100%");



                                                    });
                                                </script>
                                            @endpush
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="late-fee-charge-tab">
                                        <button type="button" data-toggle="modal" data-target="#add-late-fee"
                                        class="btn btn-primary"> +
                                        Late Fee</button>
                                        <div class="table-responsive">
                                            <table class="table" name="tablename" id="late-fees">
                                                <thead>
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>Start Date</th>
                                                        <th>End Date</th>
                                                        <th>Amount</th>
                                                        <th>Session</th>
                                                        <th>Financial Year</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                            </table>
                                            @push('scripts')
                                                <script>
                                                    $(function() {

                                                        var late_fees = $('#late-fees').DataTable({
                                                            processing: true,
                                                            serverSide: true,
                                                            deferRender: true,
                                                            "lengthMenu": [
                                                                [20, 50, 100, 200, 400, -1],
                                                                [20, 50, 100, 200, 400, "All"]
                                                            ],
                                                            ajax: "{{ route('admin.late-fees.index') }}",
                                                            columns: [{
                                                                    data: 'id',
                                                                    name: 'id',
                                                                    searchable: true
                                                                },
                                                                {
                                                                    data: 'start_date',
                                                                    name: 'start_date',
                                                                    searchable: true
                                                                },
                                                                {
                                                                    data: 'end_date',
                                                                    name: 'end_date',
                                                                    searchable: true
                                                                },
                                                                {
                                                                    data: 'amount',
                                                                    name: 'amount',
                                                                },
                                                                {
                                                                    data: 'session',
                                                                    name: 'session',
                                                                    searchable: true
                                                                },


                                                                {
                                                                    data: 'financial_year',
                                                                    name: 'financial_year',

                                                                },
                                                                {
                                                                    data: 'action',
                                                                    name: 'action',
                                                                    searchable: false,
                                                                    sortable: false

                                                                }

                                                            ]

                                                        });

                                                        $("#late-fees").css("width", "98.5%");



                                                    });
                                                </script>
                                            @endpush
                                        </div>

                                    </div>


                                </div>




                            </div>

                        </div>
                        <!-- END PANEL NO CONTROLS -->
                    </div>

                </div>


            </div>

            <!-- ADD  CENTER  MODAL -->
            <div class="modal fade bd-modal-md" id="add-fee" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog ">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close resetform" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h3 class="modal-title"> Charge </h3>
                        </div>
                        <div class="modal-body">
                            <form action="{{ route('admin.fees.store') }}" method="post" id="feeForm">
                                <div>
                                    @csrf
                                </div>
                                <div class="form-group col-md-12">
                                    <label class="control-label" for="candidate_type">Candidate Type</label>
                                    <select id="candidate_type" name="candidate_type" class="form-control">
                                        <option value=""> Please Select </option>
                                        @foreach ($levels as $level)
                                            <option value="{{ strtolower($level->level) }}-school">{{ $level->level }}
                                                school cadidate</option>
                                            <option value="{{ strtolower($level->level) }}-private">{{ $level->level }}
                                                private cadidate </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-12">
                                    <label class="control-label" for="level">Level</label>
                                    <div>
                                        <select class='form-control' name='level' id="level">
                                            <option value=''>Please Select level</option>
                                            @foreach ($levels as $level)
                                                <option value='{{ $level->id }}'>{{ $level->level }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group col-md-12">
                                    <label class="control-label" for="session">Session</label>
                                    <div>
                                        <select class='form-control' name='session' id="session">
                                            <option value=''>Please Select session</option>
                                            @foreach ($sessions as $session)
                                                <option value='{{ $session->id }}'>
                                                    {{ $session->session }}-{{ $session->financial_year }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group col-md-12">
                                    <label class="control-label" for="financial_year">Financial year</label>
                                    <input type="text" name="financial_year"
                                        value="{{ date('Y') . '-' . (date('Y') + 1) }}" class="form-control"
                                        id="financial_year">
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="control-label" for="registration_fee">Subject Fee
                                    </label>
                                    <input type="text" name="subject_fee" class="form-control" id="subject_fee">
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="control-label" for="delf_fee">Delf Fee
                                    </label>
                                    <input type="text" name="delf_fee" class="form-control" id="delf_fee">
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="control-label" for="bank_charge">Bank charge</label>
                                    <input type="text" name="bank_charge" class="form-control" id="bank_charge">
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="control-label" for="registration_fee">Registration fee</label>
                                    <input type="text" name="registration_fee" class="form-control"
                                        id="registration_fee">
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="control-label" for="local_fee">Local Fee</label>
                                    <input type="text" name="local_fee" class="form-control" id="local_fee">
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="control-label" for="level">Practical fee</label>
                                    <input type="text" name="practical_subject_fee" class="form-control"
                                        id="practical_subject_fee">
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="control-label" for="email">Bank charge</label>
                                    <input type="text" name="bank_charge" class="form-control" id="bank_charge">
                                </div>
                            </form>
                            <div class="clearfix"></div>

                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary" id="save-fees">Save</button>
                            <button type="button" class="btn btn-danger resetform" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>

            </div>

            <!-- ADD  Late Fee  MODAL -->
            <div class="modal fade bd-modal-md" id="add-late-fee" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog ">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close resetform" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h3 class="modal-title"> Charge </h3>
                        </div>
                        <div class="modal-body">
                            <form action="{{ route('admin.late-fees.store') }}" method="post" id="lateFeeForm">
                                <div>
                                    @csrf
                                </div>
                                <div class="form-group">
                                    <label class="control-label" for="start_date">Start Date
                                    </label>
                                    <input type="date" name="start_date" class="form-control" id="start_date">
                                </div>
                                <div class="form-group">
                                    <label class="control-label" for="delf_fee">End Date
                                    </label>
                                    <input type="date" name="end_date" class="form-control" id="end_date">
                                </div>
                                <div class="form-group">
                                    <label class="control-label" for="amount">Amount</label>
                                    <input type="text" name="amount" class="form-control" id="amount">
                                </div>
                                <div class="form-group">
                                    <label class="control-label" for="session">Session</label>
                                    <input type="text" name="session" class="form-control" id="session">
                                </div>
                                <div class="form-group">
                                    <label class="control-label" for="financial_year">Financial Year</label>
                                    <input type="text" name="financial_year" class="form-control"
                                        id="financial_year">
                                </div>

                            </form>
                            <div class="clearfix"></div>

                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary" id="save-late-fee">Save</button>
                            <button type="button" class="btn btn-danger resetform" data-dismiss="modal">Close</button>
                        </div>
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

        //  Add Fee charge
        $(document).on('click', '#save-fees', function(ev) {
            ev.preventDefault();
            var url = $('#feeForm').attr('action');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });


            var inputData = $("#feeForm").serialize();

            $.ajax({
                url: url,
                method: "POST",
                data: inputData,
                success: function(data) {
                    if ($.isEmptyObject(data.errors)) {
                        $('#add-fee').modal('hide');
                        $('#feeForm .help-block').remove();
                        $('#feeForm .has-error').removeClass('has-error');
                        toastr.success(data.success);
                        $('#fees').DataTable().ajax.reload();
                    } else {
                        printErrorMsg('#feeForm', data.errors);
                    }


                }
            });


        });
        // Replicate
        $(document).on("click", "#replicate-fee", function() {
            if (confirm("Are you sure you want to Replicate fees this charges !") == true) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: "{{ route('admin.fees.create') }}",
                    method: "GET",
                    success: function(data) {
                        if (data.success) {
                            toastr.success(data.success);
                            $('#fees').DataTable().ajax.reload();
                        }

                    }
                });
            } else {
                return;
            }
        });
        // edit charge
        $(document).on("click", "#fees .editBtn", function() {
            //hide edit span

            $(this).closest("tr").find(".editSpan").hide();

            //show edit input
            $(this).closest("tr").find(".editInput").show();

            //hide edit button
            $(this).closest("tr").find(".editBtn").hide();

            //show edit button
            $(this).closest("tr").find(".saveBtn").show();
        });
        // update changes fees
        $(document).on("click", "#fees .saveBtn", function() {
            actionUrl = $(this).data('url');
            var trObj = $(this).closest("tr");
            var ID = $(this).closest("tr").attr("id");
            var inputData = $(this).closest("tr").find(".editInput").serialize();
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
                        trObj.find(".editInput").hide();
                        trObj.find(".saveBtn").hide();
                        trObj.find(".editSpan").show();
                        trObj.find(".editBtn").show();
                        toastr.success("You have successfully Saved Changes");
                        $('#fees').DataTable().ajax.reload();
                    } else {
                        printErrorMsg(`#${ID}`, data.errors);
                    }

                },
            });
        });


        // delete fee
        $(document).on('click', '#fees .deleteBtn', function(ev) {
            ev.preventDefault();
            var url = $(this).data('url');
            if (confirm("Are you sure you want to delete this charges !") == true) {
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
                            $('#fees').DataTable().ajax.reload();
                        }



                    }
                });


            } else {
                return;
            }

        });






        //  Add Fee charge
        $(document).on('click', '#save-late-fee', function(ev) {
            ev.preventDefault();
            var url = $('#lateFeeForm').attr('action');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            var inputData = $("#lateFeeForm").serialize();
            $.ajax({
                url: url,
                method: "POST",
                data: inputData,
                success: function(data) {
                    console.log(data);
                    if ($.isEmptyObject(data.errors)) {
                        $('#add-late-fee').modal('hide');
                        $('#lateFeeForm .help-block').remove();
                        $('#lateFeeForm .has-error').removeClass('has-error');
                        toastr.success(data.success);
                        $('#late-fees').DataTable().ajax.reload();
                    } else {
                        printErrorMsg('#lateFeeForm', data.errors);
                    }


                }
            });


        });
        // edit charge
        $(document).on("click", "#late-fees .editBtn", function() {
            //hide edit span

            $(this).closest("tr").find(".editSpan").hide();

            //show edit input
            $(this).closest("tr").find(".editInput").show();

            //hide edit button
            $(this).closest("tr").find(".editBtn").hide();

            //show edit button
            $(this).closest("tr").find(".saveBtn").show();
        });
        // update changes fees
        $(document).on("click", "#late-fees .saveBtn", function() {
            actionUrl = $(this).data('url');
            var trObj = $(this).closest("tr");
            var ID = $(this).closest("tr").attr("id");
            var inputData = $(this).closest("tr").find(".editInput").serialize();
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
                        trObj.find(".editInput").hide();
                        trObj.find(".saveBtn").hide();
                        trObj.find(".editSpan").show();
                        trObj.find(".editBtn").show();
                        toastr.success("You have successfully Saved Changes");
                        $('#late-fees').DataTable().ajax.reload();
                    } else {
                        printErrorMsg(`#${ID}`, data.errors);
                    }

                },
            });
        });
        // delete fee
        $(document).on('click', '#late-fees .deleteBtn', function(ev) {
            ev.preventDefault();
            var url = $(this).data('url');
            if (confirm("Are you sure you want to delete this charges !") == true) {
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
                            $('#late-fees').DataTable().ajax.reload();
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
