@extends('layouts.admin')

@section('content')
    <!-- Page content excluding top navigation -->
    <div class="main">
        <!-- MAIN CONTENT -->
        <div class="main-content">
            <div class="container-fluid">
                <h3 class="page-title">{{ ucfirst(Request::segment(1)) }} / Dashboard</h3>
                <!-- OVERVIEW -->
                <div class="panel panel-headline">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="metric">
                                    <span class="icon"><i class="lnr lnr-apartment"></i></span>
                                    <p>
                                        <span class="number">{{ $registered_schools->schools }}</span>
                                        <span class="title">Registered Schools</span>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="metric">
                                    <span class="icon"><i class="lnr lnr-user"></i></span>
                                    <p>
                                        <span
                                            class="number">{{ $number_of_private_candidates->number_of_candidates }}</span>
                                        <span class="title">Private Candidates</span>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="metric">
                                    <span class="icon"><i class="lnr lnr-users"></i></span>
                                    <p>
                                        <span class="number">{{ $number_of_school_candidates->number_of_candidates }}</span>
                                        <span class="title">School Candidates</span>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="metric">
                                    <span class="icon"><i class="far fa-chart-bar"></i></span>
                                    <p>
                                        <span class="number">{{ $number_of_candidates->number_of_candidates }}</span>
                                        <span class="title">Total Candidates</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <fieldset>
                                    <legend>Filter</legend>
                                    <div class="pull-left col-md-4">
                                        <div class="input-group">
                                            <span class="input-group-btn">
                                                <button class="btn secondary" type="button">Level</button>
                                            </span>
                                            <select class="form-control status-dropdown" id="level">
                                                @foreach ($levels as $level)
                                                    <option value="{{ $level }}"
                                                        @if ($level == 'LGCSE') selected @endif>
                                                        {{ $level }}</option>
                                                @endforeach
                                            </select>

                                        </div>
                                    </div>
                                    <div class="pull-left col-md-4">
                                        <div class="input-group">
                                            <span class="input-group-btn">
                                                <button class="btn secondary" type="button">Session</button>
                                            </span>
                                            <select class="form-control status-dropdown" id="session">
                                                @foreach ($sessions as $session)
                                                    <option value="{{ $session }}"
                                                        @if ($session == 'November') selected @endif>
                                                        {{ $session }}</option>
                                                @endforeach
                                            </select>

                                        </div>
                                    </div>
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
                                <div class="row">
                                    <div class="col-md-9">
                                        <div class="ct-chart">
                                            <canvas id="registered_subjects">
                                            </canvas>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="weekly-summary text-right">
                                            <span class="number total-candidates">2315</span> <span class="percentage"><i class="fa fa-caret-up text-success"></i> 12%</span>
                                            <span class="info-label">Registered Schools</span>
                                        </div>
                                        <div class="ct-chart">
                                            <canvas id="candidates">
                                            </canvas>
                                        </div>

                                    </div>
                                </div>

                                <div class="row">


                                    <br>
                                    @foreach ($subjects as $subject)
                                        @php
                                            $count = 0;
                                        @endphp

                                        <div class="col-md-3">
                                            <ul class="list-group">
                                                @if ($count % 4 == 0)
                                                    <li class="list-group-item">
                                                        {{ $subject->subject_code }}-{{ $subject->subject_name }}</li>
                                                @endif
                                            </ul>
                                        </div>

                                        @php
                                            $count++;
                                        @endphp
                                    @endforeach

                                </div>




                            </div>
                        </div>


                    </div>
                </div>
                <!-- END OVERVIEW -->

            </div>
        </div>
        <!-- END MAIN CONTENT -->
    </div>
    <!-- END MAIN -->
    <div class="clearfix"></div>
    <!-- end Page content excluding top navigation -->
@endsection

