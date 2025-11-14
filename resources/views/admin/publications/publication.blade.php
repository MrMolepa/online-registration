@extends('layouts.admin')

@section('content')
    <!-- ============================================================== -->
    <!-- Start Page Content -->
    <!-- MAIN -->
    <div class="main">
        <!-- MAIN CONTENT -->
        <div class="main-content">
            <div class="container-fluid">
                <h3 class="page-title">Publications</h3>
                <div class="row">
                    <div class="col-md-12">
                        <!-- PANEL NO CONTROLS -->
                        <div class="panel">
                            <div class="panel-heading">
                                <h3 class="panel-title">Publications</h3>
                                <div class="pull-right">
                                    <a href="" class="btn btn-info" data-toggle="modal"
                                        data-target="#add-publication">
                                        + create
                                    </a>
                                </div>
                            </div>

                            <div class="clearfix"></div>
                            <div class="panel-body" id="publications">

                            </div>
                        </div>
                        <!-- END PANEL NO CONTROLS -->
                    </div>

                </div>
            </div>
        </div>
        <!-- END MAIN CONTENT -->


        <!-- ADD publication MODAL -->
        {{--  `title`, `display_name`, `level`, `session`, `publish`, `published_at`, --}}
        <div class="modal fade bd-modal-md" id="add-publication" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog ">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close resetform" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h3 class="modal-title">New Publication</h3>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('admin.publications.store') }}" method="post" id="addPublicationForm">
                            <div>
                                @csrf
                            </div>
                            <div class="form-group  ">
                                <label for="title">Title</label>
                                <input type="text" class="form-control" name="title" id="title" value="" />
                            </div>
                            <div class="form-group ">
                                <label for="display_name">Display Name</label>
                                <input type="text" class="form-control" name="display_name" id="display_name"
                                    value="" />
                            </div>

                            <div class="form-group">
                                <label for="level" class="control-label">Level</label>
                                <select id="level" name="level" class="form-control">
                                    <option value="">Please Select Level</option>
                                    @foreach ($levels as $level)
                                        <option value="{{ $level->level }}"> {{ $level->level }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="session" class="control-label">Session</label>
                                <select id="session" name="session" class="form-control">
                                    <option value="">Please Select Session</option>
                                    @foreach ($sessions as $session)
                                        <option value="{{ $session->session }}"> {{ $session->session }}</option>
                                    @endforeach
                                </select>
                            </div>


                            <div class="form-group">
                                <label class="control-label" for="published_at">Published at</label>
                                <input type="datetime-local" class="form-control" name="published_at" id="subject_name"
                                    value="" />
                            </div>
                            <div class="form-group">
                                <div class="form-check">
                                    <input class="form-check-input" name="publish" type="checkbox" value="1"
                                        id="publish">
                                    <label class="form-check-label" for="publish">
                                        is published
                                    </label>
                                </div>
                            </div>




                        </form>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="add-publication" class="btn btn-primary"
                            id="save-publication">Save</button>
                        <button type="button" class="btn btn-danger resetform" id="close"
                            data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>

        </div>
        <!--END ADD SUBJECT MODEL -->
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

        displayPublications();

        function displayPublications() {
            var action = "view-timetable";
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('admin.publications.display') }}",
                method: "GET",
                success: function(data) {

                    $("#publications").html(data.table);
                },
            });
        }
        // edit timetable
        $(document).on("click", "#publications .editBtn", function() {
            //hide edit span

            $(this).closest("tr").find(".editSpan").hide();

            //show edit input
            $(this).closest("tr").find(".editInput").show();

            //hide edit button
            $(this).closest("tr").find(".editBtn").hide();

            //show edit button
            $(this).closest("tr").find(".saveBtn").show();
        });
        // Update changes timetable
        $(document).on("click", "#publications .saveBtn", function() {
            var trObj = $(this).closest("tr");
            var ID = $(this).closest("tr").attr("id");
            var url = '{{ route('admin.publications.update', ':id') }}';
            url = url.replace(':id', ID);
            var inputData = $(this).closest("tr").find(".editInput").serialize();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "PUT",
                url: `${ url}`,
                dataType: "json",
                data: inputData,
                success: function(response) {
                    trObj.find(".editInput").hide();
                    trObj.find(".saveBtn").hide();
                    trObj.find(".editSpan").show();
                    trObj.find(".editBtn").show();
                    displayPublications();
                    toastr.success("You have successfully Saved Changes");
                },
            });
        });


        $(document).on('click', '#save-publication', function(ev) {
            ev.preventDefault();
            var url = $('#addPublicationForm').attr('action');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });


            var inputData = $("#addPublicationForm").serialize();

            $.ajax({
                url: url,
                method: "POST",
                data: inputData,
                success: function(data) {
                    console.log(data);
                    if ($.isEmptyObject(data.errors)) {
                        $('#add-publication').modal('hide');
                        $('#addPublicationForm .help-block').remove();
                        $('#addPublicationForm .has-error').removeClass('has-error');
                        toastr.success(data.success);
                        displayPublications();
                    } else {
                        printErrorMsg('#addPublicationForm', data.errors);
                    }


                }
            });


        });



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

                    $(`[name='${key}']`).parent().addClass('has-error');
                    if (key == "gender") {
                        $(`${parent} [name='${key}']`).next().append(
                            `<span class='help-block'>${value}</span>`);
                    } else {
                        $(`<span class='help-block'>${value}</span>`).insertAfter(
                            `${parent} [name='${key}']`)
                    }


                }
            });
        }
        /****  Print errors End*******/
    </script>
@endsection
