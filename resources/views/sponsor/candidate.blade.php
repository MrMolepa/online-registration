@extends('layouts.sponsor')
@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Candidates</h3>

                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <fieldset class="col-md-12">
                                <legend>Filter</legend>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <!-- select -->
                                        <div class="form-group">
                                            <label>District</label>
                                            <select class="form-control" name="district" id="district">
                                                @foreach ($districts as $district)
                                                    <option value="{{ $district->district_code }}">{{ $district->district }}
                                                        -
                                                        {{ $district->district_code }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <!-- select -->
                                        <div class="form-group">
                                            <label>Centre Name</label>
                                            <select class="form-control" name="centre" id="centre">
                                                <option value="">Choose...</option>
                                            </select>
                                        </div>
                                    </div>


                                </div>
                            </fieldset>


                            <ul class="nav nav-pills ml-auto">
                                <li class="nav-item">
                                    <a class="nav-link active" href="#all-candidates" data-toggle="tab">All Candidates</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#declined-candidates" data-toggle="tab">Declined Candidates </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#pending-candidates" data-toggle="tab">Pending Candidates </a>
                                </li>
                            </ul>
                            <div class="tab-content p-2">
                                <!-- Morris chart - Sales -->
                                <div class="chart tab-pane active" id="all-candidates"
                                    style="position: relative;">

                                    <table class="table" name="tablename" id="candidates">
                                        <thead>
                                            <tr>
                                                <th>Centre No</th>
                                                <th>Nationa Id</th>
                                                <th>Candidate No</th>
                                                <th>Candidate Surname</th>
                                                <th>Candidate Other Name</th>
                                                <th>Date Of Birth</th>
                                                 <th>Gender</th>
                                                <th>Status</th>
                                                <th>Total Fee</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                    </table>
                                    @push('scripts')
                                        <script>
                                            $(function() {

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

                                                var candidates = $('#candidates').DataTable({
                                                    processing: true,
                                                    serverSide: true,
                                                    deferRender: true,
                                                    "lengthMenu": [
                                                        [20, 50, 100, 200, 400, -1],
                                                        [20, 50, 100, 200, 400, "All"]
                                                    ],
                                                    ajax: {
                                                        url: "{{ route('sponsor.candidate.index') }}",
                                                        data: function(d) {
                                                            d.centre = $("#centre").val();
                                                            d.level = $("#level").val();
                                                            d.district = $("#district").val();
                                                        }
                                                    },
                                                    columns: [{
                                                            data: 'center_no',
                                                            name: 'center_candidate.center_no',
                                                            searchable: true
                                                        },

                                                        {
                                                            data: 'national_id',
                                                            name: 'center_candidate.national_id',
                                                            searchable: true
                                                        }, {
                                                            data: 'candidate_no',
                                                            name: 'candidates.candidate_no',
                                                            searchable: true
                                                        },
                                                        {
                                                            data: 'candidate_surname',
                                                            name: 'candidates.candidate_surname',
                                                            searchable: true
                                                        },
                                                        {
                                                            data: 'candidate_other_name',
                                                            name: 'candidates.candidate_other_name',
                                                            searchable: true
                                                        },
                                                        {
                                                            data: 'date_of_birth',
                                                            name: 'candidates.date_of_birth',
                                                            searchable: true
                                                        },
                                                           {
                                                            data: 'gender',
                                                            name: 'candidates.gender',
                                                            searchable: true
                                                        },
                                                        {
                                                            data: 'status',
                                                            name: 'status',
                                                            searchable: false
                                                        },
                                                        {
                                                            data: 'price',
                                                            name: 'price',
                                                            searchable: false
                                                        },
                                                        {
                                                            data: 'actions',
                                                            name: 'actions',
                                                            searchable: false
                                                        },

                                                    ]

                                                });

                                                $("#candidates").css("width", "99.5%");

                                                var district = $("#district").val();
                                                var centre = $("#centre").val();

                                                getcenters(
                                                    district,
                                                    centre,
                                                    null
                                                )


                                                $("#district").on("change", function(event) {

                                                    var center = $("#centre").val();
                                                    district = $(this).val();
                                                    getcenters(
                                                        district,
                                                        centre,
                                                        null
                                                    );
                                                    $('#candidates').DataTable().ajax.reload(null, false);

                                                });

                                                $("#centre").on("change", function(event) {
                                                    center = $(this).val();
                                                    district = $('#district').val();
                                                    getcenters(
                                                        district,
                                                        centre,
                                                        event = event
                                                    );
                                                    $('#candidates').DataTable().ajax.reload(null, false);

                                                });
                                                /****  AJAX Main Function Who Perform All Tasks Start *******/
                                                function getcenters(
                                                    district,
                                                    center,
                                                    event = null
                                                ) {

                                                    $.ajaxSetup({
                                                        headers: {
                                                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                                        }
                                                    });

                                                    $.ajax({
                                                        url: "{{ route('sponsor.candidate.centers') }}",
                                                        method: "POST",
                                                        data: {
                                                            district: district,
                                                            center: center
                                                        },
                                                        success: function(data) {
                                                            var centers = data.centers
                                                            if (event == null) {
                                                                $('#centre').html(`<option value="">
                                                                                Please Select Centre
                                                                            </option>`);
                                                                centers.forEach(center => {
                                                                    $('#centre').append($('<option>').val(center
                                                                        .center_no).text(
                                                                        center
                                                                        .center_no +
                                                                        ' - ' + center.center_name));
                                                                });

                                                            }

                                                        },


                                                    });
                                                }
                                                /****  AJAX Main Function Who Perform All Tasks End *******/








                                            });
                                            /*****  Edit Candidate  *******/
                                            $(document).on("click", ".approval_btn", function() {
                                                var url = $(this).data("action");
                                                var i = 0;
                                                $.ajaxSetup({
                                                    headers: {
                                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                                    }
                                                });
                                                $.ajax({
                                                    url: url,
                                                    cache: false,
                                                    method: "GET",
                                                    beforeSend: function() {
                                                        // setting a timeout
                                                        // $(".preloader").fadeIn();
                                                        i++;
                                                    },
                                                    success: function(data) {
                                                        $("#approval-form").attr('action', data.action);
                                                        $("#modal-candidate-approval").modal("show");
                                                    },
                                                    complete: function() {

                                                    },
                                                });
                                            });
                                            /*****  Edit Candidate *******/



                                            /*****  Update Candidate *******/
                                            $("#approve-candidate").click(function(ev) {
                                                ev.preventDefault();
                                                var i=0
                                                var caption = $(this).text()
                                                var action = $("#approval-form").attr('action');
                                                $.ajaxSetup({
                                                    headers: {
                                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                                    }
                                                });
                                                $.ajax({
                                                    url: action,
                                                    method: "PUT",
                                                    beforeSend: function() {
                                                        $(this).prop('disabled', true).text("Processing...")
                                                        i++;
                                                    },
                                                    data: $("#approval-form").serialize(),
                                                    success: function(data) {
                                                        console.log(data);
                                                        if ($.isEmptyObject(data.errors)) {
                                                            if ($.isEmptyObject(data.error)) {
                                                                $('#candidates').DataTable().ajax.reload(null, false);
                                                                toastr.success(data.success);
                                                            } else {
                                                                toastr.error(data.error);
                                                            }
                                                            $(this).prop('disabled', false).text(caption)
                                                            $("#modal-candidate-approval").modal("hide");
                                                            $('#approval-form').trigger("reset");
                                                        } else {
                                                            printErrorMsg('#approval-form', data.errors);
                                                            $(this).text(caption);
                                                            $(this).prop('disabled', false).text(caption)
                                                        }
                                                    },
                                                    complete: function() {
                                                        i--;
                                                        if (i <= 0) {
                                                            $(this).prop('disabled', false).text(caption)
                                                        }

                                                    },
                                                });
                                            });
                                            /*****  End Update Candidate subject and sponso *******/


                                            $(document).on('input', `input,select,textarea`, function() {
                                                $(`.invalid-feedback`).remove();
                                                $(`.is-invalid`).removeClass('is-invalid');
                                            });

                                            /****  Print errors*******/
                                            function printErrorMsg(parent, msg) {
                                                $(`${parent} input, ${parent} select, textarea`).each(function(index) {
                                                    $(`${parent} .invalid-feedback`).remove();
                                                    $(`${parent} .is-invalid`).removeClass('is-invalid');
                                                    // console.log(input.attr('type') + 'Name: ' + input.attr('name') + '  Value: ' + input.val());
                                                });
                                                $.each(msg, function(key, errors) {
                                                    for (const error in errors) {
                                                        const value = errors[error];
                                                        $(`[name='${key}']`).addClass('is-invalid');
                                                        $(`<span class='invalid-feedback'>${value}</span>`).insertAfter(
                                                            `${parent} [name='${key}']`)

                                                    }
                                                });
                                            }
                                            /****  Print errors End*******/
                                        </script>
                                    @endpush



                                </div>
                                <div class="tab-pane" id="declined-candidates" style="position: relative;">
                                    <table class="table" name="tablename" id="declined-candidate-datatable">
                                        <thead>
                                            <tr>
                                                <th>Centre No</th>
                                                <th>Centre Name</th>
                                                <th>Candidate No</th>
                                                <th>Candidate Surname</th>
                                                <th>Candidate Other Name</th>
                                                <th>Date Of Birth</th>
                                                <th>Gender</th>
                                            </tr>
                                        </thead>
                                    </table>
                                    @push('scripts')
                                        <script>
                                            $(function() {
                                                var declined_candidate_datatable = $('#declined-candidate-datatable').DataTable({
                                                    processing: true,
                                                    serverSide: true,
                                                    deferRender: true,
                                                    "lengthMenu": [
                                                        [20, 50, 100, 200, 400, -1],
                                                        [20, 50, 100, 200, 400, "All"]
                                                    ],
                                                    ajax: {
                                                        url: "{{ route('sponsor.candidate.index') }}",
                                                        data: function(d) {
                                                            d.centre = $("#centre").val();
                                                            d.level = $("#level").val();
                                                            d.district = $("#district").val();
                                                            d.declined_candidates = 'declined_candidates';
                                                        }
                                                    },
                                                    columns: [{
                                                            data: 'center_no',
                                                            name: 'center_candidate.center_no',
                                                            searchable: true
                                                        },

                                                        {
                                                            data: 'center_name',
                                                            name: 'centers.center_name',
                                                            searchable: true
                                                        }, {
                                                            data: 'candidate_no',
                                                            name: 'candidates.candidate_no',
                                                            searchable: true
                                                        },
                                                        {
                                                            data: 'candidate_surname',
                                                            name: 'candidates.candidate_surname',
                                                            searchable: true
                                                        },
                                                        {
                                                            data: 'candidate_other_name',
                                                            name: 'candidates.candidate_other_name',
                                                            searchable: true
                                                        },
                                                        {
                                                            data: 'date_of_birth',
                                                            name: 'candidates.date_of_birth',
                                                            searchable: true
                                                        },
                                                           {
                                                            data: 'gender',
                                                            name: 'candidates.gender',
                                                            searchable: true
                                                        },
                                                    ]

                                                });
                                                $("#declined-candidate-datatable").css("width", "99.5%");


                                            });







                                        </script>
                                    @endpush
                                </div>
                                <div class="tab-pane" id="pending-candidates" style="position: relative;">

                                    <table class="table" name="tablename" id="pending-candidate-datatable">
                                        <thead>
                                            <tr>
                                                <th>Centre No</th>
                                                <th>Centre Name</th>
                                                <th>Candidate No</th>
                                                <th>Candidate Surname</th>
                                                <th>Candidate Other Name</th>
                                                <th>Date Of Birth</th>
                                                <th>Gender</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                    </table>
                                    @push('scripts')
                                        <script>
                                            $(function() {

                                                var pending_candidate_datatable = $('#pending-candidate-datatable').DataTable({
                                                    processing: true,
                                                    serverSide: true,
                                                    deferRender: true,
                                                    "lengthMenu": [
                                                        [20, 50, 100, 200, 400, -1],
                                                        [20, 50, 100, 200, 400, "All"]
                                                    ],
                                                    ajax: {
                                                        url: "{{ route('sponsor.candidate.index') }}",
                                                        data: function(d) {
                                                            d.centre = $("#centre").val();
                                                            d.level = $("#level").val();
                                                            d.district = $("#district").val();
                                                            d.pending_candidates = 'pending_candidates';
                                                        }
                                                    },
                                                    columns: [{
                                                            data: 'center_no',
                                                            name: 'center_candidate.center_no',
                                                            searchable: true
                                                        },

                                                        {
                                                            data: 'center_name',
                                                            name: 'centers.center_name',
                                                            searchable: true
                                                        }, {
                                                            data: 'candidate_no',
                                                            name: 'candidates.candidate_no',
                                                            searchable: true
                                                        },
                                                        {
                                                            data: 'candidate_surname',
                                                            name: 'candidates.candidate_surname',
                                                            searchable: true
                                                        },
                                                        {
                                                            data: 'candidate_other_name',
                                                            name: 'candidates.candidate_other_name',
                                                            searchable: true
                                                        },
                                                        {
                                                            data: 'date_of_birth',
                                                            name: 'candidates.date_of_birth',
                                                            searchable: true
                                                        },
                                                           {
                                                            data: 'gender',
                                                            name: 'candidates.gender',
                                                            searchable: true
                                                        },
                                                        {
                                                            data: 'status',
                                                            name: 'status',
                                                            searchable: false
                                                        },
                                                    ]

                                                });
                                                $("#pending-candidate-datatable").css("width", "99.5%");




                                            });







                                        </script>
                                    @endpush







                                </div>
                            </div>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
    <div class="modal fade" id="modal-candidate-approval">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Approval</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" method="post" id="approval-form">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="action">Action</label>
                            <select class="custom-select rounded-0" id="action" name="action">
                                <option value="">Select action</option>
                                @foreach ($actions as $action)
                                    <option value="{{ $action->action_id }}">{{ $action->name }} - {{$action->description}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Comments</label>
                            <textarea class="form-control" name="comments" rows="3" placeholder="Enter ..."></textarea>
                        </div>
                    </form>

                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" id="approve-candidate" class="btn btn-primary">Save changes</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->
@endsection
