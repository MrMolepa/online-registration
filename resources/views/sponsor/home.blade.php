@extends('layouts.sponsor')
@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Dashboard</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- Small boxes (Stat box) -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ $registered_schools->schools }}</h3>
                            <p>Total Schools</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-bag"></i>
                        </div>
                        <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ $number_of_candidates->number_of_candidates }}</h3>
                            <p>Total Candidates</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-stats-bars"></i>
                        </div>
                        <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{{ $approved }}</h3>

                            <p>Approved</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-person-add"></i>
                        </div>
                        <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3>{{ $declined->status }}</h3>

                            <p>Declined </p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-pie-graph"></i>
                        </div>
                        <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <!-- ./col -->
            </div>
            <!-- /.row -->
            <!-- Main row -->
            <div class="row">
                <!-- Left col -->
                <section class="col-lg-12 connectedSortable">
                    <!-- Custom tabs (Charts with tabs)-->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-chart-pie mr-1"></i>
                                Candidates
                            </h3>
                            <div class="card-tools">
                                <ul class="nav nav-pills ml-auto">
                                    <li class="nav-item">
                                        <a class="nav-link active" href="#revenue-chart" data-toggle="tab">Bar</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#sales-chart" data-toggle="tab">Donut</a>
                                    </li>
                                </ul>
                            </div>
                        </div><!-- /.card-header -->
                        <div class="card-body">
                            <div class="tab-content p-0">
                                <!-- Morris chart - Sales -->
                                <div class="chart tab-pane active" id="revenue-chart"
                                    style="position: relative;">
                                    <canvas id="revenue-chart-canvas"></canvas>

                                </div>
                                <div class="chart tab-pane" id="sales-chart" style="position: relative;">
                                    <canvas id="sales-chart-canvas"  style="height: 300px;"></canvas>
                                    @push('scripts')
                                        <script>
                                            $(function() {
                                                districts();
                                                function districts() {
                                                    $.ajaxSetup({
                                                        headers: {
                                                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                                        }
                                                    });
                                                    $.ajax({
                                                        url: "{{ route('sponsor.home') }}",
                                                        method: "GET",
                                                        success: function(response) {
                                                            var districts = response.districts;
                                                            console.log(districts)
                                                            var districtLabels = [];
                                                            var districtData = [];
                                                            var backgroundColor = [];
                                                            var borderColor = []
                                                            for (let x in districts) {
                                                                districtLabels.push(districts[x].district_code);
                                                                districtData.push(districts[x].total)
                                                                const r = parseInt(Math.random() * 255);
                                                                const g = parseInt(Math.random() * 255);
                                                                const b = parseInt(Math.random() * 255);
                                                                backgroundColor.push(`rgba(${r},${g}, ${b}, 0.2)`)
                                                                borderColor.push(`rgba(${r},${g}, ${b}, 1)`)
                                                            }

                                                            var districtChart = new Chart($("#sales-chart-canvas"), {
                                                                type: "doughnut",
                                                                data: {
                                                                    labels: districtLabels,
                                                                    datasets: [{
                                                                        label: "# of Candidates",
                                                                        data: districtData,
                                                                        backgroundColor: backgroundColor,
                                                                        borderColor: borderColor,
                                                                        borderWidth: 2,
                                                                    }, ],
                                                                },
                                                                options: {
                                                                    mantainAspectRatio: true
                                                                },
                                                            });
                                                            districtChart.update();

                                                            var districtChartbar = new Chart($("#revenue-chart-canvas"), {
                                                                type: "bar",
                                                                data: {
                                                                    labels: districtLabels,
                                                                    datasets: [{
                                                                        label: "# of Candidates",
                                                                        data: districtData,
                                                                        backgroundColor: backgroundColor,
                                                                        borderColor: borderColor,
                                                                        borderWidth: 1,
                                                                    }, ],
                                                                },
                                                                options: {
                                                                    mantainAspectRatio: true
                                                                },
                                                            });
                                                            districtChartbar.update();



                                                        },
                                                    });
                                                }






                                            });
                                            /****  Print errors End*******/
                                        </script>
                                    @endpush
                                </div>
                            </div>
                        </div><!-- /.card-body -->
                    </div>
                    <!-- /.card -->




                </section>
                <!-- /.Left col -->

            </div>
            <!-- /.row (main row) -->
        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
@endsection
