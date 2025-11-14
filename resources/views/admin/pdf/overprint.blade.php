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
                                    <form action="{{ route('admin.pdf.over-print.pdf') }}" method="post">
                                        @csrf


                                        <div class="form-group col-md-12">
                                            <label for="template">Pdf Templates</label>
                                            <select name="template" class="form-control" id="template">
                                                <option value="">Please Select Template</option>
                                                @foreach ($pdfTemplates as $pdfTemplate)
                                                    <option value="{{ $pdfTemplate->id }}">
                                                        {{ $pdfTemplate->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @error('template')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                        <div class="form-group col-md-6">
                                            <label for="financial_year">Financial year</label>
                                            <select name="financial_year" class="form-control" id="financial_year">
                                                <option value="">Please Select financial year</option>
                                                @foreach ($years as $year)
                                                    <option value="{{ $year->financial_year }}">
                                                        {{ $year->financial_year }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="session">Sessions</label>
                                            <select name="session" class="form-control" id="session">
                                                <option value="">Please Select session</option>
                                                @foreach ($sessions as $session)
                                                    <option value="{{ $session->session }}">
                                                        {{ $session->session }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="level">Level</label>
                                            <select name="level" class="form-control" id="level">
                                                <option value="">Please Select Level</option>
                                                @foreach ($levels as $level)
                                                    <option value="{{ $level }}">
                                                        {{ $level }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="district">District</label>
                                            <select name="district_code" class="form-control" id="district">
                                                <option value="">Please Select</option>
                                                @foreach ($districts as $district_code => $district)
                                                    <option value="{{ $district_code }}">
                                                        {{ $district_code }}-{{ $district }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group col-md-12">
                                            <label for="center_no">Centers</label>
                                            <select name="center_no" class="form-control" id="center_no">
                                                <option value="">Please Select</option>
                                            </select>
                                        </div>

                                        <div class="form-group col-md-12">
                                            <label for="subject_code">Subject</label>
                                            <select name="subject_code" class="form-control" id="subject_code">
                                                <option value="">Please Select</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-12">
                                            <label for="candidate_no" class="control-label">Candidate Number</label>
                                            <input type="text" class="form-control" name="candidate_no" value=""
                                                id="candidate_no">
                                        </div>
                                        <div class="form-row col-md-12">
                                            <div class="form-group">
                                                <label class="fancy-checkbox">
                                                    <input type="checkbox" name="blank" value="1" checked>
                                                    <span>Templete Blank over print</span>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group">
                                                <input type="submit" name="print" class="form-control btn btn-primary"
                                                    value="Print">
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
        var subject = $("#subject_code").val();
        var center = $("#center_no").val();
        var district = $("#district").val();





        // /**********  Candidates Main Search Start    **************/
        $("#level").on("change", function() {
            level = $(this).val();
            subject = $("#subject_code").val();
            center = $("#center_no").val();
            district = $("#district").val();
            candidate_per_center(
                level,
                subject,
                center,
                district,
                $(this)
            );

        });

        $("#district").on("change", function() {
            level = $("#level").val();
            subject = $("#subject_code").val();
            center = $("#center_no").val();
            district = $(this).val();
            candidate_per_center(
                level,
                subject,
                center,
                district,
                $(this)
            );

        });

        // /**********  Candidates Sorting Start    **************/
        $("#center_no").on("change", function(event) {
            level = $("#level").val();
            subject = $("#subject_code").val();
            center = $(this).val();
            district = $("#district").val();
            candidate_per_center(
                level,
                subject,
                center,
                district,
                $(this)
            );

        });



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
            element
        ) {

            var i = 0;
            $.ajax({

                url: "{{ route('admin.pdf.over-print.index') }}",
                method: "GET",
                data: {
                    level: level,
                    subject_code: subject,
                    center: center,
                    district: district
                },
                beforeSend: function() {
                    // setting a timeout

                },
                success: function(data) {
                    console.log(element);
                    var centers = data.centers;
                    var subjects = data.subjects;





                    if ($(element).attr('id')!= 'center_no') {
                        $('#center_no').html(`<option value="">
                                  Please Select Subject
                              </option>`);

                        centers.forEach(center => {
                            $('#center_no').append($('<option>').val(center.center_no).text(center
                                .center_no +
                                ' - ' + center.center_name));
                        });

                    }



                    $('#subject_code').html(`<option value="">
                                  Please Select Subject
                              </option>`);
                    subjects.forEach(subject => {
                        $('#subject_code').append($('<option>').val(subject.subject_code).text(
                            subject
                            .subject_code +
                            ' - ' + subject.subject_name));
                    });




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
