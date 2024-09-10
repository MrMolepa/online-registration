@extends('layouts.admin')
@section('content')
    <div class="main">
        <!-- MAIN CONTENT -->
        <div class="main-content">
            <div class="container-fluid">
                <h3 class="page-title">PDF Settings</h3>
                <div class="row d-flex justify-content-center">
                    <div class="profile-container ">
                        <!-- PANEL NO CONTROLS -->
                        <div class="panel">
                            <div class="panel-heading">
                                <h3 class="panel-title">PDF Settings</h3>
                            </div>
                            <div class="panel-body">

                                <div>

                                    <form action="{{ route('admin.over-print.print') }}" method="post">
                                        @csrf

                                        <div class="form-group ">
                                            <label for="level">Level</label>
                                            <select name="level" class="form-control" id="level">
                                                <option value="">Please Select Level</option>
                                                @foreach ($levels as $level)
                                                    <option value="{{ $level }}">
                                                        {{ $level }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group ">
                                            <label for="district">District</label>
                                            <select name="district" class="form-control" id="district">
                                                <option value="">Please Select</option>
                                                @foreach ($districts as $district_code => $district)
                                                    <option value="{{ $district_code }}">
                                                        {{ $district_code }}-{{ $district }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group ">
                                            <label for="center_no">Centers</label>
                                            <select name="center_no" class="form-control" id="center_no">
                                                <option value="">Please Select</option>
                                            </select>
                                        </div>

                                        <div class="form-group ">
                                            <label for="subject">Subject</label>
                                            <select name="subject" class="form-control" id="subject">
                                                <option value="">Please Select</option>

                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="candidate_no" class="control-label">Candidate Number</label>
                                            <input type="text" class="form-control" name="candidate_no" value=""
                                                id="candidate_no">
                                        </div>


                                        <div class="form-row">
                                            <div class="form-group">
                                                <label class="fancy-checkbox">
                                                    <input type="checkbox" name="blank" value="1" checked>
                                                    <span>Templete Blank over print</span>
                                                </label>
                                            </div>

                                        </div>

                                        <div class="form-row">
                                            <div class="form-group">
                                                <input type="submit" name="print-statement"
                                                    class="form-control btn btn-primary" value="Print">
                                            </div>

                                        </div>




                                    </form>
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
    <!-- END MAIN -->
    <div class="clearfix"></div>

    <!-- /. PAGE WRAPPER  -->
@endsection

@section('script')
    <script>
        /*-----------------------------------/
                                                                                                                                                                                                                                                                                                                                                                                                                                                                    /*Diplay candidates
                                                                                                                                                                                                                                                                                                                                                                                                                                                                    /*----------------------------------*/

        /********** Some Variable Initial Value **************/

        var level = $("#level").val();
        var subject = $("#subject").val();
        var center = $("#center_no").val();
        var district = $("#district").val();





        // /**********  Candidates Main Search Start    **************/
        $("#level").on("change", function() {
            level = $(this).val();
            subject = $("#subject").val();
            center = $("#center_no").val();
            district = $("#district").val();
            candidate_per_center(
                level,
                subject,
                center,
                district,
                event=null
            );

        });

        $("#district").on("change", function() {
            level = $("#level").val();
            subject = $("#subject").val();
            center = $("#center_no").val();
            district = $(this).val();
            candidate_per_center(
                level,
                subject,
                center,
                district,
                event=null
            );

        });
        // /**********  Candidates Main Search End   **************/
        // /**********  Candidates Sorting Start    **************/
        $("#center_no").on("change", function(event) {
            level = $("#level").val();
            subject = $("#subject").val();
            center = $("#center_no").val();
            district = $("#district").val();


            candidate_per_center(
                level,
                subject,
                center,
                district,
                event
            );


        });
        // /**********  Candidates Sorting End    **************/



        /*****  Retrieve Value When Page First Load  *******/

        candidate_per_center(
            level,
            subject,
            center,
            district,
             null
        );



        /****  AJAX Main Function Who Perform All Tasks Start *******/
        function candidate_per_center(
            level,
            subject,
            center,
            district,
            event = null
        ) {

            var i = 0;
            $.ajax({

                url: "{{ route('admin.over-print.index') }}",
                method: "GET",
                data: {
                    level: level,
                    subject: subject,
                    center: center,
                    district: district
                },
                beforeSend: function() {
                    // setting a timeout

                },
                success: function(data) {
                    console.log(data);
                    var centers = data.centers;
                    var subjects = data.subjects;
                    console.log(subjects );


                    if (event == null) {
                        $('#center_no').html(`<option value="">
                                  Please Select Subject
                              </option>`);
                        $('#subject').html(`<option value="">
                                  Please Select Subject
                              </option>`);

                        centers.forEach(center => {
                            $('#center_no').append($('<option>').val(center.center_no).text(center
                                .center_no +
                                ' - ' + center.center_name));

                        });
                        subjects.forEach(subject => {
                            $('#subject').append($('<option>').val(subject.subject_code).text(subject.subject_code +
                                ' - ' + subject.subject_name));

                        });


                    }

                },
                complete: function() {
                    i--;
                    if (i <= 0) {
                        $(".animated-wrapper").fadeOut();
                    }
                },

            });
        }
        /****  AJAX Main Function Who Perform All Tasks End *******/





        /****  Print errors End*******/
    </script>
@endsection
