@extends('layouts.admin')
@section('content')
    <!-- MAIN -->
    <div class="main">
        <!-- MAIN CONTENT -->
        <div class="main-content">
            <div class="container-fluid">
                <h3 class="page-title">Templates </h3>
                <div class="row">
                    <div class="col-md-12">
                        <!-- PANEL NO CONTROLS -->
                        <div class="panel">
                            <div class="panel-heading">
                                <h3 class="panel-title">Templates</h3>
                            </div>
                            <div class="panel-body">
                                <div class="custom-tabs-line tabs-line-bottom left-aligned">
                                    <ul class="nav" role="tablist">
                                        <li class="active"><a href="#template-tab" role="tab"
                                                data-toggle="tab">Templates
                                            </a></li>
                                        <li><a href="#category-tab" role="tab" data-toggle="tab">Category</a></li>
                                    </ul>
                                </div>

                                <div class="tab-content">
                                    <div class="tab-pane fade in active" id="template-tab">
                                        <div class="pull-right">
                                            <a class="btn btn-info template-btn" data-toggle="modal"
                                                data-target="#add-template">+
                                                Create
                                            </a>
                                        </div>
                                        <div class="clearfix"></div>
                                        <div class="table-responsive">
                                            <table class="table" name="tablename" id="templates">
                                                <thead>
                                                    <tr>
                                                        <th>Name</th>
                                                        <th>Description</th>
                                                        <th>Orientation</th>
                                                        <th>Table: Source</th>
                                                        <th>Designer</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                            </table>
                                            @push('scripts')
                                                <script>
                                                    $(function() {

                                                        var table = $("#templates").DataTable({
                                                            ajax: "{{ route('admin.pdf.templates.index') }}",
                                                            columns: [{
                                                                    data: "name",
                                                                    name: "name",
                                                                },
                                                                {
                                                                    data: "description",
                                                                    name: "description"
                                                                },
                                                                {
                                                                    data: "orientation",
                                                                    name: "orientation"
                                                                },
                                                                {
                                                                    data: "data_source",
                                                                    name: "data_source"
                                                                },
                                                                {
                                                                    data: "designer",
                                                                    name: "designer"
                                                                },
                                                                {
                                                                    data: "actions",
                                                                    name: "actions",
                                                                    searchable: false,
                                                                    orderable: false,
                                                                },
                                                            ],

                                                        });



                                                        $("#templates").css("width", "98.5%");
                                                    });
                                                </script>
                                            @endpush
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="category-tab">

                                        <div class="pull-right">
                                            <a href="" class="btn btn-info category-btn" data-id="">+
                                                Create
                                            </a>
                                        </div>
                                        <div class="clearfix"></div>
                                        <div class="table-responsive">
                                            <table class="table" name="tablename" id="categories">
                                                <thead>
                                                    <tr>
                                                        <th></th>
                                                        <th>Actions</th>
                                                        <th>Name</th>
                                                        <th>Description</th>
                                                    </tr>
                                                </thead>


                                            </table>
                                            @push('scripts')
                                                <script>
                                                    $(function() {

                                                        var table = $("#categories").DataTable({
                                                            ajax: "{{ route('admin.pdf.categories.index') }}",
                                                            columns: [{
                                                                    className: 'dt-control',
                                                                    data: null,
                                                                    name: 'subject_code',
                                                                    "defaultContent": '',
                                                                    searchable: false,
                                                                    orderable: false,
                                                                },
                                                                {
                                                                    data: "actions",
                                                                    name: "actions",
                                                                    searchable: false,
                                                                    orderable: false,
                                                                },
                                                                {
                                                                    data: "name",
                                                                    name: "name",
                                                                },
                                                                {
                                                                    data: "description",
                                                                    name: "description"
                                                                },
                                                                {
                                                                    data: "subcategories",
                                                                    name: "subcategories",
                                                                    visible: false
                                                                }
                                                            ],
                                                            order: [
                                                                [1, "asc"]
                                                            ]
                                                        });



                                                        function format(d) {



                                                            var subcategories = JSON.parse(d.subcategories);
                                                            var category = "";

                                                            $.each(subcategories, function(i, subcategory) {
                                                                category += `<tr>
                                                                        <td>${subcategory.actions}</td>
                                                                        <td>${subcategory.name}</td>
                                                                        <td>${subcategory.description}</td>
                                                                        </tr>`;

                                                            });

                                                            return (
                                                                ` <div class='pull-right mt-5'>
                                                                <a href='' class='btn btn-info category-btn' data-id='${d.id}'>+
                                                                     Child Category
                                                                </a>
                                                            </div>
                                                        <div class='clearfix'></div>
                                                        <table class='table mb-0'>
                                                           <tr class='table-primary'>
                                                            <th></th>
                                                             <th>Name</th>
                                                             <th>Description</th>
                                                           </tr>
                                                           ${category}
                                                            </table>`
                                                            );
                                                        }
                                                        $("#categories tbody").on("click", "td.dt-control", function() {
                                                            var tr = $(this).closest("tr");
                                                            var row = table.row(tr);

                                                            if (row.child.isShown()) {
                                                                row.child.hide();
                                                                tr.removeClass("shown");
                                                            } else {
                                                                row.child(format(row.data()), "p-0").show();
                                                                tr.addClass("shown");
                                                            }
                                                        });

                                                        $("#categories").css("width", "98.5%");





                                                    });
                                                </script>
                                            @endpush
                                        </div>
                                    </div>
                                </div>





                            </div>
                        </div>
                    </div>
                    <!-- END PANEL NO CONTROLS -->
                </div>

            </div>
        </div>


        <!-- ADD TEMPLATE -->
        <div class="modal fade bd-modal-md" id="add-template" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close resetform" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h3 class="modal-title">New Template</h3>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('admin.pdf.templates.store') }}" method="post" id="addTemplateForm">
                            <div>
                                @csrf
                            </div>
                            <div class="form-group col-md-6">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" name="name" id="name" value="" />
                            </div>
                            <div class="form-group col-md-6">
                                <label for="thumbnail">Thumbnail</label>
                                <input type="file" name="thumbnail" class="form-control" value="" id="thumbnail" />
                            </div>
                            <div class="clearfix"></div>
                            <div class="form-group col-md-6">
                                <label for="category_id">Category</label>
                                <select name="category_id" class="form-control">
                                    <option value="">Select category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="orientation">Orientation</label>
                                <select class="form-control" id="orientation" name="orientation">
                                    <option value="portrait">Portrait</option>
                                    <option value="landscape">Landscape</option>
                                </select>
                            </div>
                            <div class="clearfix"></div>
                            <div class="form-group col-md-12">
                                <label for="description">Description</label>
                                <textarea name="description" class="form-control" id="description" cols="30" rows="2"></textarea>
                            </div>
                            <div class="form-group col-md-12">
                                <label for="data_source">Data Source (optional):</label>
                                <select id="data_source" name="data_source" class="form-control table-name">
                                    <option value="">-- Select Table --</option>
                                    @foreach ($availableTables as $table)
                                        <option value="{{ $table }}">{{ $table }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-12">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="is_table_filters"
                                        value="1" id="is_table_filters">
                                    <label for="is_table_filters">Are table filters ?</label>
                                </div>
                            </div>
                            <div class="form-group col-md-12" id="columns-container" style="display: none;">
                                <label>Select Columns filters:</label>
                                <div id="columns-list"
                                    style="max-height: 150px; overflow-y: auto; border: 1px solid #ddd; padding: 5px;">
                                </div>
                            </div>

                            <div class="form-group col-md-12">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="is_blank" checked
                                        value="1" id="is_blank">
                                    <label for="is_blank">Thumbnail required</label>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="add-template" class="btn btn-primary"
                            id="save-template">Save</button>
                        <button type="button" class="btn btn-danger resetform" id="close"
                            data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>

        </div>
        <!--END ADD TEMPLATE MODEL -->


        <!-- UPDATE TEMPLATE -->
        <div class="modal fade bd-modal-md" id="edit-template" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close resetform" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h3 class="modal-title">Update Template</h3>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('admin.pdf.templates.store') }}" method="post" id="editTemplateForm">
                            <div>
                                @csrf
                                @method('PUT')

                            </div>

                            <div class="form-group col-md-6">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" name="name" id="name"
                                    value="" />
                            </div>
                            <div class="form-group col-md-6">
                                <label for="thumbnail">Thumb nail</label>
                                <input type="file" name="thumbnail" class="form-control" value=""
                                    id="thumbnail" />
                            </div>
                            <div class="clearfix"></div>
                            <div class="form-group col-md-6">
                                <label for="category_id">Category</label>
                                <select name="category_id" class="form-control" id="category_id">
                                    <option value="">Select category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="orientation">Orientation</label>
                                <select class="form-control" id="orientation" name="orientation">
                                    <option value="portrait">Portrait</option>
                                    <option value="landscape">Landscape</option>
                                </select>
                            </div>
                            <div class="clearfix"></div>
                            <div class="form-group col-md-12">
                                <label for="description">Description</label>
                                <textarea name="description" class="form-control" id="description" cols="30" rows="2"></textarea>
                            </div>
                            <div class="form-group col-md-12">
                                <label for="data_source">Data Source (optional):</label>
                                <select id="data_source" name="data_source" class="form-control table-name">
                                    <option value="">-- Select Table --</option>
                                    @foreach ($availableTables as $table)
                                        <option value="{{ $table }}">{{ $table }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-12">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="is_table_filters"
                                        value="1" id="is_table_filters">
                                    <label for="is_table_filters">Are table filters</label>
                                </div>
                            </div>
                            <div class="form-group col-md-12" id="columns-container" style="display: none;">
                                <label>Select Columns filters:</label>
                                <div id="columns-list"
                                    style="max-height: 150px; overflow-y: auto; border: 1px solid #ddd; padding: 5px;">
                                </div>
                            </div>

                            <div class="form-group col-md-12">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="is_blank" value="1"
                                        id="is_blank">
                                    <label for="is_blank">blank</label>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="update-template" class="btn btn-primary"
                            id="update-template">Save</button>
                        <button type="button" class="btn btn-danger resetform" id="close"
                            data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>

        </div>
        <!--END UPDATE  TEMPLATE MODEL -->

        <!-- ADD Category -->
        <div class="modal fade bd-modal-md" id="add-category" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close resetform" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h3 class="modal-title">New Category</h3>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('admin.pdf.categories.store') }}" method="post" id="addCategoryForm">
                            <div>
                                @csrf
                                <input type="hidden" id="parent_id" name="parent_id" value="">
                            </div>
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" name="name" id="name"
                                    value="" />
                            </div>
                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea name="description" class="form-control" id="description" cols="30" rows="2"></textarea>
                            </div>
                        </form>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="add-category" class="btn btn-primary"
                            id="save-category">Save</button>
                        <button type="button" class="btn btn-danger resetform" id="close"
                            data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>

        </div>
        <!--END ADD Category MODEL -->
        <!-- UPDATE Category -->
        <div class="modal fade bd-modal-md" id="update-category-model" tabindex="-1" role="dialog"
            aria-hidden="true">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close resetform" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h3 class="modal-title">New Category</h3>
                    </div>
                    <div class="modal-body">
                        <form action="" method="post" id="updateCategoryForm">
                            <div>
                                @csrf
                                @method('PUT')
                            </div>
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" name="name" id="name"
                                    value="" />
                            </div>
                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea name="description" class="form-control" id="description" cols="30" rows="2"></textarea>
                            </div>
                        </form>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="update-category" class="btn btn-primary"
                            id="update-category">Save</button>
                        <button type="button" class="btn btn-danger resetform" id="close"
                            data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>

        </div>
        <!--END UPDATE Category MODEL -->
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


        $(document).on('click', '.category-btn', function(ev) {
            ev.preventDefault();
            var parent_id = $(this).data('id');
            var parent = '#addCategoryForm';
            $(`${parent} [name='parent_id']`).val(parent_id);
            $('#add-category').modal('show');
        });

        //Add Category
        $(document).on('click', '#save-category', function(ev) {
            ev.preventDefault();
            var url = $('#addCategoryForm').attr('action');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            var inputData = $("#addCategoryForm").serialize();
            $.ajax({
                url: url,
                method: "POST",
                data: inputData,
                success: function(data) {
                    if ($.isEmptyObject(data.errors)) {
                        $('#add-category').modal('hide');
                        $('#addCategoryForm .help-block').remove();
                        $('#addCategoryForm .has-error').removeClass('has-error');
                        toastr.success(data.success);
                        $('#categories').DataTable().ajax.reload();
                    } else {
                        printErrorMsg('#addCategoryForm', data.errors);
                    }


                }
            });


        });
        //Edit Category
        $(document).on('click', '.edit-category', function() {
            var url = $(this).data("url");
            $.ajax({
                type: "GET",
                url: url,
                success: function(data) {
                    $('#update-category-model').modal('show');
                    var category = data.category;
                    var url = data.url;
                    var form = '#updateCategoryForm';
                    $('#updateCategoryForm').attr('action', url);
                    $(`${form} :input:not([type=hidden]), ${form} select,${form} textarea`).each(
                        function(index) {
                            var input = $(this);
                            console.log('Type: ' + input.attr('type') + 'Name: ' + input.attr(
                                    'name') +
                                'Value: ' + input.val());
                            var name = input.attr('name');
                            console.log()

                            $(`${form} #${name}`).val(category[name]);


                        }
                    );
                },
                error: function(data) {
                    console.log('Error:', data);
                }
            });
        });
        //Update Category
        $(document).on('click', '#update-category', function(e) {
            var editForm = $("#updateCategoryForm");
            var url = editForm.attr('action');
            $.ajax({
                type: "POST",
                data: editForm.serializeArray(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                success: function(data) {
                    if ($.isEmptyObject(data.errors)) {
                        $('#update-category-model').modal('hide');
                        toastr.success(data.success);
                        $('#categories').DataTable().ajax.reload();
                    } else {
                        printErrorMsg('#updateCategoryForm', data.errors);
                    }


                }
            });
        });
        //Delete Category
        $(document).on('click', '.delete-category', function(ev) {
            ev.preventDefault();
            var url = $(this).data('url');
            if (confirm("Are you sure you want to delete thiscategory!") == true) {
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
                            $('#categories').DataTable().ajax.reload();
                        } else {
                            toastr.error(data.error);
                        }



                    }
                });


            } else {
                return;
            }

        });

        // Add TEMPLATE
        $(document).on('click', '#save-template', function(ev) {
            ev.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var url = $('#addTemplateForm').attr('action');
            var $btn = $(this);
            // Disable the button
            $btn.prop('disabled', true);
            // Add loading text (optional)
            $btn.html('<i class="fa fa-spinner fa-spin"></i> Processing...');

            //File data
            var formData = new FormData($('#addTemplateForm')[0]);

            $.ajax({
                url: url,
                method: "POST",
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
                success: function(data) {
                    if ($.isEmptyObject(data.errors)) {
                        $('#add-template').modal('hide');
                        $('#addTemplateForm .help-block').remove();
                        $('#addTemplateForm .has-error').removeClass('has-error');
                        toastr.success(data.success);
                        $('#templates').DataTable().ajax.reload();
                    } else {
                        printErrorMsg('#addTemplateForm', data.errors);
                    }
                },
                error: function(error) {
                    console.log(error);
                },
                complete: function(data) {
                    // Re-enable button and restore original text when request is complete
                    $btn.prop('disabled', false);
                    $btn.text('Submit');
                }
            });

        });
        // EDIT TEMPLATE
        $(document).on('click', '.edit-template', function(ev) {
            var url = $(this).data("url");
            $.ajax({
                type: "GET",
                url: url,
                success: function(data) {
                    $('#edit-template').modal('show');
                    var template = data.template;
                    var url = data.url;
                    var form = '#editTemplateForm';
                    $(`${form} #name`).val(template.name);
                    $(`${form} #category_id`).val(template.category_id);
                    $(`${form} #orientation`).val(template.orientation);
                    $(`${form} #description`).val(template.description);
                    $(`${form} #data_source`).val(template.data_source);
                    $(`${form} input[type="checkbox"][name="is_blank"]`).prop('checked', template
                        .is_blank == 1);
                    $(` ${form} input[type="checkbox"][name="is_table_filters"]`).prop('checked',
                        template.is_table_filters == 1);
                    getColumns('editTemplateForm', template.data_source, template.column_filters);
                    $(form).attr('action', url);
                },
                error: function(data) {
                    console.log('Error:', data);
                }
            });
        });
        // UPDATE TEMPLATE
        $(document).on('click', '#update-template', function(ev) {
            ev.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            var editForm = $("#editTemplateForm");
            var url = editForm.attr('action');
            var $btn = $(this);
            // Disable the button
            $btn.prop('disabled', true);
            // Add loading text (optional)
            $btn.html('<i class="fa fa-spinner fa-spin"></i> Processing...');
            //File data
            var formData = new FormData(editForm[0]);
            $.ajax({
                url: url,
                method: "POST",
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
                success: function(data) {
                    console.log(data);
                    if ($.isEmptyObject(data.errors)) {
                        $('#edit-template').modal('hide');
                        toastr.success(data.success);
                        $('#templates').DataTable().ajax.reload();
                    } else {
                        printErrorMsg("#editTemplateForm", data.errors);
                    }
                },
                error: function(error) {
                    console.log(error);
                },
                complete: function(data) {
                    // Re-enable button and restore original text when request is complete
                    $btn.prop('disabled', false);
                    $btn.text('Update');
                }
            });
        });

        // DELETE TEMPLATE
        $(document).on('click', '.delete-template', function(ev) {
            ev.preventDefault();
            var url = $(this).data('url');
            if (confirm("Are you sure you want to delete this template!") == true) {
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
                            $('#templates').DataTable().ajax.reload();
                        } else {
                            toastr.error(data.error);
                        }



                    }
                });

            } else {
                return;
            }

        });

        $(document).on('change', '.table-name', function() {
            var table = $(this).val();
            var parentFormId = $(this).parents('form:first').attr('id');;
            getColumns(parentFormId, table);
        });



        function getColumns(parentFormId, table, checkedColumns = []) {
            if (table) {
                var url = "{{ route('admin.pdf.designer.table-columns', ':table') }}";
                url = url.replace(':table', table);
                const checkedColumnKeys = checkedColumns == null ? [] : [...Object.keys(checkedColumns)];
                $.get(url, function(columns) {
                    const $columnsList = $(`#${parentFormId} #columns-list`);
                    $columnsList.empty();
                    columns.forEach(function(column,index) {
                        isChecked = checkedColumnKeys?.includes(column) ? 'checked' : ' '
                        checkedColumnValue = checkedColumnKeys?.includes(column) ? checkedColumns[column] :
                            '';

                        $columnsList.append(`
                                    <div class="form-check">
                                        <input class="form-check-input column-checkbox"
                                           name="columns[${index}]"
                                            type="checkbox"
                                            value="${column}"
                                            id="col-${column}"
                                            ${isChecked}>
                                        <label class="form-check-label" for="col-${column}">
                                            ${column}  <input  name="column_values[${index}]" value="${checkedColumnValue || ''}"   type="text" />
                                        </label>

                                    </div>
                                `);
                    });
                    tableColumns = columns;
                    $(`#${parentFormId} #columns-container`).show();
                });
            } else {
                $(`#${parentFormId} #columns-container`).hide();
            }

        }









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
