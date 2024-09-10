@extends('layouts.admin')
@section('content')
    <!-- MAIN -->
    <div class="main">
        <!-- MAIN CONTENT -->
        <div class="main-content">
            <div class="container-fluid">
                <h3 class="page-title">Documents </h3>
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

                                                // var table = $("#categories").DataTable({
                                                //     ajax: "{{ route('admin.document-categories.index') }}",
                                                //     columns: [{
                                                //             className: 'dt-control',
                                                //             data: null,
                                                //             name: 'subject_code',
                                                //             "defaultContent": '',
                                                //             searchable: false,
                                                //             orderable: false,
                                                //         },
                                                //         {
                                                //             data: "actions",
                                                //             name: "actions",
                                                //             searchable: false,
                                                //             orderable: false,
                                                //         },
                                                //         {
                                                //             data: "name",
                                                //             name: "name",
                                                //         },
                                                //         {
                                                //             data: "description",
                                                //             name: "description"
                                                //         },
                                                //         {
                                                //             data: "subcategories",
                                                //             name: "subcategories",
                                                //             visible: false
                                                //         }
                                                //     ],
                                                //     order: [
                                                //         [1, "asc"]
                                                //     ]
                                                // });

                                                // function format(d) {
                                                //     var subcategories = JSON.parse(d.subcategories) ;
                                                //     var category = "";

                                                //     $.each(subcategories, function(i,  subcategory) {
                                                //         category += `<tr>
        //                         <td>${subcategory.actions}</td>
        //                         <td>${subcategory.name}</td>
        //                         <td>${subcategory.description}</td>
        //                         </tr>`;

                                                //     });

                                                //     return (
                                                //         ` <div class='pull-right mt-5'>
        //                 <a href='' class='btn btn-info' data-toggle='modal' data-target='#add-category'>+
        //                      Child Category
        //                 </a>
        //             </div>
        //         <div class='clearfix'></div>
        //         <table class='table mb-0'>
        //            <tr class='table-primary'>
        //             <th></th>
        //              <th>Name</th>
        //              <th>Description</th>
        //            </tr>
        //            ${category}
        //             </table>`
                                                //     );
                                                // }
                                                // $("#categories tbody").on("click", "td.dt-control", function() {
                                                //     var tr = $(this).closest("tr");
                                                //     var row = table.row(tr);

                                                //     if (row.child.isShown()) {
                                                //         row.child.hide();
                                                //         tr.removeClass("shown");
                                                //     } else {
                                                //         row.child(format(row.data()), "p-0").show();
                                                //         tr.addClass("shown");
                                                //     }
                                                // });





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
                                        <optgroup label="{{ $category->name }}">
                                            @foreach ($category->childs as $child)
                                                <option value="{{ $child->id }}">{{ $child->name }}</option>
                                            @endforeach
                                        </optgroup>
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
                                    <input class="form-control" type="text" name="document_meta_datas[]">
                                    <span class="input-group-btn"><button class="btn btn-primary add-document-meta"
                                            type="button"><i class="fas fa-plus"></i></button></span>
                                </div>

                            </div>
                            <div class="clearfix"></div>
                            <div class="form-group col-md-6">
                                <label for="description">Assign/share with roles</label>
                                <select class="js-example-basic-multiple form-select" id="roles"
                                    name="document_role_Permission[roles][]" multiple="multiple">
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}">{{ $role->display_name }}</option>
                                    @endforeach
                                </select>
                                <label class="fancy-checkbox">
                                    <input type="checkbox" name="document_role_Permission[is_time_bound]">
                                    <span>Spacify the Period</span>
                                </label>
                                <div class="specify-period">
                                    <div class="date-inserted">
                                        <label for="name">Start Date</label>
                                        <input type="date" class="form-control"
                                            name="document_role_Permission[start_date]" id="start_date" value="" />
                                    </div>
                                    <div class="date-inserted">
                                        <label for="name">End Date</label>
                                        <input type="date" class="form-control"
                                            name="document_role_Permission[end_date]" id="end_date" value="" />
                                    </div>
                                </div>
                                <label class="fancy-checkbox">
                                    <input type="checkbox" name="document_role_Permission[is_allow_download]">
                                    <span>Allow Download</span>
                                </label>

                            </div>
                            <div class="form-group col-md-6">
                                <label for="description">Assign/share with users</label>
                                <select class="js-example-basic-multiple form-select"
                                    name="document_user_Permission[users][]" id='users' multiple="multiple">
                                    <option></option>

                                </select>
                                <label class="fancy-checkbox">
                                    <input type="checkbox" name="document_user_Permission[is_time_bound]">
                                    <span>Spacify the Period</span>
                                </label>
                                <div class="specify-period">
                                    <div class="date-inserted">
                                        <label for="name">Start Date</label>
                                        <input type="date" class="form-control"
                                            name="document_user_Permission[start_date]" id="start_date" value="" />
                                    </div>
                                    <div class="date-inserted">
                                        <label for="name">End Date</label>
                                        <input type="date" class="form-control"
                                            name="document_user_Permission[end_date]" id="end_date" value="" />
                                    </div>
                                </div>
                                <label class="fancy-checkbox">
                                    <input type="checkbox" name="document_user_Permission[is_allow_download]">
                                    <span>Allow Download</span>
                                </label>

                            </div>

                            <!-- Copy Fields -->
                            <div class="fields-copy hidden">
                                <div class="input-group control-copy m-2">
                                    <input class="form-control" type="text" name="documentMetaDatas[]">
                                    <span class="input-group-btn"><button class="btn btn-danger remove" type="button"><i
                                                class='fas fa-trash'></i></button></span>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                        </form>
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
        <!--END ADD Category MODEL -->
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
            console.log('ok');
            var html = $(".fields-copy").html();
            $(".after-add-more").after(html);
        });

        $(document).on("click", ".remove", function() {
            $(this).parents(".control-copy").remove();
        });

        $(document).ready(function() {
            $('#roles').select2({
                width: '100%',
                containerCss: {
                    "display": "block"
                }
            });
        });


        $("#users").select2({
            placeholder: "Select the Center",
            dropdownParent: $("#add-document"),
            ajax: {
                url: "{{ route('admin.documents.getRoleUser') }}",
                method: "GET",
                dataType: "json",
                delay: 250,
                processResults: function(data) {

                    return {
                        results: $.map(data, function(item, key) {

                            var children = [];
                            for (var k in item) {

                                if ($.isArray(item[k]) && !$.isEmptyObject(item[k])) {
                                    $.map(item[k], function(user) {
                                        if (user.status == 1) {
                                            var childItem = {};
                                            childItem.text = user.email + ' ' + user.username;
                                            childItem.id = user.id;
                                            children.push(childItem);
                                        }

                                    })





                                }


                            }
                            return {
                                id: item.id,
                                text: item.name,
                                children: children,
                            }
                        })


                        // $.map(data, function(item) {
                        //     return {
                        //         text: `${item.name}`,
                        //         id: item.id,
                        //     };
                        // }),
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
        //  Add Category
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
                success: function(response) {
                    console.log(response);


                },
                complete: function(data) {
                    // element.prop('disabled', false).html(caption);
                }
            });




        });













        // delete Candidate
        // $(document).on('click', '#candidates .deleteBtn', function(ev) {
        //     ev.preventDefault();
        //     var url = $(this).data('url');
        //     if (confirm("Are you sure you want to delete this candidates!") == true) {
        //         $.ajaxSetup({
        //             headers: {
        //                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //             }
        //         });

        //         $.ajax({
        //             url: url,
        //             method: "DELETE",
        //             success: function(data) {
        //                 if (data.success) {
        //                     toastr.success(data.success);
        //                     $('#candidates').DataTable().ajax.reload();
        //                 }



        //             }
        //         });


        //     } else {
        //         return;
        //     }

        // });


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
