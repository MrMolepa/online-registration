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
                            <div class="panel-body" id="table-view">
                                <div class="custom-tabs-line tabs-line-bottom left-aligned">
                                    <ul class="nav" role="tablist">
                                        <li class="active"><a href="#admin-tab" role="tab" data-toggle="tab">ECoL
                                                Users</a></li>
                                        <li><a href="#center-tab" role="tab" data-toggle="tab">Centre Users</a></li>
                                        <li><a href="#sponsor-tab" role="tab" data-toggle="tab">Sponsor Users</a></li>
                                        <li><a href="#candidate-tab" role="tab" data-toggle="tab">Candidate Users</a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="tab-content">
                                    <div class="tab-pane fade in active" id="admin-tab">
                                        <button type="button" data-toggle="modal" data-target="#add-user"
                                            class="btn btn-primary">Add Admin Users</button>
                                        <div class="table-responsive" id="admin-users">
                                            <table class="table table-striped" id="admin_users_table">
                                                <thead>
                                                    <tr>
                                                        <th>State</th>
                                                        <th>Profile Picture</th>
                                                        <th>User Id</th>
                                                        <th>Occupation</th>
                                                        <th>Email</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="center-tab">
                                        <div class="table-responsive" id="center-users">
                                            <table class="table table-striped" id="center_users_table">
                                                <thead>
                                                    <tr>
                                                        <th>State</th>
                                                        <th>Profile Picture</th>
                                                        <th>User Id</th>
                                                        <th>Centre No</th>
                                                        <th>Occupation</th>
                                                        <th>Email</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>
                                    </div>

                                    <div class="tab-pane fade" id="sponsor-tab">
                                        <button type="button" data-toggle="modal" data-target="#add-sponsor"
                                            class="btn btn-primary">Add Sponsor Users</button>
                                        <div class="table-responsive" id="sponser-users">
                                            <table class="table table-striped" id="sponsor_users_table">
                                                <thead>
                                                    <tr>
                                                        <th>State</th>
                                                        <th>Profile Picture</th>
                                                        <th>User Id</th>
                                                        <th>Sponsor Key</th>
                                                        <th>Level</th>
                                                        <th>Occupation</th>
                                                        <th>Email</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>
                                    </div>

                                    <div class="tab-pane fade" id="candidate-tab">
                                        <div class="table-responsive" id="candidate-users">
                                            <table class="table table-striped" id="candidate_users_table">
                                                <thead>
                                                    <tr>
                                                        <th>State</th>
                                                        <th>Profile Picture</th>
                                                        <th>Username</th>
                                                        <th>Candidate No</th>
                                                        <th>Centre No</th>
                                                        <th>National ID</th>
                                                        <th>Session</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- END BORDERED TABLE -->


                        <!-- USER PERMISSIONS MANAGEMENT MODAL -->
                        <div class="modal fade" id="manage-permissions" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                        <h3 class="modal-title">Manage User Permissions</h3>
                                    </div>
                                    <div class="modal-body">
                                        <div class="alert alert-info">
                                            <strong>User:</strong> <span id="perm-user-username"></span>
                                            (<span id="perm-user-email"></span>)
                                            <br>
                                            <strong>Role:</strong> <span id="perm-user-role"
                                                class="label label-primary"></span>
                                        </div>

                                        <!-- Quick Assign Permission Section -->
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <h4 class="panel-title">Quick Assign Permission</h4>
                                            </div>
                                            <div class="panel-body">
                                                <div class="row">
                                                    <div class="col-md-9">
                                                        <div class="form-group">
                                                            <label for="quick-permission-select">Select Permission</label>
                                                            <select id="quick-permission-select" class="form-control">
                                                                <option value="">-- Select a Permission --</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label>&nbsp;</label><br>
                                                            <button type="button" class="btn btn-success"
                                                                id="btn-quick-allow">
                                                                Allow
                                                            </button>
                                                            <button type="button" class="btn btn-danger"
                                                                id="btn-quick-deny">
                                                                Deny
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <hr>

                                        <!-- Permissions Legend -->
                                        <div class="row" style="margin-bottom: 15px;">
                                            <div class="col-md-12">
                                                <strong>Legend:</strong>
                                                <span class="label label-info">From Role</span>
                                                <span class="label label-warning">User Override</span>
                                                <span class="label label-default">Not Assigned</span>
                                            </div>
                                        </div>

                                        <!-- Permissions Table -->
                                        <div class="table-responsive">
                                            <table class="table table-striped table-condensed"
                                                id="permissions-table">
                                                <thead>
                                                    <tr>
                                                        <th>Permission</th>
                                                        <th>Description</th>
                                                        <th>Source</th>
                                                        <th>Status</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="permissions-tbody">
                                                    <tr>
                                                        <td colspan="5" class="text-center">Loading permissions...</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>

                                        <input type="hidden" id="current-user-id" value="">
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- END USER PERMISSIONS MANAGEMENT MODAL -->
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
                    <div class="errors"></div>
                    <form id="addUserSponsorForm" action="{{ route('admin.sponsor-users.store') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="form-group text-center">
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
                            <input type="text" name="occupation" id="occupation" value=" " class="form-control">
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
                        @method('PUT')
                        @csrf
                        <div class="form-group text-center">
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
                            <input type="text" name="occupation" id="occupation" value="" class="form-control">
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
                            <input type="text" readonly="readonly" name="username" value="" class="form-control">
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

    <!--  CHANGE PASSWORD MODAL FOR CANDIDATE -->
    <div class="modal fade bd-modal-md" id="change-password-candidate" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close resetform" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h3 class="modal-title"> Change Candidate Password </h3>
                </div>
                <form action="" method="post" id="changeCandidatePasswordForm">
                    <div class="modal-body">
                        <div class="form-group ">
                            <input type="hidden" name="userid" value="" class="form-control">
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
                            <label for="password_confirmation">Confirm Password</label>
                            <input type="password" name="password_confirmation" class="form-control">
                            <span class="text-danger error-text  password_confirmation_error"></span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id="btn_change_candidate_password">Submit</button>
                        <button type="button" class="btn btn-danger resetform" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- END CHANGE PASSWORD MODAL FOR CANDIDATE -->

    </div>
    <!-- END WRAPPER -->

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

        // Initialize DataTables
        var adminUsersTable, centerUsersTable, sponsorUsersTable, candidateUsersTable, permissionsTable;

        function initializeAdminUsersTable() {
            if ($.fn.DataTable.isDataTable('#admin_users_table')) {
                $('#admin_users_table').DataTable().destroy();
            }

            adminUsersTable = $('#admin_users_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.users.index') }}",
                    data: function (d) {
                        d.type = 'admin';
                    }
                },
                columns: [{
                    data: 'state',
                    name: 'state',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'profile_picture',
                    name: 'profile_picture',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'username',
                    name: 'username'
                },
                {
                    data: 'occupation',
                    name: 'occupation'
                },
                {
                    data: 'email',
                    name: 'email'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
                ],
                order: [
                    [2, 'asc']
                ]
            });
        }

        function initializeCenterUsersTable() {
            if ($.fn.DataTable.isDataTable('#center_users_table')) {
                $('#center_users_table').DataTable().destroy();
            }

            centerUsersTable = $('#center_users_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.users.index') }}",
                    data: function (d) {
                        d.type = 'center';
                    }
                },
                columns: [{
                    data: 'state',
                    name: 'state',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'profile_picture',
                    name: 'profile_picture',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'username',
                    name: 'username'
                },
                {
                    data: 'center_no',
                    name: 'center_no'
                },
                {
                    data: 'occupation',
                    name: 'occupation'
                },
                {
                    data: 'email',
                    name: 'email'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
                ],
                order: [
                    [2, 'asc']
                ]
            });
        }

        function initializeSponsorUsersTable() {
            if ($.fn.DataTable.isDataTable('#sponsor_users_table')) {
                $('#sponsor_users_table').DataTable().destroy();
            }

            sponsorUsersTable = $('#sponsor_users_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.users.index') }}",
                    data: function (d) {
                        d.type = 'sponsor';
                    }
                },
                columns: [{
                    data: 'state',
                    name: 'state',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'profile_picture',
                    name: 'profile_picture',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'username',
                    name: 'username'
                },
                {
                    data: 'sponsor_key',
                    name: 'sponsor_key'
                },
                {
                    data: 'level',
                    name: 'level'
                },
                {
                    data: 'occupation',
                    name: 'occupation'
                },
                {
                    data: 'email',
                    name: 'email'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
                ],
                order: [
                    [2, 'asc']
                ]
            });
        }

        function initializeCandidateUsersTable() {
            if ($.fn.DataTable.isDataTable('#candidate_users_table')) {
                $('#candidate_users_table').DataTable().destroy();
            }

            candidateUsersTable = $('#candidate_users_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.users.index') }}",
                    data: function (d) {
                        d.type = 'candidate';
                    }
                },
                columns: [{
                    data: 'state',
                    name: 'state',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'profile_picture',
                    name: 'profile_picture',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'username',
                    name: 'username'
                },
                {
                    data: 'candidate_no',
                    name: 'candidate_no'
                },
                {
                    data: 'center_no',
                    name: 'center_no'
                },
                {
                    data: 'national_id',
                    name: 'national_id'
                },
                {
                    data: 'session',
                    name: 'session'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
                ],
                order: [
                    [2, 'asc']
                ]
            });
        }

        /**
         * Initialize Permissions DataTable
         */
        function initializePermissionsTable(permissionsData) {
            // Destroy existing DataTable if it exists
            if ($.fn.DataTable.isDataTable('#permissions-table')) {
                $('#permissions-table').DataTable().destroy();
            }

            // Initialize DataTable with the permissions data
            permissionsTable = $('#permissions-table').DataTable({
                data: permissionsData,
                columns: [
                    {
                        data: null,
                        render: function (data, type, row) {
                            return '<strong>' + row.display_name + '</strong><br><small class="text-muted">' + row.name + '</small>';
                        }
                    },
                    {
                        data: 'description',
                        render: function (data, type, row) {
                            return '<small>' + (data || '-') + '</small>';
                        }
                    },
                    {
                        data: null,
                        render: function (data, type, row) {
                            return row.sourceLabel;
                        }
                    },
                    {
                        data: null,
                        render: function (data, type, row) {
                            return row.statusLabel;
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        render: function (data, type, row) {
                            return row.actionButtons;
                        }
                    }
                ],
                pageLength: 10,
                lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
                order: [[0, 'asc']], // Sort by permission name
                language: {
                    search: "Search permissions:",
                    lengthMenu: "Show _MENU_ permissions",
                    info: "Showing _START_ to _END_ of _TOTAL_ permissions",
                    infoEmpty: "No permissions available",
                    infoFiltered: "(filtered from _MAX_ total permissions)",
                    zeroRecords: "No matching permissions found"
                },
                dom: 'lftip', // length, filter, table, info, pagination
                responsive: true,
                autoWidth: false
            });
        }

        // Initialize tables on page load
        $(document).ready(function () {
            initializeAdminUsersTable();

            // Prevent hash from being added to URL when clicking tabs
            $('.nav-tabs a').on('click', function (e) {
                e.preventDefault();
                $(this).tab('show');
            });

            // Initialize center users table when tab is clicked
            $('a[href="#center-tab"]').on('shown.bs.tab', function (e) {
                initializeCenterUsersTable();
            });

            // Initialize sponsor users table when tab is clicked
            $('a[href="#sponsor-tab"]').on('shown.bs.tab', function (e) {
                initializeSponsorUsersTable();
            });

            // Initialize candidate users table when tab is clicked
            $('a[href="#candidate-tab"]').on('shown.bs.tab', function (e) {
                initializeCandidateUsersTable();
            });
        });

        /**********  Add new user **************/
        $(document).on("click", "#save-user", function () {
            var i = 0;
            var data = new FormData();
            var form_data = $("#addUserForm").serializeArray();
            $.each(form_data, function (key, input) {
                data.append(input.name, input.value);
            });

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
                success: function (data) {
                    $('.error-text').text('');
                    if ($.isEmptyObject(data.errors)) {
                        $("#addUserForm").trigger("reset");
                        $("#add-user").modal("hide");
                        toastr.success(data.success);
                        adminUsersTable.ajax.reload(null, false);
                    } else {
                        printErrorMsg('#addUserForm', data.errors);
                    }
                },
            });
        });

        /**********  Update user **************/
        $(document).on("click", "#update-user", function () {
            var i = 0;
            var data = new FormData();
            var form_data = $("#editUserForm").serializeArray();
            $.each(form_data, function (key, input) {
                data.append(input.name, input.value)
            });

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

                success: function (data) {
                    $('.error-text').text('');
                    if (data.status == 1) {
                        toastr.success(data.message);
                        $("#editUserForm").trigger("reset");
                        $("#edit-user").modal("hide");
                        adminUsersTable.ajax.reload(null, false);
                    } else {
                        printErrorMsg('#editUserForm', data.error);
                    }
                },
            });
        });

        /**********  Get particular Records for user **************/
        $(document).on("click", "#btn-edit-user", function () {
            var id = $(this).attr("data-id");
            var i = 0;

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: "users/edit/" + id,
                method: "GET",
                success: function (data) {
                    $('#editUserForm input[name="username"]').val(data.user.username);
                    $('#editUserForm input[name="email"]').val(data.user.email);
                    $('#editUserForm input[name="occupation"]').val(data.user.occupation);
                    $('#editUserForm #updateRole option[value="' + data.user.roles[0].id + '"]').attr(
                        "selected", "selected");

                    $("#profileDisplay").attr("src",
                        '{{ asset('adminAssets/assets/img/profile.png') }}');
                    if (data.user.profile) {
                        $("#profileDisplay").attr("src", data.profile);
                    }
                },
            });
        });

        /**********  update user status**************/
        $(document).on("click", ".change-status", function () {
            var userid = $(this).data("id");
            var status = $(this).data("user_status");
            var check = confirm("Are sure you want to change status of this user?");

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
                    success: function (data) {
                        toastr.success(data.message);
                        if (adminUsersTable) {
                            adminUsersTable.ajax.reload(null, false);
                        }
                        if (centerUsersTable) {
                            centerUsersTable.ajax.reload(null, false);
                        }
                    },
                });
            }
        });


        /**********  update sponsor status**************/
        $(document).on("click", ".change-status-sponsor", function () {
            var userid = $(this).data("id");
            var status = $(this).data("user_status");
            var check = confirm("Are sure you want to change status of this sponsor?");

            if (check) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    url: '{{ route('admin.users.changeSponsorStatus') }}',
                    method: "POST",
                    data: {
                        userid: userid,
                        status: status,
                    },
                    success: function (data) {
                        toastr.success(data.message);
                        if (sponsorUsersTable) {
                            sponsorUsersTable.ajax.reload(null, false);
                        }
                    },
                });
            }
        });

        /**********  update candidate status**************/
        $(document).on("click", ".change-status-candidate", function () {
            var userid = $(this).data("id");
            var status = $(this).data("user_status");
            var check = confirm("Are sure you want to change status of this candidate user?");

            if (check) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    url: '{{ route('admin.users.changeCandidateStatus') }}',
                    method: "POST",
                    data: {
                        userid: userid,
                        status: status,
                    },
                    success: function (data) {
                        toastr.success(data.message);
                        if (candidateUsersTable) {
                            candidateUsersTable.ajax.reload(null, false);
                        }
                    },
                });
            }
        });

        /**********  Change Password **************/
        $(document).on("click", "#btn-edit-user-password", function () {
            var id = $(this).attr("data-id");
            $('#changePasswordForm input[name="username"]').val(id);
        });

        $(document).on("click", "#btn_change_pasword", function () {
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
                success: function (data) {
                    if (data.status == 1) {
                        $("#change-password").modal("hide");
                        $("#changePasswordForm").trigger("reset");
                        toastr.success(data.message);
                    } else {
                        printErrorMsg('#changePasswordForm', data.error);
                    }
                }
            });
        });

        /**********  Change Candidate Password **************/
        $(document).on("click", ".btn-edit-candidate-password", function () {
            var id = $(this).attr("data-id");
            $('#changeCandidatePasswordForm input[name="userid"]').val(id);
        });

        $(document).on("click", "#btn_change_candidate_password", function () {
            var form = $("#changeCandidatePasswordForm").serialize();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: "{{ route('admin.users.changeCandidatePassword') }}",
                method: "POST",
                data: form,
                success: function (data) {
                    if (data.status == 1) {
                        $("#change-password-candidate").modal("hide");
                        $("#changeCandidatePasswordForm").trigger("reset");
                        toastr.success(data.message);
                    } else {
                        printErrorMsg('#changeCandidatePasswordForm', data.error);
                    }
                }
            });
        });

        /**********  Manage User Permissions **************/
        $(document).on("click", ".btn-manage-permissions", function () {
            var userId = $(this).data("id");
            $('#current-user-id').val(userId);

            // Show loading state
            $('#permissions-tbody').html('<tr><td colspan="5" class="text-center">Loading permissions...</td></tr>');
            $('#quick-permission-select').html('<option value="">Loading...</option>');

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: "/admin/users/" + userId + "/permissions/data",
                method: "GET",
                success: function (data) {
                    if (data.success) {
                        // Populate user info
                        $('#perm-user-username').text(data.user.username);
                        $('#perm-user-email').text(data.user.email);
                        $('#perm-user-role').text(data.user.role_name);

                        // Populate quick permission select dropdown
                        var permissionOptions = '<option value="">-- Select a Permission --</option>';
                        data.all_permissions.forEach(function (permission) {
                            permissionOptions += '<option value="' + permission.id + '">' +
                                permission.display_name + ' (' + permission.name + ')' +
                                '</option>';
                        });
                        $('#quick-permission-select').html(permissionOptions);

                        // Prepare data for DataTable
                        var permissionsData = [];
                        var userSpecificPerms = {};

                        // Create lookup for user-specific permissions
                        data.user_specific_permissions.forEach(function (perm) {
                            userSpecificPerms[perm.id] = perm.allowed;
                        });

                        // Create lookup for role permissions
                        var rolePermissions = {};
                        data.effective_permissions.forEach(function (perm) {
                            if (perm.source === 'role') {
                                rolePermissions[perm.id] = true;
                            }
                        });

                        // Build permissions data array for DataTable
                        data.all_permissions.forEach(function (permission) {
                            var hasUserPerm = userSpecificPerms.hasOwnProperty(permission.id);
                            var hasRolePerm = rolePermissions.hasOwnProperty(permission.id);
                            var userAllowed = hasUserPerm ? userSpecificPerms[permission.id] : null;

                            var sourceLabel = '';
                            var statusLabel = '';
                            var actionButtons = '';

                            if (hasUserPerm) {
                                // User has explicit permission override
                                sourceLabel = '<span class="label label-warning">User Override</span>';

                                if (userAllowed) {
                                    statusLabel = '<span class="label label-primary">Allowed</span>';
                                    actionButtons = '<button class="btn btn-xs btn-danger btn-deny-permission" data-permission-id="' + permission.id + '" title="Deny this permission">Deny</button> ';
                                } else {
                                    statusLabel = '<span class="label label-danger">Denied</span>';
                                    actionButtons = '<button class="btn btn-xs btn-success btn-allow-permission" data-permission-id="' + permission.id + '" title="Allow this permission">Allow</button> ';
                                }
                                actionButtons += '<button class="btn btn-xs btn-warning btn-remove-permission" data-permission-id="' + permission.id + '" title="Remove user override">Reset</button>';

                            } else if (hasRolePerm) {
                                // Permission comes from role only
                                sourceLabel = '<span class="label label-info">From Role</span>';
                                statusLabel = '<span class="label label-success">Allowed</span>';
                                actionButtons = '<button class="btn btn-xs btn-danger btn-deny-permission" data-permission-id="' + permission.id + '" title="Override and deny this permission">Override</button>';

                            } else {
                                // Not assigned at all
                                sourceLabel = '<span class="label label-default">-</span>';
                                statusLabel = '<span class="label label-default">Not Assigned</span>';
                                actionButtons = '<button class="btn btn-xs btn-success btn-allow-permission" data-permission-id="' + permission.id + '" title="Allow this permission"><i class="fas fa-check"></i> Allow</button>';
                            }

                            permissionsData.push({
                                id: permission.id,
                                name: permission.name,
                                display_name: permission.display_name,
                                description: permission.description,
                                sourceLabel: sourceLabel,
                                statusLabel: statusLabel,
                                actionButtons: actionButtons,
                                hasUserPerm: hasUserPerm,
                                hasRolePerm: hasRolePerm
                            });
                        });

                        // Initialize DataTable with the permissions data
                        initializePermissionsTable(permissionsData);
                    }
                },
                error: function (xhr) {
                    console.error('Error:', xhr);
                    toastr.error('Failed to load permissions: ' + (xhr.responseJSON?.message || 'Unknown error'));
                    $('#permissions-tbody').html('<tr><td colspan="5" class="text-center text-danger">Failed to load permissions.</td></tr>');
                }
            });
        });

        /**********  Quick Allow Permission *****************/
        $(document).on("click", "#btn-quick-allow", function () {
            var userId = $('#current-user-id').val();
            var permissionId = $('#quick-permission-select').val();

            if (!permissionId) {
                toastr.warning('Please select a permission first.');
                return;
            }

            assignPermission(userId, permissionId, true);
        });

        /**********  Quick Deny Permission **************/
        $(document).on("click", "#btn-quick-deny", function () {
            var userId = $('#current-user-id').val();
            var permissionId = $('#quick-permission-select').val();

            if (!permissionId) {
                toastr.warning('Please select a permission first.');
                return;
            }

            assignPermission(userId, permissionId, false);
        });

        /**********  Allow Permission from Table **************/
        $(document).on("click", ".btn-allow-permission", function () {
            var userId = $('#current-user-id').val();
            var permissionId = $(this).data('permission-id');
            assignPermission(userId, permissionId, true);
        });

        /**********  Deny Permission from Table **************/
        $(document).on("click", ".btn-deny-permission", function () {
            var userId = $('#current-user-id').val();
            var permissionId = $(this).data('permission-id');
            assignPermission(userId, permissionId, false);
        });

        /**********  Assign Permission Function **************/
        function assignPermission(userId, permissionId, allowed) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: "/admin/users/" + userId + "/permissions/assign",
                method: "POST",
                data: {
                    permission_id: permissionId,
                    allowed: allowed
                },
                success: function (data) {
                    console.log('Success response:', data);
                    if (data.success) {
                        toastr.success(data.message);
                        // Reset dropdown
                        $('#quick-permission-select').val('');
                        // Reload permissions
                        $('.btn-manage-permissions[data-id="' + userId + '"]').trigger('click');
                    } else {
                        toastr.error(data.message || 'Failed to assign permission');
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error response:', xhr);
                    console.error('Status:', status);
                    console.error('Error:', error);

                    var errorMessage = 'Failed to assign permission';

                    if (xhr.responseJSON) {
                        if (xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        if (xhr.responseJSON.errors) {
                            var errors = xhr.responseJSON.errors;
                            var errorList = Object.values(errors).flat().join('<br>');
                            errorMessage = errorList;
                        }
                    } else if (xhr.responseText) {
                        errorMessage += ': ' + xhr.responseText;
                    }

                    toastr.error(errorMessage);
                }
            });
        }

        /**********  Remove Permission Override **************/
        $(document).on("click", ".btn-remove-permission", function () {
            var userId = $('#current-user-id').val();
            var permissionId = $(this).data('permission-id');

            if (!confirm('Remove this permission override? User will inherit from their role.')) {
                return;
            }

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: "/admin/users/" + userId + "/permissions/" + permissionId + "/revoke",
                method: "DELETE",
                success: function (data) {
                    if (data.success) {
                        toastr.success(data.message);
                        // Reload permissions
                        $('.btn-manage-permissions[data-id="' + userId + '"]').trigger('click');
                    }
                },
                error: function (xhr) {
                    console.error('Error:', xhr);
                    toastr.error('Failed to remove permission: ' + (xhr.responseJSON?.message || 'Unknown error'));
                }
            });
        });

        /**********  Reset form **************/
        $(document).on("click", ".resetform", function () {
            $('.error-text').text('');
            $('.help-block').remove();
            $('.has-error').removeClass('has-error');
            $("form").trigger("reset");
        });

        /****  Print errors*******/
        function printErrorMsg(parent, msg) {
            $(`${parent} .help-block`).remove();
            $(`${parent} .has-error`).removeClass('has-error');

            $.each(msg, function (key, errors) {
                for (const error in errors) {
                    const value = errors[error];
                    $(`${parent} [name='${key}']`).parent().addClass('has-error');
                    $(`<span class='help-block'>${value}</span>`).insertAfter(`${parent} [name='${key}']`);
                }
            });
        }
    </script>
@endsection