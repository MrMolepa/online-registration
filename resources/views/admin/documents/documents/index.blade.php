@extends('layouts.admin')
@section('content')
    <!-- MAIN -->
    <div class="main">
        <!-- MAIN CONTENT -->
        <div class="main-content">
            <div class="container-fluid">
                <h3 class="page-title">Documents <i class="fas fa-pencil-alt"></i> </h3>
                <div class="row">
                    <div class="col-md-12">
                        <!-- PANEL NO CONTROLS -->
                        <div class="panel">
                            <div class="panel-heading">
                                <h3 class="panel-title">Documents</h3>
                            </div>
                            <div class="panel-body">
                                <div class="pull-right">
                                    <a href="" class="btn btn-info" data-toggle="modal" data-target="#add-document">+
                                        Create
                                    </a>
                                </div>
                                <div class="clearfix"></div>


                                <table class="table" name="tablename" id="documents">
                                    <thead>
                                        <tr>
                                            <th>Action</th>
                                            <th>Name</th>
                                            <th>Document Category</th>
                                            <th>Storage</th>
                                            <th>Created By</th>
                                            <th>Created Date</th>
                                        </tr>
                                    </thead>
                                </table>
                                @push('scripts')
                                    <script>
                                        $(function() {
                                            var table = $("#documents").DataTable({
                                                ajax: "{{ route('admin.documents.index') }}",
                                                columns: [{
                                                        data: "actions",
                                                        name: "actions",
                                                        searchable: false,
                                                        orderable: false,
                                                    },

                                                    {
                                                        data: "document_name",
                                                        name: "document_name",
                                                        searchable: false,
                                                        orderable: false,
                                                    },
                                                    {
                                                        data: "categories.name",
                                                        name: "categories.name",
                                                        searchable: false,
                                                        orderable: false,
                                                    },
                                                    {
                                                        data: "location",
                                                        name: "location",
                                                        searchable: false,
                                                        orderable: false,
                                                    },

                                                    {
                                                        data: "users.document_user.email",
                                                        name: "users.document_user.email",

                                                    },

                                                    {
                                                        data: "created_date",
                                                        name: "created_date",

                                                    }

                                                ],

                                            });
                                        });
                                    </script>
                                @endpush


                            </div>
                        </div>
                    </div>
                    <!-- END PANEL NO CONTROLS -->
                </div>

            </div>
        </div>

        <!-- ADD DOCUMENT -->
        <div class="modal fade bd-modal-md" id="add-document" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close resetform" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h3 class="modal-title">Documents</h3>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('admin.documents.store') }}" method="post" id="addDocumentForm"
                            enctype="multipart/form-data">
                            <div>
                                @csrf
                            </div>
                            <div class="form-group col-md-6">
                                <label for="document_upload">Document Upload</label>
                                <input type="file" class="form-control" name="document_upload" id="document_upload" />
                            </div>
                            <div class="form-group col-md-6">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" name="name" id="name" value="" />
                            </div>
                            <div class="form-group col-md-6">
                                <label for="location">Storage</label>
                                <select class="form-control" id="storage" name="location">
                                    <option value="local">Local Disk (Default)</option>

                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="category_id">Category</label>
                                <select class="form-control" name="category_id" id="category_id">
                                    <option value="">Select category </option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @foreach ($category->childs as $child)
                                            <option value="{{ $child->id }}">---{{ $child->name }}</option>
                                        @endforeach
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="description">Description</label>
                                <textarea name="description" class="form-control" id="description" cols="30" rows="2"></textarea>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="description">Meta Tags</label>
                                <div class="input-group after-add-more">
                                    <input class="form-control" type="text" name="document_meta_datas[]" value="">
                                    <span class="input-group-btn"><button class="btn btn-primary add-document-meta"
                                            type="button"><i class="fas fa-plus"></i></button></span>
                                </div>

                            </div>
                            <div class="clearfix"></div>
                            <div class="form-group col-md-6">
                                <label for="description">Assign/share with roles</label>
                                <select class="roles-select2 form-select" id="document-roles-select2"
                                    name="document_role_Permission[roles][]" multiple="multiple">
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}">{{ $role->display_name }}</option>
                                    @endforeach
                                </select>
                                <label class="fancy-checkbox">
                                    <input type="checkbox" name="document_role_Permission[is_time_bound]" value="1">
                                    <span>Spacify the Period</span>
                                </label>
                                <div class="specify-period">
                                    <div class="date-inserted">
                                        <label for="name">Start Date</label>
                                        <input type="datetime-local" class="form-control"
                                            name="document_role_Permission[start_date]" id="start_date" value="" />
                                    </div>
                                    <div class="date-inserted">
                                        <label for="name">End Date</label>
                                        <input type="datetime-local" class="form-control"
                                            name="document_role_Permission[end_date]" id="end_date" value="" />
                                    </div>
                                </div>
                                <label class="fancy-checkbox">
                                    <input type="checkbox" name="document_role_Permission[is_allow_download]"
                                        value="1">
                                    <span>Allow Download</span>
                                </label>

                            </div>
                            <div class="form-group col-md-6">
                                <label for="description">Assign/share with users</label>
                                <select class=" form-select" name="document_user_Permission[users][]"
                                    id='document-users-select2' multiple="multiple">
                                    <option></option>
                                </select>
                                <label class="fancy-checkbox">
                                    <input type="checkbox" name="document_user_Permission[is_time_bound]" value="1">
                                    <span>Spacify the Period</span>
                                </label>
                                <div class="specify-period">
                                    <div class="date-inserted">
                                        <label for="name">Start Date</label>
                                        <input type="datetime-local" class="form-control"
                                            name="document_user_Permission[start_date]" id="start_date" value="" />
                                    </div>
                                    <div class="date-inserted">
                                        <label for="name">End Date</label>
                                        <input type="datetime-local" class="form-control"
                                            name="document_user_Permission[end_date]" id="end_date" value="" />
                                    </div>
                                </div>
                                <label class="fancy-checkbox">
                                    <input type="checkbox" name="document_user_Permission[is_allow_download]"
                                        value="1">
                                    <span>Allow Download</span>
                                </label>

                            </div>
                            <div class="clearfix"></div>
                        </form>
                        <!-- Copy Fields -->
                        <div class="fields-copy hidden">
                            <div class="input-group control-copy m-2">
                                <input class="form-control" type="text" name="document_meta_datas[]" value="">
                                <span class="input-group-btn"><button class="btn btn-danger remove" type="button"><i
                                            class='fas fa-trash'></i></button></span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="add-document" class="btn btn-primary"
                            id="save-document">Save</button>
                        <button type="button" class="btn btn-danger resetform" id="close"
                            data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>

        </div>
        <!--END ADD  DOCUMENT MODEL -->
        <!-- EDIT DOCUMENT -->
        <div class="modal fade bd-modal-md" id="edit-document-modal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close resetform" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h3 class="modal-title">Edit Documents</h3>
                    </div>
                    <div class="modal-body">
                        <form action="" method="post" id="editDocumentForm">
                            <div>
                                @csrf
                                @method('PUT')
                            </div>
                            <div class="form-group col-md-12">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" name="name" id="name"
                                    value="" />
                            </div>
                            <div class="form-group col-md-12">
                                <label for="location">Storage</label>
                                <select class="form-control" id="storage" name="location">
                                    <option value="local">Local Disk (Default)</option>
                                </select>
                            </div>
                            <div class="form-group col-md-12">
                                <label for="category_id">Category</label>
                                <select class="form-control" name="category_id" id="category_id">
                                    <option value="">Select category </option>
                                    @foreach ($categories as $category)
                                        <optgroup label="{{ $category->name }}">
                                            @foreach ($category->childs as $child)
                                                <option value="{{ $child->id }}">{{ $child->name }}</option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-12">
                                <label for="description">Description</label>
                                <textarea name="description" class="form-control" id="description" cols="30" rows="2"></textarea>
                            </div>
                            <div class="form-group col-md-12">
                                <label for="description">Meta Tags</label>
                                <div class="input-group after-add-more">
                                    <input class="form-control" type="text" name="document_meta_datas[]"
                                        value="">
                                    <span class="input-group-btn"><button class="btn btn-primary add-document-meta"
                                            type="button"><i class="fas fa-plus"></i></button></span>
                                </div>

                            </div>


                            <div class="clearfix"></div>
                        </form>
                        <!-- Copy Fields -->
                        <div class="fields-copy hidden">
                            <div class="input-group control-copy m-2">
                                <input class="form-control" type="text" name="document_meta_datas[]" value="">
                                <span class="input-group-btn"><button class="btn btn-danger remove" type="button"><i
                                            class='fas fa-trash'></i></button></span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="update-document" class="btn btn-primary"
                            id="update-document">Save</button>
                        <button type="button" class="btn btn-danger resetform" id="close"
                            data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>

        </div>
        <!--END EDIT DOCUMENT MODEL -->
        <!-- SHARE DOCUMENT -->
        <div class="modal fade" id="share-document-modal">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title">Share Document </h4>

                    </div>
                    <div class="container"></div>
                    <div class="modal-body">
                        <div class="pull-right">
                            <a href="" class="btn btn-info" data-toggle="modal"
                                data-target="#permissions-users-model">+
                                Assign/share with Users
                            </a>
                            <a href="" class="btn btn-info" data-toggle="modal"
                                data-target="#permissions-roles-model">+
                                Assign/share with Roles
                            </a>
                        </div>
                        <div class="clearfix"></div>
                        <table class="table" name="tablename" id="share-document-datatable">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Allow Download</th>
                                    <th>User/Role Name</th>
                                    <th>Email</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                        @push('scripts')
                            <script>
                                $(function() {

                                    $(document).on("click", ".share-document", function(ev) {
                                        ev.preventDefault();
                                        var url = $(this).attr("href");
                                        $.ajax({
                                            type: "GET",
                                            url: url,
                                            success: function(data) {
                                                var document = data;
                                                var urlpermissions =
                                                    '{{ route('admin.documents.permissions.index', ':id') }}';
                                                var urluserpermissions =
                                                    '{{ route('admin.documents.permissions.users.store', ':id') }}';
                                                var urlrolespermissions =
                                                    '{{ route('admin.documents.permissions.roles.store', ':id') }}';

                                                urlpermissions = urlpermissions.replace(':id', document.id);
                                                urluserpermissions = urluserpermissions.replace(':id', document.id);
                                                urlrolespermissions = urlrolespermissions.replace(':id', document.id);

                                                $("#permissions-users-form").attr('action', urluserpermissions);
                                                $("#permissions-roles-form").attr('action', urlrolespermissions);

                                                

                                                var table = $("#share-document-datatable").DataTable({
                                                    processing: true,
                                                    serverSide: true,
                                                    deferRender: true,
                                                    destroy: true,
                                                    "lengthMenu": [
                                                        [20, 50, 100, 200, 400, -1],
                                                        [20, 50, 100, 200, 400, "All"]
                                                    ],


                                                    ajax: {
                                                        url: `${urlpermissions}`,
                                                        error: function(xhr, error, code) {
                                                            console.log(xhr, code);
                                                        },
                                                        data: function(d) {
                                                            d.year = $("#year").val()
                                                        },

                                                    },

                                                    columns: [{
                                                            data: "type",
                                                            name: "type",
                                                        },
                                                        {
                                                            data: "is_allow_download",
                                                            name: "is_allow_download"
                                                        },
                                                        {
                                                            data: "name",
                                                            name: "name",
                                                        },
                                                        {
                                                            data: "email",
                                                            name: "email",
                                                        },
                                                        {
                                                            data: "start_date",
                                                            name: "start_date",
                                                        },
                                                        {
                                                            data: "end_date",
                                                            name: "end_date",
                                                        },
                                                        {
                                                            data: "actions",
                                                            name: "actions",
                                                            searchable: false,
                                                            orderable: false,
                                                        }

                                                    ],

                                                });


                                                $('#share-document-modal').modal('show');









                                            },
                                            error: function(data) {
                                                console.log('Error:', data);
                                            }
                                        });
                                    });

                                });
                            </script>
                        @endpush
                    </div>
                    <div class="modal-footer">
                        <a href="#" data-dismiss="modal" class="btn">Close</a>
                    </div>
                </div>
            </div>
        </div>
        <!--END SHARE DOCUMEN MODEL -->
        <!--ADD  PERMISSIONS USERS MODEL -->
        <div class="modal fade" id="permissions-users-model">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title">User Permission</h4>

                    </div>
                    <div class="container"></div>
                    <div class="modal-body">
                        <form action="" id="permissions-users-form" method="post">
                            <div>
                                @csrf
                            </div>

                            <div class="form-group col-md-12">
                                <label for="description">Assign/share with users</label>
                                <select class="form-select" name="document_user_Permission[users][]"
                                    id='permissions-users-select2' multiple="multiple">
                                    <option></option>

                                </select>
                                <label class="fancy-checkbox">
                                    <input type="checkbox" name="document_user_Permission[is_time_bound]" value="1">
                                    <span>Spacify the Period</span>
                                </label>
                                <div class="specify-period">
                                    <div class="date-inserted">
                                        <label for="name">Start Date</label>
                                        <input type="datetime-local" class="form-control"
                                            name="document_user_Permission[start_date]" id="start_date" value="" />
                                    </div>
                                    <div class="date-inserted">
                                        <label for="name">End Date</label>
                                        <input type="datetime-local" class="form-control"
                                            name="document_user_Permission[end_date]" id="end_date" value="" />
                                    </div>
                                </div>
                                <label class="fancy-checkbox">
                                    <input type="checkbox" name="document_user_Permission[is_allow_download]"
                                        value="1">
                                    <span>Allow Download</span>
                                </label>

                            </div>


                            <div class="clearfix"></div>

                        </form>
                    </div>
                    <div class="modal-footer"> <a href="#" data-dismiss="modal" class="btn">Close</a>
                        <a href="#" id="save-permissions-users" class="btn btn-primary">Save changes</a>

                    </div>
                </div>
            </div>
        </div>
        <!--END ADD  PERMISSIONS MODEL -->
        <!--ADD  ROLES USERS MODEL -->
        <div class="modal fade" id="permissions-roles-model">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title">Permissions Role</h4>
                    </div>
                    <div class="container"></div>
                    <div class="modal-body">
                        <form action="" id="permissions-roles-form" method="post">
                            <div>
                                @csrf
                            </div>
                            <div class="form-group col-md-12">
                                <label for="description">Assign/share with roles</label>
                                <select class="form-select" id="permissions-roles-select2"
                                    name="document_role_Permission[roles][]" multiple="multiple">
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}">{{ $role->display_name }}</option>
                                    @endforeach
                                </select>
                                <label class="fancy-checkbox">
                                    <input type="checkbox" name="document_role_Permission[is_time_bound]" value="1">
                                    <span>Spacify the Period</span>
                                </label>
                                <div class="specify-period">
                                    <div class="date-inserted">
                                        <label for="name">Start Date</label>
                                        <input type="datetime-local" class="form-control"
                                            name="document_role_Permission[start_date]" id="start_date" value="" />
                                    </div>
                                    <div class="date-inserted">
                                        <label for="name">End Date</label>
                                        <input type="datetime-local" class="form-control"
                                            name="document_role_Permission[end_date]" id="end_date" value="" />
                                    </div>
                                </div>
                                <label class="fancy-checkbox">
                                    <input type="checkbox" name="document_role_Permission[is_allow_download]"
                                        value="1">
                                    <span>Allow Download</span>
                                </label>

                            </div>
                            <div class="clearfix"></div>
                        </form>
                    </div>
                    <div class="modal-footer"> <a href="#" data-dismiss="modal" class="btn">Close</a>
                        <a href="#" id="save-permissions-roles" class="btn btn-primary">Save changes</a>
                    </div>
                </div>
            </div>
        </div>
        <!--END ADD  ROLES MODEL -->

        <!--ADD  COMMENTS MODEL -->
        <div class="modal fade" id="comments-model">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title">Comments</h4>
                    </div>
                    <div class="container"></div>
                    <div class="modal-body">

                        <form action="{{ route('admin.documents-comments.store') }}" id="comment-form" method="post">
                            <div>
                                @csrf
                            </div>
                            <div class="form-group col-md-12" id="comments-panel">
                                <div class="comments">
                                </div>
                            </div>
                            <div class="clearfix"></div>
                        </form>
                    </div>
                    <div class="modal-footer"> <a href="#" data-dismiss="modal" class="btn">Close</a>
                        <a href="#" id="save-comments" class="btn btn-primary">Save changes</a>
                    </div>
                </div>
            </div>
        </div>
        <!--END  ADD COMMENTS MODEL -->

         <!--ADD  Versions MODEL -->
         <div class="modal fade" id="add-version-history-model">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title">Comments</h4>
                    </div>
                    <div class="container"></div>
                    <div class="modal-body">
                        <form action="{{ route('admin.documents-comments.store') }}" id="version-form" method="post">
                            <div>
                                @csrf
                            </div>
                            <div class="form-group col-md-12" id="comments-panel">

                            </div>
                            <div class="clearfix"></div>
                        </form>
                    </div>
                    <div class="modal-footer"> <a href="#" data-dismiss="modal" class="btn">Close</a>
                        <a href="#" id="save-comments" class="btn btn-primary">Save changes</a>
                    </div>
                </div>
            </div>
        </div>
        <!-- ADD END  Versions MODEL -->



         <!-- Versions MODEL -->
         <div class="modal fade" id="version-history-model">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title">Version History</h4>
                    </div>
                    <div class="container"></div>
                    <div class="modal-body">
                        <table class="table" name="tablename" id="version-history-datatable">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>End Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                        @push('scripts')
                            <script>
                                $(function() {

                                    $(document).on("click", ".version-document", function(ev) {
                                        ev.preventDefault();
                                        var url = $(this).attr("href");
                                        $.ajax({
                                            type: "GET",
                                            url: url,
                                            success: function(data) {
                                                var document = data;

                                                var table = $("#version-history-datatable").DataTable({
                                                    processing: true,
                                                    serverSide: true,
                                                    deferRender: true,
                                                    destroy: true,
                                                    "lengthMenu": [
                                                        [20, 50, 100, 200, 400, -1],
                                                        [20, 50, 100, 200, 400, "All"]
                                                    ],
                                                    ajax: {
                                                        url: `{{ route('admin.documents-versions.index') }}`,
                                                        error: function(xhr, error, code) {
                                                            console.log(xhr, code);
                                                        },
                                                        data: function(d) {
                                                            d.id = document.id
                                                        },

                                                    },

                                                    //'created_by', 'modified_date','is_current_version'

                                                    columns: [{
                                                            data: "modified_date",
                                                            name: "modified_date",
                                                        },
                                                        {
                                                            data: "created_by",
                                                            name: "created_by"
                                                        },

                                                        {
                                                            data: "is_current_version",
                                                            name: "is_current_version",
                                                            searchable: false,
                                                            orderable: false,
                                                        }

                                                    ],

                                                });


                                                $('#version-history-model').modal('show');









                                            },
                                            error: function(data) {
                                                console.log('Error:', data);
                                            }
                                        });
                                    });

                                });
                            </script>
                        @endpush
                    </div>
                    <div class="modal-footer"> <a href="#" data-dismiss="modal" class="btn">Close</a>
                        <a href="#" id="save-comments" class="btn btn-primary">Save changes</a>
                    </div>
                </div>
            </div>
        </div>
        <!-- END  Versions MODEL -->







    </div>
    <!-- END MAIN CONTENT -->
    <!-- END MAIN -->
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
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Add this somewhere before the ajax
        var groupBy = function(xs, key) {
            return xs.reduce(function(rv, x) {
                (rv[x[key]] = rv[x[key]] || []).push(x);
                return rv;
            }, {});
        };




        $(".add-document-meta").click(function() {
            var html = $(".fields-copy").html();
            $(".after-add-more").after(html);
        });

        $(document).on("click", ".remove", function() {
            $(this).parents(".control-copy").remove();


        });


        $(document).on("change", "[name='document_user_Permission[is_time_bound]']", function() {
            $(this).parent().next().css({
                'display': !this.checked ? 'none' : 'flex'
            })
        });

        $(document).on("change", "[name='document_role_Permission[is_time_bound]']", function() {
            $(this).parent().next().css({
                'display': !this.checked ? 'none' : 'flex'
            })
        });

        $(document).ready(function() {
            $('#document-roles-select2').select2({
                width: '100%',
                containerCss: {
                    "display": "block"
                }
            });

            $('#permissions-roles-select2').select2({
                width: '100%',
                containerCss: {
                    "display": "block"
                }
            });





            $("#document-users-select2").select2({
                placeholder: "Select User",
                dropdownParent: $("#add-document"),
                ajax: {
                    url: "{{ route('admin.documents.getRoleUser') }}",
                    method: "GET",
                    dataType: "json",
                    delay: 250,
                    processResults: function(data) {
                        return {
                            results: $.map(data, function(user) {
                                return {
                                    text: user.document_user.email,
                                    id: user.id,
                                };
                            }),
                        };
                    },
                    cache: true,
                    error: function(jqXHR, status, error) {
                        console.log(error + ": " + jqXHR);
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


            $("#permissions-users-select2").select2({
                placeholder: "Select User",
                dropdownParent: $("#permissions-users-model"),
                ajax: {
                    url: "{{ route('admin.documents.getRoleUser') }}",
                    method: "GET",
                    dataType: "json",
                    delay: 250,
                    processResults: function(data) {
                        return {
                            results: $.map(data, function(user) {
                                return {
                                    text: user.document_user.email,
                                    id: user.id,
                                };
                            }),
                        };
                    },
                    cache: true,
                    error: function(jqXHR, status, error) {
                        console.log(error + ": " + jqXHR);
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





        });

        // ADD Documents
        $(document).on('click', '#save-document', function(ev) {
            ev.preventDefault();
            var url = $('#addDocumentForm').attr('action');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            //File data
            var formData = new FormData($('#addDocumentForm')[0]);
            // var caption = element.html();
            $.ajax({
                url: "{{ route('admin.documents.store') }}",
                method: "POST",
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
                beforeSend: function() {
                    // element.prop('disabled', true).html("Processing.....");
                },
                success: function(data) {
                    console.log(data);
                    if ($.isEmptyObject(data.errors)) {
                        $('#add-document').modal('hide');
                        toastr.success(data.success);
                        $('#documents').DataTable().ajax.reload();
                    } else {
                        printErrorMsg('#addDocumentForm', data.errors);
                    }

                },
                error: function(error) {
                    console.log(error);
                    // element.prop('disabled', false).html(caption);
                },
                complete: function(data) {
                    // element.prop('disabled', false).html(caption);
                }
            });




        });
        //END ADD Documents
        //EDIT Documents
        $(document).on("click", ".edit-document", function(ev) {
            ev.preventDefault();
            var url = $(this).attr("href");

            $('.after-add-more').nextAll().remove();
            $.ajax({
                type: "GET",
                url: url,
                success: function(data) {
                    var document = data;
                    var urlUpdate = '{{ route('admin.documents.update', ':id') }}';
                    urlUpdate = urlUpdate.replace(':id', document.id);
                    $("#editDocumentForm").attr('action', urlUpdate);
                    $('#edit-document-modal').modal('show');
                    var form = '#editDocumentForm';
                    $(`${form} :input:not([type=hidden]), ${form} select,${form} textarea`).each(
                        function(index) {
                            var input = $(this);
                            var name = $(this).attr('name');
                            if (name?.indexOf('[]') > -1) {
                                name = name.replace('[]', '');
                                if (!$.isEmptyObject(document[name])) {
                                    var total_inputs = Object.keys(document[name]).length
                                    for (let index = 0; index < total_inputs - 1; index++) {
                                        var html = $(".fields-copy").html();
                                        $(".after-add-more").after(html);
                                    }
                                    var index = 0;
                                    $.each(document[name], function(key, item) {
                                        $(`${form} [name^='${name}']`).eq(index).val(item);
                                        index++;
                                    });
                                }
                            } else {
                                $(`${form} [name='${name}']`).val(document[name]);
                            }
                        }
                    );

                },
                error: function(data) {
                    console.log('Error:', data);
                }
            });
        });
        //END EDIT Documents
        // UPDATE Documents
        $(document).on('click', '#update-document', function(e) {
            var editForm = $("#editDocumentForm");
            var url = editForm.attr('action');
            $.ajax({
                type: "POST",
                data: editForm.serializeArray(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                success: function(data) {
                    console.log(data);
                    if ($.isEmptyObject(data.errors)) {
                        $('#edit-document-modal').modal('hide');
                        toastr.success(data.success);
                        $('#documents').DataTable().ajax.reload();
                    } else {
                        printErrorMsg('#editDocumentForm', data.errors);
                    }


                }
            });
        });
        //DELETE Documents

        $(document).on('click', '.delete-document', function(ev) {
            ev.preventDefault();
            var url = $(this).attr("href");
            if (confirm("Are you sure you want to delete this Document!") == true) {
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
                            $('#documents').DataTable().ajax.reload();
                        }else{
                            toastr.error("error");
                        }
                    }
                });


            } else {
                return;
            }

        });

        //DELETE Documents

        //END UPDATE Documents
        //DOWNLOAD Documents
        $(document).on('click', '.download-document', function(ev) {
            ev.preventDefault();
            var url = $(this).attr('href');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            //File data
            // var caption = element.html();
            let filename = '';
            fetch(url)
                .then(resp => {
                    const header = resp.headers.get('Content-Disposition');
                    const parts = header?.split(';');
                    filename = parts[1].split('=')[1];
                    return resp.blob()
                })
                .then(blob => {
                    console.log(blob)
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.style.display = 'none';
                    a.href = url;
                    // the filename you want
                    a.download = filename;
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);
                    toastr.success(`Successfully downloaded ${filename}`);

                })
                .catch(() => toastr.error(`Failed to downloaded the file`));

        });
        //END DOWNLOAD Documents
        // ADD User Permission
        $(document).on('click', '#save-permissions-users', function(ev) {
            ev.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            var form = $("#permissions-users-form");
            var url = form.attr('action');
            $.ajax({
                url: url,
                method: "POST",
                data: form.serializeArray(),
                beforeSend: function() {

                },
                success: function(data) {
                    console.log(data);

                    if ($.isEmptyObject(data.errors)) {
                        $('#permissions-users-model').modal('hide');
                        toastr.success(data.success);
                        $('#share-document-datatable').DataTable().ajax.reload();
                    } else {
                        printErrorMsg('#permissions-users-form', data.errors);
                    }
                },
                error: function(error) {
                    console.log(error);

                },
                complete: function(data) {

                }
            });




        });
        // END ADD User Permission
        // ADD Roles Permission
        $(document).on('click', '#save-permissions-roles', function(ev) {
            ev.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            var form = $("#permissions-roles-form");
            var url = form.attr('action');
            $.ajax({
                url: url,
                method: "POST",
                data: form.serializeArray(),
                beforeSend: function() {

                },
                success: function(data) {
                    console.log(data);
                    if ($.isEmptyObject(data.errors)) {
                        $('#permissions-roles-model').modal('hide');
                        toastr.success(data.success);
                        $('#share-document-datatable').DataTable().ajax.reload();
                    } else {
                        printErrorMsg('#permissions-roles-form', data.errors);
                    }
                },
                error: function(error) {
                    console.log(error);

                },
                complete: function(data) {

                }
            });




        });
        // END ADD Roles  Permission
        // DELETE  Permission
        $(document).on('click', '.delete-permission', function(ev) {
            ev.preventDefault();
            var url = $(this).data('url');
            if (confirm("Are you sure you want to delete this permission?") == true) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: url,
                    method: "DELETE",
                    success: function(data) {
                        console.log(data);
                        if (data.success) {
                            toastr.success(data.success);
                            $("#share-document-datatable").DataTable().ajax.reload();
                        }
                    }
                });

            } else {
                return;
            }

        });
        //END DELETE Permission
        // SHOW comments
        $(document).on("click", ".comments-document", function(ev) {
            ev.preventDefault();
            var url = $(this).attr("href");
            $.ajax({
                type: "GET",
                url: url,
                success: function(data) {
                    $('.comments').html(data.comments);
                    $('#comments-model').modal('show');
                },
                error: function(data) {
                    console.log('Error:', data);
                }
            });
        });
        //END SHOW comments
        //ADD comments
        $(document).on('click', '#save-comments', function(ev) {
            ev.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            var form = $("#comment-form");
            var url = form.attr('action');
            $.ajax({
                url: url,
                method: "POST",
                data: form.serializeArray(),
                beforeSend: function() {

                },
                success: function(data) {
                    console.log(data);
                    if ($.isEmptyObject(data.errors)) {
                        $(".comments-document").trigger("click");
                        toastr.success(data.success);
                    } else {
                        printErrorMsg('#comment-form', data.errors);
                    }
                },
                error: function(error) {
                    console.log(error);

                },
                complete: function(data) {

                }
            });




        });
        //END ADD comments












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
                    console.log(key);
                    $(`[name='${key}']`).parent().addClass('has-error');
                    if (key == "gender") {
                        $(`${parent} [name='${key}']`).next().append(`<span class='help-block'>${value}</span>`);
                    } else if (key.indexOf('.') > -1) {
                        console.log(key);
                        var input = key.split(".");
                        var parentinput = input[0];
                        var childinput = input[1];
                        $(`[name^='${parentinput}[${childinput}]']`).parent().addClass('has-error');
                        $(`<span class='help-block'>${value}</span>`).insertAfter(
                            `${parent} [name^='${parentinput}[${childinput}]']`)
                    } else {
                        $(`<span class='help-block'>${value}</span>`).insertAfter(`${parent} [name='${key}']`)
                    }


                }
            });
        }
        /****  Print errors End*******/
    </script>
@endsection
