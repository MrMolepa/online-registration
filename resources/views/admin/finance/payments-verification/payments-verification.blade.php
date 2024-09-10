@extends('layouts.admin')

@section('content')
    <!-- ============================================================== -->
    <!-- Start Page Content -->
    <!-- ============================================================== -->
    <div class="main">
        <!-- MAIN CONTENT -->
        <div class="main-content">
            <div class="container-fluid">
                <h3 class="page-title">Payments Verification</h3>

                <div class="row">
                    <div class="col-md-12">
                        <!-- PANEL NO CONTROLS -->
                        <div class="panel">
                            <div class="panel-heading">
                                <h3 class="panel-title">Payments Verification</h3>
                            </div>
                            <div class="panel-body">
                                @if (session()->has('success'))
                                    <div class="alert alert-success fade in alert-dismissible">
                                        <a href="#" class="close" data-dismiss="alert" aria-label="close"
                                            title="close">×</a>
                                        <strong>Success!</strong> {{ session('success') }}
                                    </div>
                                @endif

                                @if (session()->has('error'))
                                    <div class="alert alert-danger fade in alert-dismissible">
                                        <a href="#" class="close" data-dismiss="alert" aria-label="close"
                                            title="close">×</a>
                                        <strong>Success!</strong> {{ session('success') }}
                                    </div>
                                @endif
                                <div class="custom-tabs-line tabs-line-bottom left-aligned">
                                    <ul class="nav" role="tablist">
                                        <li class="active"><a href="#school-fee" role="tab" data-toggle="tab">Center
                                                Payments Verification
                                            </a></li>
                                        <li><a href="#private-fee" role="tab" data-toggle="tab">Private Candidate
                                                Payments Verification</a></li>
                                    </ul>
                                </div>
                                <div class="tab-content">
                                    <div class="tab-pane fade in active" id="school-fee">

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
                                            <table class="table" name="tablename" id="school-fee-datatable">
                                                <thead>
                                                    <tr>
                                                        <th>Centre Number</th>
                                                        <th>Centre Name</th>
                                                        <th>District</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>


                                            </table>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="private-fee">
                                        <div class="pull-left">
                                            <button class="btn btn-primary" type="button">
                                                Total Candidates <span class="badge">{{ $totalCandidates }}</span>
                                            </button>

                                        </div>
                                        <div class="pull-right">
                                            <button class="btn btn-success" type="button">
                                                Registered Candidates <span
                                                    class="badge">{{ $totalRegisteredCandidates }}</span>
                                            </button>

                                            <button class="btn btn-danger" type="button">
                                                Unregistered Candidates <span
                                                    class="badge">{{ $totalUnregisteredCandidates }}</span>
                                            </button>

                                        </div>
                                        <div class="clearfix"></div>
                                        <hr>

                                        <button type="button" data-toggle="modal" data-target="#add-candidate"
                                            class="btn btn-primary">+
                                            Candidate</button>
                                        <div class="bankStatement-status">
                                            <span class="invalid-status"></span>Invalid
                                            <span class="valid-status"></span>Valid
                                            <span class="not-checked-status"></span>Not checked
                                        </div>
                                        <div class="table-responsive">

                                            <table class="table display compact" id="candidates-datatable">
                                                <thead>
                                                    <tr>
                                                        <th></th>
                                                        <th>Upload at</th>
                                                        <th>Candidate No</th>
                                                        <th>Check By</th>
                                                        <th>Bank Statements</th>
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
                                                        var table = $('#candidates-datatable').DataTable({
                                                            processing: true,
                                                            serverSide: true,
                                                            scrollY: 500,
                                                            scrollX: true,
                                                            scrollCollapse: true,
                                                            deferRender: true,
                                                            "lengthMenu": [
                                                                [100, 250, 500, -1],
                                                                [100, 250, 500, "All"]
                                                            ],
                                                            ajax: "{{ route('admin.payments-verification.privatecandidates') }}",
                                                            columns: [{
                                                                    "className": 'dt-control',
                                                                    data: 'checked_status',
                                                                    name: 'checked_status',
                                                                    "orderable": false,
                                                                    "defaultContent": '',
                                                                    searchable: false

                                                                },

                                                                {
                                                                    data: 'created_at',
                                                                    name: 'candidate_confirmation.created_at',
                                                                    searchable: true

                                                                },
                                                                {
                                                                    data: 'candidate_no',
                                                                    name: 'candidate_no',
                                                                    searchable: true


                                                                },
                                                                {
                                                                    data: 'checked_by',
                                                                    name: 'candidate_confirmation.checked_by',
                                                                    searchable: true

                                                                },
                                                                {
                                                                    data: 'bank_statements',
                                                                    name: 'bank_statements',
                                                                    orderable: false,
                                                                    searchable: false
                                                                },
                                                                {
                                                                    data: 'actions',
                                                                    name: 'actions',
                                                                    orderable: false,
                                                                    searchable: false
                                                                },




                                                            ]
                                                        });
                                                        $("#candidates-datatable").css("width", "98.5%");
                                                        // Add event listener for opening and closing details
                                                        $('#candidates-datatable').on('click', 'td.dt-control', function() {
                                                            var tr = $(this).closest('tr');
                                                            var row = table.row(tr);

                                                            if (row.child.isShown()) {
                                                                // This row is already open - close it
                                                                row.child.hide();
                                                                tr.removeClass('shown');
                                                            } else {
                                                                // Open this row
                                                                row.child(format(row.data())).show();
                                                                tr.addClass('shown');
                                                            }
                                                        });
                                                        var table_school_fee = $('#school-fee-datatable').DataTable({
                                                            processing: true,
                                                            serverSide: true,

                                                            deferRender: true,
                                                            "lengthMenu": [
                                                                [20, 50, 100, 200, 400, -1],
                                                                [20, 50, 100, 200, 400, "All"]
                                                            ],


                                                            ajax: {
                                                                url: "{{ route('admin.payments-verification.index') }}",
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
                                                                    data: 'center_name',
                                                                    name: 'center_name',
                                                                    searchable: true

                                                                },
                                                                {
                                                                    data: 'district',
                                                                    name: 'district',

                                                                },


                                                                {
                                                                    data: 'actions',
                                                                    name: 'actions',
                                                                    searchable: false
                                                                }


                                                            ]

                                                        });
                                                        $("#school-fee-datatable").css("width", "98.5%");

                                                    });



                                                    /********** Search Candidate Number And display info **************/
                                                    $(document).on("keyup", "#addCandidateForm #candidate_no", function() {
                                                        var search = $(this).val();

                                                        $("#add-candidate .errors").html("");
                                                        if (search.length >= 9) {
                                                            $.ajaxSetup({
                                                                headers: {
                                                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                                                }
                                                            });
                                                            $.ajax({
                                                                url: "{{ route('admin.payments-verification.searchcandidate') }}",
                                                                method: "POST",
                                                                data: {
                                                                    candidate_no: search
                                                                },
                                                                success: function(data) {



                                                                    if ($.isEmptyObject(data.errors)) {

                                                                        if (data.status == 1) {
                                                                            $(".candidateinfo").html(data.html);
                                                                            $("#save-candidate").prop('disabled', false);
                                                                        }
                                                                    } else {
                                                                        printErrorMsgCandidate('#addCandidateForm', data.errors);
                                                                    }



                                                                },
                                                            });
                                                        } else {
                                                            $(".candidateinfo").html("");
                                                            $("#save-candidate").prop('disabled', true);
                                                        }
                                                    });
                                                    /********** Search  Candidate Number And display info End**************/



                                                    /********** Store Candidate  **************/
                                                    $(document).on('click', '#save-candidate', function(ev) {
                                                        ev.preventDefault();
                                                        var url = $('#addCandidateForm').attr('action');
                                                        $.ajaxSetup({
                                                            headers: {
                                                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                                            }
                                                        });
                                                        var caption = $(this).html();
                                                        var formData = new FormData($("#candidate_confirmation").parents('form')[0]);
                                                        $.ajax({
                                                            url: "{{ route('admin.payments-verification.storecandidate') }}",
                                                            type: 'POST',
                                                            xhr: function() {
                                                                var myXhr = $.ajaxSettings.xhr();
                                                                return myXhr;
                                                            },
                                                            beforeSend: function() {
                                                                $(this).prop('disabled', true).html("Processing.....");

                                                            },
                                                            success: function(data) {
                                                                $("#add-candidate .subjects-error ul").html("");
                                                                if ($.isEmptyObject(data.errors)) {
                                                                    $(this).prop('disabled', false).html(caption);
                                                                    if (data.status == 1) {
                                                                        $("#add-candidate .subjects-error ul").html("");
                                                                        $("#candidate_confirmation").parents('form')[0].reset();
                                                                        $("#add-candidate").modal("hide");
                                                                        $("#addCandidateForm .candidateinfo").html("");
                                                                        //  Reload dataTable
                                                                        $('#candidates-datatable').DataTable().ajax.reload(null, false);
                                                                        toastr.success('You have Successfully added candidate');
                                                                    }
                                                                } else {
                                                                    $(this).prop('disabled', false).html(caption);
                                                                    printErrorMsgCandidate('#addCandidateForm', data.errors);
                                                                }
                                                            },
                                                            data: formData,
                                                            cache: false,
                                                            contentType: false,
                                                            processData: false
                                                        });
                                                        return false;
                                                    });
                                                    /********** Store  Candidate  End**************/


                                                    $(document).on("change", "#physical_science_core", function() {
                                                        if ($(this).prop("checked")) {
                                                            if ($("#physical_science_extended").prop("checked")) {
                                                                $("#physical_science_extended").prop("checked", false);
                                                                $(this).prop("checked", true);
                                                            } else {
                                                                $(this).prop("checked", true);
                                                            }
                                                        } else {
                                                            $(this).prop("checked", false);
                                                        }
                                                    });

                                                    $(document).on("change", "#physical_science_extended", function() {
                                                        if ($(this).prop("checked")) {
                                                            if ($("#physical_science_core").prop("checked")) {
                                                                $("#physical_science_core").prop("checked", false);
                                                                $(this).prop("checked", true);
                                                            } else {
                                                                $(this).prop("checked", true);
                                                            }
                                                        } else {
                                                            $(this).prop("checked", false);
                                                        }
                                                    });

                                                    $(document).on("change", "#maths_core", function() {
                                                        if ($(this).prop("checked")) {
                                                            if ($("#maths_extended").prop("checked")) {
                                                                $("#maths_extended").prop("checked", false);
                                                                $(this).prop("checked", true);
                                                            } else {
                                                                $(this).prop("checked", true);
                                                            }
                                                        } else {
                                                            $(this).prop("checked", false);
                                                        }
                                                    });

                                                    $(document).on("change", "#maths_extended", function() {
                                                        if ($(this).prop("checked")) {
                                                            if ($("#maths_core").prop("checked")) {
                                                                $("#maths_core").prop("checked", false);

                                                                $(this).prop("checked", true);
                                                            } else {
                                                                $(this).prop("checked", true);
                                                            }
                                                        } else {
                                                            $(this).prop("checked", false);
                                                        }
                                                    });






                                                    /**********  Rest input when close Add user Modal **************/
                                                    $(document).on("click", ".resetform", function() {
                                                        $('.error-text').text('');
                                                        $("form").trigger("reset");
                                                    });
                                                    /**********  Rest input when close Add user Modal End **************/



                                                    /**********  Get particular  Records for candidates to check **************/
                                                    $(document).on("click", ".btn-edit-check", function() {
                                                        var url = $(this).data('url');
                                                        var i = 0;
                                                        $.ajaxSetup({
                                                            headers: {
                                                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                                            }
                                                        });

                                                        $.ajax({
                                                            url: url,
                                                            method: "GET",
                                                            beforeSend: function() {
                                                                // setting a timeout
                                                                $(".preloader").fadeIn();
                                                                i++;
                                                            },
                                                            success: function(data) {
                                                                var candidate = data.candidate
                                                                $('#checkCandidateForm input[name="candidate_no"]').val(pad(9, candidate
                                                                    .candidate_no,
                                                                    '0'));
                                                                $('#checkCandidateForm input[name="candidate_surname"]').val(candidate
                                                                    .candidate_surname);
                                                                $('#checkCandidateForm input[name="candidate_other_name"]').val(candidate
                                                                    .candidate_other_name);
                                                                $('#checkCandidateForm input[name="amount"]').val(candidate.amount);
                                                                $('#checkCandidateForm input[name="bank_reference"]').val(candidate.bank_ref);
                                                                $('#checkCandidateForm  input[name="confirmation"][value="' + candidate
                                                                    .checked_status +
                                                                    '"]'
                                                                ).prop("checked", true);
                                                                $('#checkCandidateForm input[name="bank_confirmation_id"]').val(candidate.id);
                                                                $("#checkCandidateForm").attr('action', data.url)
                                                                $("#check-candidate-modal").modal("show");
                                                            },
                                                            complete: function() {
                                                                i--;
                                                                if (i <= 0) {
                                                                    $(".preloader").fadeOut();
                                                                }
                                                            },
                                                        });
                                                    });
                                                    /********** End Get particular  Records for user to check **************/



                                                    /*****  Save Updates Candidate *******/
                                                    $(document).on("click", "#approve-candidate", function() {

                                                        var actionUrl = $("#checkCandidateForm").attr('action');

                                                        var i = 0;
                                                        $.ajaxSetup({
                                                            headers: {
                                                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                                            }
                                                        });

                                                        $.ajax({
                                                            url: actionUrl,
                                                            method: "PUT",
                                                            data: $("#checkCandidateForm").serialize(),
                                                            beforeSend: function() {
                                                                // setting a timeout
                                                                $(".preloader").fadeIn();
                                                                i++;
                                                            },
                                                            success: function(data) {
                                                                console.log(data);

                                                                $('.error-text').text('');
                                                                if ($.isEmptyObject(data.error)) {
                                                                    $("#checkCandidateForm").trigger("reset");
                                                                    $("#check-candidate-modal").modal("hide");
                                                                    toastr.success(data.msg);
                                                                    $('#candidates-datatable').DataTable().ajax.reload(null, false);
                                                                } else {
                                                                    printErrorMsg(data.error);
                                                                }

                                                            },
                                                            complete: function() {
                                                                i--;
                                                                if (i <= 0) {
                                                                    $(".preloader").fadeOut();
                                                                }
                                                            },
                                                        });
                                                    });
                                                    /***** End Update Candidate  *******/


                                                    /**********  Get particular  Records for candidates to comment on **************/
                                                    $(document).on("click", ".btn-edit-comment", function() {
                                                        var actionUrl = $(this).data("url");

                                                        var i = 0;
                                                        $.ajaxSetup({
                                                            headers: {
                                                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                                            }
                                                        });

                                                        $.ajax({
                                                            url: actionUrl,
                                                            method: "GET",
                                                            beforeSend: function() {
                                                                // setting a timeout
                                                                $(".preloader").fadeIn();
                                                                i++;
                                                            },
                                                            success: function(data) {




                                                                $('#commentsForm input[name="candidate_no"]').val(pad(9, data.privateCandidate
                                                                    .candidate_no,
                                                                    '0'));
                                                                $('#commentsForm input[name="candidate_surname"]').val(data.privateCandidate
                                                                    .candidate_surname);
                                                                $('#commentsForm input[name="candidate_other_name"]').val(data.privateCandidate
                                                                    .candidate_other_name);

                                                                $('#commentsForm input[name="bank_confirmation_id"]').val(data.privateCandidate.id);


                                                                $('#commentsForm input[name="phone_no"]').val(data.candidateInfo.phone_No);
                                                                $('#commentsForm input[name="email"]').val(data.candidateInfo.email_Address);
                                                                var comments = data.privateCandidate.comments;
                                                                $('#commentsForm #comments').val(comments);
                                                                $("#comments-modal").modal("show");
                                                                $("#commentsForm").attr('action', data.url)

                                                            },
                                                            complete: function() {
                                                                i--;
                                                                if (i <= 0) {
                                                                    $(".preloader").fadeOut();
                                                                }
                                                            },
                                                        });
                                                    });
                                                    /**********  Get particular  Records for candidates to comment on **************/


                                                    /*****  Save Comments*******/
                                                    $(document).on("click", "#save-comments", function() {

                                                        var actionUrl = $("#commentsForm").attr('action');
                                                        var i = 0;
                                                        $.ajaxSetup({
                                                            headers: {
                                                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                                            }
                                                        });

                                                        $.ajax({
                                                            url: actionUrl,
                                                            method: "PUT",
                                                            data: $("#commentsForm").serialize(),
                                                            beforeSend: function() {
                                                                // setting a timeout
                                                                $(".preloader").fadeIn();
                                                                i++;
                                                            },
                                                            success: function(data) {


                                                                $('.error-text').text('');
                                                                if ($.isEmptyObject(data.error)) {
                                                                    $("#commentsForm").trigger("reset");
                                                                    $("#comments-modal").modal("hide");
                                                                    toastr.success(data.msg);
                                                                    $('#candidates-datatable').DataTable().ajax.reload(null, false);
                                                                } else {
                                                                    printErrorMsg(data.error);
                                                                }

                                                            },
                                                            complete: function() {
                                                                i--;
                                                                if (i <= 0) {
                                                                    $(".preloader").fadeOut();
                                                                }
                                                            },
                                                        });
                                                    });
                                                    /***** End Save Comments  *******/


                                                    //
                                                    /**********  Delete Candidates **************/
                                                    $(document).on("click", ".delete-candidate", function(ev) {
                                                        var url = $(this).data("url");
                                                        if (confirm("Are You sure delete this candidate ?")) {
                                                            var i = 0;
                                                            $.ajaxSetup({
                                                                headers: {
                                                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                                                }
                                                            });
                                                            $.ajax({
                                                                url: url,
                                                                method: "DELETE",
                                                                beforeSend: function() {
                                                                    // setting a timeout
                                                                    $(".preloader").fadeIn();
                                                                    i++;
                                                                },
                                                                success: function(data) {
                                                                    if (data.status == 1) {
                                                                        toastr.success(data.msg);
                                                                        $('#candidates-datatable').DataTable().ajax.reload(null, false);
                                                                    } else {
                                                                        toastr.error(data.msg);
                                                                    }

                                                                },
                                                                complete: function() {
                                                                    i--;
                                                                    if (i <= 0) {
                                                                        $(".preloader").fadeOut();
                                                                    }
                                                                },
                                                            });
                                                        }
                                                    });
                                                    /**********  End Delete Candidate **************/
                                                    /**********  Delete Candidates **************/
                                                    $(document).on("click", ".deleteImageBtn", function(ev) {
                                                        ev.preventDefault();
                                                        var url = $(this).attr("href");
                                                        if (confirm("Are You sure delete this file ?")) {
                                                            var i = 0;
                                                            $.ajaxSetup({
                                                                headers: {
                                                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                                                }
                                                            });
                                                            $.ajax({
                                                                url: url,
                                                                method: "GET",
                                                                beforeSend: function() {
                                                                    // setting a timeout
                                                                    $(".preloader").fadeIn();
                                                                    i++;
                                                                },
                                                                success: function(data) {
                                                                    if (data.success !== undefined) {
                                                                        toastr.success(data.success);
                                                                        $('#candidates-datatable').DataTable().ajax.reload(null, false);
                                                                    } else {
                                                                        toastr.error(data.error);
                                                                    }

                                                                },
                                                                complete: function() {
                                                                    i--;
                                                                    if (i <= 0) {
                                                                        $(".preloader").fadeOut();
                                                                    }
                                                                },
                                                            });
                                                        }
                                                    });
                                                    /**********  End Delete Candidate **************/
                                                    /* Formatting function for row details - modify as you need */
                                                    function format(d) {

                                                        // `d` is the original data object for the row
                                                        var session = d.session !== null ? d.candidate_information.Session : 'Not registered';
                                                        var subjects = d.level !== null ? JSON.stringify(d.candidate_subjects) : 'Not registered';
                                                        var center_no = d.center_no !== null ? d.center_no : 'Not registered';
                                                        var fileshref = "";
                                                        d.bank_confirmation_path.forEach(element => {
                                                            fileshref += `<a href='/admin/candidate-confirmation-remove-image?candidate_no=${pad(9, d.candidate_no, '0')}&file_name=${element.file_name}'
                                                                        data-toggle='tooltip'
                                                                        title='${element.file_name}'
                                                                        class='btn btn-danger deleteImageBtn'><i class='fas fa-trash-alt'
                                                                            download></i></a>`

                                                        });







                                                        return '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">' +
                                                            '<tr>' +
                                                            '<td>Candidate number:</td>' +
                                                            '<td>' + pad(9, d.candidate_no, '0') + '</td>' +
                                                            '</tr>' +
                                                            '<tr>' +
                                                            '<td>Full name:</td>' +
                                                            '<td>' + d.candidate_surname + '  ' + d.candidate_other_name + '</td>' +
                                                            '</tr>' +
                                                            '<tr>' +
                                                            '<td>Session :</td>' +
                                                            '<td>' + session + '</td>' +
                                                            '</tr>' +
                                                            '<tr>' +
                                                            '<td>Subjects :</td>' +
                                                            '<td>' + subjects + '</td>' +
                                                            '</tr>' +
                                                            '<tr>' +
                                                            '<td>Center no :</td>' +
                                                            '<td>' + center_no + '</td>' +
                                                            '</tr>' +
                                                            '<tr>' +
                                                            '<td>Reference :</td>' +
                                                            '<td>' + d.bank_ref + '</td>' +
                                                            '</tr>' +
                                                            '<tr>' +
                                                            '<td>Total Amount :</td>' +
                                                            '<td>LSL' + d.amount + '</td>' +
                                                            '</tr>' +
                                                            '<tr>' +
                                                            '<td><b>File Remove</b> :</td>' +
                                                            '<td>' + fileshref + '</td>' +
                                                            '</tr>' +
                                                            '</table>';
                                                    }

                                                    function pad(width, string, padding) {
                                                        return string.toString().padStart(width, padding)
                                                    }

                                                    /****  Print errors*******/
                                                    function printErrorMsg(msg) {
                                                        $.each(msg, function(key, value) {

                                                            $('.' + key + '_error').text(value);
                                                            $("input[name='" + key + "']").addClass("is-valid");

                                                        });

                                                    }

                                                    // /****  Print errors*******/
                                                    function printErrorMsgCandidate(parent, msg) {
                                                        $(`${parent} input, ${parent} select, textarea`).each(function(index) {
                                                            var input = $(this);
                                                            var inputName = input.attr('name');
                                                            $("[name='" + inputName + "']").parent().removeClass('has-error')
                                                            $("[name='" + inputName + "']").siblings('span').html('');
                                                            // console.log(input.attr('type') + 'Name: ' + input.attr('name') + '  Value: ' + input.val());
                                                        });
                                                        $.each(msg, function(key, errors) {
                                                            for (const error in errors) {
                                                                const value = errors[error];
                                                                $("[name='" + key + "']").parent().addClass('has-error');
                                                                $("[name='" + key + "']").next().html(`<strong>${value}</strong>`);

                                                                if (key == 'subjects') {
                                                                    $(".subjects-errors").addClass('has-error');
                                                                    $(".subjects-errors").find('span').html(`<strong>${value}</strong>`);
                                                                }
                                                            }

                                                        });
                                                    }
                                                    /****  Print errors End*******/


                                                    $("#year").on("change", function(event) {
                                                        $('#school-fee-datatable').DataTable().ajax.reload();
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

                </div>
            </div>
            <!-- END MAIN CONTENT -->
        </div>
        <!-- END MAIN CONTENT -->
    </div>
    <!-- ============================================================== -->
    <!-- End PAge Content -->
    <!-- ============================================================== -->
    <!-- CHECK CANDIDATE MODAL -->
    <div class="modal fade bd-modal-md" id="check-candidate-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">

                    <button type="button" class="close resetform" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h3 class="modal-title"> Check Candidate </h3>
                </div>
                <div class="modal-body">
                    <form action=" " method="post" id="checkCandidateForm">
                        <div class="form-group ">
                            <label for="candidate_no">Candidate no</label>
                            <input type="text" class="form-control" name="candidate_no" value=" " readonly
                                id="candidate_no">
                            <span class="text-danger error-text candidate_no_error"></span>
                        </div>
                        <div class="form-group   @error('candidate_surname') has-error @enderror">
                            <label for="candidate_surname">Candidate Surname</label>
                            <input type="text" class="form-control" name="candidate_surname" value="" readonly
                                id="candidate_surname">
                            <span class="text-danger error-text candidate_surname_error"></span>

                        </div>
                        <div class="form-group">
                            <label for="candidate_other_name">Candidate Other Names</label>
                            <input type="text" class="form-control" name="candidate_other_name" value=""
                                readonly id="candidate_other_name">
                            <span class="text-danger error-text candidate_other_name_error"></span>

                        </div>
                        <div class="form-group">
                            <label for="amount">Paid Amount</label>
                            <input type="text" class="form-control" name="amount" value=" " id="amount">
                            <span class="text-danger error-text amount_error"></span>
                        </div>
                        <div class="form-group">
                            <label for="bank_reference">Bank reference</label>
                            <input type="text" class="form-control" name="bank_reference" value=""
                                id="bank_reference">
                            <span class="text-danger error-text bank_reference_error"></span>
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="confirmation">Bank Statement Confirmation
                            </label>
                            <label class="fancy-radio">
                                <input name="confirmation" value="0" type="radio">
                                <span><i></i>Not Checked </span>
                            </label>
                            <label class="fancy-radio">
                                <input name="confirmation" value="1" type="radio">
                                <span><i></i>Not Valid </span>
                            </label>
                            <label class="fancy-radio">
                                <input name="confirmation" value="2" type="radio">
                                <span><i></i>Valid</span>
                            </label>
                            <span class="text-danger error-text confirmation_error"></span>
                        </div>

                    </form>

                </div>
                <div class="modal-footer">
                    <button type="submit" data-id="" name="approve-candidate" class="btn btn-primary"
                        id="approve-candidate">Update</button>
                    <button type="button" class="btn btn-danger resetform" data-dismiss="modal">Close</button>

                </div>
            </div>
        </div>
    </div>
    <!-- END CHECK CANDIDATE MODAL -->
    <!-- ADD COMMENT MODAL -->
    <div class="modal fade bd-modal-md" id="comments-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">

                    <button type="button" class="close resetform" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h3 class="modal-title">Add Comments </h3>
                </div>
                <div class="modal-body">
                    <div>
                        <form action="" method="post" id="commentsForm">

                            <div class="form-group  ">
                                <label for="candidate_no">Candidate no</label>
                                <input type="text" class="form-control" name="candidate_no" value="" readonly
                                    id="candidate_no">
                                <span class="text-danger error-text candidate_no_error"></span>

                            </div>
                            <div class="form-group">
                                <label for="candidate_surname">Candidate Surname</label>
                                <input type="text" class="form-control" name="candidate_surname" value=""
                                    readonly id="candidate_surname">
                                <span class="text-danger error-text candidate_surname_error"></span>
                                <input type="hidden" name="bank_confirmation_id" value="">
                            </div>
                            <div class="form-group">
                                <label for="candidate_other_name">Candidate Other Names</label>
                                <input type="text" class="form-control" name="candidate_other_name" value=" "
                                    readonly id="candidate_other_name">
                                <span class="text-danger error-text candidate_other_name_error"></span>

                            </div>
                            <div class="form-group">
                                <label for="email">Email Address</label>
                                <input type="text" class="form-control" name="email" value=" " readonly
                                    id="email">
                                <span class="text-danger error-text email_error"></span>

                            </div>
                            <div class="form-group">
                                <label for="phone_no">phone No</label>
                                <input type="text" class="form-control" name="phone_no" value="" readonly
                                    id="phone_no">
                                <span class="text-danger error-text phone_no_error"></span>

                            </div>
                            <div class="form-group">
                                <label for="comments" class="form-label">Comments
                                </label>
                                <textarea class="form-control" name="comments" id="comments" rows="7"></textarea>
                                <span class="text-danger error-text comments_error"></span>
                            </div>


                        </form>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="save-comments" class="btn btn-primary" id="save-comments">Save
                        changes</button>
                    <button type="button" class="btn btn-danger resetform" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!-- END ADD COMMENT MODAL -->

    <!-- ADD CANDIDATE MODAL -->
    <div class="modal fade bd-modal-md" id="add-candidate" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close resetform" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h3 class="modal-title"> Add Candidate </h3>
                </div>
                <div class="modal-body">
                    <form action="{{ route('admin.candidate-registation.store') }}" method="post"
                        id="addCandidateForm">
                        <div>
                            @csrf
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="candidate_no" class="control-label">Candidate number</label>
                                <input type="text" class="form-control " placeholder="Enter Candidate Number"
                                    name="candidate_no" id="candidate_no" placeholder="*Candidate number">
                                <span class="help-block"></span>
                            </div>
                            <div class="form-group  col-md-6">
                                <label for="center_no" class="control-label">Center</label>
                                <select name="center_no" class="form-control" id="center_no">
                                    <option value="">
                                        Select Center</option>
                                    @foreach ($centers as $center)
                                        <option value="{{ $center->center_no }}">
                                            {{ $center->center_no }}-{{ $center->center_name }}</option>
                                    @endforeach
                                </select>
                                <span class="help-block"></span>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="level" class="control-label">Level</label>
                                <select name="level" class="form-control" id="level">
                                    <option value="">Select level</option>
                                    <option value="LGCSE">LGCSE</option>
                                </select>
                                <span class="help-block"></span>
                            </div>
                            <div class="form-group  col-md-6">
                                <label for="candidate_confirmation" class="control-label">Proof of Payment</label>
                                <input type="file" class="form-control" name="candidate_confirmation"
                                    id="candidate_confirmation">
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="candidateinfo">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="add_candidate" class="btn btn-primary" disabled
                        id="save-candidate">Submit</button>
                    <button type="button" class="btn btn-danger resetform" id="adduser_close"
                        data-dismiss="modal">Close</button>

                </div>
            </div>
        </div>

    </div>
    <!--END ADD CANDIDATE MODEL -->
@endsection
