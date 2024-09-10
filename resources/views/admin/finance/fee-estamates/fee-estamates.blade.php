@extends('layouts.admin')

@section('content')
    <!-- ============================================================== -->
    <!-- Start Page Content -->
    <!-- ============================================================== -->
    <div class="main">
        <!-- MAIN CONTENT -->
        <div class="main-content">
            <div class="container-fluid">
                <h3 class="page-title">Fee Estamates</h3>
                <div class="row">
                    <div class="col-md-12">
                        <!-- PANEL NO CONTROLS -->
                        <div class="panel">
                            <div class="panel-heading">
                                <h3 class="panel-title"> Fee Estamates</h3>
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="metric">
                                            <span class="icon"><i class="lnr lnr-apartment"></i></span>
                                            <p>
                                                <span class="number">LSL
                                                    {{ number_format($mosd, 2, '.', '') }}</span>
                                                <span class="title">MoSD</span>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="metric">
                                            <span class="icon"><i class="lnr lnr-user"></i></span>
                                            <p>
                                                <span class="number">LSL
                                                    {{ number_format($nmds, 2, '.', '') }}</span>
                                                <span class="title">NMDS</span>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="metric">
                                            <span class="icon"><i class="lnr lnr-users"></i></span>
                                            <p>
                                                <span class="number">LSL
                                                    {{ number_format($other, 2, '.', '') }}</span>
                                                <span class="title">OTHER</span>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="metric">
                                            <span class="icon"><i class="far fa-chart-bar"></i></span>
                                            <p>
                                                <span class="number">LSL
                                                    {{ number_format($total_sum, 2, '.', '') }}</span>
                                                <span class="title">Total Sum</span>
                                            </p>
                                        </div>
                                    </div>
                                </div>


                                <div class="custom-tabs-line tabs-line-bottom left-aligned">
                                    <ul class="nav" role="tablist">
                                        <li class="active"><a href="#school-fee" role="tab"
                                                data-toggle="tab">Public(Schools) Centers
                                                fees</a></li>
                                        <li><a href="#private-fee" role="tab" data-toggle="tab">Private Centers </a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="tab-content">
                                    <div class="tab-pane fade in active" id="school-fee">
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

                                            </table>
                                            @push('scripts')
                                                <script>
                                                    $(function() {
                                                        var table_school_fee = $('#school-fee-datatable').DataTable({
                                                            processing: true,
                                                            serverSide: true,
                                                            deferRender: true,
                                                            "lengthMenu": [
                                                                [20, 50, 100, 200, 400, -1],
                                                                [20, 50, 100, 200, 400, "All"]
                                                            ],
                                                            ajax: "{{ route('admin.fee-estamates.index') }}",
                                                            columns: [{
                                                                    data: 'center_no',
                                                                    name: 'centers.center_no',
                                                                    searchable: true
                                                                },
                                                                {
                                                                    data: 'center_name',
                                                                    name: 'centers.center_name',
                                                                    searchable: true

                                                                },
                                                                {
                                                                    data: 'district',
                                                                    name: 'centers.district',


                                                                },


                                                                {
                                                                    data: 'actions',
                                                                    name: 'actions',
                                                                }


                                                            ]

                                                        });

                                                        $("#school-fee-datatable").css("width", "99.5%");

                                                    });


                                                    /**********  Rest input when close Add user Modal **************/
                                                    $(document).on("click", ".resetform", function() {
                                                        $('.error-text').text('');
                                                        $("form").trigger("reset");
                                                    });
                                                    /**********  Rest input when close Add user Modal End **************/
                                                </script>
                                            @endpush

                                        </div>

                                    </div>
                                    <div class="tab-pane fade" id="private-fee">
                                        <div class="table-responsive">
                                            <table class="table" name="tablename" id="private-fee-datatable">
                                                <thead>
                                                    <tr>
                                                        <th>Centre Number</th>
                                                        <th>Centre Name</th>
                                                        <th>District</th>
                                                    </tr>
                                                </thead>
                                            </table>
                                            @push('scripts')
                                                <script>
                                                    $(function() {

                                                        var table_private_fee = $('#private-fee-datatable').DataTable({
                                                            processing: true,
                                                            serverSide: true,

                                                            deferRender: true,
                                                            "lengthMenu": [
                                                                [20, 50, 100, 200, 400, -1],
                                                                [20, 50, 100, 200, 400, "All"]
                                                            ],
                                                            ajax: "{{ route('admin.fee-estamates.privatecenters') }}",
                                                            columns: [{
                                                                    data: 'center_no',
                                                                    name: 'centers.center_no',
                                                                    searchable: true
                                                                },
                                                                {
                                                                    data: 'center_name',
                                                                    name: 'centers.center_name',
                                                                    searchable: true

                                                                },
                                                                {
                                                                    data: 'district',
                                                                    name: 'centers.district',


                                                                },





                                                            ]

                                                        });

                                                        $("#private-fee-datatable").css("width", "98.5%");

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
        </div>
        <!-- END MAIN CONTENT -->
    </div>
    <!-- ============================================================== -->
    <!-- End PAge Content -->
    <!-- ============================================================== -->
@endsection
