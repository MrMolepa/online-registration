@extends('layouts.admin')

@section('content')
    <!-- ============================================================== -->
    <!-- Start Page Content -->
    <!-- MAIN -->
    <div class="main">
        <!-- MAIN CONTENT -->
        <div class="main-content">
            <div class="container-fluid">
                <h3 class="page-title">All Services</h3>
                <div class="row">
                    <div class="col-md-12">
                        <!-- PANEL NO CONTROLS -->
                        <div class="panel">
                            <div class="panel-heading">
                                <h3 class="panel-title">Services</h3>
                            </div>
                            <div class="panel-body">
                                <div class="custom-tabs-line tabs-line-bottom left-aligned">
                                    <ul class="nav" role="tablist">
                                        <li class="active"><a href="#all-services-tab" role="tab" data-toggle="tab">All
                                                Services</a>
                                        </li>
                                        <li><a href="#service-item-tab" role="tab" data-toggle="tab">Service Items</a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="tab-content">
                                    <div class="tab-pane fade in active" id="all-services-tab">
                                        <div class="pull-right">
                                            <a href="" class="btn btn-info" data-toggle="modal"
                                                data-target="#add-service">
                                                + create
                                            </a>
                                        </div>
                                        <div class="clearfix"></div>
                                        <table class="table" name="tablename"id="services">
                                            <thead>
                                                <tr>
                                                    <th>Display Name</th>
                                                    <th>Descrption</th>
                                                    <th>Emails</th>
                                                    <th>Service Item</th>
                                                    <th>Requirements</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>


                                        </table>
                                        @push('scripts')
                                            <script>
                                                $(function() {
                                                    var service = $('#services').DataTable({
                                                        processing: true,
                                                        serverSide: true,
                                                        deferRender: true,
                                                        "lengthMenu": [
                                                            [20, 50, 100, 200, 400, -1],
                                                            [20, 50, 100, 200, 400, "All"]
                                                        ],
                                                        ajax: "{{ route('admin.services.index') }}",
                                                        columns: [{
                                                                data: 'name',
                                                                name: 'name',
                                                                searchable: true
                                                            },
                                                            {
                                                                data: 'desciption',
                                                                name: 'desciption',
                                                            },
                                                            {
                                                                data: 'emails',
                                                                name: 'emails',
                                                            },
                                                            {
                                                                data: 'service_item',
                                                                name: 'service_item',
                                                                searchable: false,
                                                                sortable: false
                                                            },
                                                            {
                                                                data: 'requirements',
                                                                name: 'requirements',
                                                                searchable: false,
                                                                sortable: false
                                                            },
                                                            {
                                                                data: 'action',
                                                                name: 'action',
                                                                searchable: false,
                                                                sortable: false
                                                            }

                                                        ]

                                                    });
                                                    $("#services").css("width", "98.5%");
                                                });
                                            </script>
                                        @endpush



                                    </div>
                                    <div class="tab-pane fade" id="service-item-tab">

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


                                        <a href="" id="copy-financial-year" class="btn btn-primary">
                                            + Copy Previous Financial year
                                        </a>


                                        <table class="table" name="tablename"id="service-item">
                                            <thead>
                                                <tr>
                                                    <th>Name</th>
                                                    <th>Descrption</th>
                                                    <th>Financial year</th>
                                                    <th>Price</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>


                                        </table>
                                        @push('scripts')
                                            <script>
                                                $(function() {

                                                    var service = $('#service-item').DataTable({
                                                        processing: true,
                                                        serverSide: true,
                                                        deferRender: true,
                                                        "lengthMenu": [
                                                            [20, 50, 100, 200, 400, -1],
                                                            [20, 50, 100, 200, 400, "All"]
                                                        ],

                                                        ajax: {
                                                            url: "{{ route('admin.services.index') }}",
                                                            data: function(d) {
                                                                d.year = $('#year').val();
                                                                d.service_items = 'Yes';

                                                            }
                                                        },
                                                        columns: [{
                                                                data: 'name',
                                                                name: 'name',
                                                                searchable: true
                                                            },
                                                            {
                                                                data: 'description',
                                                                name: 'description',
                                                            },
                                                            {
                                                                data: 'financial_year',
                                                                name: 'financial_year',
                                                            },
                                                            {
                                                                data: 'price',
                                                                name: 'price',
                                                            },
                                                            {
                                                                data: 'action',
                                                                name: 'action',
                                                                searchable: false,
                                                                sortable: false

                                                            }

                                                        ]

                                                    });
                                                    $("#service-item").css("width", "98.5%");



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
        <!-- ADD SERVICE MODAL -->
        <div class="modal fade bd-modal-md" id="add-service" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog ">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close resetform" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h3 class="modal-title">Service</h3>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('admin.services.store') }}" method="post" id="addServiceForm">
                            <div>
                                @csrf
                            </div>
                            <div class="form-group  ">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" name="name" id="name" value="" />
                            </div>
                            <div class="form-group">
                                <label class="control-label" for="subject_name">Desciption</label>
                                <input type="text" class="form-control" name="desciption" id="desciption"
                                    value="" />
                            </div>

                        </form>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="add-service" class="btn btn-primary" id="save-service">Save</button>
                        <button type="button" class="btn btn-danger resetform" id="close"
                            data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>

        </div>
        <!--END ADD SERVICE MODEL -->

        <!-- ADD EMAIL MODAL -->
        <div class="modal fade bd-modal-md" id="email-modal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog ">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close resetform" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h3 class="modal-title">Emails</h3>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('admin.service-emails.store') }}" method="post" id="addEmailForm">
                            <div>
                                @csrf
                                <input type="hidden" name="service" value="">
                            </div>

                            <table width='100%'>
                                <tr>
                                    <thead>
                                        <th colspan="8">Email</th>
                                    </thead>
                                </tr>
                                <tr>
                                    <td>
                                        <div>
                                            <input type="text" class="form-control" name="email" id="email"
                                                value="" />





                                        </div>
                                        <div>
                                            <button type="submit" name="add-service" class="btn btn-primary"
                                                id="save-email">Save</button>
                                        </div>

                                    </td>

                                </tr>
                            </table>



                        </form>

                        <div class="clearfix"></div>

                        <table class="table" name="tablename"id="email-datatable">
                            <thead>
                                <tr>
                                    <th>Email</th>
                                    <th>Action</th>
                                </tr>
                            </thead>


                        </table>
                        @push('scripts')
                            <script>
                                $(function() {
                                    $(document).on("click", ".service-email-btn", function(ev) {
                                        ev.preventDefault();

                                        var url = new URL($(this).attr("href"));
                                        var service = url.searchParams.get("service");
                                        $("input[name='service']").val(service);
                                        var table = $("#email-datatable").DataTable({
                                            processing: true,
                                            serverSide: true,
                                            deferRender: true,
                                            destroy: true,
                                            "lengthMenu": [
                                                [20, 50, 100, 200, 400, -1],
                                                [20, 50, 100, 200, 400, "All"]
                                            ],
                                            ajax: {
                                                url: `${ $(this).attr("href")}`,
                                                error: function(xhr, error, code) {
                                                    console.log(xhr, code);
                                                },


                                            },

                                            columns: [{
                                                    data: "email",
                                                    name: "email",
                                                },
                                                {
                                                    data: "action",
                                                    name: "action",
                                                    searchable: false,
                                                    orderable: false,
                                                }

                                            ],

                                        });


                                        $('#email-modal').modal('show');

                                    });


                                });
                            </script>
                        @endpush

                    </div>
                    <div class="modal-footer">

                        <button type="button" class="btn btn-danger resetform" id="close"
                            data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>

        </div>
        <!--END ADD EMAIL MODEL -->


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
        // SERVICES
        //  Add Service
        $(document).on('click', '#save-service', function(ev) {
            ev.preventDefault();
            var url = $('#addServiceForm').attr('action');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            var inputData = $("#addServiceForm").serialize();
            $.ajax({
                url: url,
                method: "POST",
                data: inputData,
                success: function(data) {
                    if ($.isEmptyObject(data.errors)) {
                        $('#add-servcice').modal('hide');
                        $('#addServiceForm .help-block').remove();
                        $('#addServiceForm .has-error').removeClass('has-error');
                        toastr.success(data.success);
                        $('#services').DataTable().ajax.reload();
                    } else {
                        console.log(data);
                        printErrorMsg('#addServiceForm', data.errors);
                    }


                }
            });


        });
        // Edit Service
        $(document).on("click", "#services .editBtn", function() {
            //hide edit span

            $(this).closest("tr").find(".editSpan").hide();

            //show edit input
            $(this).closest("tr").find(".editInput").show();

            //hide edit button
            $(this).closest("tr").find(".editBtn").hide();

            //show edit button
            $(this).closest("tr").find(".saveBtn").show();
        });
        // Update Service
        $(document).on("click", "#services .saveBtn", function() {
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
                        $('#services').DataTable().ajax.reload();
                    } else {
                        printErrorMsg(`#${ID}`, data.errors);
                    }

                },
            });
        });
        // Delete  Service
        $(document).on('click', '#services .deleteBtn', function(ev) {
            ev.preventDefault();
            var url = $(this).data('url');
            if (confirm("Are you sure you want to delete this !") == true) {
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
                            $('#services').DataTable().ajax.reload();
                        }



                    }
                });


            } else {
                return;
            }

        });
        //EMAIL
        // Add Email
        $(document).on('click', '#save-email', function(ev) {
            ev.preventDefault();
            var url = $('#addEmailForm').attr('action');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            var inputData = $("#addEmailForm").serialize();
            $.ajax({
                url: url,
                method: "POST",
                data: inputData,
                success: function(data) {
                    if ($.isEmptyObject(data.errors)) {
                        // $('#add-servcice').modal('hide');
                        // $('#addServiceForm .help-block').remove();
                        $('#addServiceForm .has-error').removeClass('has-error');
                        toastr.success(data.success);
                        $('#email-datatable').DataTable().ajax.reload();
                    } else {
                        console.log(data);
                        printErrorMsg('#addEmailForm', data.errors);
                    }


                }
            });


        });
        // Edit Email
        $(document).on("click", "#email-datatable .editBtn", function() {
            //hide edit span

            $(this).closest("tr").find(".editSpan").hide();

            //show edit input
            $(this).closest("tr").find(".editInput").show();

            //hide edit button
            $(this).closest("tr").find(".editBtn").hide();

            //show edit button
            $(this).closest("tr").find(".saveBtn").show();
        });
        // Update SEmail
        $(document).on("click", "#email-datatable .saveBtn", function() {
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
                        $('#email-datatable').DataTable().ajax.reload();
                    } else {
                        printErrorMsg(`#${ID}`, data.errors);
                    }

                },
            });
        });

        // Delete  Email
        $(document).on('click', '#email-datatables .deleteBtn', function(ev) {
            ev.preventDefault();
            var url = $(this).data('url');
            if (confirm("Are you sure you want to delete this !") == true) {
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
                            $('#email-datatable').DataTable().ajax.reload();
                        }

                    }
                });


            } else {
                return;
            }

        });

        // Edit Service
        $(document).on("click", "#service-item .editBtn", function() {
            //hide edit span

            $(this).closest("tr").find(".editSpan").hide();

            //show edit input
            $(this).closest("tr").find(".editInput").show();

            //hide edit button
            $(this).closest("tr").find(".editBtn").hide();

            //show edit button
            $(this).closest("tr").find(".saveBtn").show();
        });
        // Update Service
        $(document).on("click", "#service-item .saveBtn", function() {
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
                        $('#service-item').DataTable().ajax.reload();
                    } else {
                        printErrorMsg(`#${ID}`, data.errors);
                    }

                },
            });
        });


        //copy-financial-year
        $(document).on("click", "#copy-financial-year", function(ev) {
            ev.preventDefault()
            actionUrl = `{{ route('admin.services.create') }}`;

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "GET",
                url: actionUrl,
                success: function(data) {
                     toastr.success("You have successfully Saved Changes");
                },
            });
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
