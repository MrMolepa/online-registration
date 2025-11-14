@extends('layouts.admin')
@section('content')
    <!-- MAIN -->
    <div class="main">
        <!-- MAIN CONTENT -->
        <div class="main-content">
            <div class="container-fluid">
                <h3 class="page-title">Users</h3>

                <div class="row">
                    <div class="col-md-12">
                        <!-- BORDERED TABLE -->
                        <div class="panel">
                            <div class="panel-heading">
                                Users
                            </div>
                            <div class="panel-body" id="table-view">
                                <div class="custom-tabs-line tabs-line-bottom left-aligned">
                                    <ul class="nav" role="tablist">
                                        <li class="active"><a href="#admin-tab" role="tab" data-toggle="tab">ECoL
                                                Users</a></li>
                                        <li>
                                            <a href="#center-tab" role="tab" data-toggle="tab">Centre Users</a>
                                        </li>
                                        <li>
                                            <a href="#sponsor-tab" role="tab" data-toggle="tab">Sponsor Users</a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="tab-content">
                                    <div class="tab-pane fade in active" id="admin-tab">
                                        <button type="button" data-toggle="modal" data-target="#add-user"
                                            class="btn btn-primary">Add Admin
                                            Users</button>
                                        <div class="table-responsive" id="admin-users">

                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="center-tab">
                                        <div class="table-responsive" id="center-users">

                                        </div>
                                    </div>

                                    <div class="tab-pane fade" id="sponsor-tab">
                                        <button type="button" data-toggle="modal" data-target="#add-sponsor"
                                            class="btn btn-primary">Add
                                            Sponsor Users</button>
                                        <div class="table-responsive" id="sponser-users">

                                        </div>
                                    </div>
                                </div>
                                <!-- END TABBED CONTENT -->



                            </div>
                        </div>
                        <!-- END BORDERED TABLE -->
                    </div>

                </div>
            </div>
        </div>
        <!-- END MAIN CONTENT -->
    </div>
    <!-- END MAIN -->
    <div class="clearfix"></div>


    <!-- ADD ADMIN USER MODAL -->
    <div class="modal fade bd-modal-md" id="add-user" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close resetform" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h3 class="modal-title"> Add new user </h3>
                </div>
                <div class="modal-body">

                    <form id="addUserForm" action="" method="POST" enctype="multipart/form-data">
                        <div class="form-group text-center">
                            <img src="{{ asset('adminAssets/assets/img/profile.png') }} " width="50px"
                                id="AddprofileDisplay" alt="">
                            <label for="profileImage">Profile Image</label>
                            <input type="file" name="profileImage" id="profileImage" class="form-control">
                            <span class="text-danger error-text profileImag_error"></span>
                        </div>
                        <div class="form-group">
                            <label for="user_type">User Type</label>
                            <select id="user_type" name="user_type" class="form-control">
                                @foreach ($user_types as $key => $type)
                                @if ($key == 'admins')
                                    <option value="{{ $type['model'] }}"> {{ ucfirst($key) }}</option>
                                @endif
                            @endforeach
                            </select>
                            <span class="text-danger error-text user_type_error"></span>
                        </div>
                        <div class="form-group">
                            <label for="inputAddress">Email Address</label>
                            <input type="text" name="email" value="" class="form-control">

                        </div>

                        <div class="form-group">
                            <label for="inputAddress">Occupation</label>
                            <input type="text" name="occupation" value=" " class="form-control">
                            <span class="text-danger error-text occupation_error"></span>
                        </div>
                        <div class="form-group">
                            <label for="inputState">Role</label>
                            <select id="inputState" name="role" class="form-control">
                                <option value="">Select Role</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}"> {{ $role->display_name }}</option>
                                @endforeach
                            </select>

                        </div>

                    </form>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="add_user" class="btn btn-primary" id="save-user">Add</button>
                    <button type="button" class="btn btn-danger resetform" id="adduser_close"
                        data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>

    </div>
    <!--END ADD  ADMIN USER MODEL -->


    <!-- ADD SPONSOR USER MODAL -->
    <div class="modal fade bd-modal-md" id="add-sponsor" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close resetform" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h3 class="modal-title"> Add Sponsor new user </h3>
                </div>
                <div class="modal-body">
                    <div class="errors">
                    </div>
                    <form id="addUserSponsorForm" action="{{ route('admin.sponsor-users.store') }}" method="POST"
                        enctype="multipart/form-data">
                        <div class="form-group text-center">
                            @csrf
                            <img src="{{ asset('adminAssets/assets/img/profile.png') }} " width="50px"
                                id="AddprofileDisplay" alt="">
                            <label for="profileImage">Profile Image</label>
                            <input type="file" name="profileImage" id="profileImage" class="form-control">
                            <span class="text-danger error-text profileImag_error"></span>
                        </div>
                        <div class="form-group col-md-12">
                            <label for="user_type">User Type</label>
                            <select id="user_type" name="user_type" class="form-control">
                                @foreach ($user_types as $key => $type)
                                    @if ($key == 'sponsors')
                                        <option value="{{ $type['model'] }}"> {{ ucfirst($key) }}</option>
                                    @endif
                                @endforeach
                            </select>

                        </div>
                        <div class="form-group col-md-6">
                            <label for="email">Email Address</label>
                            <input type="text" name="email" value="" id="email" class="form-control">

                        </div>
                        <div class="form-group col-md-6">
                            <label for="occupation">Occupation</label>
                            <input type="text" name="occupation" id="occupation" value=" "
                                class="form-control">
                        </div>

                        <div class="form-group col-md-6">
                            <label for="sponsor">Sponsor Key</label>
                            <input type="text" name="sponsor" value=" " id="sponsor" class="form-control">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="level">Level</label>
                            <input type="text" name="level" value=" " id="level" class="form-control">
                        </div>
                        <div class="form-group">
                            @foreach ($districts as $district)
                                <div class="col-md-4">
                                    <label>
                                        <input type="checkbox" class="form-check-input" name="districts[]"
                                            value="{{ $district->district_code }}">
                                        <span>{{ $district->district_code }} {{ $district->district_name }}</span>
                                    </label>
                                </div>
                            @endforeach
                            <div class="clearfix"></div>
                        </div>

                    </form>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="add_user" class="btn btn-primary" id="save-sponsor">Add</button>
                    <button type="button" class="btn btn-danger resetform" id="adduser_close"
                        data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>

    </div>
    <!-- ADD SPONSOR USER MODEL -->

    <!-- EDIT SPONSOR USER MODAL -->
    <div class="modal fade bd-modal-md" id="edit-sponsor" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close resetform" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h3 class="modal-title"> Edit Sponsor new user </h3>
                </div>
                <div class="modal-body">

                    <form id="editUserSponsorForm" action="" method="POST" enctype="multipart/form-data">
                        <div class="form-group text-center">
                            @method('PUT')
                            @csrf
                            <img src="{{ asset('adminAssets/assets/img/profile.png') }} " width="50px"
                                id="AddprofileDisplay" alt="">
                            <label for="profileImage">Profile Image</label>
                            <input type="file" name="profileImage" id="profileImage" class="form-control">
                            <span class="text-danger error-text profileImag_error"></span>
                        </div>
                        <div class="form-group col-md-12">
                            <label for="user_type">User Type</label>
                            <select id="user_type" name="user_type" class="form-control">
                                @foreach ($user_types as $key => $type)
                                    @if ($key == 'sponsors')
                                        <option value="{{ $type['model'] }}"> {{ ucfirst($key) }}</option>
                                    @endif
                                @endforeach
                            </select>

                        </div>
                        <div class="form-group col-md-6">
                            <label for="email">Email Address</label>
                            <input type="text" name="email" value="" id="email" class="form-control">

                        </div>
                        <div class="form-group col-md-6">
                            <label for="occupation">Occupation</label>
                            <input type="text" name="occupation" id="occupation" value=""
                                class="form-control">
                        </div>

                        <div class="form-group col-md-6">
                            <label for="sponsor">Sponsor Key</label>
                            <input type="text" name="sponsor" value="" id="sponsor" class="form-control">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="level">Level</label>
                            <input type="text" name="level" value="" id="level" class="form-control">
                        </div>
                        <div class="form-group">
                            @foreach ($districts as $district)
                                <div class="col-md-4">
                                    <label>
                                        <input type="checkbox" class="form-check-input" name="districts[]"
                                            value="{{ $district->district_code }}">
                                        <span>{{ $district->district_code }} {{ $district->district_name }}</span>
                                    </label>
                                </div>
                            @endforeach
                            <div class="clearfix"></div>
                        </div>

                    </form>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="sponsor" class="btn btn-primary" id="update-sponsor">Update</button>
                    <button type="button" class="btn btn-danger resetform" id="adduser_close"
                        data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>

    </div>
    <!--END EDIT SPONSOR USER MODEL -->

    <!-- EDIT USER MODAL -->
    <div class="modal fade bd-modal-md" id="edit-user" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">

                    <button type="button" class="close resetform" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h3 class="modal-title"> Edit user </h3>
                </div>
                <div class="modal-body">
                    <form action="" method="post" id="editUserForm" enctype="multipart/form-data">
                        <div class="form-group text-center">
                            <img src="assets/img/profile.png" id="profileDisplay" alt="">
                            <label for="profileImage">Profile Image</label>
                            <input type="file" name="profileImage" id="profileImage" class="form-control">
                            <span class="text-danger error-text profileImage_error"></span>
                        </div>
                        <div class="form-group">
                            <label for="inputAddress">User Id</label>
                            <input type="text" readonly="readonly" name="username" value=""
                                class="form-control">
                            <span class="text-danger error-text username_error"></span>

                        </div>
                        <div class="form-group">
                            <label for="inputAddress">Email Address</label>
                            <input type="text" name="email" value="" class="form-control">
                            <span class="text-danger error-text email_error"></span>
                        </div>
                        <div class="form-group">
                            <label for="inputAddress">Occupation</label>
                            <input type="text" name="occupation" value=" " class="form-control">
                            <span class="text-danger error-text occupation_error"></span>
                        </div>

                        <div class="form-group">
                            <label for="updateRole">Role</label>
                            <select id="updateRole" name="role" class="form-control">
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}"> {{ $role->display_name }}</option>
                                @endforeach
                            </select>
                            <span class="text-danger error-text role_error"></span>
                        </div>

                    </form>

                </div>
                <div class="modal-footer">
                    <button type="submit" name="edit_user" class="btn btn-primary" id="update-user">Update</button>
                    <button type="button" class="btn btn-danger resetform" data-dismiss="modal">Close</button>

                </div>
            </div>
        </div>
    </div>
    <!-- END EDIT USER MODAL -->
    <!--  CHANGE PASSWORD MODAL -->
    <div class="modal fade bd-modal-md" id="change-password" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close resetform" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h3 class="modal-title"> Change Password </h3>
                </div>
                <form action="" method="post" id="changePasswordForm">
                    <div class="modal-body">

                        <div class="form-group ">
                            <input type="hidden" name="username" value="" class="form-control">
                            <label for="inputAddress">Current Password</label>
                            <input type="password" name="current_password" class="form-control">
                            <span class="text-danger error-text current_password_error"></span>
                        </div>
                        <div class="form-group">
                            <label for="inputAddress">New Password</label>
                            <input type="password" name="password" class="form-control">
                            <span class="text-danger error-text password_error"></span>
                        </div>
                        <div class="form-group">
                            <label for="password_confirmationd">Confirm Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                class="form-control">
                            <span class="text-danger error-text  password_confirmation_error"></span>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id="btn_change_pasword">Submit</button>
                        <button type="button" class="btn btn-danger resetform" data-dismiss="modal">Close</button>

                    </div>
                </form>
            </div>
        </div>

    </div>
    <!-- END CHANGE PASSWORD MODAL -->

    </div>
    <!-- END WRAPPER -->

    <div class="clearfix"></div>



    <!-- /. PAGE WRAPPER  -->
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
        /**********  Add new user **************/
        $(document).on("click", "#save-user", function() {
            var i = 0;
            var data = new FormData();
            //Form data
            var form_data = $("#addUserForm").serializeArray();
            $.each(form_data, function(key, input) {
                data.append(input.name, input.value);
            });

            //File data
            data.append("profileImage", $('#addUserForm input[type="file"]')[0].files[0]);

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: "{{ route('admin.users.store') }}",
                method: "POST",
                contentType: false,
                processData: false,
                data: data,
                beforeSend: function() {
                    $(".preloader").fadeIn();
                    i++;
                },

                success: function(data) {
                    console.log(data);
                    $('.error-text').text('');
                    if ($.isEmptyObject(data.errors)) {
                        $("#addUserForm").trigger("reset");
                        $("#add-user").modal("hide");
                        toastr.success(data.success);
                    } else {
                        printErrorMsg('#addUserForm',data.errors);

                    }
                    displayAllUsers();

                },
                complete: function() {
                    i--;
                    if (i <= 0) {
                        $(".preloader").fadeOut();
                    }
                },
            });
        });
        /**********  Add new user end **************/

        /*-----------------------------------/
        /*	SPONSOR USER
        /*----------------------------------*/
        /**********  Add Sponsor user **************/
        $(document).on("click", "#save-sponsor", function() {
            var i = 0;
            var data = new FormData();
            var url = $('#addUserSponsorForm').attr('action');
            //Form data
            var form_data = $("#addUserSponsorForm").serializeArray();
            $.each(form_data, function(key, input) {
                data.append(input.name, input.value);
            });
            //addUserSponsorForm
            //File data
            data.append("profileImage", $('#addUserSponsorForm input[type="file"]')[0].files[0]);

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: url,
                method: "POST",
                contentType: false,
                processData: false,
                data: data,
                beforeSend: function() {
                    $(".preloader").fadeIn();
                    i++;
                },

                success: function(data) {
                    if ($.isEmptyObject(data.errors)) {
                        $("#addUserSponsorForm").trigger("reset");
                        $("#add-sponsor").modal("hide");
                        toastr.success(data.message);
                        // alert(data.success);
                    } else {
                        printErrorMsg("#addUserSponsorForm",data.errors);
                    }
                    displaySponsers();
                },
                complete: function() {
                    i--;
                    if (i <= 0) {
                        $(".preloader").fadeOut();
                    }
                },
            });
        });
        /**********  Add new Sponsor end **************/
        // edit-sponsor

        /**********  Get particular  Records for Sponsor **************/
        $(document).on("click", ".edit-sponsor", function() {
            $(':checkbox:checked').each(function() {
                $(this).removeAttr('checked');
            })
            var action = $(this).data("action");
            var i = 0;
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: action,
                method: "GET",
                beforeSend: function() {
                    // setting a timeout
                    $(".preloader").fadeIn();
                    i++;
                },
                success: function(data) {
                    var user = data.user;
                    var action = data.action;
                    var parent = "#editUserSponsorForm";

                    $(`form${parent} input,form${parent} select, form${parent}  textarea`).each(
                        function(index) {
                            var input = $(this);
                            var type = input.prop('type');
                            var name = input.attr('name');
                            //
                            if (user[name.replace('[]','')] != null ) {
                                if (type == 'checkbox') {
                                    user?.districts.forEach(function(district) {
                                        $(`form${parent} [name='${name}'][value='${district?.district_code}']`)
                                            .attr('checked', 'checked');
                                    });

                                } else {
                                    $(`form${parent} [name='${name}']`).val(user[name]);
                                }

                            }
                        }
                    );

                    $("#profileDisplay").attr("src", '{{ asset('school/assets/img/profile.png') }}');
                    if (data.user.profile) {
                        $("#profileDisplay").attr("src", data.profile);
                    }
                    $("#editUserSponsorForm").attr('action', action);
                    $("#edit-sponsor").modal("show");
                },
                complete: function() {
                    i--;
                    if (i <= 0) {
                        $(".preloader").fadeOut();
                    }
                },
            });
        });
        /**********  Get particular  Records for Sponsor **************/


        /**********  Update Sponser **************/
        $(document).on("click", "#update-sponsor", function() {
            var i = 0;
            var data = new FormData();
            //Form data
            var form_data = $("#editUserSponsorForm").serializeArray();
            $.each(form_data, function(key, input) {
                data.append(input.name, input.value)
            });

            var url = $("#editUserSponsorForm").attr('action');

            //File data
            data.append("profileImage", $('#editUserSponsorForm input[type="file"]')[0].files[0]);
            data.append('_method', 'PUT');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: url,
                method: "POST",
                data: data,
                contentType: false,
                processData: false,
                beforeSend: function() {
                    // setting a timeout
                    $(".preloader").fadeIn();
                    i++;
                },
                success: function(data) {
                    if ($.isEmptyObject(data.errors)) {
                        $('#editUserSponsorForm').trigger("reset");
                        $("#edit-sponsor").modal("hide");
                        toastr.success(data.success);
                        displaySponsers();
                    } else {
                        printErrorMsg('#editUserSponsorForm', data.errors);
                    }
                },
                complete: function() {
                    i--;
                    if (i <= 0) {
                        $(".preloader").fadeOut();
                    }
                },
            });
        });
        /**********  Update Sponsor End **************/






        /**********  Rest input when close Add user Modal **************/
        $(document).on("click", ".resetform", function() {
            $('.error-text').text('');
            $("form").trigger("reset");
        });
        /**********  Rest input when close Add user Modal End **************/

        /*-----------------------------------/
        /*	UPDATE NEW USER
        /*----------------------------------*/

        /**********  Get particular  Records for user **************/
        $(document).on("click", "#btn-edit-user", function() {
            var id = $(this).attr("data-id");
            console.log(id);

            var i = 0;
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: "users/edit/" + id,
                method: "GET",
                beforeSend: function() {
                    // setting a timeout
                    $(".preloader").fadeIn();
                    i++;
                },
                success: function(data) {
                    $('#editUserForm input[name="username"]').val(data.user.username);
                    $('#editUserForm input[name="email"]').val(data.user.email);
                    $('#editUserForm input[name="occupation"]').val(
                        data.user.occupation
                    );
                    $('#editUserForm #updateRole option[value="' + data.user.roles[0].id + '"]').attr(
                        "selected", "selected");

                    $("#profileDisplay").attr("src", '{{ asset('school/assets/img/profile.png') }}');
                    if (data.user.profile) {
                        $("#profileDisplay").attr(
                            "src", data.profile
                        );
                    }
                },
                complete: function() {
                    i--;
                    if (i <= 0) {
                        $(".preloader").fadeOut();
                    }
                },
            });
        });
        /**********  Get particular  Records for user **************/

        /**********  Update user **************/
        $(document).on("click", "#update-user", function() {

            var i = 0;
            var data = new FormData();
            //Form data
            var form_data = $("#editUserForm").serializeArray();
            $.each(form_data, function(key, input) {
                data.append(input.name, input.value)

            });
            var username = $('input[name="username"]').val();
            //File data
            data.append("profileImage", $('#editUserForm input[type="file"]')[0].files[0]);

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('admin.users.update') }}",
                method: "POST",
                data: data,
                contentType: false,
                processData: false,
                beforeSend: function() {
                    // setting a timeout
                    $(".preloader").fadeIn();
                    i++;
                },
                success: function(data) {
                    $('.error-text').text('');
                    if (data.status == 1) {
                        toastr.success(data.message);
                        $("#editUserForm").trigger("reset");
                        $("#edit-user").modal("hide");
                    } else {
                        printErrorMsg(data.error);
                    }
                    displayAllUsers();
                },
                complete: function() {
                    i--;
                    if (i <= 0) {
                        $(".preloader").fadeOut();
                    }
                },
            });
        });
        /**********  Update user End **************/



        /****  Get all Users *******/

        displayAllUsers();
        displaySponsers();


        // display  all Users
        function displayAllUsers() {
            var i = 0;
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('admin.user.allusers') }}",
                method: "GET",
                success: function(data) {
                    console.log(data);
                    if (data.status == "success") {
                        $("#admin-users").html(data.tableAdmin);
                        $("#center-users").html(data.tablecenter);
                    }
                },
            });
        }


        function displaySponsers() {
            var i = 0;
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('admin.sponsor-users.index') }}",
                method: "GET",
                success: function(data) {
                    console.log(data);
                    if (data.status == "success") {
                        $("#sponser-users").html(data.tableSponsor);

                    }
                },
            });
        }

        /****  Get all Users End*******/

        /**********  update user status**************/
        $(document).on("click", ".change-status", function() {
            var userid = $(this).data("id");
            var status = $(this).data("user_status");
            var check = confirm("Are  sure you want to change status of this user;");
            if (check) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: '{{ route('admin.users.changeuserstatus') }}',
                    method: "POST",
                    data: {
                        userid: userid,
                        status: status,
                    },
                    success: function(data) {
                        displayAllUsers();

                    },
                });

            }

        });
        /**********  update user status End**************/

        /*-----------------------------------/
        	/*UPDATE PASSWORD
          /*----------------------------------*/
        // set user id
        $(document).on("click", "#btn-edit-user-password", function() {
            var id = $(this).attr("data-id");
            $('#changePasswordForm input[name="username"]').val(id);
        });

        // change Password
        $(document).on("click", "#btn_change_pasword", function() {
            var form = $("#changePasswordForm").serialize();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: "{{ route('admin.users.changepassword') }}",
                method: "POST",
                data: form,
                success: function(data) {
                    console.log(data);






                    if (data.status == 1) {
                        $("#change-password").modal("hide");
                        $("#changePasswordForm").trigger("reset");
                        toastr.success(data.message);

                    } else {

                        printErrorMsg(parent, msg)


                    }

                }


            });
        });


        // /****  Print errors*******/
        function printErrorMsg(parent, msg) {
            $(`${parent} input, ${parent} select, ${parent} textarea`).each(function(index) {
                $(`${parent} .help-block`).remove();
                $(`${parent} .has-error`).removeClass('has-error');

            });
            $.each(msg, function(key, errors) {
                for (const error in errors) {
                    const value = errors[error];
                    $(`[name='${key}']`).parent().addClass('has-error');
                    $(`<span class='help-block'>${value}</span>`).insertAfter(`${parent} [name='${key}']`)


                }
            });
        }
        /****  Print errors End*******/
    </script>
@endsection
