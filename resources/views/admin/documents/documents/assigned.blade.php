@extends('layouts.admin')
@section('content')
    <!-- MAIN -->
    <div class="main">
        <!-- MAIN CONTENT -->
        <div class="main-content">
            <div class="container-fluid">
                <h3 class="page-title">Documents</h3>
                <div class="row">
                    <div class="col-md-12">
                        <!-- PANEL NO CONTROLS -->
                        <div class="panel">
                            <div class="panel-heading">
                                <h3 class="panel-title">Documents</h3>
                            </div>
                            <div class="panel-body">
                                <div class="table-responsive">
                                    <table class="table" name="tablename" id="documents">
                                        <thead>
                                            <tr>
                                                <th>Action</th>
                                                <th>Name</th>
                                                <th>Category</th>
                                                <th>Storage</th>
                                                <th>Created Date</th>
                                                <th>Expired Date</th>
                                                <th>Created By</th>
                                            </tr>
                                        </thead>
                                    </table>
                                    @push('scripts')
                                        <script>
                                            $(function() {
                                                var table = $("#documents").DataTable({
                                                    ajax: "{{ route('admin.documents.assigned') }}",
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
                                                            data: "created_date",
                                                            name: "created_date",

                                                        },
                                                        {
                                                            data: "expired_date",
                                                            name: "expired_date",

                                                        },
                                                        {
                                                            data: "created_by",
                                                            name: "created_by",

                                                        },
                                                    ],

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
