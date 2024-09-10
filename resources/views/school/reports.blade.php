@extends('layouts.school')

@section('content')
    <div id="page-wrapper">

        <div class="header">
            <h1 class="page-header">
                Reports
                <!--<small>Welcome John Doe</small>-->
            </h1>

            <ol class="breadcrumb">
                <li><a href="javascript:void();">Home</a></li>
                <li class="active"><a href="javascript:void();">Reports</a></li>
            </ol>

        </div>

        <div id="page-inner" class="reports">

            <!-- List of reports available -->

            <div class="row">
                <div class="col-md-12">
                    <!-- Advanced Tables -->
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Reports
                        </div>
                        <div class="panel-body">
                            <table class="table table-striped  table-hover">
                                <thead>
                                    <th scope="col">Centre No</th>
                                    <th scope="col">Reports</th>
                                    <th scope="col">Centre Name</th>
                                    <th scope="col">View</th>
                                    <th scope="col">Download</th>
                                </thead>
                                <tbody>
                                    @permission('reports-download')
                                        @foreach ($levels as $level)
                                            <tr>
                                                <td> {{ auth()->user()->center_no }}</td>
                                                <td>Sponsor Report ({{ $level }})</td>



                                                <td>{{ auth()->user()->center_name }}</td>
                                                <td>
                                                    <a href="{{ route('center.reports.printSponsorReport', ['report_type' => 'sponsor', 'level' => $level]) }}"
                                                        target="_blank">View <i class="fa fa-eye" aria-hidden="true"></i></a>

                                                </td>
                                                <td>
                                                    <a
                                                        href="{{ route('center.reports.printSponsorReport', ['report_type' => 'sponsor', 'download' => 1, 'level' => $level]) }}">All
                                                        &nbsp;<i class="fa fa-download"></i></a>
                                                    &nbsp;
                                                    &nbsp;
                                                    <a
                                                        href="{{ route('center.reports.printSponsorReport', ['report_type' => 'sponsor_moet', 'download' => 1, 'level' => $level]) }}">MoET
                                                        &nbsp;<i class="fa fa-download"></i></a>
                                                    &nbsp;
                                                    &nbsp;
                                                    <a
                                                        href="{{ route('center.reports.printSponsorReport', ['report_type' => 'sponsor_nmds', 'download' => 1, 'level' => $level]) }}">NMDS
                                                        &nbsp;<i class="fa fa-download"></i></a>
                                                    &nbsp;
                                                    &nbsp;
                                                    <a
                                                        href="{{ route('center.reports.printSponsorReport', ['report_type' => 'sponsor_o', 'download' => 1, 'level' => $level]) }}">OTHER
                                                        &nbsp;<i class="fa fa-download"></i></a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endpermission
                                    @permission('reports-download')
                                        @foreach ($levels as $level)
                                            <tr>
                                                <td> {{ auth()->user()->center_no }}</td>
                                                <td>Entry list ({{$level}})</td>
                                                <td>{{ auth()->user()->center_name }}</td>

                                                <td>

                                                    <a href="{{ route('center.reports.printEntryList', ['report_type' => 'entrylist', 'level' => $level]) }}"
                                                        target="_blank">View <i class="fa fa-eye"></i></a>

                                                </td>
                                                <td>

                                                    <a
                                                        href="{{ route('center.reports.printEntryList', ['report_type' => 'entrylist', 'download' => 1, 'level' => $level]) }}">Download
                                                        <i class="fa fa-download"></i></a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endpermission
                                    @permission('timetable-download')
                                        <tr>
                                            <td> {{ auth()->user()->center_no }}</td>
                                            <td>Timetable</td>
                                            <td>{{ auth()->user()->center_name }}</td>
                                            <td>
                                                <a
                                                    href="{{ route('center.reports.printtimetable', ['report_type' => 'timetable']) }}">View
                                                    <i class="fa fa-eye"></i></a>
                                            </td>
                                            <td>
                                                <a
                                                    href="{{ route('center.reports.printtimetable', ['report_type' => 'timetable', 'download' => 1]) }}">Download
                                                    <i class="fa fa-download"></i></a>
                                            </td>

                                        </tr>
                                    @endpermission

                                </tbody>
                            </table>


                        </div>
                    </div>
                    <!--End Advanced Tables -->
                </div>
            </div>
            <!-- end List of reports available -->

        </div>
        <!-- /. PAGE INNER  -->

    </div>
    <!-- /. PAGE WRAPPER  -->
@endsection

@section('script')
@endsection
