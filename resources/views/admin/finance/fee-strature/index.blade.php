@extends('layouts.admin')

@section('content')
    <!-- ============================================================== -->
    <!-- Start Page Content -->
    <!-- ============================================================== -->
    <div class="main">
        <!-- MAIN CONTENT -->
        <div class="main-content">
            <div class="container-fluid">
                <h3 class="page-title">Fee Setup</h3>
                <div class="row">
                    <div class="col-md-12">
                        <!-- PANEL NO CONTROLS -->
                        <div class="panel">
                            <div class="panel-heading">
                                <h3 class="panel-title">Fee Setup</h3>
                            </div>
                            <div class="panel-body">

                                <div class="custom-tabs-line tabs-line-bottom left-aligned">
                                    <ul class="nav" role="tablist">
                                        <li class="active"><a href="#fee-type-tab" role="tab" data-toggle="tab">Fee
                                                Type</a></li>
                                        <li>
                                            <a href="#fee-group-tab" role="tab" data-toggle="tab">Fee Group</a>
                                        </li>
                                        <li>
                                            <a href="#fee-fine-tab" role="tab" data-toggle="tab">Fee
                                                Fine</a>
                                        </li>

                                    </ul>
                                </div>
                                <div class="tab-content">
                                    <div class="tab-pane fade in active" id="fee-type-tab">
                                        <button type="button" data-toggle="modal" data-target="#add-fee-type"
                                            class="btn btn-primary"> + Fee Type</button>
                                        <div class="table-responsive">
                                            <table class="table" name="tablename" id="fee-type">
                                                <thead>
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>Fee Code</th>
                                                        <th>Fee Name</th>
                                                        <th>Fee Description</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="fee-group-tab">
                                        <button type="button" class="btn btn-primary" data-toggle="modal"
                                            data-target="#add-group-modal">
                                            + Add Fee Group
                                        </button>

                                        <table class="table table-striped" id="data-table-fee-group">
                                            <thead>
                                                <tr>
                                                    <th></th>
                                                    <th>Group Name</th>
                                                    <th>Description</th>
                                                    <th>Candidate Type</th>
                                                    <th>Level</th>
                                                    <th>Session</th>
                                                    <th>Year</th>
                                                    <th>Fee Detail</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>


                                    </div>

                                    <div class="tab-pane fade" id="fee-fine-tab">
                                        <button type="button" class="btn btn-primary" data-toggle="modal"
                                            data-target="#add-fine-modal">
                                            + Add Fee Fine
                                        </button>
                                        <table class="table table-striped" id="data-table-fine">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Group Name</th>
                                                    <th>Fee Type</th>
                                                    <th>Fine Type</th>
                                                    <th>Amount</th>
                                                    <th>Late Fee Frequency</th>
                                                    <th>Start Date</th>
                                                    <th>End Date</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>

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


    <!-- Modal add-->
    <div class="modal fade" id="add-group-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Fee Group </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="add-group-form" method="post" action="{{ route('admin.fees-stracture.groups.store') }}">
                        @csrf
                        <fieldset class="fieldset-border">
                            <legend class="fieldset-border">Fee Group Setup</legend>
                            <div class="form-group">
                                <label for="name" class="form-label">Group
                                    Name</label>
                                <input type="text" class="form-control" name="name" id="name"
                                    placeholder="Group Name">
                            </div>

                            <div class="form-group">
                                <label for="type" class="form-label">Candidate
                                    Type</label>
                                <select class="form-control" id="candidate_type" name="candidate_type">
                                    <option value="" selected>Select</option>
                                    <option value="1">Type 1</option>
                                    <option value="2">Type 2</option>
                                    <option value="3">Type 3</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="session" class="form-label">Session</label>
                                <select class="form-control" id="session_id" name="session_id">
                                    <option value="">Select</option>
                                    @foreach ($sessions as $session)
                                        <option value="{{ $session->id }}">
                                            {{ $session->session }} -
                                            {{ $session->financial_year }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="level" class="form-label">Level</label>
                                <select class="form-control" id="level_id" name="level_id">
                                    <option value="">Select</option>
                                    @foreach ($levels as $level)
                                        <option value="{{ $level->id }}">
                                            {{ $level->level }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="description" class="form-label">Group
                                    Description</label>
                                <textarea class="form-control" name="description" id="description" placeholder="Group Description" cols="30"
                                    rows="5"></textarea>
                            </div>

                            <div class="clearfix"></div>
                        </fieldset>


                    </form>
                </div>
                <div class="modal-footer">

                    <button type="button" class="btn btn-primary" id="add-group">Save</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Edit-->
    <div class="modal fade" id="edit-group-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Fee Group</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="edit-group-form" method="POST" action="">
                        @csrf
                        @method('PUT')
                        <fieldset class="fieldset-border">
                            <legend class="fieldset-border">Fee Group Setup</legend>
                            <div class="form-group">
                                <label for="name" class="form-label">Group
                                    Name</label>
                                <input type="text" class="form-control" name="name" id="name"
                                    placeholder="Group Name">
                            </div>

                            <div class="form-group">
                                <label for="type" class="form-label">Candidate
                                    Type</label>
                                <select class="form-control" id="candidate_type" name="candidate_type">
                                    <option value="" selected>Select</option>
                                    <option value="1">Type 1</option>
                                    <option value="2">Type 2</option>
                                    <option value="3">Type 3</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="session" class="form-label">Session</label>
                                <select class="form-control" id="session_id" name="session_id">
                                    <option value="">Select</option>
                                    @foreach ($sessions as $session)
                                        <option value="{{ $session->id }}">
                                            {{ $session->session }} -
                                            {{ $session->financial_year }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="level" class="form-label">Level</label>
                                <select class="form-control" id="level_id" name="level_id">
                                    <option value="">Select</option>
                                    @foreach ($levels as $level)
                                        <option value="{{ $level->id }}">
                                            {{ $level->level }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="description" class="form-label">Group
                                    Description</label>
                                <textarea class="form-control" name="description" id="description" placeholder="Group Description" cols="30"
                                    rows="5"></textarea>
                            </div>
                            <div class="clearfix"></div>
                        </fieldset>


                    </form>
                </div>
                <div class="modal-footer">

                    <button type="button" class="btn btn-primary" id="update-group">Update</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Add Modal-->
    <div class="modal fade" id="add-detail-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Fee Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="add-detail-form" method="POST" action="">
                        @csrf
                        <input type="hidden" name="form_group_id" id="form_group_id">
                        <div class="form-group">
                            <label for="fee_type_id" class="col-sm-12 col-form-label">Fee Type</label>
                            <select class="form-control" name="fee_type_id" id="fee_type_id">
                                <option value=""> select</option>
                                @foreach ($feetypes as $feetype)
                                    <option value="{{ $feetype->id }}">
                                        {{ $feetype->fee_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="radio-inline">

                                <input type="radio" value="L" name="key_type" class="keytype"> Level
                            </label>
                            <label class="radio-inline">

                                <input type="radio" value="S" name="key_type" class="keytype"> Subject
                            </label>
                            <label class="radio-inline">

                                <input type="radio" value="C" name="key_type" class="keytype">Component
                            </label>
                            <label class="radio-inline">
                                <input type="radio" value="O" name="key_type" class="keytype">Option
                            </label>
                        </div>
                        <div id="fee-details-subjects" class="form-group" class="keytype"></div>

                    </form>
                </div>
                <div class="modal-footer">

                    <button type="button" class="btn btn-primary" id="add-detail">Save</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Fee Type  Modal -->
    <div class="modal fade bd-modal-md" id="add-fee-type" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog ">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close resetform" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h3 class="modal-title"> Fee-Type </h3>
                </div>
                <div class="modal-body">
                    <form action="{{ route('admin.fees-stracture.types.store') }}" method="post" id="feetypeForm">
                        @csrf
                        <fieldset class="fieldset-border">
                            <legend class="fieldset-border">Fee Type Setup</legend>
                            <div class="form-group">
                                <label class="control-label" for="fee_code">Fee Code</label>
                                <input type="text" name="fee_code" class="form-control" id="fee_code">
                            </div>
                            <div class="form-group">
                                <label class="control-label" for="fee_name">Fee Name</label>
                                <input type="text" name="fee_name" class="form-control" id="fee_name">
                            </div>
                            <div class="form-group">
                                <label class="control-label" for="fee_description">Fee Description</label>
                                <textarea cols="30" rows="5" name="fee_description" class="form-control" id="fee_description"></textarea>

                            </div>
                        </fieldset>
                    </form>
                    <div class="clearfix"></div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="save-fee-type">Save</button>
                    <button type="button" class="btn btn-danger resetform" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


    {{-- Add modal --}}
    <div class="modal fade" id="add-fine-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Fine Fee</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="add-fine-form" method="post" action="{{ route('admin.fees-stracture.fines.store') }}">
                        @csrf
                        <fieldset class="row  fieldset-border">
                            <legend class="fieldset-border">Fine Fee Setup</legend>
                            <div class="row">
                                <div class="form-group  col-lg-12">
                                    <label for="group" class="form-label">Fee Group</label>
                                    <select class="form-control" id="fee_group_id" name="fee_group_id">
                                        <option value="">Select</option>
                                        @foreach ($feegroups as $feegroup)
                                            <option value="{{ $feegroup->id }}">
                                                {{ $feegroup->name }}- {{ $feegroup->session->session }}
                                                {{ $feegroup->session->financial_year }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group  col-lg-12">
                                    <label for="type" class="form-label">Fee Type</label>
                                    <select class="form-control" id="fee_type_id" name="fee_type_id">
                                        <option value="">Select</option>
                                        @foreach ($feetypes as $feetype)
                                            <option value="{{ $feetype->id }}">
                                                {{ $feetype->fee_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-lg-12">
                                    <label for="fine" class="form-label">Fine Type</label>
                                    <input type="text" class="form-control" name="fine_type" id="fine_type"
                                        placeholder="Fine Type">
                                </div>
                                <div class="form-group col-lg-12">
                                    <label for="fine" class="form-label">Fine Amount</label>
                                    <input type="text" class="form-control" name="fine_value" id="fine_value"
                                        placeholder="Fine Amount">
                                </div>
                                <div class="form-group  col-lg-4">
                                    <label for="late" class="form-label">Late Fee
                                        Frequency</label>
                                    <select class="form-control" id="fee_frequency_id" name="fee_frequency_id">
                                        <option value="">Select</option>
                                        @foreach ($frequencies as $frequency)
                                            <option value="{{ $frequency->id }}">
                                                {{ $frequency->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-sm-4">
                                    <label for="date" class="form-label">Start Date</label>
                                    <input type="date" class="form-control" name="start_date" id="start_date"
                                        placeholder="">
                                </div>
                                <div class="form-group col-sm-4">
                                    <label for="date" class="form-label">End Date</label>
                                    <input type="date" class="form-control" name="end_date" id="end_date"
                                        placeholder="">
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </fieldset>
                    </form>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id="add-fine">Save</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>

    </div>
    {{-- End modal --}}
    <div class="modal fade" id="edit-fine-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit Fine Fee</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="edit-fine-form" method="post" action="">
                        @csrf
                        @method('PUT')
                        <fieldset class="row  fieldset-border">
                            <legend class="fieldset-border">Fine Fee Setup</legend>
                            <div class="row">
                                <div class="form-group  col-lg-12">
                                    <label for="group" class="form-label">Fee Group</label>
                                    <select class="form-control" id="fee_group_id" name="fee_group_id">
                                        <option value="">Select</option>
                                        @foreach ($feegroups as $feegroup)
                                            <option value="{{ $feegroup->id }}">
                                                {{ $feegroup->name }}- {{ $feegroup->session->session }}
                                                {{ $feegroup->session->financial_year }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group  col-lg-12">
                                    <label for="type" class="form-label">Fee Type</label>
                                    <select class="form-control" id="fee_type_id" name="fee_type_id">
                                        <option value="">Select</option>
                                        @foreach ($feetypes as $feetype)
                                            <option value="{{ $feetype->id }}">
                                                {{ $feetype->fee_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-lg-12">
                                    <label for="fine" class="form-label">Fine Type</label>
                                    <input type="text" class="form-control" name="fine_type" id="fine_type"
                                        placeholder="Fine Type">
                                </div>
                                <div class="form-group col-lg-12">
                                    <label for="fine" class="form-label">Fine Amount</label>
                                    <input type="text" class="form-control" name="fine_value" id="fine_value"
                                        placeholder="Fine Amount">
                                </div>
                                <div class="form-group  col-lg-4">
                                    <label for="late" class="form-label">Late Fee
                                        Frequency</label>
                                    <select class="form-control" id="fee_frequency_id" name="fee_frequency_id">
                                        <option value="">Select</option>
                                        @foreach ($frequencies as $frequency)
                                            <option value="{{ $frequency->id }}">
                                                {{ $frequency->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-sm-4">
                                    <label for="date" class="form-label">Start Date</label>
                                    <input type="date" class="form-control" name="start_date" id="start_date"
                                        placeholder="">
                                </div>
                                <div class="form-group col-sm-4">
                                    <label for="date" class="form-label">End Date</label>
                                    <input type="date" class="form-control" name="end_date" id="end_date"
                                        placeholder="">
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </fieldset>
                    </form>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id="update-fine">Update</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>

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
        // data tables
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            // datatable
            var feetable = $('#data-table-fee-group').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.fees-stracture.groups.index') }}",
                columns: [{
                        className: 'dt-control',
                        orderable: false,
                        data: null,
                        defaultContent: ''
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'description',
                        name: 'description'
                    },
                    {
                        data: 'candidate_type',
                        name: 'candidate_type'
                    },
                    {
                        data: 'level.level',
                        name: 'level.level'
                    },
                    {
                        data: 'session.session',
                        name: 'session.session'
                    },

                    {
                        data: 'session.financial_year',
                        name: 'session.financial_year'
                    },
                    {
                        data: 'fee_details',
                        name: 'fee_details'

                    },
                    {
                        data: 'action',
                        name: 'action'
                    },
                ]
            });
            $("#data-table-fee-group").css("width", "100%");

            function format(data) {
                var feetypes = $.parseJSON(data.feetypes);
                var childRows = '';
                console.log(feetypes);
                $.each(feetypes, function(index, child) {
                    console.log(child['fee_name']);
                    childRows += `
                                <tr>
                                    <td>${child['fee_name']}</td>
                                    <td>${child['pivot']['subject_code']}</td>
                                    <td>${child['pivot']['option_code']}</td>
                                     <td>${child['pivot']['component_code']}</td>
                                    <td>${child['pivot']['amount']}</td>
                                    <td>${child['pivot']['actions']}</td>
                                </tr>
                            `;
                });
                return (
                    `<table class='table' id='fee-details-table'>
                            <thead>
                                <tr>
                                    <th scope='col'>Fee Type</th>
                                    <th scope='col'>Subject Code</th>
                                    <th scope='col'>Option Code</th>
                                    <th scope='col'>Component Code</th>
                                    <th scope='col'>Amount</th>
                                     <th scope='col'>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <td>${childRows}</td>
                            </tbody>
                        </table>`
                );
            }
            feetable.on('click', 'td.dt-control', function(e) {
                e.preventDefault();
                let tr = e.target.closest('tr');
                let row = feetable.row(tr);
                if (row.child.isShown()) {
                    row.child.hide();
                    $(tr).removeClass('shown');
                } else {
                    row.child(format(row.data())).show();
                    $(tr).addClass('shown');
                }
            });
            $("#data-table-fee-group").css("width", "100%");
            //add
            $('#add-group').on('click', function(event) {
                var addForm = $("#add-group-form");
                var url = addForm.attr('action');
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: addForm.serialize(),
                    success: function(data) {
                        console.log(data)
                        if ($.isEmptyObject(data.errors)) {
                            $('#add-group-modal').modal('hide');
                            toastr.success(data.success);
                            $('#add-group-form')[0].reset();
                            $('#data-table-fee-group').DataTable().ajax.reload();
                        } else {
                            printErrorMsg('#add-group-modal', data.errors);
                        }
                    }

                });
            })
            //edit
            $(document).on('click', '.edit-group', function() {
                var url = $(this).data("url");
                $.ajax({
                    type: "GET",
                    url: url,
                    success: function(data) {
                        $('#edit-group-modal').modal('show');
                        var feegroup = data.feegroup;
                        var url = data.url;
                        var form = '#edit-group-form';
                        $("#edit-group-form").attr('action', url);
                        $(`${form} input,${form} select,${form} textarea`).each(
                            function(index) {
                                var input = $(this);
                                var name = input.attr('name');
                                $(`${form} #${name}`).val(feegroup[name]);
                            }
                        );
                    },
                    error: function(data) {
                        console.log('Error:', data);
                    }
                });
            });
            // Update
            $(document).on('click', '#update-group', function() {
                var editForm = $("#edit-group-form");
                var url = editForm.attr('action');

                $.ajax({
                    type: "POST",
                    data: editForm.serializeArray(),
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: url,
                    success: function(data) {
                        if ($.isEmptyObject(data.errors)) {
                            $('#edit-group-modal').modal('hide');
                            toastr.success(data.success);
                            $('#data-table-fee-group').DataTable().ajax.reload();
                        } else {
                            printErrorMsg('#edit-group-modal', data.errors);
                        }


                    }
                });
            });
            // Delete
            $(document).on('click', '.delete-group', function() {
                var url = $(this).data("url");
                $.ajax({
                    type: "DELETE",
                    url: url,
                    success: function(data) {
                        //Refresh table
                        table.draw();
                    },
                    error: function(data) {
                        console.log('Error:', data);
                    }
                });
            });
            //Get Details
            $(document).on('click', '.btn-details', function() {
                var url = $(this).data("url");
                $.ajax({
                    type: "GET",
                    url: url,
                    success: function(data) {
                        console.log(data);
                        $('#add-detail-modal').modal('show');
                        var feedetails = data.feedetails;
                        // $('#fee-details-subjects').html(data.subjects)
                        var url = data.url;
                        var form = '#add-detail-form';
                        $("#add-detail-form").attr('action', url);

                        $(`${form} input, ${form} select`).each(

                            function(index) {
                                var input = $(this);
                                console.log('Type: ' + input.attr('type') + 'Name: ' + input
                                    .attr(
                                        'name') +
                                    'Value: ' + input.val());
                                var name = input.attr('name');

                            }
                        );

                        $('#form_group_id').val(feedetails.id);
                    },
                    error: function(data) {
                        console.log('Error:', data);
                    }
                });
            });
            //Add Details
            $('#add-detail').click(function() {
                var addForm = $("#add-detail-form");
                var url = addForm.attr('action');
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: addForm.serialize(),
                    success: function(data) {
                        console.log(data)
                        if ($.isEmptyObject(data.errors)) {
                            $('#add-detail-modal').modal('hide');
                            toastr.success(data.success);
                            $('#data-table-fee-group').DataTable().ajax.reload();
                        } else {
                            printErrorMsg('#add-detail-modal', data.errors);
                        }
                    }
                });
            });
            $(document).on('change', '.keytype', function() {
                var fee_group_id = $('#form_group_id').val();
                var key_type = $(this).val();
                feedetail(fee_group_id, key_type)
            });

            // edit timetable
            $(document).on("click", "#fee-details-table .editBtn", function() {
                //hide edit span

                $(this).closest("tr").find(".editSpan").hide();

                //show edit input
                $(this).closest("tr").find(".editInput").show();

                //hide edit button
                $(this).closest("tr").find(".editBtn").hide();

                //show edit button
                $(this).closest("tr").find(".saveBtn").show();
            });


            // save changes
            $(document).on("click", "#fee-details-table .saveBtn", function() {
                var url = $(this).data("url");
                var trObj = $(this).closest("tr");
                var inputData = $(this).closest("tr").find(".editInput").serialize();

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type: "POST",
                    url: url,
                    data: inputData,
                    success: function(response) {
                        trObj.find(".editInput").hide();
                        trObj.find(".saveBtn").hide();
                        trObj.find(".editSpan").show();
                        trObj.find(".editBtn").show();
                        toastr.success("You have successfully Saved Changes");
                    },
                });
            });


            $(document).on('click', '#fee-details-table .delete-detail ', function(ev) {
                ev.preventDefault();
                var url = $(this).data('url');
                if (confirm("Are you sure you want to delete this charges ?") == true) {
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
                                $('#fee-type').DataTable().ajax.reload();
                            }
                        }
                    });

                } else {
                    return;
                }
            });








            // datatable
            var fee_types = $('#fee-type').DataTable({
                processing: true,
                serverSide: true,
                deferRender: true,
                "lengthMenu": [
                    [20, 50, 100, 200, 400, -1],
                    [20, 50, 100, 200, 400, "All"]
                ],
                ajax: "{{ route('admin.fees-stracture.types.index') }}",
                columns: [{
                        data: 'id',
                        name: 'id',
                        searchable: true
                    },
                    {
                        data: 'fee_code',
                        name: 'fee_code',
                        searchable: true
                    },
                    {
                        data: 'fee_name',
                        name: 'fee_name',
                    },
                    {
                        data: 'fee_description',
                        name: 'fee_description',
                        searchable: true
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]

            });
            $("#fee-type").css("width", "100%");
            //  Add Fee Type
            $(document).on('click', '#save-fee-type', function(ev) {
                ev.preventDefault();
                var url = $('#feetypeForm').attr('action');
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var inputData = $("#feetypeForm").serialize();
                $.ajax({
                    url: url,
                    method: "POST",
                    data: inputData,
                    success: function(data) {
                        if ($.isEmptyObject(data.errors)) {
                            $('#add-fee-type').modal('hide');
                            toastr.success(data.success);
                            $('#feetypeForm')[0].reset();
                            $('#fee-type').DataTable().ajax.reload();

                        } else {
                            printErrorMsg('#feetypeForm', data.errors);
                        }
                    }
                });
            });
            //edit
            $(document).on('click', '.editBtn', function() {

                var url = $(this).data("url");
                $.ajax({
                    type: "GET",
                    url: url,
                    success: function(data) {
                        var feetype = data.feetype;
                        var url = data.url;
                        var form = '#EditFeeTypeForm';
                        $("#EditFeeTypeForm").attr('action', url);
                        $(`${form} input, ${form} select,${form} textarea`).each(
                            function(index) {
                                var input = $(this);
                                var name = input.attr('name');
                                $(`${form} #${name}`).val(feetype[name]);
                            }
                        );
                    },
                    error: function(data) {
                        console.log('Error:', data);
                    }
                });
            });
            // Update
            $(document).on('click', '#update-fee-type', function(e) {
                var editForm = $("#EditFeeTypeForm");
                var url = editForm.attr('action');

                $.ajax({
                    type: "POST",
                    data: editForm.serializeArray(),
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: url,
                    success: function(data) {
                        console.log();
                        if ($.isEmptyObject(data.errors)) {
                            toastr.success(data.success);
                            $('#fee-type').DataTable().ajax.reload();
                        } else {
                            printErrorMsg('#type-modal', data.errors);
                        }
                    }
                });
            });
            // delete fee
            $(document).on('click', '.deleteBtn', function(ev) {
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
                                $('#fee-type').DataTable().ajax.reload();
                            }
                        }
                    });

                } else {
                    return;
                }
            });






            // datatable
            var table = $('#data-table-fine').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.fees-stracture.fines.index') }}",
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'fee_group_id',
                        name: 'fee_group_id'
                    },
                    {
                        data: 'feetypes.fee_name',
                        name: 'feetypes.fee_name'
                    },
                    {
                        data: 'fine_type',
                        name: 'fine_type'
                    },
                    {
                        data: 'fine_value',
                        name: 'fine_value',
                        render: $.fn.dataTable.render.number(',', '.', 2, 'LSL')
                    },
                    {
                        data: 'frequencies.name',
                        name: 'frequencies.name'
                    },

                    {
                        data: 'start_date',
                        name: 'start_date',
                    },

                    {
                        data: 'end_date',
                        name: 'end_date',
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },

                ]
            });

            $("#data-table-fine").css("width", "100%");
            //add
            $('#add-fine').on('click', function(event) {
                var addForm = $("#add-fine-form");
                var url = addForm.attr('action');
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: addForm.serialize(),
                    success: function(data) {
                        console.log(data)
                        if ($.isEmptyObject(data.errors)) {
                            $('#add-fine-modal').modal('hide');
                            toastr.success(data.success);
                            addForm[0].reset();
                            $('#data-table-fine').DataTable().ajax.reload();

                        } else {
                            printErrorMsg('#add-fine-modal', data.errors);
                        }
                    }

                });
            })
            //edit
            $(document).on('click', '.edit-fine', function() {

                var url = $(this).data("url");
                $.ajax({
                    type: "GET",
                    url: url,
                    success: function(data) {
                        $('#edit-fine-modal').modal('show');
                        var fine = data.fine;
                        var url = data.url;

                        var form = '#edit-fine-form';

                        $(`${form} input, ${form} select`).each(
                            function(index) {
                                var input = $(this);
                                console.log('Type: ' + input.attr('type') + 'Name: ' + input
                                    .attr(
                                        'name') +
                                    'Value: ' + input.val());
                                var name = input.attr('name');


                                if (input.attr('type') == "checkbox") {
                                    $(`${form} #${name}`).attr("checked", fine[
                                        name] == 1 ? true : false);
                                } else {
                                    $(`${form} #${name}`).val(fine[name]);
                                }
                                $("#edit-fine-form").attr('action', url);

                            }
                        );
                    },
                    error: function(data) {
                        console.log('Error:', data);
                    }
                });
            });
            // Update
            $(document).on('click', '#update-fine', function(e) {
                var editForm = $("#edit-fine-form");
                var url = editForm.attr('action');

                $.ajax({
                    type: "POST",
                    data: editForm.serializeArray(),
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: url,
                    success: function(data) {
                        if ($.isEmptyObject(data.errors)) {
                            $('#edit-fine-modal').modal('hide');
                            toastr.success(data.success);
                            editForm[0].reset();
                            $('#data-table-fine').DataTable().ajax.reload();
                        } else {
                            printErrorMsg('#edit-fine-modal', data.errors);
                        }


                    }
                });
            });
            // Delete
            $(document).on('click', '.delete-fine', function() {
                var url = $(this).data("url");
                $.ajax({
                    type: "DELETE",
                    url: url,
                    success: function(data) {
                        //Refresh table
                        table.draw();
                    },
                    error: function(data) {
                        console.log('Error:', data);
                    }
                });
            });
















            function feedetail(fee_group_id, key_type) {
                var url = "{{ route('admin.fees-stracture.groups.detail') }}";
                $.ajax({
                    type: "GET",
                    url: url,
                    data: {
                        'fee_group_id': fee_group_id,
                        'key_type': key_type
                    },
                    success: function(data) {
                        $('#fee-details-subjects').html(data.output)
                        console.log(data);


                    },
                    error: function(data) {
                        console.log('Error:', data);
                    }
                });
            }
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
                        $(`${parent} [name='${key}']`).parent().addClass('has-error');
                        $(`<span class='help-block'>${value}</span>`).insertAfter(
                            `${parent} [name='${key}']`)

                    }
                });
            }
            /****  Print errors End*******/
            $(document).on('click', '.custom-checkbox', function() {
                $('input:checkbox').not(this).prop('checked', this.checked);
            });
        });
        //add
    </script>
@endsection
