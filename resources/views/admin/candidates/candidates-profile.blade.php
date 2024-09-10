@extends('layouts.admin')

@section('content')
    <!-- ============================================================== -->
    <!-- Start Page Content -->
    <!-- MAIN -->
    <div class="main">
        <!-- MAIN CONTENT -->
        <div class="main-content">
            <div class="container-fluid">
                <h3 class="page-title">Candidate Profiles</h3>
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel">
                            <div class="panel-heading">
                                <h3 class="panel-title">Candidate Profiles</h3>
                            </div>
                            <div class="panel-body">
                                <div class="pull-left">
                                    <a href="" class="btn btn-info" data-toggle="modal"
                                        data-target="#change-candidate-modal" >+ Changes candidate
                                    </a>

                                </div>
                                <div class="pull-right">
                                    <a href="" class="btn btn-info" data-toggle="modal"
                                        data-target="#add-candidate">+ Create
                                    </a>
                                    <a href="{{ route('admin.candidate-profile.create') }}" class="btn btn-info">Import</a>
                                </div>
                                <div class="clearfix"></div>

                                <div class="table-responsive">
                                    <table class="table" name="tablename" id="candidates">
                                        <thead>
                                            <tr>
                                                <th>Candidate no</th>
                                                <th>National Id</th>
                                                <th>candidate other names</th>
                                                <th>candidate surname</th>
                                                <th>Date of birth</th>
                                                <th>Gender</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>


                                    </table>
                                    @push('scripts')
                                        <script>
                                            $(function() {

                                                var candidates = $('#candidates').DataTable({
                                                    processing: true,
                                                    serverSide: true,
                                                    deferRender: true,
                                                    "lengthMenu": [
                                                        [20, 50, 100, 200, 400, -1],
                                                        [20, 50, 100, 200, 400, "All"]
                                                    ],
                                                    ajax: "{{ route('admin.candidate-profile.index') }}",
                                                    columns: [{
                                                            data: 'candidate_no',
                                                            name: 'candidate_no',
                                                            searchable: true
                                                        },
                                                        {
                                                            data: 'national_id',
                                                            name: ' national_id',
                                                            searchable: true
                                                        },
                                                        {
                                                            data: 'candidate_other_name',
                                                            name: 'candidate_other_name',
                                                            searchable: true
                                                        },
                                                        {
                                                            data: 'candidate_surname',
                                                            name: 'candidate_surname',


                                                        },
                                                        {
                                                            data: 'date_of_birth',
                                                            name: 'date_of_birth',
                                                        },

                                                        {
                                                            data: 'gender',
                                                            name: 'gender',

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

                    </div>

                </div>


            </div>
        </div>

        <!-- ADD CANDIDATE MODAL -->
        <div class="modal fade bd-modal-md" id="add-candidate" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog ">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close resetform" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h3 class="modal-title">New Candidate</h3>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('admin.candidate-profile.store') }}" method="post" id="addCandidateForm">
                            <div>
                                @csrf
                            </div>
                            <div class="form-group  ">
                                <label for="candidate_no">Candidate Number</label>
                                <input type="text" class="form-control" name="candidate_no" id="candidate_no"
                                    value="" />

                            </div>
                            <div class="form-group  ">
                                <label for="national_id">National ID</label>
                                <input type="text" class="form-control" name="national_id" id="national_id"
                                    value="" />
                            </div>
                            <div class="form-group">
                                <label class="control-label" for="candidate_surname">Surname</label>
                                <input type="text" class="form-control" name="candidate_surname" id="candidate_surname"
                                    value="" />

                            </div>
                            <div class="form-group">
                                <label class="control-label" for="candidate_other_name">Other name</label>
                                <input type="text" name="candidate_other_name" class="form-control"
                                    id="candidate_other_name" value="" />
                            </div>
                            <div class="form-group">
                                <label class="control-label" for="date_of_birth">Date of Birth</label>
                                <input type="date" name="date_of_birth" class="form-control" id="date_of_birth"
                                    value=" " />
                            </div>
                            <div class="form-group">
                                <label class="control-label" for="centre-name">Gender</label>
                                <label class="fancy-radio">
                                    <input name="gender" value="M" type="radio">
                                    <span><i></i>Male</span>
                                </label>
                                <label class="fancy-radio">
                                    <input name="gender" value="F" type="radio" />
                                    <span><i></i>Female</span>
                                </label>
                            </div>
                        </form>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="add-candidate" class="btn btn-primary"
                            id="save-candidate">Save</button>
                        <button type="button" class="btn btn-danger resetform" id="close"
                            data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>

        </div>
        <!--END ADD CANDIDATE MODEL -->

        <!-- ADD CANDIDATE MODAL -->
        <div class="modal fade bd-modal-md" id="change-candidate-modal" tabindex="-1" role="dialog"
            aria-hidden="true">
            <div class="modal-dialog ">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close resetform" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h3 class="modal-title">New Candidate</h3>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('admin.candidate-profile.updateCandidateNumber') }}" method="post"
                            id="changeCandidateNumberForm">
                            <div>
                                @csrf
                            </div>
                            <div class="form-group  ">
                                <label for="candidate_no">Candidate Number</label>
                                <input type="text" class="form-control" name="candidate_no" id="candidate_no"
                                    value="" />

                            </div>
                            <div class="form-group  ">
                                <label for="national_id">National ID</label>
                                <input type="text" class="form-control" name="national_id" id="national_id"
                                    value="" />
                            </div>


                            <div class="form-group  ">
                                <label for="national_id">New Candidate Number</label>
                                <input type="text" class="form-control" name="new_candidate_no" id="new_candidate_no"
                                    value="" />
                            </div>


                        </form>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="change-candidate-number" class="btn btn-primary"
                            id="change-candidate-number">Save</button>
                        <button type="button" class="btn btn-danger resetform" id="close"
                            data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>

        </div>
        <!--END ADD CANDIDATE MODEL -->
        <!-- END MAIN CONTENT -->

    </div>
    <!-- END MAIN -->
    <div class="clearfix"></div>
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

        //  Add Candidate
        $(document).on('click', '#save-candidate', function(ev) {
            ev.preventDefault();
            var url = $('#addCandidateForm').attr('action');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });


            var inputData = $("#addCandidateForm").serialize();

            $.ajax({
                url: url,
                method: "POST",
                data: inputData,
                success: function(data) {
                    if ($.isEmptyObject(data.errors)) {
                        $('#add-candidate').modal('hide');
                        $('#addCandidateForm .help-block').remove();
                        $('#addCandidateForm .has-error').removeClass('has-error');
                        toastr.success(data.success);
                        $('#candidates').DataTable().ajax.reload();
                    } else {
                        printErrorMsg('#addCandidateForm', data.errors);
                    }


                }
            });


        });


        $(document).on('click', '#change-candidate-number', function(ev) {
            ev.preventDefault();
            if (!confirm('Are You sure you want to change the candidate number?')) {
                return;
            } else {
                var url = $('#changeCandidateNumberForm').attr('action');
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                var inputData = $("#changeCandidateNumberForm").serialize();
                $.ajax({
                    url: url,
                    method: "POST",
                    data: inputData,
                    success: function(data) {
                        $('#changeCandidateNumberForm .help-block').remove();
                        $('#changeCandidateNumberForm .has-error').removeClass('has-error');
                        if ($.isEmptyObject(data.errors)) {
                            $('#new_candidate_no').val(data.new_candidate);
                            // $('#change-candidate-number').modal('hide');
                            toastr.success(data.success);
                            $('#candidates').DataTable().ajax.reload();
                        } else {
                            printErrorMsg('#changeCandidateNumberForm', data.errors);
                        }


                    }
                });


            }
        });

        // edit candidate
        $(document).on("click", "#candidates .editBtn", function() {
            //hide edit span

            $(this).closest("tr").find(".editSpan").hide();

            //show edit input
            $(this).closest("tr").find(".editInput").show();

            //hide edit button
            $(this).closest("tr").find(".editBtn").hide();

            //show edit button
            $(this).closest("tr").find(".saveBtn").show();
        });
        // update changes candidate
        $(document).on("click", "#candidates .saveBtn", function() {
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

                    if ($.isEmptyObject(data.errors)) {
                        trObj.find(".editInput").hide();
                        trObj.find(".saveBtn").hide();
                        trObj.find(".editSpan").show();
                        trObj.find(".editBtn").show();
                        toastr.success("You have successfully Saved Changes");
                        $('#candidates').DataTable().ajax.reload();
                    } else {
                        printErrorMsg(`#${ID}`, data.errors);
                    }

                },
            });
        });


        // delete Candidate
        $(document).on('click', '#candidates .deleteBtn', function(ev) {
            ev.preventDefault();
            var url = $(this).data('url');
            if (confirm("Are you sure you want to delete this candidates!") == true) {
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
                            $('#candidates').DataTable().ajax.reload();
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
