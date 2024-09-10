@extends('layouts.admin')

@section('content')
    <!-- ============================================================== -->
    <!-- Start Page Content -->
    <!-- ============================================================== -->
    <!-- MAIN -->

    <div class="main">
        <!-- MAIN CONTENT -->
        <div class="main-content backup_restore">
            <div class="container-fluid">
                <h3 class="page-title">Activity Logs</h3>
                <div class="row mt-4">

                    <div class="col-md-12">
                        <!-- PANEL NO CONTROLS -->
                        <div class="panel">
                            <div class="panel-heading">
                                <h3 class="panel-title">Logs</h3>
                            </div>
                            <div class="panel-body">
                                <div class="logs">

                                    <label class="switch3 switch3-checked">
                                        <input type="checkbox" />
                                        <div></div>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <!-- END PANEL NO CONTROLS -->
                    </div>

                </div>
                <div class="row">
                    <div class="col-md-12">
                        <!-- BUTTONS -->
                        <div class="panel">
                            <div class="panel-heading">
                                <h3 class="panel-title">Activity Logs</h3>
                            </div>

                            <div class="panel-body">


                                <div class="table-responsive">
                                    <table class="table" name="tablename" id="activities">
                                        <thead>
                                            <tr>
                                            <tr>
                                                <th>Date and Time</th>
                                                <th>Center</th>
                                                <th>User</th>
                                                <th>description</th>
                                                <th>Changes</th>
                                            </tr>
                                            </tr>
                                        </thead>


                                    </table>
                                    @push('scripts')
                                        <script>
                                            $(function() {

                                                var candidates = $('#activities').DataTable({
                                                    processing: true,
                                                    serverSide: true,
                                                    deferRender: true,
                                                    "lengthMenu": [
                                                        [20, 50, 100, 200, 400, -1],
                                                        [20, 50, 100, 200, 400, "All"]
                                                    ],
                                                    ajax: "{{ route('admin.logs.index') }}",
                                                    columns: [{
                                                            data: 'created_at',
                                                            name: 'created_at',
                                                            searchable: true
                                                        },
                                                        {
                                                            data: 'subject_type',
                                                            name: 'subject_type',
                                                            searchable: true
                                                        },
                                                        {
                                                            data: 'causer_id',
                                                            name: 'causer_id',


                                                        },
                                                        {
                                                            data: 'properties',
                                                            name: 'properties',
                                                        },
                                                        {
                                                            data: 'description',
                                                            name: 'description',
                                                        },

                                                    ]

                                                });



                                                /*-----------------------------------/
                                                /*LOGS SETTINGS
                                                /*----------------------------------*/
                                                // logs
                                              
                                                $(".switch3 input").on("change", function() {
                                                    var dad = $(this).parent();
                                                    if ($(this).is(":checked")) {

                                                        dad.addClass("switch3-checked");
                                                    } else {
                                                        dad.removeClass("switch3-checked");
                                                    }
                                                });

                                                function enableLogs(status) {
                                                    $.ajaxSetup({
                                                    headers: {
                                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                                    }
                                                   });
                                                    $.ajax({
                                                        type: "POST",
                                                        url: "{{ route('admin.logs.setActitiesLogs') }}",
                                                        data: {
                                                            status: status
                                                        },
                                                        success: function(data) {
                                                            console.log(data);
                                                        },
                                                    });

                                                }

                                            });
                                        </script>
                                    @endpush
                                </div>


                            </div>
                        </div>
                        <!-- END BUTTONS -->

                    </div>

                </div>
            </div>
        </div>
        <!-- END MAIN CONTENT -->
    </div>

    <!-- END MAIN -->
    <div class="clearfix"></div>
    <!-- ============================================================== -->
    <!-- End PAge Content -->
    <!-- ============================================================== -->
@endsection