@section('script')
    <script>
        /*-----------------------------------/
     /*DISPLAY  REGISTERED SUBJECTS
  /*----------------------------------*/
        var session = $("#session").val();
        var year = $("#year").val();
        var level = $("#level").val();
        registered_subjects(level,session, year)

        $("#level").on("change", function() {
            level = $(this).val();
            session = $("#session").val();
            year = $("#year").val();
            registered_subjects(level,session, year);
        });

        $("#year").on("change", function() {
            level = $("#level").val();
            session = $("#session").val();
            year = $(this).val();

            registered_subjects(level,session, year);
        });
        $("#session").on("change", function() {
            level = $("#level").val();
            year = $("#year").val();
             session = $('#session').val();
            registered_subjects(   level,session, year);
        });
        // display registered subjects;
        function registered_subjects(level,session, year) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('admin.home') }}",
                method: "GET",
                data: {
                    level: level,
                    session: session,
                    year: year
                },
                success: function(response) {
                    var subjects=response.subjects;
                    var candidates=response.candidates;
                    var school=response.schools;


                     $('.total-candidates').html(school.total);

                    var subjectLabels = [];
                    var subjectData = [];

                    var candidateLabels = [];
                    var candidateData = [];

                    var backgroundColor = [];
                    var borderColor = []
                    for (let x in subjects) {
                        subjectLabels.push(subjects[x].subject_code + "-" + subjects[x].subject_option);
                        subjectData.push(subjects[x].total)
                        const r = parseInt(Math.random() * 255);
                        const g = parseInt(Math.random() * 255);
                        const b = parseInt(Math.random() * 255);
                        backgroundColor.push(`rgba(${r},${g}, ${b}, 0.2)`)
                        borderColor.push(`rgba(${r},${g}, ${b}, 1)`)
                    }
                    var subjectChart = new Chart($("#registered_subjects"), {
                        type: "bar",
                        data: {
                        labels:  subjectLabels,
                        datasets: [{
                            label: "# of Candidates",
                            data: subjectData,
                            backgroundColor: backgroundColor,
                            borderColor: borderColor,
                            borderWidth: 2,
                        }, ],
                    },
                        options: {
                            scales: {
                                yAxes: [{
                                    ticks: {
                                        beginAtZero: true,
                                    },
                                }, ],
                                xAxes: [{
                                    ticks: {
                                        fontSize: 10,
                                    },
                                }, ],
                            },
                        },
                    });
                    subjectChart.update();
                    for (let x in candidates) {

                        console.log(candidates)
                        candidateLabels.push(x);

                        candidateData.push(candidates[x])
                        const r = parseInt(Math.random() * 255);
                        const g = parseInt(Math.random() * 255);
                        const b = parseInt(Math.random() * 255);
                        backgroundColor.push(`rgba(${r},${g}, ${b}, 0.2)`)
                        borderColor.push(`rgba(${r},${g}, ${b}, 1)`)
                    }
                    var candidateChart = new Chart($("#candidates"), {
                        type: "doughnut",
                        data: {
                        labels:  candidateLabels,
                        datasets: [{
                            label: "# of Candidates",
                            data: candidateData,
                            backgroundColor: backgroundColor,
                            borderColor: borderColor,
                            borderWidth: 2,
                        }, ],
                    },
                        options: {
                           aspectRatio:0.8
                        },
                    });
                    candidateChart.update();

                },
            });
        }

        function resetGraph() {
            $('#registered_subjects').remove(); // this is my <canvas> element
            $('.ct-chart').append('<canvas id="registered_subjects"><canvas>');
            canvas = document.querySelector('#registered_subjects');
            ctx = canvas.getContext('2d');
            ctx.canvas.width = $('#registered_subjects').width(); // resize to parent width
            ctx.canvas.height = $('#registered_subjects').height(); // resize to parent height
            var x = canvas.width / 2;
            var y = canvas.height / 2;
            ctx.font = '10pt Verdana';
            ctx.textAlign = 'center';
            ctx.fillText('', x, y);
        };
    </script>
@endsection
