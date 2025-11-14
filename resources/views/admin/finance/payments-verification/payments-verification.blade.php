@extends('layouts.admin')

@section('content')
    <!-- ============================================================== -->
    <!-- Start Page Content -->
    <!-- ============================================================== -->
    <div class="main">
        <!-- MAIN CONTENT -->
        <div class="main-content">
            <div class="container-fluid">
                <h3 class="page-title">Payments Verification</h3>

                <div class="row">
                    <div class="col-md-12">
                        <!-- PANEL NO CONTROLS -->
                        <div class="panel">
                            <div class="panel-heading">
                                <h3 class="panel-title">Payments Verification</h3>
                            </div>
                            <div class="panel-body">

                                <fieldset>
                                    <legend>Filter</legend>
                                    <div class="pull-right col-md-4">
                                        <div class="input-group">
                                            <span class="input-group-btn">
                                                <button class="btn secondary" type="button">Year</button>
                                            </span>
                                            <select class="form-control status-dropdown" id="year">
                                                @foreach ($years as $year)
                                                    <option
                                                        value="{{ $year }}"@if ($year == date('Y')) selected @endif>
                                                        {{ $year }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                </fieldset>
                                <div class="table-responsive">
                                    <table class="table" name="tablename" id="school-fee-datatable">
                                        <thead>
                                            <tr>
                                                <th>Centre Number</th>
                                                <th>Centre Name</th>
                                                <th>District</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>

                                        @push('scripts')
                                            <script>
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
                                                $(function() {

                                                    var table_school_fee = $('#school-fee-datatable').DataTable({
                                                        processing: true,
                                                        serverSide: true,
                                                        deferRender: true,
                                                        "lengthMenu": [
                                                            [20, 50, 100, 200, 400, -1],
                                                            [20, 50, 100, 200, 400, "All"]
                                                        ],

                                                        ajax: {
                                                            url: "{{ route('admin.centre-collection.fees.index') }}",
                                                            data: function(d) {
                                                                d.year = $("#year").val()
                                                            }
                                                        },
                                                        columns: [{
                                                                data: 'center_no',
                                                                name: 'center_no',
                                                                searchable: true
                                                            },
                                                            {
                                                                data: 'center_name',
                                                                name: 'center_name',
                                                                searchable: true

                                                            },
                                                            {
                                                                data: 'district',
                                                                name: 'district',

                                                            },


                                                            {
                                                                data: 'actions',
                                                                name: 'actions',
                                                                searchable: false
                                                            }


                                                        ]

                                                    });


                                                });


                                                $("#year").on("change", function(event) {
                                                    $('#school-fee-datatable').DataTable().ajax.reload();
                                                });
                                            </script>
                                        @endpush


                                    </table>
                                </div>
                            </div>
                            <!-- END PANEL NO CONTROLS -->
                        </div>


                    </div>

                </div>
            </div>
            <!-- END MAIN CONTENT -->
        </div>
        <!-- END MAIN CONTENT -->
    </div>
    <!-- ============================================================== -->
    <!-- End PAge Content -->
    <!-- ============================================================== -->

@endsection
