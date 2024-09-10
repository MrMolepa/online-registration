@extends('layouts.admin')

@section('content')
    <!-- END LEFT SIDEBAR -->
    <!-- MAIN -->
    <div class="main">
        <!-- MAIN CONTENT -->
        <div class="main-content">
            <div class="container-fluid">
                <h3 class="page-title">Manage Centres</h3>
                <div class="row">
                    <div class="col-md-12">
                        <!-- PANEL NO CONTROLS -->
                        <div class="panel">
                            <div class="panel-heading">
                                <h3 class="panel-title">Manage Centre</h3>
                            </div>
                            <div class="panel-body">
                                <fieldset>
                                    <legend>Filter</legend>
                                    <div class="row">
                                        <div class="form-group col-md-8">


                                        </div>
                                        <div class="col-md-4">
                                            <div class="input-group" id="filters">
                                                <span class="input-group-btn">
                                                    <button class="btn secondary" type="button">Level</button>
                                                </span>
                                                <select id="level" name="level" class="form-control status-dropdown">
                                                    @foreach ($levels as $level)
                                                        <option value="{{ $level->id }}"> {{ $level->level }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                    </div>
                                </fieldset>
                                <div class="custom-tabs-line tabs-line-bottom left-aligned">
                                    <ul class="nav" role="tablist">
                                        <li class="active"><a href="#center-accounts-tab" role="tab"
                                                data-toggle="tab">Center Accounts
                                            </a></li>
                                        <li><a href="#center-tab" role="tab" data-toggle="tab">All Centers</a></li>
                                    </ul>
                                </div>
                                <div class="tab-content">
                                    <div class="tab-pane fade in active" id="center-accounts-tab">

                                        <div class="table-responsive">
                                            <button type="button" data-toggle="modal" data-target="#add-center"
                                                class="btn btn-primary">+ Center</button>

                                            &nbsp;
                                            &nbsp;
                                            <a href="{{ route('admin.centers.exportpassword') }}" class="btn btn-primary "
                                                onclick="return confirm('Are you sure you want to export passwords  of  all centres')">Export
                                                passwords</a>
                                            <table class="table" name="tablename" id="centers">
                                                <thead>
                                                    <tr>
                                                        <th>Centre Number</th>
                                                        <th>Centre Name</th>
                                                        <th>Email Address</th>
                                                        <th>Temporary password</th>
                                                        <th>Role</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                            </table>
                                            @push('scripts')
                                                <script>
                                                    $(function() {
                                                        var centers = $('#centers').DataTable({
                                                            processing: true,
                                                            serverSide: true,
                                                            deferRender: true,
                                                            scrollY: 500,
                                                            scrollCollapse: true,
                                                            scrollX: true,
                                                            "lengthMenu": [
                                                                [20, 50, 100, 200, 400, -1],
                                                                [20, 50, 100, 200, 400, "All"]
                                                            ],
                                                            ajax: {
                                                                url: "{{ route('admin.centers.index') }}",
                                                                data: function(d) {
                                                                    d.level = $("#filters #level").val()
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
                                                                    data: 'email',
                                                                    name: 'email',
                                                                    searchable: false,
                                                                    sortable: false
                                                                },
                                                                {
                                                                    data: 'centre_account_password',
                                                                    name: 'centre_account_password',
                                                                    searchable: false,
                                                                    sortable: false
                                                                },
                                                                {
                                                                    data: 'role',
                                                                    name: 'role',
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

                                                        $("#centers").css("width", "98.5%");
                                                    });
                                                </script>
                                            @endpush

                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="center-tab">
                                        <div class="table-responsive">
                                            <table class="table" name="tablename" id="all-centers">
                                                <thead>
                                                    <tr>
                                                        <th>Centre Number</th>
                                                        <th>Centre Name</th>
                                                        <th>Levels</th>
                                                        <th>Sessions</th>
                                                        <th>Subjects</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                            </table>
                                            @push('scripts')
                                                <script>
                                                    $(function() {
                                                        var allCenters = $('#all-centers').DataTable({
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
                                                                url: "{{ route('admin.centers.allCenters') }}",
                                                                data: function(d) {
                                                                    d.level = $("#filters #level").val()
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
                                                                    data: 'levels',
                                                                    name: 'levels',
                                                                    searchable: false,
                                                                    sortable: false
                                                                },
                                                                {
                                                                    data: 'sessions',
                                                                    name: 'sessions',
                                                                    searchable: false,
                                                                    sortable: false
                                                                },
                                                                {
                                                                    data: 'subjects',
                                                                    name: 'subjects',
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
                                                        $("#all-centers").css("width", "98.5%");
                                                    });
                                                </script>
                                            @endpush

                                        </div>


                                    </div>
                                </div>

                                <!-- END TABBED CONTENT -->
                            </div>


                        </div>
                    </div>
                    <!-- END PANEL NO CONTROLS -->
                </div>

            </div>


        </div>
    </div>
    <!-- END MAIN CONTENT -->


    <!-- ADD  CENTER  MODAL -->
    <div class="modal fade bd-modal-md" id="add-center" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog ">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close resetform" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h3 class="modal-title"> New Center </h3>
                </div>
                <div class="modal-body">
                    <form action="{{ route('admin.centers.store') }}" method="post" id="centerForm">
                        <div>
                            @csrf
                        </div>
                        <div class="form-group col-md-6">
                            <label class="control-label" for="center_no">Centre Number</label>
                            <input type="text" class="form-control" name="center_no" id="center_no" value=" ">
                        </div>

                        <div class="form-group col-md-6">
                            <label class="control-label" for="center_name">Centre Name</label>
                            <input type="text" name="center_name" class="form-control" id="center_name">
                        </div>

                        <div class="form-group col-md-6">
                            <label class="control-label" for="district">District</label>
                            <input type="text" name="district" class="form-control" id="district">

                        </div>

                        <div class="form-group col-md-6 ">
                            <label class="control-label" for="district_code">District Code</label>
                            <input type="text" name="district_code" class="form-control" id="district_code">
                        </div>
                        <div class="form-group col-md-12">
                            <label class="control-label" for="address">Address</label>
                            <input type="text" name="address" class="form-control" id="address">
                        </div>
                        <div class="form-group col-md-12">
                            <label class="control-label" for="level">Level</label>
                            <input type="text" name="level" class="form-control" id="level">

                        </div>
                        <div class="form-group col-md-12">
                            <label class="control-label" for="email">Email</label>
                            <input type="text" name="email" class="form-control" id="email">
                        </div>
                    </form>
                    <div class="clearfix"></div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="save-center">Save</button>
                    <button type="button" class="btn btn-danger resetform" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>

    </div>


    <!--END ADD  CENTER  MODEL -->

    <!-- UPDATE  CENTER MODAL -->
    <div class="modal fade bd-modal-md" id="update-center" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog ">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close resetform" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h3 class="modal-title"> Update Center </h3>
                </div>
                <div class="modal-body">
                    <form action="" method="post" id="centerUpdateForm">
                        <div>
                            @csrf
                        </div>
                        <div class="form-group col-md-6">
                            <label class="control-label" for="center_no">Centre Number</label>
                            <input type="text" readonly class="form-control" name="center_no" id="center_no"
                                value=" ">
                        </div>
                        <div class="form-group col-md-6">
                            <label class="control-label" for="center_name">Centre Name</label>
                            <input type="text" name="center_name" class="form-control" id="center_name">
                        </div>

                        <div class="form-group col-md-6">
                            <label class="control-label" for="district">District</label>
                            <input type="text" name="district" class="form-control" id="district">

                        </div>

                        <div class="form-group col-md-6 ">
                            <label class="control-label" for="district_code">District Code</label>
                            <input type="text" name="district_code" class="form-control" id="district_code">
                        </div>
                        <div class="form-group col-md-12">
                            <label class="control-label" for="address">Address</label>
                            <input type="text" name="address" class="form-control" id="address">
                        </div>
                        <div class="form-group col-md-12">
                            <label class="control-label" for="level">Level</label>
                            <input type="text" name="level" class="form-control" id="level">

                        </div>
                        <div class="form-group col-md-12 ">
                            <label class="control-label" for="email">Email</label>
                            <input type="text" name="email" class="form-control" id="email">
                        </div>
                    </form>
                    <div class="clearfix"></div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="update-center" class="btn btn-primary" id="save-updates">Save</button>
                    <button type="button" class="btn btn-danger resetform" data-dismiss="modal">Close</button>

                </div>
            </div>
        </div>

    </div>
    <!--END UPDATE  CENTER  MODEL -->
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
        $(document).on('change', '#filters #level', function(ev) {
            $('#centers').DataTable().ajax.reload();
            $('#all-centers').DataTable().ajax.reload();
        });
        //  Add Center
        $(document).on('click', '#save-center', function(ev) {
            console.log('cliecked');
            ev.preventDefault();
            var url = $('#centerForm').attr('action');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });


            var inputData = $("#centerForm").serialize();

            $.ajax({
                url: url,
                method: "POST",
                data: inputData,
                success: function(data) {
                    console.log(data);
                    resetErrorMsg('#centerForm');
                    if ($.isEmptyObject(data.errors)) {
                        $('#add-center').modal('hide');
                        $('#centerForm .help-block').remove();
                        $('#centers').DataTable().ajax.reload();
                        $('#centerForm .has-error').removeClass('has-error');
                        toastr.success(data.success);
                    } else {
                        printErrorMsg('#centerForm', data.errors);
                    }


                }
            });


        });

        // Edit Center
        $(document).on("click", "#centers .editBtn-account", function(ev) {
            ev.preventDefault();
            var url = $(this).data('url');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: url,
                method: "GET",
                success: function(response) {
                    resetErrorMsg('#centerUpdateForm');
                    var object = response.center;
                    for (const property in object) {
                        console.log(`${property}: ${object[property]}`);
                        $(`#centerUpdateForm [name='${property}']`).val(object[property]);

                        if (property == 'users') {
                            console.log(object[property][0].email);
                            $(`#centerUpdateForm [name='email']`).val(object[property][0].email);
                        }

                    }
                    $("#centerUpdateForm").attr('action', response.url);

                    $('#update-center').modal('show');

                }
            });
        });

        // Edit all-centers
        $(document).on("click", "#all-centers .editBtn", function() {
            //hide edit span

            $(this).closest("tr").find(".editSpan").hide();

            //show edit input
            $(this).closest("tr").find(".editInput").show();

            //hide edit button
            $(this).closest("tr").find(".editBtn").hide();

            //show edit button
            $(this).closest("tr").find(".saveBtn").show();

            $(this).closest("tr").find(".sessions-multiple").multiselect({
                includeSelectAllOption: true,
            });


        });

        // Update changes all-centers
        $(document).on("click", "#all-centers .saveBtn", function() {
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
                        $('#subjects').DataTable().ajax.reload();
                    } else {
                        printErrorMsg(`#${ID}`, data.errors);
                    }

                },
            });
        });

        // delete Candidate
        $(document).on('click', '#all-centers .deleteBtn', function(ev) {
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
                            $('#all-centers').DataTable().ajax.reload();
                        } else {
                            toastr.error(data.error);
                        }



                    }
                });


            } else {
                return;
            }

        });














        // Reset password Center
        $(document).on("click", "#centers .resetBtn", function(ev) {
            ev.preventDefault();

            if (confirm("Are you sure you want to reset the password?")) {
                var url = $(this).data('url');
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: url,
                    method: "GET",
                    success: function(response) {
                        console.log(response)
                        $('#centers').DataTable().ajax.reload();
                        toastr.success(data.success);

                    }
                });
            } else {
                return false;
            }


        });
        // Update changes center
        $(document).on("click", "#save-updates", function() {
            var actionUrl = $('#centerUpdateForm').attr('action');
            var inputData = $("#centerUpdateForm").serialize();
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
                    resetErrorMsg('#centerUpdateForm');
                    if ($.isEmptyObject(data.errors)) {
                        toastr.success("You have successfully Saved Changes");
                        $('#update-center').modal('hide');
                        $('#centers').DataTable().ajax.reload();
                    } else {
                        printErrorMsg('#centerUpdateForm', data.errors);



                    }

                },
            });
        });

        // Update changes Role
        $(document).on("change", ".edit-role", function() {
            var role = $(this).val();
            var actionUrl = $(this).data('url');
            console.log(actionUrl);
            var inputData = $("#centerUpdateForm").serialize();;
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "PUT",
                url: actionUrl,
                dataType: "json",
                data: {
                    role: role
                },
                success: function(data) {
                    if ($.isEmptyObject(data.errors)) {
                        toastr.success("You have successfully Saved Changes");
                        $('#centers').DataTable().ajax.reload();
                    }

                },
            });
        });
        /****  Print errors*******/
        function printErrorMsg(parent, msg = null) {
            resetErrorMsg(parent);
            if (msg) {
                $.each(msg, function(key, errors) {
                    for (const error in errors) {
                        const value = errors[error];
                        $(`[name='${key}']`).parent().addClass('has-error');
                        $(`<span class='help-block'>${value}</span>`).insertAfter(
                            `${parent} [name='${key}']`)

                    }
                });
            }

        }
        /****  Print errors End*******/

        function resetErrorMsg(parent) {
            $(`${parent} input, ${parent} select, textarea`).each(function(index) {
                $(`${parent} .help-block`).remove();
                $(`${parent} .has-error`).removeClass('has-error');
                // console.log(input.attr('type') + 'Name: ' + input.attr('name') + '  Value: ' + input.val());
            });

        }
    </script>
@endsection
