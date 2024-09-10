@extends('layouts.school')

@section('content')
    <div>

        <div id="page-wrapper">

            <div class="header">
                <h1 class="page-header">
                    Manage Users
                    <!--<small>Welcome John Doe</small>-->
                </h1>

                <ol class="breadcrumb">
                    <li><a href="javascript:void();">Home</a></li>
                    <li class="active"><a href="javascript:void();">Manage Users</a></li>
                </ol>

            </div>

            <div id="page-inner" class="manage_users">
                <div class="row">
                    <div class="col-md-12">
                        <!-- List of users available -->
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                Users
                            </div>
                            <div class="panel-body">
                                @permission('users-create')
                                    <button type="button" data-toggle="modal" data-target="#add-user"
                                        class="btn btn-primary">Add
                                        User</button>
                                    <br>
                                    <br>
                                    @endpermission

                                    <div class="table-responsive" id="table-view">

                                    </div>

                                </div>
                            </div>
                            <!-- end List of users available -->
                        </div>
                    </div>

                </div>
                <!-- /. PAGE INNER  -->

            </div>
            <!-- /. PAGE WRAPPER  -->

        </div>
        <!-- /. WRAPPER  -->

        <!-- ADD USER MODAL -->
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
                        <div class="errors">
                            <span class="text-danger error-text number_error"></span>
                        </div>

                        <form action="" method="post" id="addUserForm" enctype="multipart/form-data">
                            <div class="form-group text-center">
                                <img src="{{ asset('school/assets/img/profile.png') }}" width="50px" id="AddprofileDisplay"
                                    alt="">
                                <label for="profileImage">Profile Image</label>
                                <input type="file" name="profileImage" id="profileImage" class="form-control">
                                <span class="text-danger error-text profileImage_error"></span>
                            </div>
                            <div class="form-group">
                                <label for="inputAddress">Email Address</label>
                                <input type="text" name="email" value="" class="form-control">
                                <span class="text-danger error-text email_error"></span>
                            </div>
                            <div class="form-group">
                                <label for="inputAddress">Occupation</label>
                                <input type="text" name="occupation" value="" class="form-control">
                                <span class="text-danger error-text occupation_error"></span>
                            </div>
                            <div class="form-group">
                                <label for="inputState">Role</label>
                                <select id="inputState" name="role" class="form-control">
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}">{{ $role->display_name }}</option>
                                    @endforeach
                                </select>
                                <span class="text-danger error-text role_error"></span>
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
        <!--END ADD USER MODEL -->

        <!-- EDIT USER MODAL -->
        <div class="modal fade bd-modal-md" id="edit-user" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="resetform close" data-dismiss="modal" aria-label="Close">
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

                                <input type="hidden" name="imageName" value="">
                                <span class="text-danger error-text role_error"></span>
                            </div>
                            <div class="form-group">
                                <label for="inputAddress">User Id</label>
                                <input type="text" name="username" readonly="readonly" value="" class="form-control">

                            </div>
                            <div class="form-group">
                                <label for="inputAddress">Email Address</label>
                                <input type="text" name="email" value="" class="form-control">
                                <span class="text-danger error-text email_error"></span>
                            </div>
                            <div class="form-group">
                                <label for="inputAddress">Occupation</label>
                                <input type="text" name="occupation" value="" class="form-control">
                                <span class="text-danger error-text occupation_error"></span>
                            </div>

                            <div class="form-group">
                                <label for="updateRole">Role</label>
                                <select id="updateRole" name="role" class="form-control">
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}">{{ $role->display_name }}</option>
                                    @endforeach
                                </select>
                                <span class="text-danger error-text role_error"></span>
                            </div>

                        </form>

                    </div>
                    <div class="modal-footer">
                        <button type="button" name="edit_user" class="btn btn-primary" id="update-user">Update</button>
                        <button type="button" class="btn btn-danger resetform" data-dismiss="modal">Close</button>

                    </div>
                </div>
            </div>

        </div>
        <!-- END EDIT USER MODAL -->

        <!--  CHANGE PASSWORD MODAL modal-sm -->
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



        <!-- /. PAGE WRAPPER  -->
    @endsection

