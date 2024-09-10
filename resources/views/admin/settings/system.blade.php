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
                                <h3 class="panel-title">Settings</h3>
                            </div>
                            <div class="panel-body">
                                <button type="button" data-toggle="modal" data-target="#add-fee" class="btn btn-primary">+
                                    Add Seting</button>
                                <div class="table-responsive">
                                    <table class="table" name="tablename" id="system">
                                        <thead>
                                            <tr>
                                                <th>Meta Field</th>
                                                <th>Meta Value</th>
                                                <th>action</th>
                                            </tr>
                                        </thead>
                                    </table>
                                    @push('scripts')
                                        <script>
                                            $(function() {
                                                var system = $('#system').DataTable({
                                                    processing: true,
                                                    serverSide: true,
                                                    deferRender: true,
                                                    "lengthMenu": [
                                                        [20, 50, 100, 200, 400, -1],
                                                        [20, 50, 100, 200, 400, "All"]
                                                    ],
                                                    ajax: "{{ route('admin.setting.index') }}",
                                                    columns: [{
                                                            data: 'meta_field',
                                                            name: 'meta_field',
                                                            searchable: false,
                                                            sortable: false
                                                        },
                                                        {
                                                            data: 'meta_value',
                                                            name: 'meta_value',
                                                        },
                                                        {
                                                            data: 'action',
                                                            name: 'action',
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
                            {{-- <form action="{{ route('admin.system.store') }}" method="post" id="feeForm">
                                <div>
                                    @csrf
                                </div>
                                <div class="form-group col-md-12">
                                    <label class="control-label" for="candidate_type">Candidate Type</label>
                                    <select id="candidate_type" name="candidate_type" class="form-control">
                                        <option value=""> Please Select </option>
                                        <option value="lgcse-school">lgcse school cadidate</option>
                                        <option value="lgcse-private">lgcse private cadidate </option>
                                        <option value="jc-private">jc private candidate</option>
                                    </select>
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
                            </form> --}}
                            <div class="clearfix"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary" id="save-system">Save</button>
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

        // //  Add Fee charge
        // $(document).on('click', '#save-system', function(ev) {
        //     ev.preventDefault();
        //     var url = $('#feeForm').attr('action');
        //     $.ajaxSetup({
        //         headers: {
        //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //         }
        //     });


        //     var inputData = $("#feeForm").serialize();

        //     $.ajax({
        //         url: url,
        //         method: "POST",
        //         data: inputData,
        //         success: function(data) {
        //             if ($.isEmptyObject(data.errors)) {
        //                 $('#add-fee').modal('hide');
        //                 $('#feeForm .help-block').remove();
        //                 $('#feeForm .has-error').removeClass('has-error');
        //                 toastr.success(data.success);
        //                 $('#system').DataTable().ajax.reload();
        //             } else {
        //                 printErrorMsg('#feeForm', data.errors);
        //             }


        //         }
        //     });


        // });

        // // edit charge
        $(document).on("click", "#system .editBtn", function() {
            //hide edit span

            $(this).closest("tr").find(".editSpan").hide();

            //show edit input
            $(this).closest("tr").find(".editInput").show();

            //hide edit button
            $(this).closest("tr").find(".editBtn").hide();

            //show edit button
            $(this).closest("tr").find(".saveBtn").show();
        });
        // // update changes system
        $(document).on("click", "#system .saveBtn", function() {
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
                        $('#system').DataTable().ajax.reload();
                    } else {
                        printErrorMsg(`#${ID}`, data.errors);
                    }

                },
            });
        });


        // // delete Candidate
        // $(document).on('click', '#system .deleteBtn', function(ev) {
        //     ev.preventDefault();
        //     var url = $(this).data('url');
        //     if (confirm("Are you sure you want to delete this charges !") == true) {
        //         $.ajaxSetup({
        //             headers: {
        //                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //             }
        //         });

        //         $.ajax({
        //             url: url,
        //             method: "DELETE",
        //             success: function(data) {
        //                 if (data.success) {
        //                     toastr.success(data.success);
        //                     $('#system').DataTable().ajax.reload();
        //                 }



        //             }
        //         });


        //     } else {
        //         return;
        //     }

        // });


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
