@extends('layouts.admin')

@section('content')
    <!-- ============================================================== -->
    <!-- Start Page Content -->
    <!-- MAIN -->
    <div class="main">
        <!-- MAIN CONTENT -->
        <div class="main-content">
            <div class="container-fluid">
                <h3 class="page-title">Timetable</h3>
                <div class="row">
                    <div class="col-md-12">
                        <!-- PANEL NO CONTROLS -->
                        <div class="panel">
                            <div class="panel-heading">
                                <h3 class="panel-title">Timetable </h3>
                            </div>
                            <fieldset>
                                <legend>Filter</legend>
                                <div class="pull-left col-md-4">
                                    <div class="input-group">
                                        <span class="input-group-btn">
                                            <button class="btn secondary" type="button">Session</button>
                                        </span>
                                        <select class="form-control status-dropdown" id="session">
                                            @foreach ($sessions as $session)
                                                <option value="{{ $session->session }}"
                                                    @if ($session->session == 'November') selected @endif>
                                                    {{ $session->session }}</option>
                                            @endforeach
                                        </select>

                                    </div>
                                </div>
                                <div class="pull-right col-md-4">
                                    <div class="input-group">
                                        <span class="input-group-btn">
                                            <button class="btn secondary" type="button">level</button>
                                        </span>
                                        <select class="form-control status-dropdown" id="level">
                                            @foreach ($levels as $level)
                                                <option
                                                    value="{{ $level->level }}"@if ($level->level == 'LGCSE') selected @endif>
                                                    {{ $level->level }}</option>
                                            @endforeach
                                        </select>

                                    </div>

                                </div>
                                <div class="clearfix"></div>
                            </fieldset>
                            <div class="panel-body" id="timetable-view">

                            </div>
                        </div>
                        <!-- END PANEL NO CONTROLS -->
                    </div>

                </div>
            </div>
        </div>
        <!-- END MAIN CONTENT -->
    </div>
    <!-- END MAIN -->
    <div class="clearfix"></div>
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
        /*-----------------------------------/
                        	/*DISPLAY  TIMETABLE
                          /*----------------------------------*/

        displayTimetable();

        function displayTimetable() {
            var action = "view-timetable";
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });


            $.ajax({
                url: "{{ route('admin.timetable.index') }}",
                method: "GET",
                data: {
                    level: $("#level").val(),
                    session: $("#session").val(),
                },
                success: function(data) {
                    $("#timetable-view").html(data.table);
                },
            });
        }
        // edit timetable
        $(document).on("click", "#timetable-view .editBtn", function() {
            //hide edit span

            $(this).closest("tr").find(".editSpan").hide();

            //show edit input
            $(this).closest("tr").find(".editInput").show();

            //hide edit button
            $(this).closest("tr").find(".editBtn").hide();

            //show edit button
            $(this).closest("tr").find(".saveBtn").show();
        });
        // save changes timetable
        $(document).on("click", "#timetable-view .saveBtn", function() {
            var trObj = $(this).closest("tr");
            var ID = $(this).closest("tr").attr("id");
            var inputData = $(this).closest("tr").find(".editInput").serialize();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                url: "{{ route('admin.timetable.update') }}",
                dataType: "json",
                data: "id=" + ID + "&" + inputData,
                success: function(response) {
                    trObj.find(".editInput").hide();
                    trObj.find(".saveBtn").hide();
                    trObj.find(".editSpan").show();
                    trObj.find(".editBtn").show();
                    displayTimetable();
                    toastr.success("You have successfully Saved Changes");
                },
            });
        });





        $(document).on("change", ".timetable-publisher", function() {




            var publisher = $(`.timetable-publisher:checked`).val() === undefined ? 0 : $(
                `.timetable-publisher:checked`).val()

            var status = publisher == 1 ?"publish": "unpublish";
            if (confirm(`Are you sure you want to ${status} this timetable?`)) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: "{{ route('admin.timetable.index') }}",
                    method: "GET",
                    data: {
                        level: $("#level").val(),
                        session: $("#session").val(),
                        publisher: publisher
                    },
                    success: function(response) {
                        displayTimetable();
                        toastr.success(`You have successfully ${status}  the timetable `);
                    },
                });
            } else {
                return;
            }
        });


        $("#level").on("change", function() {
            displayTimetable();
        });

        $("#session").on("change", function() {
            displayTimetable();
        });
    </script>
@endsection