@section('script')
    <script>
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
        var i = 0;
        $(document).on("click", "#save-user", function() {

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
                url: "{{ route('center.users.store') }}",
                method: "POST",
                cache: false,
                contentType: false,
                processData: false,
                data: data,
                beforeSend: function() {
                    // setting a timeout
                    $(".preloader").fadeIn();
                    // $("body").addClass("data-loaded");
                    i++;
                },

                success: function(data) {
                    console.log(data);
                    $('.error-text').text('');
                    if ($.isEmptyObject(data.error)) {
                        $("#addUserForm").trigger("reset");
                        $("#add-user").modal("hide");
                        toastr.success(data.success);
                        // alert(data.success);
                    } else {
                        printErrorMsg(data.error);

                    }
                    getAllUsers();

                },
                complete: function() {
                    i--;
                    if (i <= 0) {
                        // $("body").removeClass("data-loaded");
                        $(".preloader").fadeOut();
                    }
                },
            });
        });
        /**********  Add new user end **************/

        /**********  Rest input when close Add user Modal **************/
        $(document).on("click", ".resetform", function() {
            $("form").trigger("reset");
        });
        /**********  Rest input when close Add user Modal End **************/


        /*-----------------------------------/
        	/*	UPDATE NEW USER
          /*----------------------------------*/

        /**********  Get particular  Records for user **************/
        $(document).on("click", "#btn-edit-user", function() {
            var id = $(this).attr("data-id");

            var i = 0;
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: "/center/getuser/" + id,
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

                    $("#profileDisplay").attr(
                        "src",
                        data.profile
                    );


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


            data.append("profileImage", $('#editUserForm  input[type="file"]')[0].files[0]);

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: "{{ route('center.users.updateuser') }}",
                method: "POST",
                data: data,
                cache: false,
                contentType: false,
                processData: false,
                beforeSend: function() {
                    // setting a timeout
                    $(".preloader").fadeIn();
                    i++;
                },
                success: function(data) {

                    $('.error-text').text('');
                    if ($.isEmptyObject(data.error)) {
                        $("#editUserForm").trigger("reset");
                        $("#edit-user").modal("hide");
                        toastr.success(data.success);
                    } else {
                        printErrorMsg(data.error);

                    }
                    getAllUsers();


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
        getAllUsers();

        function getAllUsers() {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: "{{ route('center.users.getallusers') }}",
                method: "GET",
                cache: false,
                contentType: false,
                processData: false,
                success: function(data) {

                    $("#table-view").html(data.table);

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
                    url: '{{ route('center.users.changeuserstatus') }}',
                    method: "POST",
                    data: {
                        userid: userid,
                        status: status,
                    },
                    success: function(data) {
                        getAllUsers();
                        toastr.success(data.success);


                    },
                });

            }

        });
        /**********  update user status End**************/



        /**********  UPDATE PASSWORD**************/

        // set user id
        $(document).on("click", "#btn-edit-user-password", function() {
            var id = $(this).attr("data-id");
            $('#changePasswordForm input[name="username"]').val(id);
            console.log(id);
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
                url: "{{ route('center.users.changePassword') }}",
                method: "POST",
                data: form,
                success: function(data) {


                    $('.error-text').text('');
                    if (data.status == 1) {
                        $("#change-password").modal("hide");
                        $("#changePasswordForm").trigger("reset");
                        toastr.success(data.message);

                    } else {
                        printErrorMsg(data.error);

                    }

                }


            });
        });




        /**********  UPDATE PASSWORD**************/



        /****  Print errors*******/
        function printErrorMsg(msg) {
            $.each(msg, function(key, value) {

                $('.' + key + '_error').text(value);
                $("input[name='" + key + "']").addClass("is-valid");

            });

        }

        /****  Print errors End*******/
    </script>
@endsection
