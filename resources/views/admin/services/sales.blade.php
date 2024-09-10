@extends('layouts.admin')

@section('content')
    <!-- ============================================================== -->
    <!-- Start Page Content -->
    <!-- MAIN -->
    <div class="main">
        <!-- MAIN CONTENT -->
        <div class="main-content">
            <div class="container-fluid">
                <h3 class="page-title">All Paid Services </h3>

                <div class="row">
                    <div class="col-md-12">
                        <!-- PANEL NO CONTROLS -->
                        <div class="panel">
                            <div class="panel-heading">
                                <h3 class="panel-title">Paid Services</h3>
                            </div>
                            <div class="panel-body">
                                <fieldset>
                                    <legend>Filter</legend>
                                    <div class="pull-left col-md-4">
                                        <div class="input-group">
                                            <span class="input-group-btn">
                                                <button class="btn secondary" type="button">Service Name</button>
                                            </span>
                                            <select class="form-control status-dropdown" id="service-name">
                                                <option value="">Please Select Service</option>
                                                @foreach ($oneTimeServicesItems as $oneTimeServicesItem)
                                                    <option value="{{ $oneTimeServicesItem->id }}">
                                                        {{ $oneTimeServicesItem->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
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

                                <table class="table" name="tablename"id="sales">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>First Name</th>
                                            <th>Last Name</th>
                                            <th>Reference Number</th>
                                            <th>Applied Date</th>
                                            <th>Invoice Number</th>
                                            <th>Service</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>


                                </table>
                                @push('scripts')
                                    <script>
                                        $(function() {
                                            var service = $('#sales').DataTable({
                                                processing: true,
                                                serverSide: true,
                                                scrollY: 500,
                                                scrollX: true,
                                                scrollCollapse: true,
                                                deferRender: true,
                                                "lengthMenu": [
                                                    [20, 50, 100, 200, 400, -1],
                                                    [20, 50, 100, 200, 400, "All"]
                                                ],
                                                ajax: {
                                                    url: "{{ route('admin.service-sales.index') }}",
                                                    data: function(d) {
                                                        d.year = $("#year").val();
                                                        d.service = $("#service-name").val();
                                                    }
                                                },

                                                columns: [{
                                                        "className": 'dt-control',
                                                        data: 'status',
                                                        name: 'status',
                                                        "orderable": false,
                                                        "defaultContent": '',
                                                        searchable: false
                                                    },

                                                    {
                                                        data: 'first_name',
                                                        name: 'first_name',
                                                    },
                                                    {
                                                        data: 'last_name',
                                                        name: 'last_name',
                                                        searchable: true
                                                    },
                                                    {
                                                        data: 'reference_number',
                                                        name: 'reference_number',
                                                        searchable: true
                                                    },
                                                     {
                                                        data: 'created_at',
                                                        name: 'created_at',
                                                        searchable: true
                                                    },
                                                    {
                                                        data: 'reference_no',
                                                        name: 'reference_no',
                                                        searchable: true
                                                    },
                                                    {
                                                        data: 'name',
                                                        name: 'name',
                                                        searchable: true
                                                    },


                                                    {
                                                        data: 'actions',
                                                        name: 'actions',
                                                        searchable: false,
                                                        sortable: false
                                                    }

                                                ]

                                            });
                                            $("#sales").css("width", "98.5%");
                                            // Add event listener for opening and closing details
                                            $('#sales').on('click', 'td.dt-control', function() {
                                                var tr = $(this).closest('tr');
                                                var row = service.row(tr);
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



                                            /* Formatting function for row details - modify as you need */
                                            function format(d) {
                                                var attributes = $.parseJSON(d.service_attributes);
                                                var requirements = d.requirements;
                                                var requirementHTML = "";
                                                $.each(requirements, function(requirement_key, requirements_val) {
                                                    if (attributes.filter(attribute => attribute.code === requirement_key && attribute
                                                            .frontend_type === "file").length > 0) {
                                                        requirementHTML += `<tr>
                                                                                <td> ${requirement_key} :</td>
                                                                                <td><a href='{{ asset('${requirements_val}') }}' 'data-toggle='tooltip' target="_blank" title='ggg'class='btn btn-primary btn-xs '>
                                                                                        VIEW
                                                                                        <i class='fas fa-eye'
                                                                                            download>
                                                                                        </i>
                                                                                    </a>
                                                                                </td>
                                                                            </tr>`;


                                                    } else {
                                                        requirementHTML += `<tr>
                                                                                <td> ${requirement_key} :</td>
                                                                                <td>${requirements_val} </td>
                                                                            </tr>`;
                                                    }


                                                });



                                                // `d` is the original data object for the ro
                                                // console.log(attributes);
                                                // console.log(requirements);
                                                return `<table cellpadding='5' cellspacing='0' border='0' style='padding-left:50px;'>
                                                            <tr>
                                                                <td> Service Name :</td>
                                                                <td>${d.name} </td>
                                                            </tr>
                                                            <tr>
                                                                <td> Full Names :</td>
                                                                <td>${d.first_name} ${d.last_name}</td>
                                                            </tr>
                                                            <tr>
                                                                <td>Email  Address:</td>
                                                                <td>${d.email} </td>
                                                            </tr>
                                                            <tr>
                                                                <td>Phone Number:</td>
                                                                <td>${d.phone} </td>
                                                            </tr>
                                                            <tr>
                                                                <td>ID Number or Passport Nmuber:</td>
                                                                <td>${d.national_identity} </td>
                                                            </tr>
                                                            ${requirementHTML}


                                                    </table>`;
                                            }

                                            $("#year").on("change", function(event) {
                                                $('#sales').DataTable().ajax.reload();
                                            });
                                            $("#service-name").on("change", function(event) {
                                                $('#sales').DataTable().ajax.reload();
                                            });



                                        });
                                    </script>
                                @endpush
                            </div>
                        </div>
                        <!-- END PANEL NO CONTROLS -->
                    </div>

                </div>


            </div>
        </div>
        <!-- END MAIN CONTENT -->
    </div>
    <!-- END MAIN -->
    <div class="clearfix"></div>
@endsection
@section('script')
    <script>
        $(document).ready(function() {


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

            /**********  Get particular  Records for Service check **************/
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
                        $.fn.modal.Constructor.prototype.enforceFocus = function() {};
                        console.log(data);
                        for (const key in data.itemsale) {
                            if (data.itemsale.hasOwnProperty(key)) {
                                $(`#${key}`).val(`${data.itemsale[key]}`)
                                console.log(`${key}: ${data.itemsale[key]}`);
                            }
                        }
                        $("#check-service-modal").modal("show");
                        $("#servicesEditForm").attr('action', data.url)
                        $("#servicesEditForm .service-requirements").html(data
                            .serviceAttributes);
                        initailizeSelect2();
                        center_no = $("#selected-center").val();
                        $("#livesearch-all-centers").select2("trigger", "select", {
                            data: {
                                id: center_no,
                                text: center_no,
                            }
                        });

                    },
                    complete: function() {
                        i--;
                        if (i <= 0) {
                            $(".preloader").fadeOut();
                        }

                    },
                });
            });
            /********** End Get particular  Records for Service to check **************/


            $(document).on("click", ".btn-edit-comment", function() {
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
                        for (const key in data.itemsale) {
                            if (data.itemsale.hasOwnProperty(key)) {
                                if (key == 'name') {
                                    $("#service-comment-modal .modal-title").html(data.itemsale[
                                        key]);
                                    console.log(data.itemsale[key]);
                                }


                                $(`#servicesCommentForm #${key}`).val(`${data.itemsale[key]}`)

                            }

                        }
                        $("#servicesCommentForm").attr('action', data.url)
                        $("#service-comment-modal").modal("show");


                    },
                    complete: function() {
                        i--;
                        if (i <= 0) {
                            $(".preloader").fadeOut();
                        }

                    },
                });
            });

            /**********  Update Service check **************/
            $(document).on("click", "#save-update-service", function() {
                var formData = new FormData($("#servicesEditForm #first_name").parents('form')[0]);
                var action = $("#servicesEditForm").attr('action')
                $.ajaxSetup({
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                    },
                });
                var caption = $(this).html();
                $.ajax({
                    url: action,
                    type: 'POST',
                    xhr: function() {
                        var myXhr = $.ajaxSettings.xhr();
                        return myXhr;
                    },
                    data: formData,
                    beforeSend: function() {
                        $(this).prop('disabled', true).html("Processing...");
                    },
                    cache: false,
                    contentType: false,
                    processData: false
                }).done(function(response) {
                    console.log(response);
                    $(this).prop('disabled', false).html(caption);
                    if ($.isEmptyObject(response.errors)) {
                        $('#check-service-modal').modal('hide');
                        $('#servicesEditForm .help-block').remove();
                        $('#servicesEditForm .has-error').removeClass('has-error');
                        toastr.success(response.success);
                        $('#sales').DataTable().ajax.reload();
                        $(this).prop('disabled', false).html(caption);
                    } else {
                        printErrorMsg("#servicesEditForm", response.errors);
                        $(this).prop('disabled', false).html(caption);
                    }
                }).fail(function(xhr, status, error) {
                    $(this).prop('disabled', false).html(caption);
                });
            });
            /********** Update Service **************/

            /**********  Save Comments and Status **************/
            $(document).on("click", "#save-comments", function() {

                var formData = new FormData($("#is_checked").parents('form')[0]);
                var action = $("#servicesCommentForm").attr('action')
                $.ajaxSetup({
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                    },
                });
                var caption = $(this).html();
                $.ajax({
                    url: action,
                    type: 'POST',
                    xhr: function() {
                        var myXhr = $.ajaxSettings.xhr();
                        return myXhr;
                    },
                    data: formData,
                    beforeSend: function() {
                        $(this).prop('disabled', true).html("Processing...");
                    },
                    cache: false,
                    contentType: false,
                    processData: false
                }).done(function(response) {
                    $(this).prop('disabled', false).html(caption);
                    if ($.isEmptyObject(response.errors)) {
                        $('#service-comment-modal').modal('hide');
                        $('#servicesCommentForm .help-block').remove();
                        $('#servicesCommentForm .has-error').removeClass('has-error');
                        toastr.success(response.success);
                        $('#sales').DataTable().ajax.reload();
                        $(this).prop('disabled', false).html(caption);
                    } else {
                        printErrorMsg("#servicesCommentForm", response.errors);
                        $(this).prop('disabled', false).html(caption);
                    }
                }).fail(function(xhr, status, error) {
                    $(this).prop('disabled', false).html(caption);
                });
            });
            /********** Save Comments and Status **************/


            /**********  Delete Service Paid **************/
            $(document).on("click", ".btn-delete", function() {
                var url = $(this).data('url');
                var i = 0;
                if (confirm("Are you sure you want to dletete this?")) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        url: url,
                        method: "DELETE",
                        success: function(response) {
                            toastr.success(response.success);
                            $('#sales').DataTable().ajax.reload();
                        }

                    });

                    // your deletion code
                }
                return false;


            });
            /********** End Delete Service Paid **************/




            /********** Auto Search For School Centres **************/
            /**** Initailize Select2 functions *******/
            $.fn.modal.Constructor.prototype.enforceFocus = function() {};

            function initailizeSelect2() {
                $.ajaxSetup({
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                    },
                });
                $("#livesearch-all-centers").select2({
                    placeholder: "Select the Center",
                    dropdownParent: $("#check-service-modal"),
                    ajax: {
                        url: "{{ route('services.autocomplete') }}",
                        method: "POST",
                        dataType: "json",
                        delay: 250,
                        processResults: function(data) {
                            return {
                                results: $.map(data, function(item) {
                                    return {
                                        text: `${item.center_name}`,
                                        id: item.center_no,
                                    };
                                }),
                            };
                        },
                        cache: true,
                        error: function(jqXHR, status, error) {
                            console.log(error + ": " + jqXHR.responseText);
                            return {
                                results: []
                            }; // Return dataset to load after error
                        }
                    },
                    width: '100%',
                    containerCss: {
                        "display": "block"
                    }
                })
            }
            /**** Initailize Select2 functions *******/



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
                        if (key == "gender") {
                            $(`${parent} [name='${key}']`).next().append(
                                `<span class='help-block'>${value}</span>`);
                        } else {
                            $(`<span class='help-block'>${value}</span>`).insertAfter(
                                `${parent} [name='${key}']`)
                        }


                    }
                });
            }

            /****  Print errors End*******/
        });
    </script>


    <!-- CHECK SERVICE SERVICE  -->
    <div class="modal fade bd-modal-md" id="check-service-modal" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close resetform" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h3 class="modal-title">Update Service </h3>
                </div>
                <div class="modal-body">
                    <form action=" " method="post" id="servicesEditForm" enctype="multipart/form-data">
                        <div>
                            @csrf
                            @method('put')
                        </div>

                        <div class="form-group col-md-6">
                            <label for="first_name" class="control-label">First Name</label>
                            <input type="text" class="form-control" name="first_name" value="" id="first_name">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="last_name" class="control-label">Last Name</label>
                            <input type="text" class="form-control" name="last_name" value="" id="last_name">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="email">Email</label>
                            <input type="text" class="form-control" name="email" value="" id="email">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="phone">Phone Number</label>
                            <input type="text" class="form-control" name="phone" value="" id="phone">
                        </div>
                        <div class="form-group col-md-12">
                            <label for="phone">National identity</label>
                            <input type="text" class="form-control" name="national_identity" value=""
                                id="national_identity">
                        </div>
                        <div class="service-requirements">

                        </div>

                        <div class="form-group col-md-6">
                            <label for="reference_no">Invoice Reference</label>
                            <input type="text" class="form-control" name="reference_no" value="" id="reference_no">
                        </div>
                        <div class="clearfix"></div>
                    </form>
                </div>
                <div class="clearfix"></div>
                <div class="modal-footer">
                    <button type="submit" name="save-update-service" class="btn btn-primary"
                        id="save-update-service">Save</button>
                    <button type="button" class="btn btn-danger resetform" data-dismiss="modal">Close</button>

                </div>
            </div>
        </div>
    </div>
    <!-- END CHECK SERVICE MODAL -->

    <!-- COMMENTS  -->
    <div class="modal fade bd-modal-md" id="service-comment-modal" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close resetform" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h3 class="modal-title">Update Service </h3>
                </div>
                <div class="modal-body">
                    <form action="" method="post" id="servicesCommentForm" enctype="multipart/form-data">
                        <div>
                            @csrf
                            @method('put')
                        </div>

                        <div class="form-group col-md-6">
                            <label for="first_name  control-label">First Name</label>
                            <input type="text" class="form-control" name="first_name" value=""
                                id="first_name">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="last_name" class="control-label">Last Name</label>
                            <input type="text" class="form-control" name="last_name" value="" id="last_name">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="email">Email</label>
                            <input type="text" class="form-control" name="email" value="" id="email">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="phone">Phone Number</label>
                            <input type="text" class="form-control" name="phone" value="" id="phone">
                        </div>
                        <div class="form-group col-md-12">
                            <label for="phone">National identity</label>
                            <input type="text" class="form-control" name="national_identity" value=""
                                id="national_identity">
                        </div>
                        <div class="form-group col-md-12">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="send_email" value="1"
                                    id="send_email">
                                <label for="send_email"><i class="fa fa-envelope fa-lg" aria-hidden="true"></i> Send
                                    Email</label>
                            </div>
                        </div>
                        <div class="form-group col-md-12">
                            <label for="comments">Comments</label>
                            <textarea class="form-control" id="comments" rows="6" name="comments" placeholder="Comments"></textarea>
                        </div>
                        <div class="form-group col-md-12">
                            <label for="is_checked" class="control-label">Status</label>
                            <select name="is_checked" class="form-control" id="is_checked">
                                <option value="">Select Status</option>
                                <option value="1">Pending</option>
                                <option value="2">Checked</option>
                                <option value="3">Done</option>
                            </select>
                            <span class="help-block"></span>
                        </div>
                        <div class="clearfix"></div>
                    </form>
                </div>
                <div class="clearfix"></div>
                <div class="modal-footer">
                    <button type="submit" name="save-comments" class="btn btn-primary" id="save-comments">Save</button>
                    <button type="button" class="btn btn-danger resetform" data-dismiss="modal">Close</button>

                </div>
            </div>
        </div>
    </div>
    <!-- END COMMENTS -->
@endsection
