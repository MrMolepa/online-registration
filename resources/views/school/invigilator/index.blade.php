@extends('layouts.school')

@section('content')
    <div id="page-wrapper">

        <div class="header">
            <h1 class="page-header">
                Manage Invigilators
            </h1>
            <ol class="breadcrumb">
                <li><a href="javascript:void();">Home</a></li>
                <li class="active"><a href="javascript:void();">Invigilators</a></li>
            </ol>
        </div>
        <div id="page-inner" class="reports">

            <!-- List of reports available -->

            <div class="row">
                <div class="col-md-12">
                    <!-- Advanced Tables -->
                    <div class="panel panel-default">

                        <div class="panel-heading">
                            Allocated Invigilators
                            <div class="top-heading-invigilator">
                                <div class="allocated-text">Total students: <span
                                        class="text-span-color">{{ $totalcandidates }}</span></div>
                                @foreach ($invigilation_types as $invigilation_type)
                                    <div value="{{ $invigilation_type->id }}"> Required Invigilator:
                                        <span class="text-span-color"> {{ $invigilation_type->invigilator_number }}
                                            {{ $invigilation_type->name }}</span>
                                    </div>
                                @endforeach
                            </div>

                        </div>

                        <div class="panel-body ">
                            <button type="button" class="btn btn-primary" data-toggle="modal"
                                data-target="#add-center-modal">
                                Add Invigilator
                            </button>
                            <br>
                            <br>
                            <div class="table-responsive" id="table-view">
                                <table class="table table-striped" id="data-table-invigilation">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Invigilation Types</th>
                                            <th>ID Number</th>
                                            <th>Surname</th>
                                            <th>Other Names</th>
                                            <th>Gender</th>
                                            <th>Date Of Birth</th>
                                            <th>Qualification</th>
                                            <th>Email</th>
                                            <th>Phone Number</th>
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
                <!--End Advanced Tables -->
            </div>
        </div>
        <!-- end List of reports available -->

    </div>
    <!-- Modal add center-->
    <div class="modal fade" id="add-center-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Invigilator</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="center-add-form" method="post" action="{{ route('center.invigilators.store') }}">
                        @csrf
                        @method('POST')
                        <div class="form-group row">
                            <label for="invigilation_type" class="col-sm-12 col-form-label">Invigilation Type</label>
                            <div class="col-sm-12">
                                <select class="form-control" name="invigilation_role_id" id="invigilation_role_id">
                                    <option value="">Select</option>

                                    @foreach ($invigilation_types as $invigilation_type)
                                        {{-- @if (in_array($invigilation_type->id, $list_rules))


                                    @endif --}}

                                        <option value="{{ $invigilation_type->id }}">
                                            {{ $invigilation_type->name }}</option>
                                    @endforeach




                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="invigilator_number" class="col-sm-12 col-form-label">National ID</label>
                            <div class="col-sm-12">
                                <input type="number" class="form-control" name="national_id" id="national_id"
                                    placeholder="National ID">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="invigilator_number" class="col-sm-12 col-form-label">Surname</label>
                            <div class="col-sm-12">
                                <input type="text" class="form-control" name="surname" id="surname"
                                    placeholder="Surname">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="invigilator_number" class="col-sm-12 col-form-label">Other Names
                            </label>
                            <div class="col-sm-12">
                                <input type="text" class="form-control" name="other_names" id="other_names"
                                    placeholder="Other names">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="invigilator_number" class="col-sm-12 col-form-label">Phone Number</label>
                            <div class="col-sm-12">
                                <input type="number" class="form-control" name="phone_number" id="phone_number"
                                    placeholder="Phone numbers">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="invigilator_number" class="col-sm-12 col-form-label">Email</label>
                            <div class="col-sm-12">
                                <input type="text" class="form-control" name="email" id="email"
                                    placeholder="email address">
                            </div>
                        </div>

                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="add-center">Save</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Edit center-->
    <div class="modal fade" id="center-edit-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Update Invigilator</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="center-edit-form" method="POST" action="">
                        @csrf
                        @method('PUT')
                        <div class="form-group row">
                            <label for="invigilation_type" class="col-sm-12 col-form-label">Invigilation Type</label>
                            <div class="col-sm-12">
                                <select class="form-control" name="invigilation_role_id" id="invigilation_role_id">
                                    <option selected></option>

                                    @foreach ($invigilation_types as $invigilation_type)
                                        <option name="{{ $invigilation_type->id }}" value="{{ $invigilation_type->id }}">

                                            {{ $invigilation_type->name }}</option>
                                    @endforeach




                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="invigilator_number" class="col-sm-12 col-form-label">National ID</label>
                            <div class="col-sm-12">
                                <input type="text" class="form-control" name="national_id" id="national_id">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="invigilator_number" class="col-sm-12 col-form-label">Surname</label>
                            <div class="col-sm-12">
                                <input type="text" class="form-control" name="surname" id="surname">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="invigilator_number" class="col-sm-12 col-form-label">Other Names
                            </label>
                            <div class="col-sm-12">
                                <input type="text" class="form-control" name="other_names" id="other_names">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="invigilator_number" class="col-sm-12 col-form-label">Phone Number</label>
                            <div class="col-sm-12">
                                <input type="text" class="form-control" name="phone_number" id="phone_number">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="invigilator_number" class="col-sm-12 col-form-label">Email</label>
                            <div class="col-sm-12">
                                <input type="text" class="form-control" name="email" id="email">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="update-center">Update</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // TOASTER AND NOTIFICATION SETUP
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

            $('.modal').on('hidden.bs.modal', function(e) {
                $('form').trigger("reset");
            });
            $(document).ready(function() {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                // datatable
                var table = $('#data-table-invigilation').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('center.invigilators.index') }}",
                    columns: [{
                            data: 'id',
                            name: 'id'
                        },
                        {
                            data: 'invigilation_role.invigilation_type.name',
                            name: 'invigilation_role.invigilation_type.name'
                        },
                        {
                            data: 'national_id',
                            name: 'national_id'
                        },
                        {
                            data: 'surname',
                            name: 'surname'
                        },
                        {
                            data: 'other_names',
                            name: 'other_names'
                        },
                        {
                            data: 'gender',
                            name: 'gender'
                        },
                        {
                            data: 'date_of_birth',
                            name: 'date_of_birth'
                        },
                        {
                            data: 'qualification',
                            name: 'qualification'
                        },
                        {
                            data: 'email',
                            name: 'email'
                        },
                        {
                            data: 'phone_number',
                            name: 'phone_number'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        },

                    ]
                });

                //add
                $('#add-center').on('click', function(event) {
                    var addForm = $("#center-add-form");
                    var url = addForm.attr('action');
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: addForm.serialize(),
                        success: function(data) {
                            console.log(data)
                            if ($.isEmptyObject(data.errors)) {
                                $('#add-center-modal').modal('hide');
                                $('form').trigger("reset");
                                toastr.success(data.success);
                                $('#data-table-invigilation').DataTable().ajax.reload();
                            } else {
                                printErrorMsg('#add-center-modal', data.errors);
                            }
                        }


                    });
                });

                //edit
                $(document).on('click', '.edit-paymentmethods ', function() {
                    var url = $(this).data("url");
                    $.ajax({
                        type: "GET",
                        url: url,
                        success: function(data) {
                            $('#center-edit-modal').modal('show');

                            var invigilation = data.invigilation;
                            var url = data.url;

                            var form = '#center-edit-form';

                            $(`${form} input, ${form} select`).each(
                                function(index) {
                                    var input = $(this);
                                    console.log('Type: ' + input.attr('type') + 'Name: ' + input
                                        .attr(
                                            'name') +
                                        'Value: ' + input.val());
                                    var name = input.attr('name');

                                    $(`${form} #${name}`).val(invigilation[name]);
                                    $("#center-edit-form").attr('action', url);

                                }
                            );
                        },
                        error: function(data) {
                            console.log('Error:', data);
                        }
                    });
                });

                // Update
                $(document).on('click', '#update-center', function(e) {
                    var editForm = $("#center-edit-form");
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
                                $('#center-modal').modal('hide');
                                $('form').trigger("reset");
                                toastr.success(data.success);
                                $('#center-edit-form').DataTable().ajax.reload();
                            } else {
                                printErrorMsg('#center-modal', data.errors);
                            }


                        }
                    });
                });
                // Delete
                $(document).on('click', '.delete-paymentmethods', function() {

                    var url = $(this).data("url");
                    $.ajax({
                        type: "DELETE",
                        url: url,
                        success: function(data) {
                            //Refresh table
                            toastr.success(data.success);
                            table.draw();
                        },
                        error: function(data) {
                            console.log('Error:', data);
                        }
                    });
                });
                /****  Print errors*******/
                function printErrorMsg(parent, msg) {
                    $(`${parent} input, ${parent} select, textarea`).each(function(index) {
                        $(`${parent} .help-block`).remove();
                        $(`${parent} .has-error`).removeClass('has-error');

                    });
                    $.each(msg, function(key, errors) {
                        for (const error in errors) {
                            const value = errors[error];
                            $(`[name='${key}']`).parent().addClass('has-error');
                            $(`<span class='help-block'>${value}</span>`).insertAfter(
                                `${parent} [name='${key}']`)
                            if (key == 'subjects' || key == 'subject') {
                                $(".subjects-errors").find('span').css({
                                    "color": "#ff0000"
                                }).html(`<strong>${value}</strong>`);
                            }

                        }
                    });
                }
                /****  Print errors End*******/



            });
        </script>
    @endpush
@endsection
