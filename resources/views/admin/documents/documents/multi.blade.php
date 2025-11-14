@extends('layouts.admin')

@section('content')
    <!-- ============================================================== -->
    <!-- Start Page Content -->
    <!-- MAIN -->
    <div class="main">
        <!-- MAIN CONTENT -->
        <div class="main-content">
            <div class="container-fluid">
                <h3 class="page-title">All Candidates</h3>
                <div class="row">

                    <div class="panel">
                        <div class="panel-heading">
                            <h3 class="panel-title">Filter Registered Center</h3>
                        </div>


                        <div class="panel-body">

                            <form action="">
                                <div class="form-group col-md-4">
                                    <label for="">By Level</label>
                                    <select class="form-control" name="level" id="level">
                                        <option value="">Please Select Level</option>
                                        @foreach ($levels as $level)
                                            <option value="{{ $level->level }}">{{ $level->level }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="">By Session</label>
                                    <select class="form-control" name="session" id="session">
                                        <option value="">Please Select Session</option>
                                        @foreach ($sessions as $session)
                                            <option value="{{ $session->session }}">{{ $session->session }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="">By Center</label>
                                    <select class="form-control" name="center" id="center">
                                        <option value="">Please Select Center</option>
                                    </select>
                                </div>

                                <div class="form-group col-md-4">
                                    <label for="year">Year</label>
                                    <select class="form-control status-dropdown" name="year" id="year">
                                        @foreach ($years as $year)
                                            <option
                                                value="{{ $year }}"@if ($year == date('Y')) selected @endif>
                                                {{ $year }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="filter">Show</label>
                                    <select class="form-control" name="filter" id="filter">
                                        <option value="10">10</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                        <option value="500">500</option>
                                        <option value="1000">1000</option>
                                        <option value="5000">5000</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="subjects">Subjects</label>
                                    <select class="form-control" name="subjects" id="subjects">
                                        <option value="">Select Subject</option>
                                    </select>
                                </div>

                        </div>
                        <div class="clearfix"></div>
                        </form>
                    </div>

                    <!-- PANEL NO CONTROLS -->
                    <div class="panel">
                        <div class="panel-heading">
                            <h3 class="panel-title">All Candidates</h3>
                        </div>
                        <div class="panel-body">
                            <div class="pull-left">
                                <a href="" id="share-document-multi" class="btn btn-info hidden">
                                    <i class='fas fa-share-alt' aria-hidden='true'></i> Share
                                </a>
                            </div>
                            <div class="clearfix"></div>
                            <div id="candidates-per-center" class="table-responsive">
                                <div class="animated-wrapper">
                                    <div class="loader">Please wait...</div>
                                </div>
                            </div>

                        </div>
                        <div class="clearfix"></div>
                        <!-- TABBED CONTENT -->


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

    <!--ADD  PERMISSIONS USERS MODEL -->
    <div class="modal fade" id="permissions-users-model">
        <div class="modal-dialog  modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title">User Permission</h4>

                </div>
                <div class="container"></div>
                <div class="modal-body">
                    <form action="{{ route('admin.documents.multipermissions.multipleDocumentsToUser') }}" id="permissions-users-form" method="post">
                        <div>
                            @csrf
                        </div>
                        <div class="form-group col-md-12">
                            <label class="fancy-checkbox">
                                <input type="checkbox" name="document_user_Permission[is_time_bound]" value="1">
                                <span>Spacify the Period</span>
                            </label>
                            <div class="specify-period">
                                <div class="date-inserted">
                                    <label for="name">Start Date</label>
                                    <input type="datetime-local" class="form-control" name="document_user_Permission[start_date]"
                                        id="start_date" value="" />
                                </div>
                                <div class="date-inserted">
                                    <label for="name">End Date</label>
                                    <input type="datetime-local" class="form-control" name="document_user_Permission[end_date]"
                                        id="end_date" value="" />
                                </div>
                            </div>
                            <label class="fancy-checkbox">
                                <input type="checkbox" name="document_user_Permission[is_allow_download]" value="1">
                                <span>Allow Download</span>
                            </label>

                        </div>
                        <div class="form-group">
                            <table class="table" name="tablename" id="documents">
                                <thead>
                                    <tr>
                                        <th><input type='checkbox' class='documents-select-all' name='documents-selected'
                                                value='1'></th>
                                        <th>Name</th>
                                        <th>Document Category</th>
                                        <th>Storage</th>
                                        <th>Created Date</th>
                                    </tr>
                                </thead>
                            </table>
                            @push('scripts')
                                <script>
                                    $(function() {
                                        var table = $("#documents").DataTable({
                                            ajax: "{{ route('admin.documents.multipermissions.index') }}",
                                            columns: [{
                                                    data: "checkbox",
                                                    name: "checkbox",
                                                    searchable: false,
                                                    orderable: false,
                                                },
                                                {
                                                    data: "document_name",
                                                    name: "document_name",
                                                    searchable: false,
                                                    orderable: false,
                                                },
                                                {
                                                    data: "categories.name",
                                                    name: "categories.name",
                                                    searchable: false,
                                                    orderable: false,
                                                },
                                                {
                                                    data: "location",
                                                    name: "location",
                                                    searchable: false,
                                                    orderable: false,
                                                },



                                                {
                                                    data: "created_date",
                                                    name: "created_date",

                                                }

                                            ],

                                        });
                                        $("#documents").css("width", "98.5%");
                                    });
                                </script>
                            @endpush
                        </div>

                    </form>

                </div>
                <div class="modal-footer"> <a href="#" data-dismiss="modal" class="btn">Close</a>
                    <a href="#" id="save-permissions-users" class="btn btn-primary">Save changes</a>

                </div>
            </div>
        </div>
    </div>
    <!--END ADD  PERMISSIONS MODEL --->
@endsection
@section('script')
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
        $(document).on('click', '.users-permissions-select-all', function() {
            $('.users-permissions-select').prop('checked', this.checked);

            $("#share-document-multi").toggleClass('hidden', this.checked)

            if (this.checked) {
                $("#share-document-multi").removeClass('hidden');
            } else {
                $("#share-document-multi").addClass('hidden');
            }

        });

        $(document).on('change', '.users-permissions-select', function(ev) {
            var check = ($('.users-permissions-select').filter(":checked").length == $('.users-permissions-select')
                .length);
            $('.users-permissions-select-all').prop("checked", check);
            if (!check) {
                $("#share-document-multi").removeClass('hidden');
            } else {
                $("#share-document-multi").addClass('hidden');
            }
        });

        $(document).on('click', '.documents-select-all', function() {
            $('.documents-select').prop('checked', this.checked);
        });

        $(document).on('change', '.documents-select', function(ev) {
            var check = ($('.document-select').filter(":checked").length == $('.users-permissions-select')
                .length);
            $('.documents-select-all').prop("checked", check);

        });



        $(document).on("change", "[name='document_user_Permission[is_time_bound]']", function() {
            $(this).parent().next().css({
                'display': !this.checked ? 'none' : 'flex'
            })
        });





        $(document).on("click", "#share-document-multi", function(ev) {
            ev.preventDefault();

            var users = [];
            $("[name='users_permissions[]']:checked").each(function(i) {
                users[i] = $(this).val();
            });
            if (users.length === 0) {
                toastr.error("select atleast one candidate");
            } else {
                $('#permissions-users-model').modal('show');
            }



            // if (confirm("are You sure delete this candidate ?")) {
            //     var candidateNo = [];
            //     $("[name='candidates[]']:checked").each(function(i) {
            //         candidateNo[i] = $(this).val();
            //     });

            //     if (candidateNo.length === 0) {
            //         alert("Please select atleast one candidate");
            //     } else {
            //         $.ajaxSetup({
            //             headers: {
            //                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            //             }
            //         });
            //         $.ajax({
            //             type: "POST",
            //             url: "{{ route('center.candidates.deleteCandidates') }}",
            //             data: {
            //                 candidateNumbers: candidateNo
            //             },
            //             success: function(data) {
            //                 $('#amend-datatable').DataTable().ajax.reload(null, false);
            //                 toastr.success("successfully deleted " + candidateNo.length +
            //                     " candidates");

            //             },
            //         });
            //     }
            // }

        });

        $(document).on('click', '#save-permissions-users', function(ev) {
            ev.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var form = $("#permissions-users-form");
            var data = form.serializeArray()
            var users = [];
            var documents = [];
            $("[name='users_permissions[]']:checked").each(function(i) {
                users[i] = $(this).val();
                data.push({
                    name: `users_permissions[${i}]`,
                    value: $(this).val()
                });

            });


            $("[name='documents[]']:checked").each(function(i) {
                documents[i] = $(this).val();
            });


            if (documents.length === 0) {
                toastr.error("select atleast one document");
            } else {

                var url = form.attr('action');
                $.ajax({
                    url: url,
                    method: "POST",
                    data:data,
                    beforeSend: function() {

                    },
                    success: function(data) {

                        if ($.isEmptyObject(data.errors)) {
                            $('#permissions-users-model').modal('hide');
                            toastr.success(data.success);
                            $('#share-document-datatable').DataTable().ajax.reload();
                        } else {
                            printErrorMsg('#permissions-users-form', data.errors);
                        }
                    },
                    error: function(error) {
                        console.log(error);

                    },
                    complete: function(data) {

                    }
                });

            }
        });




        /*-----------------------------------/
        /********** Some Variable Initial Value **************/

        var level = $("#level").val();
        var session = $("#session").val();
        var center = $("#center").val();
        var year = $("#year").val();
        var subject = $("#subjects").val();

        var filter = $("#filter").val();

        /**********  Center_no,level,session Change get Subjects,   **************/
        $(document).on("change",
            "#edit-form  #center_no, #addCandidateForm #center_no,#edit-form  #level, #addCandidateForm #level,#edit-form  #session, #addCandidateForm #session",
            function() {
                var form = $(this).closest("form").attr('id');
                centerSubjects(form)
            });
        /**********  Center_no,level,session Change get Subjects,   **************/


        /**********  Candidates Sorting Start    **************/
        $("#center").on("change", function(event) {
            level = $("#level").val();
            session = $("#session").val();
            center = $(this).val();
            year = $("#year").val();
            subject = $("#subjects").val();

            filter = $("#filter").val();



            candidate_per_center(
                level,
                session,
                center,
                filter,
                year,
                subject,
                event = event
            );
        });
        /**********  Candidates Sorting End    **************/

        /**********  Candidates filter Start    **************/
        $("#session").on("change",
            function() {
                $('#center').html(`<option value="">
                                  Please Select Center
                              </option>`);
                level = $("#level").val();
                var session = $("#session").val();
                center = $("#center").val();
                year = $("#year").val();
                filter = $("#filter").val();
                var subject = $("#subjects").val();



                candidate_per_center(
                    level,
                    session,
                    center,
                    filter,
                    year,
                    subject,
                    event = null
                );


            });
        /**********  Candidates filter  End    **************/
        /**********  Candidates filter Start    **************/
        $("#filter").on("change", function() {
            level = $("#level").val();
            session = $("#session").val();
            center = $("#center").val();
            year = $("#year").val();
            subject = $("#subjects").val();

            filter = $(this).val();



            candidate_per_center(
                level,
                session,
                center,
                filter,
                year,

                subject,

                event = null
            );


        });
        /**********  Candidates filter  End    **************/

        /**********  Candidates Main Search Start    **************/
        $("#level").on("change", function() {
            $('#center').html(`<option value="">
                                  Please Select Center
                              </option>
                              <option value="All">
                                  Please Select Center
                              </option>`);
            level = $(this).val();
            session = $("#session").val();
            center = $("#center").val();
            year = $("#year").val();
            filter = $("#filter").val();


            candidate_per_center(
                level,
                session,
                center,
                filter,
                year,

                subject,

                event = null
            );

        });
        /**********  Candidates Main Search End   **************/

        /**********  Candidates Main Search Start    **************/
        $("#year").on("change", function() {
            $('#center').html(`<option value="">
                                  Please Select Center
                              </option>
                              <option value="All">
                                  Please Select Center
                              </option>`);

            level = $("#level").val();
            session = $("#session").val();
            center = $("#center").val();
            var year = $("#year").val();
            filter = $("#filter").val();


            filter = $(this).val();
            candidate_per_center(
                level,
                session,
                center,
                filter,
                year,

                subject,

                event = null
            );

        });
        /**********  Candidates Main Search End   **************/





        /**********  Candidates Main Search Start    **************/
        $("#subjects").on("change", function(event) {
            $('#center').html(`<option value="">
                                  Please Select Center
                              </option>
                              <option value="All">
                                  Please Select Center
                              </option>`);

            level = $("#level").val();
            session = $("#session").val();
            center = $("#center").val();
            year = $("#year").val();
            filter = $("#filter").val();


            subject = $(this).val();
            candidate_per_center(
                level,
                session,
                center,
                filter,
                year,
                subject,
                event = event
            );

        });
        /**********  Candidates Main Search End   **************/


        candidate_per_center(
            level,
            session,
            center,
            filter,
            year,
            subject,
        );
        /****  AJAX Main Function Who Perform All Tasks Start *******/
        function candidate_per_center(
            level,
            session,
            center,
            filter,
            year,
            subject,

            event = null
        ) {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            var i = 0;
            $.ajax({
                url: "{{ route('admin.documents.multipermissions.centersAccounts') }}",
                method: "GET",
                data: {
                    level: level,
                    session: session,
                    center: center,
                    filter: filter,
                    year: year,

                    subject: subject,

                },
                beforeSend: function() {
                    // setting a timeout
                    $(".animated-wrapper").fadeIn();
                    i++;
                },
                success: function(data) {
                    $("#candidates-per-center").html(data.cendidate_per_center);
                    var centers = data.centers
                    var subjects = data.subjects
                    if (event == null) {
                        $('#subjects').html(`<option value="">
                                  Please Select subject
                              </option>`);
                        $('#center').html(`<option value="">
                                  Please Select Center
                              </option>`);

                        centers.forEach(center => {
                            $('#center').append($('<option>').val(center.center_no).text(center
                                .center_no +
                                ' - ' + center.center_name));
                        });


                        subjects.forEach(subject => {
                            $('#subjects').append($('<option>').val(subject.subject_code).text(subject
                                .subject_code +
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
        /****  Print errors*******/
        function printErrorMsg(parent, msg) {
            $(`${parent} input, ${parent} select, textarea`).each(function(index) {
                $(`${parent} .help-block`).remove();
                $(`${parent} .has-error`).removeClass('has-error');
                // console.log(input.attr('type') + 'Name: ' + input.attr('name') + '  Value: ' + input.val());
            });
            $.each(msg, function(key, errors) {
                for (const error in errors) {
                    const value = errors[error];
                    console.log(key);
                    $(`[name='${key}']`).parent().addClass('has-error');
                    if (key == "gender") {
                        $(`${parent} [name='${key}']`).next().append(`<span class='help-block'>${value}</span>`);
                    } else if (key.indexOf('.') > -1) {
                        console.log(key);
                        var input = key.split(".");
                        var parentinput = input[0];
                        var childinput = input[1];
                        $(`[name^='${parentinput}[${childinput}]']`).parent().addClass('has-error');
                        $(`<span class='help-block'>${value}</span>`).insertAfter(
                            `${parent} [name^='${parentinput}[${childinput}]']`)
                    } else {
                        $(`<span class='help-block'>${value}</span>`).insertAfter(`${parent} [name='${key}']`)
                    }


                }
            });
        }
        /****  Print errors End*******/
    </script>
@endsection
