@extends('layouts.school')

@section('content')
    <div id="page-wrapper">

        <div class="header">
            <h3 class="page-header">
                Invigilators Allocation
            </h3>
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
                        <div class="panel-heading table-style">
                            <div class="top-heading-invigilator">
                                <div class="allocated-text">Total students (Highest Subject):
                                    <span class="text-span-color">
                                        {{ $totalcandidates }}
                                    </span>
                                </div>
                                <p></p>
                                <table>
                                    <tr>
                                        <th class="tablestype">Invigilator Type</th>
                                        <th>Allocated</th>
                                        <th>Assigned</th>
                                    </tr>
                                    <tr>
                                        <td>
                                            @foreach ($invigilation_types as $invigilation_type)
                                                <div value="{{ $invigilation_type->id }}">
                                                    <span>
                                                        {{ $invigilation_type->name }}(s)</span>
                                                </div>
                                            @endforeach
                                        </td>
                                        <td class="text-align">
                                            @foreach ($invigilation_types as $invigilation_type)
                                                <div value="{{ $invigilation_type->id }}"> :
                                                    <span>{{ $invigilation_type->invigilator_number }}
                                                    </span>
                                                </div>
                                            @endforeach
                                        </td>
                                        <td class="text-align">
                                            @foreach ($invigilation_types as $invigilation_type)
                                                <div value="{{ $invigilation_type->id }}">:
                                                    <span class="text-span-color"> </span>
                                                    <span>
                                                        @foreach ($list_rules as $list_rule)
                                                            @if ($list_rule->id == $invigilation_type->id)
                                                                <span class="text-span-color">
                                                                    {{ $list_rule->number_of_invigilators }}
                                                                </span>
                                                            @endif
                                                        @endforeach
                                                    </span>
                                                </div>
                                            @endforeach
                                        </td>
                                    </tr>
                                </table>
                            </div>

                        </div>

                        <div class="panel-body ">
                            <button type="button" class="btn btn-primary" data-toggle="modal"
                                data-target="#add-center-modal">
                                + Invigilator
                            </button>
                            <div class="pull-right">
                                <div class='status-tag auxiliar-low accepted'>
                                    <i class='highlight auxiliar-low'></i>
                                    <p class='status-tag__txt bac-l-stack-xs'>Accepted: {{ $acceptedNumber }}
                                    </p>
                                </div>
                                <div class='status-tag auxiliar-low pending'>
                                    <i class='highlight auxiliar-low'></i>
                                    <p class='status-tag__txt bac-l-stack-xs'>Pending: {{ $pendingNumber }}</p>
                                </div>
                                <div class='status-tag auxiliar-low declined'>
                                    <i class='highlight auxiliar-low'></i>
                                    <p class='status-tag__txt bac-l-stack-xs'>Declined: {{ $declinedNumber }}
                                    </p>
                                </div>
                            </div>
                            <br>
                            <br>
                            <div class="table-responsive" id="table-view">
                                <table class="table table-striped" id="data-table-invigilation">
                                    <thead>
                                        <tr>
                                            <th>ID Number</th>
                                            <th>Invigilation Types</th>
                                            <th>Surname</th>
                                            <th>Other Names</th>
                                            <th>Email</th>
                                            <th>Phone Number</th>
                                            <th>Status</th>
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
    <div class="modal fade" id="add-center-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h5 class="modal-title">Add Invigilator Offer</h5>
                </div>
                <div class="modal-body">
                    <form id="center-add-form" method="POST" action="{{ route('center.invigilators.store') }}">
                        @csrf
                        @method('POST')
                        <fieldset class="row  fieldset-border">
                            <legend class="fieldset-border">Personal Information</legend>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="invigilation_type" class="col-form-label">Invigilation Type</label>

                                    <select class="form-control" name="invigilation_role_id" id="invigilation_role_id">
                                        <option value="">Select</option>

                                        @foreach ($invigilation_types as $invigilation_type)
                                            <option value="{{ $invigilation_type->id }}">
                                                {{ $invigilation_type->catergory_name }} {{ $invigilation_type->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="invigilator_number" class="col-form-label">National ID</label>
                                    <input type="text" class="form-control" name="national_id" id="national_id"
                                        placeholder="National ID">
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="invigilator_number" class="form-label">Surname</label>
                                    <input type="text" class="form-control" name="surname" id="surname"
                                        placeholder="Surname">

                                </div>
                                <div class="form-group col-md-6">
                                    <label for="invigilator_number" class="form-label">Other Names
                                    </label>
                                    <input type="text" class="form-control" name="other_names" id="other_names"
                                        placeholder="Other names">

                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="invigilator_number" class="col-form-label">Phone
                                        Number</label>
                                    <input type="number" class="form-control" name="phone_number" id="phone_number"
                                        placeholder="">

                                </div>
                                <div class="form-group col-md-6">
                                    <label for="invigilator_number" class="col-form-label">Email</label>

                                    <input type="text" class="form-control" name="email" id="email"
                                        placeholder="email address">

                                </div>
                            </div>
                        </fieldset>
                        <fieldset class="row  fieldset-border">
                            <legend class="fieldset-border">Selection Criteria</legend>

                            <div class="form-group row">
                                <label for="invigilation_type" class="col-md-2 form-label">Experience</label>
                                <div class="col-md-4">
                                    <select class="form-control " name="experience_id" id="experience_id">
                                        <option value="">Select</option>
                                        @foreach ($invigilator_experiences as $invigilator_experience)
                                            <option value="{{ $invigilator_experience->id }}">
                                                {{ $invigilator_experience->years }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-check col-md-6">
                                    <label for="description" class="col-md-5 form-label">Invigilator
                                        Availability</label>
                                    <input class="form-check-input" type="checkbox" value="1" name="accessibility"
                                        id="accessibility">
                                </div>

                            </div>
                            <div class="form-group row">

                                <div class="form-check col-md-6">
                                    <label for="description" class="col-md-4 col-form-label">Invigilator Integrity</label>
                                    <input class="form-check-input" type="checkbox" value="1" name="integrity"
                                        id="integrity">
                                </div>
                                <div class="form-check col-md-6">
                                    <label for="description" class="col-md-5 form-label">Invigilator Induction</label>
                                    <input class="form-check-input" type="checkbox" value="1" name="workshop"
                                        id="workshop">
                                </div>
                            </div>

                        </fieldset>


                        <fieldset class="row  fieldset-border">
                            <legend class="fieldset-border">Declaration</legend>
                            <div class="form-group row col-md-12">
                                <p> I declare that:
                                    <li>I declare that I have appointed above person to invigilate at our center.</li>
                                    <br>
                                </p>
                                <div class="row col-sm-8">
                                    <input type="checkbox" class="col-sm-1" name="principal_declare" value="1"
                                        id="principal_declare" />
                                    <label class="col-sm-7" for="principal_declare"> I agree with terms and
                                        conditions </label>
                                </div>
                            </div>
                        </fieldset>

                        <div class="clearfix"></div>

                    </form>
                </div>
                <div class="modal-footer ">

                    <button type="button" class="btn btn-primary" id="add-center"> <i
                            class="fas fa-spinner fa-spin hidden loadingSpinnersave"></i><span> Save
                        </span></button>

                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Edit center-->
    <div class="modal fade" id="center-edit-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog  modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Update Invigilator Offer</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="center-edit-form" method="POST" action="">
                        @csrf
                        @method('PUT')
                        <fieldset class="row  fieldset-border">
                            <legend class="fieldset-border">Personal Information</legend>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="invigilation_type" class="col-form-label">Invigilation Type</label>

                                    <select class="form-control" name="invigilation_role_id" id="invigilation_role_id">
                                        <option value="">Select</option>

                                        @foreach ($invigilation_types as $invigilation_type)
                                            <option value="{{ $invigilation_type->id }}">
                                                {{ $invigilation_type->catergory_name }} {{ $invigilation_type->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="invigilator_number" class="col-form-label">National ID</label>
                                    <input type="text" class="form-control" name="national_id" id="national_id"
                                        placeholder="National ID">
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="invigilator_number" class="form-label">Surname</label>
                                    <input type="text" class="form-control" name="surname" id="surname"
                                        placeholder="Surname">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="invigilator_number" class="form-label">Other Names
                                    </label>
                                    <input type="text" class="form-control" name="other_names" id="other_names"
                                        placeholder="Other names" maxlength="2">
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="invigilator_number" class="col-form-label">Phone Number</label>

                                    <input type="number" class="form-control" name="phone_number" id="phone_number"
                                        placeholder="Phone numbers">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="invigilator_number" class="col-form-label">Email</label>

                                    <input type="text" class="form-control" name="email" id="email"
                                        placeholder="email address">
                                </div>
                            </div>
                        </fieldset>
                        <fieldset class="row  fieldset-border">
                            <legend class="fieldset-border">Selection Criteria</legend>

                            <div class="form-group row">
                                <label for="invigilation_type" class="col-md-2 form-label">Experience</label>
                                <div class="col-md-4">
                                    <select class="form-control " name="experience_id" id="experience_id">
                                        <option value="">Select</option>
                                        @foreach ($invigilator_experiences as $invigilator_experience)
                                            <option value="{{ $invigilator_experience->id }}">
                                                {{ $invigilator_experience->years }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-check col-md-6">
                                    <label for="description" class="col-md-5 form-label">Invigilator
                                        Availability</label>
                                    <input class="form-check-input" type="checkbox" value="1" name="accessibility"
                                        id="accessibility">
                                </div>

                            </div>
                            <div class="form-group row">

                                <div class="form-check col-md-6">
                                    <label for="description" class="col-md-4 col-form-label">Invigilator Integrity</label>
                                    <input class="form-check-input" type="checkbox" value="1" name="integrity"
                                        id="integrity">
                                </div>
                                <div class="form-check col-md-6">
                                    <label for="description" class="col-md-5 form-label">Invigilator Induction</label>
                                    <input class="form-check-input" type="checkbox" value="1" name="workshop"
                                        id="workshop">
                                </div>
                            </div>

                        </fieldset>

                        {{-- Resend email --}}
                        <fieldset class="row  fieldset-border">
                            <legend class="fieldset-border">Resend Invigilator Offer</legend>
                            <div class="row">
                                <div class="form-group">
                                    <label class="col-md-2"> Resend Email</label>
                                    <input type="checkbox" class="col-md-1" name="resend_token" id="resend_token"
                                        value="1" />
                                </div>
                                <br>
                                <label class="col-md-12">
                                    You will send Invigilator Offer via email by tick above box.
                                </label>



                            </div>
                        </fieldset>
                        {{-- Resend email --}}
                        <fieldset class="row  fieldset-border">
                            <legend class="fieldset-border">Declaration</legend>
                            <div class="form-group row col-md-12">
                                <p> I declare that:
                                    <li>I declare that I have appointed above person to invigilate at out center.</li>
                                    <br>
                                </p>
                                <div class="row col-sm-8">
                                    <input type="checkbox" class="col-sm-1" name="principal_declare" value="1"
                                        id="principal_declare" />
                                    <label class="col-sm-7" for="principal_declare">
                                        You will send Invigilator Offer via email by tick above box.
                                    </label>
                                </div>
                            </div>
                        </fieldset>

                        <div class="clearfix"></div>
                    </form>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="update-center"> <i
                            class="fas fa-spinner fa-spin hidden loadingSpinnersave"></i><span> Update </span></button>
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
                            data: 'national_id',
                            name: 'national_id'
                        },
                        {
                            data: 'invigilation_role.invigilation_type.name',
                            name: 'invigilation_role.invigilation_type.name'
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
                            data: 'email',
                            name: 'email'
                        },
                        {
                            data: 'phone_number',
                            name: 'phone_number'
                        },

                        {
                            data: 'status',
                            name: 'status'
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
                    var captionsave = $('span', this).html();
                    var $button = $(this);
                    $button.prop('disabled', true);
                    var i = 0;
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: addForm.serialize(),
                        beforeSend: function() {
                            $(".loadingSpinnersave").removeClass('hidden');
                            $button.find("span").html('Saving..');
                            $button.prop('disabled', true);
                            i++;
                        },
                        success: function(data) {
                            console.log(data);
                            if ($.isEmptyObject(data.errors)) {
                                $('#add-center-modal').modal('hide');
                                toastr.success(data.success);
                                $('#data-table-invigilation').DataTable().ajax.reload();
                                addForm[0].reset();
                            } else {
                                printErrorMsg('#add-center-modal', data.errors);
                            }
                            $(".loadingSpinnersave").addClass('hidden');
                        },
                        complete: function() {
                            i--;
                            if (i <= 0) {
                                $(".loadingSpinner").addClass('hidden');
                                $button.find("span").html(captionsave);
                                $button.prop('disabled', false);
                            }
                        },
                    });
                });
                //edit
                $(document).on('click', '.edit-center', function() {
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

                                    if (input.attr('type') == "checkbox") {
                                        $(`${form} #${name}`).attr("checked", invigilation[
                                            name] == 1 ? true : false);
                                    } else {
                                        $(`${form} #${name}`).val(invigilation[name]);
                                    }



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
                    e.preventDefault();
                    var editForm = $("#center-edit-form");
                    var url = editForm.attr('action');
                    var captionsave = $('span', this).html();
                    var $button = $(this);
                    $button.prop('disabled', true);
                    $.ajax({
                        type: "POST",
                        data: editForm.serializeArray(),
                        url: url,
                        beforeSend: function() {
                            $(".loadingSpinnersave").removeClass('hidden');
                            $button.find("span").html('Updating..');
                            $button.prop('disabled', true);
                        },
                        success: function(data) {
                            if ($.isEmptyObject(data.errors)) {
                                $('#center-edit-modal').modal('hide');
                                toastr.success(data.success);
                                $('#data-table-invigilation').DataTable().ajax.reload();
                                editForm[0].reset();
                            } else {
                                printErrorMsg('#center-edit-form', data.errors);
                            }
                        },
                        complete: function() {
                            $(".loadingSpinnersave").addClass('hidden');
                            $button.find("span").html(captionsave);
                            $button.prop('disabled', false);
                        },
                        error: function(xhr, status, error) {
                            toastr.error('An error occurred. Please try again.');
                        }
                    });
                });
                // Delete
                $(document).on('click', '.delete-center', function() {

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
                // Disable submit button
                $('#principal_declare').change(function() {
                    handleDisable(this);
                });

                function handleDisable(elm) {
                    $('#add-center').attr('disabled', !elm.checked);
                }
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
            /**** Limit number of ID Number *****/

            $('#nationaid').keydown(function(e) {
                var max_chars = 12;
                if ($(this).val().length >= max_chars) {
                    $(this).val($(this).val().substr(0, max_chars));
                }
            });

            $('#input').keyup(function(e) {
                if ($(this).val().length >= max_chars) {
                    $(this).val($(this).val().substr(0, max_chars));
                }
            });
            /**** Limit number of phone Number *****/
            $('#phone_number').keydown(function(e) {
                var max_chars = 8;
                if ($(this).val().length >= max_chars) {
                    $(this).val($(this).val().substr(0, max_chars));
                }
            });
            $('#input').keyup(function(e) {
                if ($(this).val().length >= max_chars) {
                    $(this).val($(this).val().substr(0, max_chars));
                }
            });
            //**** Phone number format ******/
        </script>
    @endpush
@endsection
