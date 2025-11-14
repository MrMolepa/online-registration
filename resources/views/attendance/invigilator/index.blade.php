@extends('layouts.attendance')
@section('content')
    <div class="container">
        <div class="header">
            <p id="title" class="text-center">Attendance</p>
            <div id="description" class="text-center" value="">
                <h1> Examinations Council of Lesotho </h1>


            </div>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-md-5">
                    <div class="form-group">
                        <input readonly type="text" value="" name="qr-code" id="qr-code"
                            placeholder="Scan Qr Code" class="form-control">
                    </div>
                    <video id="preview" width="100%"></video>
                </div>
                <div class="col-md-7">
                    <div class="attendance-table">

                    </div>
                </div>
            </div>


        </div>
    </div>


    <!-- partial -->

    @push('scripts')
        <script type="text/javascript">
            let scanner = new Instascan.Scanner({
                video: document.getElementById('preview')
            });
            scanner.addListener('scan', function(content) {
                document.getElementById('qr-code').value = content.split('\n');
                $('#qr-code').trigger("click");

                //qr-results
                // console.log(content);
                $.ajaxSetup({
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                    },
                });

                $.ajax({
                    url: "{{ route('attendance.invigilators.store') }}",
                    method: "POST",
                    data: {
                        'qr-code': content.split('\n')
                    },
                    success: function(data) {
                        getAttendance();

                    },
                    error: function(data) {
                        console.log("error", data);
                    }
                });


            });
            getAttendance();

            Instascan.Camera.getCameras().then(function(cameras) {
                if (cameras.length > 0) {
                    scanner.start(cameras[0]);
                } else {
                    console.error('No cameras found.');
                }
            }).catch(function(e) {
                console.error(e);
            });


            function getAttendance() {

                $.ajaxSetup({
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                    },
                });

                $.ajax({
                    url: "{{ route('attendance.invigilators.index') }}",
                    method: "GET",
                    success: function(data) {

                        $('.attendance-table').html(data.attendances)


                    },
                    error: function(data) {
                        console.log("error", data);
                    }
                });

            }
        </script>
    @endpush
@endsection
