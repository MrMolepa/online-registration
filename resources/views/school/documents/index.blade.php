@extends('layouts.school')

@section('content')
    <div id="page-wrapper">

        <div class="header">
            <h1 class="page-header">
                Documents
            </h1>
            <ol class="breadcrumb">
                <li><a href="javascript:void();">Home</a></li>
                <li class="active"><a href="javascript:void();">Documents</a></li>
            </ol>
        </div>
        <div id="page-inner" class="reports">

            <!-- List of repots available -->

            <div class="row">
                <div class="col-md-12">
                    <!-- Advanced Tables -->
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Documents
                        </div>
                        <div class="panel-body ">

                            <a href="" class="btn btn-info" data-toggle="modal" data-target="#add-document">+
                                Upload Document
                            </a>
                            <br>
                            <br>
                            <br>


                            <div class="table-responsive mt-3" id="table-view">
                                <table class="table display compact" name="tablename" id="documents">
                                    <thead>
                                        <tr>
                                            <th>Action</th>
                                            <th>Name</th>
                                            <th>Category</th>
                                            <th>Storage</th>
                                            <th>Created Date</th>
                                            <th>Expired Date</th>
                                        </tr>
                                    </thead>
                                </table>
                                @push('scripts')
                                    <script>
                                        $(function() {
                                            var table = $("#documents").DataTable({
                                                ajax: "{{ route('center.documents.index') }}",
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
                                                        data: "category_name",
                                                        name: "category_name",

                                                    },
                                                    {
                                                        data: "location",
                                                        name: "location",

                                                    },
                                                    {
                                                        data: "created_date",
                                                        name: "created_date",

                                                    },
                                                    {
                                                        data: "expired_date",
                                                        name: "expired_date",

                                                    },

                                                ],

                                            });

                                            setInterval(function() {
                                                $('#documents').DataTable().ajax.reload();

                                            }, 3000);

                                        });
                                    </script>
                                @endpush




                            </div>
                        </div>
                    </div>
                </div>
                <!--End Advanced Tables -->
            </div>
        </div>
        <!-- end List of reports available -->

    </div>
    <!-- ADD DOCUMENT -->
    <div class="modal fade bd-modal-md" id="add-document" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close resetform" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h3 class="modal-title">Documents</h3>
                </div>
                <div class="modal-body">
                    <form action="{{ route('center.documents.store') }}" method="post" id="addDocumentForm"
                        enctype="multipart/form-data">
                        <div>
                            @csrf
                        </div>
                        <div class="form-group col-md-12">
                            <label for="document_upload">Document Upload</label>
                            <input type="file" class="form-control" name="document_upload" id="document_upload" />
                        </div>
                        <div class="form-group col-md-12">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" name="name" id="name" value="" />
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
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @foreach ($category->childs as $child)
                                        <option value="{{ $child->id }}">---{{ $child->name }}</option>
                                    @endforeach
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-12">
                            <label for="description">Description</label>
                            <textarea name="description" class="form-control" id="description" cols="30" rows="2"></textarea>
                        </div>




                        <div class="clearfix"></div>
                    </form>

                </div>
                <div class="modal-footer">
                    <button type="submit" name="add-document" class="btn btn-primary" id="save-document">Save</button>
                    <button type="button" class="btn btn-danger resetform" id="close"
                        data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>

    </div>
    <!--END ADD  DOCUMENT MODEL -->


    @push('scripts')
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
                    url:  url,
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
            //DOWNLOAD Documents
            $(document).on('click', '.download-document', function(ev) {
                ev.preventDefault();
                var url = $(this).attr('href');
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                var filename = "";



                // We first call fetch with the URL of the resource we want to download
                fetch(url).then((result) => {
                        if (!result.ok) {
                            throw Error(result.statusText);
                        }

                        // We are reading the *Content-Disposition* header for getting the original filename given from the server
                        const header = result.headers.get('Content-Disposition');
                        const parts = header.split(';');
                        filename = parts[1].split('=')[1].replaceAll("\"", "");
                        return result.blob();
                    }).then((blob) => {
                        if (blob != null) {
                            var url = window.URL.createObjectURL(blob);
                            var a = document.createElement('a');
                            a.href = url;
                            a.download = filename;
                            document.body.appendChild(a);
                            a.click();
                            a.remove();
                        }
                    })
                    .catch((err) => {
                        console.log(err);
                    });

            });
            //END DOWNLOAD Documents

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
        {{-- Phone number format --}}
    @endpush
@endsection
