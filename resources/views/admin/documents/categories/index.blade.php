@extends('layouts.admin')
@section('content')
    <!-- MAIN -->
    <div class="main">
        <!-- MAIN CONTENT -->
        <div class="main-content">
            <div class="container-fluid">
                <h3 class="page-title">Categories </h3>
                <div class="row">
                    <div class="col-md-12">
                        <!-- PANEL NO CONTROLS -->
                        <div class="panel">
                            <div class="panel-heading">
                                <h3 class="panel-title">Categories</h3>
                            </div>
                            <div class="panel-body">
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
                                                    ajax: "{{ route('admin.document-categories.index') }}",
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
                                                    console.log(d);


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
                        <form action="{{ route('admin.document-categories.store') }}" method="post" id="addCategoryForm">
                            <div>
                                @csrf
                                <input type="hidden" id="parent_id" name="parent_id" value="">
                            </div>
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" name="name" id="name" value="" />
                            </div>
                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea name="description" class="form-control" id="description" cols="30" rows="2"></textarea>
                            </div>
                        </form>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="add-category" class="btn btn-primary" id="save-category">Save</button>
                        <button type="button" class="btn btn-danger resetform" id="close"
                            data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>

        </div>
        <!--END ADD Category MODEL -->
        <!-- UPDATE Category -->
        <div class="modal fade bd-modal-md" id="update-category-model" tabindex="-1" role="dialog" aria-hidden="true">
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
                                <input type="text" class="form-control" name="name" id="name" value="" />
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

        //  Add Category
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


        //edit
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

          // Update
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



        // delete Candidate
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
