@extends('layouts.admin')
@section('content')
    <div class="main">
        <!-- MAIN CONTENT -->
        <div class="main-content">
            <div class="container-fluid">
                <h3 class="page-title">Manage Invigilator Contracts</h3>
                <div class="row">
                    <div class="col-md-12">
                        <!-- PANEL NO CONTROLS -->
                        <div class="panel">
                            <div class="panel-heading">
                                <h3 class="panel-title">Invigilator Contracts <b></b></h3>
                            </div>
                            <div class="panel-body">
                                <div class="col-sm-12">
                                    <table class="table table-striped"id="data-table-invigilation">
                                        <thead>
                                            <tr>
                                                <th>Invigilation Types</th>
                                                <th>ID Number</th>

                                                <th>Surname</th>
                                                <th>Other Names</th>

                                                <th>Gender</th>
                                                <th>Date Of Birth</th>

                                                <th>Email</th>
                                                <th>Phone Number</th>
                                                <th>Center Number</th>
                                                <th>Action</th>

                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- END PANEL NO CONTROLS -->
                    </div>

                </div>


            </div>
        </div>
        <!-- END MAIN CONTENT -->
    </div>


    @push('scripts')
        <script>
            // TOASTER AND NOTIFICATION SETUP
            toastr.options = {
                "closeButton": true,
                "debug": false,
                "newestOnTop": false,
                "progressBar": true,
                "positionClass": "toast-top-right",
                "preventDuplicates": false,
                "onclick": null,
                "showDuration": "300",
                "hideDuration": "1000",
                "timeOut": "5000",
                "extendedTimeOut": "1000",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
            }

            $('.modal').on('hidden.bs.modal', function(e) {
                $('form').trigger("reset");
            });
            $(document).ready(function() {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                // datatable
                var table = $('#data-table-invigilation').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('admin.invigilations.contracts.index') }}",
                    columns: [{
                            data: 'invigilation_role.invigilation_type.name',
                            name: 'invigilation_role.invigilation_type.name'
                        },
                        {
                            data: 'national_id',
                            name: 'national_id'
                        },
                        {
                            data: 'surname',
                            name: 'surname'
                        },
                        {
                            data: 'other_names',
                            name: 'other_names'
                        },
                        {
                            data: 'gender',
                            name: 'gender'
                        },
                        {
                            data: 'date_of_birth',
                            name: 'date_of_birth'
                        },
                        {
                            data: 'email',
                            name: 'email'
                        },
                        {
                            data: 'phone_number',
                            name: 'phone_number'
                        },
                        {
                            data: 'center_no',
                            name: 'center_no'
                        },

                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        },

                    ]
                });
                // Delete
                $(document).on('click', '.delete-invigilator', function() {

                    var url = $(this).data("url");


                    $.ajax({
                        type: "DELETE",
                        url: url,
                        success: function(data) {
                            //Refresh table
                            table.draw();
                        },
                        error: function(data) {
                            console.log('Error:', data);
                        }
                    });
                });



                /****  Print errors*******/
                function printErrorMsg(parent, msg) {
                    $(`${parent} input, ${parent} select, textarea`).each(function(index) {
                        $(`${parent} .help-block`).remove();
                        $(`${parent} .has-error`).removeClass('has-error');

                    });
                    $.each(msg, function(key, errors) {
                        for (const error in errors) {
                            const value = errors[error];
                            $(`[name='${key}']`).parent().addClass('has-error');
                            $(`<span class='help-block'>${value}</span>`).insertAfter(
                                `${parent} [name='${key}']`)
                            if (key == 'subjects' || key == 'subject') {
                                $(".subjects-errors").find('span').css({
                                    "color": "#ff0000"
                                }).html(`<strong>${value}</strong>`);
                            }

                        }
                    });
                }
                /****  Print errors End*******/



            });
        </script>
    @endpush
@endsection
