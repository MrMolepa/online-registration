@extends('layouts.school')

@section('content')
    <div id="page-wrapper">

        <div class="header">
            <h3 class="page-header">
                Markers
            </h3>
            <ol class="breadcrumb">
                <li><a href="javascript:void();">Home</a></li>
                <li class="active"><a href="javascript:void();">Markers</a></li>
            </ol>
        </div>
        <div id="page-inner" class="reports">
            <!-- List of reports available -->
            <div class="row">
                <div class="col-md-12">
                    <!-- Advanced Tables -->
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Invitations <button type="button" data-toggle="modal" id="action-btn"
                                data-target="#action-modal" class="btn  btn-primary">Action</button>
                        </div>
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table table-striped" id="data-table-invitations">
                                    <thead>
                                        <tr>
                                            <th><label><input type="checkbox" id="select-all"
                                                        name="select-all-invitations"></label>
                                            </th>
                                            <th>ID Number</th>
                                            <th>First name</th>
                                            <th>Last Names</th>
                                            <th>Email</th>
                                            <th>Phone Number</th>
                                            <th>Role</th>
                                            <th>Status</th>
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
    <div class="modal fade" id="action-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h5 class="modal-title">Principal Action</h5>
                </div>
                <div class="modal-body">
                    <form id="action-form" method="POST" action="{{ route('center.invigilators.store') }}">
                        @csrf
                        @method('POST')
                        <fieldset class="row  fieldset-border">
                            <legend class="fieldset-border">Personal Information</legend>
                            <div class="row">
                                <div class="form-group col-md-12">
                                    <label for="national_id" class="col-form-label">National ID</label>
                                    <input type="text" class="form-control"
                                        value="{{ optional($principal)->national_id }}" name="national_id" id="national_id"
                                        placeholder="National ID">
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="first_name" class="form-label">First Name</label>
                                    <input type="text" class="form-control" name="first_name" id="first_name"
                                        placeholder="First Name" value="{{ optional($principal)->first_name }}">

                                </div>
                                <div class="form-group col-md-6">
                                    <label for="last_name" class="form-label">Last Name
                                    </label>
                                    <input type="text" class="form-control" name="last_name" id="last_name"
                                        placeholder="last name" value="{{ optional($principal)->last_name }}">
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="invigilator_number" class="col-form-label">Phone
                                        Number</label>
                                    <input type="text" class="form-control" name="phone_number" id="phone_number"
                                        placeholder="" value="{{ optional($principal)->phone_number }}">

                                </div>
                                <div class="form-group col-md-6">
                                    <label for="invigilator_number" class="col-form-label">Email</label>

                                    <input type="text" class="form-control" name="email" id="email"
                                        placeholder="email address"value="{{ optional($principal)->email }}">
                                </div>
                            </div>
                        </fieldset>

                        <fieldset class="row  fieldset-border">
                            <legend class="fieldset-border">Declaration</legend>
                            <div class="form-group">

                            </div>
                            <div class="form-group row col-md-12">
                                <p> I declare that:
                                    <li style="margin-left: 2em;">The person appointed above is my teacher at my school.          |
                                     <label class="radio-inline">
                                    <input type="radio" name="action" value="approve">Accept
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="action" value="reject"> Decline
                                </label></li>
                                </p>

                                <br>
                                <label for="comments" class="col-form-label">Comments</label>
                                <textarea name="comments" class="form-control" id="comments" class="" cols="50" rows="5"></textarea>
                                <label for="declaration">
                                    <input type="checkbox" name="declaration" value="1" id="declaration" />
                                    I agree with terms and conditions
                                </label>

                            </div>
                        </fieldset>

                        <div class="clearfix"></div>

                    </form>
                </div>
                <div class="modal-footer ">
                    <button type="button" class="btn btn-primary" id="save-action">Save</button>
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
                // ID selector on Master Checkbox
                var checkedAll = "#select-all";
                var checkedItems = "[name='invitations[]']";
                $(document).on("change", checkedAll, function() {
                    $(checkedItems).prop("checked", $(this).prop("checked"));
                });
                $(document).on("click", checkedItems, function() {
                    let inputs = $(checkedItems).length;
                    let inputs_checked = $(checkedItems + ":checked").length;
                    if (inputs_checked <= 0) {

                        $(checkedAll).prop("checked", false);
                        $(checkedAll).prop("indeterminate", null);
                    } else if (inputs == inputs_checked) {
                        $(checkedAll).prop("checked", true);
                        $(checkedAll).prop("indeterminate", false);
                    } else {
                        $("#action-btn").css("display", "inline-block");
                        $(checkedAll).prop("checked", true);
                        $(checkedAll).prop("indeterminate", true);
                    }
                });
                // datatable
                var table = $('#data-table-invitations').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('center.invitations.index', 'markers') }}",
                    columns: [{
                            data: 'checkbox',
                            name: 'checkbox',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'recipient.national_id',
                            name: 'recipient.national_id'
                        },

                        {
                            data: 'recipient.first_name',
                            name: 'recipient.first_name'
                        },
                        {
                            data: 'recipient.last_name',
                            name: 'recipient.last_name'
                        },
                        {
                            data: 'recipient.email',
                            name: 'recipient.email'
                        },
                        {
                            data: 'recipient.phone_number',
                            name: 'recipient.phone_number'
                        },
                        {
                            data: 'role.name',
                            name: 'role.name'
                        },
                        {
                            data: 'status',
                            name: 'status'
                        },

                    ]
                });





                $(document).on('click', '#save-action', function(ev) {
                    ev.preventDefault();

                    if (confirm("Are You aa invitation to selected recipients?")) {
                        var invitations = [];
                        $("[name='invitations[]']:checked").each(function(i) {
                            invitations[i] = $(this).val();
                        });
                        if (invitations.length === 0) {
                            toastr.error("Please select atleast one recipients");
                        } else {


                            var actionForm = $("#action-form");
                            var url = actionForm.attr('action');

                            var $btn = $(this);

                            // Store original button text
                            var originalText = $btn.html();

                            // Disable button and show loader
                            $btn.prop('disabled', true).html(
                                '<i class="fas fa-spinner fa-spin"></i> Sending...'
                            );


                            var formData = actionForm.serializeArray();
                            invitations.forEach(function(invitation) {
                                formData.push({
                                    name: 'invitations[]',
                                    value: invitation
                                });
                            });

                            $.ajax({
                                type: "POST",
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                url: "{{ route('center.invitations.process') }}",
                                data: $.param(formData), // turn array into query string
                                success: function(data) {
                                    if ($.isEmptyObject(data.errors)) {
                                        $('#action-modal').modal('hide');
                                        $('#data-table-invitations').DataTable().ajax.reload();
                                        // ✅ Reset form after success
                                        actionForm[0].reset();
                                        toastr.success(data.success);
                                    } else {
                                        printErrorMsg("#action-form", data.errors)
                                    }

                                },
                                error: function(xhr) {
                                    toastr.error(xhr.responseJSON?.error ||
                                        'Something went wrong.');
                                },
                                complete: function() {
                                    // Restore original button text and re-enable
                                    $btn.prop('disabled', false).html(originalText);
                                }
                            });


                        }
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
        </script>
    @endpush
@endsection
