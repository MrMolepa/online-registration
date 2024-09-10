@extends('layouts.admin')

@section('content')
    <!-- ============================================================== -->
    <!-- Start Page Content -->
    <!-- MAIN -->
    <div class="main">
        <!-- MAIN CONTENT -->
        <div class="main-content">
            <div class="container-fluid">
                <h3 class="page-title">{{ Str::title($oneTimeService->desciption) }}</h3>
                <div class="row">
                    <div class="col-md-12">
                        <!-- PANEL NO CONTROLS -->
                        <div class="panel">
                            <div class="panel-heading">
                                <h3 class="panel-title">{{ Str::title($oneTimeService->desciption) }}</h3>
                            </div>
                            <div class="panel-body">
                                <div class="pull-right">
                                    <a href="" class="btn btn-info" data-toggle="modal"
                                        data-target="#serviceItmeModal">
                                        + create
                                    </a>
                                </div>
                                <div class="clearfix"></div>

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
                                                    url: "{{ route('admin.service-item.index') }}",
                                                    data: function(d) {
                                                        d.service = "{{ $oneTimeService->id }}"
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
        <!-- ADD SERVICE MODAL -->
        <div class="modal fade bd-modal-md" id="serviceItmeModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog ">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close resetform" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h3 class="modal-title">Service Item</h3>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('admin.service-item.store') }}" method="post" id="serviceItemForm">
                            <div>
                                @csrf
                                <input type="hidden" name="service" value="{{ $oneTimeService->id }}">
                            </div>
                            <div class="form-group  ">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" name="name" id="name" value="" />
                            </div>
                            <div class="form-group">
                                <label class="control-label" for="description">Description</label>
                                <input type="text" class="form-control" name="description" id="description"
                                    value="" />
                            </div>

                            <div class="form-group">
                                <label class="control-label" for="financial_year">Financial year</label>
                                <input type="text" class="form-control" name="financial_year" id="financial_year"
                                    value="{{ $financial_year }}" />
                            </div>



                            <div class="form-group">
                                <label class="control-label" for="price">Price</label>
                                <input type="text" class="form-control" name="price" id="price" value="" />
                            </div>

                        </form>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="serviceItmeModal" class="btn btn-primary"
                            id="save-service-item">Save</button>
                        <button type="button" class="btn btn-danger resetform" id="close"
                            data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>

        </div>
        <!--END ADD CANDIDATE MODEL -->
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
        //  Add Service
        $(document).on('click', '#save-service-item', function(ev) {
            ev.preventDefault();
            var url = $('#serviceItemForm').attr('action');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            var inputData = $("#serviceItemForm").serialize();
            $.ajax({
                url: url,
                method: "POST",
                data: inputData,
                success: function(data) {
                    if ($.isEmptyObject(data.errors)) {
                        $('#serviceItmeModal').modal('hide');
                        $('#serviceItemForm .help-block').remove();
                        $('#serviceItemForm .has-error').removeClass('has-error');
                        toastr.success(data.success);
                        $('#service-item').DataTable().ajax.reload();
                    } else {
                        console.log(data);
                        printErrorMsg('#serviceItemForm', data.errors);
                    }


                }
            });


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


        // delete Candidate
        $(document).on('click', '#service-item .deleteBtn', function(ev) {
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
                            $('#service-item').DataTable().ajax.reload();
                        }



                    }
                });


            } else {
                return;
            }

        });


        /****  Print errors*******/
        function printErrorMsg(parent, msg) {

            console.log(parent);

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
