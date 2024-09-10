@extends('layouts.school')

@section('content')
    <div id="page-wrapper">

        <div class="header">
            <h1 class="page-header">
                Dashboard
            </h1>

            <ol class="breadcrumb">
                <li><a href="javascript:void();">Home</a></li>
                <li class="active"><a href="javascript:void();"> Dashboard</a></li>
            </ol>

        </div>
        <div id="page-inner" class="manage_candidates">

            <!-- /. ROW  -->

            <div class="row mb-2 candidate_stats">
                <!-- first card -->
                <div class="col-md-4 col-sm-12 col-xs-12">

                    <div class="board">
                        <div class="panel panel-primary">
                            <div class="number">
                                <h3>
                                    <h3>{{ $center->center_no }}</h3>
                                    <small>{{ $center->center_name }}</small>
                                </h3>
                            </div>
                            <div class="icon non_fees_icon">
                                <i class="bx bxs-school fa-5x red school_icon"></i>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- end first card -->

                <!-- second card -->
                <div class="col-md-4 col-sm-12 col-xs-12">
                    <div class="board">
                        <div class="panel panel-primary">
                            <div class="number">
                                <h3>
                                    <h3 class="card-title total-canidates"> </h3>
                                    <small>Registered Candidates</small>
                                    <div class="stats">
                                        <span class="canidates-per-sponsor"></span>
                                    </div>

                                </h3>
                            </div>
                            <div class="icon non_fees_icon">
                                <i class="fas fa-users fa-5x blue registered_candidates_icon"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end second card -->

                <!-- third card -->
                <div class="col-md-4 col-sm-12 col-xs-12">
                    <div class="board">
                        <div class="panel panel-primary">
                            <div class="number">
                                <h3>
                                    <h3 class="card-title total-amount"></h3>
                                    <small>Total Amount</small>
                                </h3>
                                <div class="stats">
                                    <span class="balance badge"></span>
                                    <span class="sponsor_overdue"></span>
                                </div>
                            </div>
                            <div class="icon fees_icon">
                                <i class="fas fa-money-bill-wave fa-5x green"></i>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- end third card -->

            </div>
            <!--/.row-->
            <div class="row">
                <div class="col-md-12">
                    <!-- Advanced Tables -->
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Candidates Information
                            <div class="fees-status">
                                <span class="sponsored"></span>Sponsored
                                <span class="unpaid-fee"></span>Unpaid
                                <span class="paid-fee"></span>Paid
                            </div>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <!-- search -->
                                <div class="col-md-6">

                                    <div class="search">
                                        <label for="">Search</label>
                                        <div class="button-search"></div>
                                        <input type="text" class=" form-control" id="search_txt" name="search"
                                            placeholder="Search" />
                                    </div>
                                </div>
                                <!-- End search -->
                                <div class="col-md-6">
                                    <!-- Limit -->
                                    <div class="limit">
                                        <label for="">Show:</label>
                                        <select class="form-control" id="candidates_filter">
                                            <option value="10" selected="selected">10</option>
                                            <option value="25">25</option>
                                            <option value="50">50</option>
                                            <option value="100">100</option>
                                            <option value="500">500</option>
                                        </select>
                                    </div>
                                    <!-- End Limit -->
                                    <!-- Sort -->
                                    <div class="sort">
                                        <label for="">Sort By:</label>
                                        <select class="form-control" id="candidates_sort">
                                            <option value="1" selected="selected">Candidate Number</option>
                                            <option value="2">Surname </option>
                                            <option value="3">Other Name</option>
                                            <option value="4">Sponsor</option>
                                            <option value="5">Type </option>
                                        </select>
                                    </div>
                                    <!-- End SOrt -->
                                </div>
                            </div>
                            <div class="row registerted-candidates">
                                <div class="col-md-12">
                                    <div class="tabbable-panel mt-3">
                                        <div class="tabbable-line">
                                            <ul class="nav nav-tabs ">
                                                <li class="active">
                                                    <a href="#school-candidates" data-toggle="tab">{{ $center->level }} School Candidates
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="#candidateUser" data-toggle="tab">Candidates Accounts</a>
                                                </li>
                                                <li>
                                                    <a href="#private-candidates" data-toggle="tab">Private Candidates</a>
                                                </li>


                                            </ul>
                                            <div class="tab-content">
                                                <div class="tab-pane active" id="school-candidates">
                                                    <div class="table-responsive candidateInfo">
                                                    </div>
                                                </div>
                                                <div class="tab-pane" id="candidateUser">
                                                    <div class="table-responsive candidateInfoUser">
                                                    </div>
                                                </div>
                                                <div class="tab-pane" id="private-candidates">
                                                    <div class="table-responsive candidateInfoPrivate">
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                        </div>
                    </div>
                    <!--End Advanced Tables -->
                </div>
            </div>
        </div>
        <!-- /. PAGE INNER  -->

    </div>
    <!-- /. PAGE WRAPPER  -->

    <!-- SHOW MODEL CANDIDATE -->
    <div class="modal fade show-candidate-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h5 class="modal-title"> Candidate Information </h5>
                </div>
                <div class="modal-body">
                    <form action="" method="post" id="show-candidate">
                        <div class="tabbable-panel">
                            <div class="tabbable-line">
                                <ul class="nav nav-tabs">
                                    <li class="active">
                                        <a href="#candidate-information" data-toggle="tab">Candidate Information</a>
                                    </li>
                                    <li>
                                        <a href="#candidate-subjects" data-toggle="tab">Subjects</a>
                                    </li>
                                    <li>
                                        <a href="#candidate-guardian" data-toggle="tab">Guardian</a>
                                    </li>
                                    <li>
                                        <a href="#candidate-payments" data-toggle="tab">Payments</a>
                                    </li>
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane p-3 active" id="candidate-information">
                                        <fieldset class="row  fieldset-border">
                                            <legend class="fieldset-border">Candidate</legend>
                                            <div class="form-group col-md-6">
                                                <label for="level" class="control-label">Registration
                                                    Level</label>
                                                <select name="level" class="form-control" id="level">
                                                    <option value=" ">Select Registration
                                                        Level</option>
                                                    @foreach ($levels as $level)
                                                        <option value="{{ $level->level }}"
                                                            data-level="{{ $level->id }}">
                                                            {{ $level->level }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="session" class="control-label">Registration
                                                    Session</label>
                                                <select class="form-control" name="session" id="session">
                                                    @foreach ($sessions as $session)
                                                        <option value="{{ $session->session }}"
                                                            data-session="{{ $session->id }}">
                                                            {{ $session->description }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="candidate_no" class="control-label">Candidate Number</label>
                                                <input type="text" class="form-control " readonly
                                                    placeholder="Enter Candidate Number" name="candidate_no"
                                                    id="candidate_no">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="national_id" class="control-label">National Id</label>
                                                <input type="text" class="form-control" readonly
                                                    placeholder="Enter National ID" name="national_id" id="national_id">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="candidate_surname" class="control-label">Surname</label>
                                                <input type="text" class="form-control" readonly
                                                    placeholder="Enter Candidate Surname" name="candidate_surname"
                                                    id="candidate_surname">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="candidate_other_name" class="control-label">Other name</label>
                                                <input type="text" class="form-control " readonly
                                                    placeholder="Enter Other_name" name="candidate_other_name"
                                                    id="candidate_other_name">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="date_of_birth" class="control-label">Date of birth</label>
                                                <input type="date" class="form-control " readonly
                                                    placeholder="Enter date of birth" name="date_of_birth"
                                                    id="date_of_birth">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="gender" class="control-label">Gender</label>
                                                <select name="gender" class="form-control" id="gender">
                                                    <option value=" ">Please Select Gender</option>
                                                    <option value="M">Male</option>
                                                    <option value="F">Female</option>
                                                </select>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="type" class="control-label">Type</label>
                                                <select name="type" class="form-control" id="type">
                                                    <option value=" ">Please Select type</option>
                                                    <option value="1">1</option>
                                                    <option value="2">2</option>
                                                    <option value="3">3</option>
                                                </select>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="sponsor" class="control-label">Sponsor</label>
                                                <select name="sponser" class="form-control" id="sponsor">
                                                    <option value=" ">Please Select sponsor</option>
                                                    <option value="O">O</option>
                                                    <option value="M">M</option>
                                                </select>
                                            </div>
                                        </fieldset>
                                        <fieldset class="row  fieldset-border">
                                            <legend class="fieldset-border">Address</legend>
                                            <div class="form-group col-md-6">
                                                <label for="postal_address">Postal
                                                    Address </label>
                                                <input type="text" class="form-control" id="postal_address"
                                                    name="postal_address" readonly>

                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="physical_address">Physical
                                                    Address</label>
                                                <input type="text" class="form-control" id="physical_address"
                                                    name="physical_address" readonly>
                                            </div>

                                            <div class="form-group col-md-6">
                                                <label for="village">Village</label>
                                                <input type="text" class="form-control" id="village" name="village"
                                                    readonly>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="candidate_district">District</label>
                                                <select class="form-control" name="district" id="district">
                                                    @foreach ($districts as $district)
                                                        <option value="{{ $district->district }}">
                                                            {{ $district->district }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                        </fieldset>
                                    </div>
                                    <div class="tab-pane " id="candidate-subjects">

                                        <fieldset class="row  fieldset-border">
                                            <legend class="fieldset-border">Subjects</legend>
                                            <ul class="list-group">
                                            </ul>
                                        </fieldset>

                                    </div>
                                    <div class="tab-pane " id="candidate-guardian">
                                        <fieldset class="row  fieldset-border">
                                            <legend class="fieldset-border">Personal Information</legend>
                                            <div class="form-group col-md-12">
                                                <label for="guardian_type">Relationship
                                                    Between</label>
                                                <select name="guardian_type" class="form-control" id="guardian_type">
                                                    <option value="">Please select
                                                        relationship</option>
                                                    @foreach ($guardian_types as $guardian_type)
                                                        <option value="{{ $guardian_type->id }}">
                                                            {{ $guardian_type->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group col-md-12">
                                                <label for="guardian_national_id">National Id</label>
                                                <input type="text" class="form-control" id="guardian_national_id"
                                                    name="guardian_national_id" readonly placeholder="national id">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="guardian_name">Other
                                                    Names</label>
                                                <input type="text" class="form-control form-control-sm"
                                                    id="guardian_name" readonly name="guardian_name" placeholder="Name">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="guardian_surname">Surname</label>
                                                <input type="text" class="form-control" id="guardian_surname"
                                                    name="guardian_surname" readonly placeholder="Surname">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="guardian_email">Email</label>
                                                <input type="text" class="form-control form-control-sm"
                                                    id="guardian_email" readonly name="guardian_email"
                                                    placeholder="Email">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="guardian_phone">Phone
                                                    Number</label>
                                                <input type="text" class="form-control form-control-sm"
                                                    id="guardian_phone_number" readonly name="guardian_phone_number"
                                                    placeholder="Phone Number">
                                            </div>
                                        </fieldset>

                                        <fieldset class="row  fieldset-border">
                                            <legend class="fieldset-border">Address</legend>
                                            <div class="form-group col-md-6">
                                                <label for="guardian_postal_address">Postal
                                                    Address </label>
                                                <input type="text" class="form-control form-control-sm"
                                                    id="guardian_postal_address" readonly name="guardian_postal_address"
                                                    placeholder="P.O.Box 2398">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="guardian_physical_address">Physical
                                                    Address</label>
                                                <input type="text" class="form-control form-control-sm"
                                                    id="guardian_physical_address" readonly
                                                    name="guardian_physical_address" placeholder="Qoaling">
                                            </div>

                                            <div class="form-group col-md-6">
                                                <label for="guardian_village">Village</label>
                                                <input type="text" class="form-control form-control-sm"
                                                    id="guardian_village" readonly name="guardian_village"
                                                    placeholder="Ha Seoli">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="guardian_district">District</label>
                                                <select class="form-control form-control-sm" name="guardian_district"
                                                    id="guardian_district">
                                                    <option value="">Please Select
                                                        District</option>
                                                    @foreach ($districts as $district)
                                                        <option value="{{ $district->district }}">
                                                            {{ $district->district }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </fieldset>
                                    </div>
                                    <div class="tab-pane " id="candidate-payments">
                                        <fieldset class="row  fieldset-border">
                                            <legend class="fieldset-border">Fee</legend>
                                            <div class="form-group col-md-12">
                                                <label for="amount">Paid Fee</label>
                                                <input type="text" class="form-control" id="amount" name="amount"
                                                    readonly>
                                            </div>
                                        </fieldset>




                                    </div>

                                </div>
                            </div>
                        </div>
                    </form>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </div>

            </div>
        </div>
    </div>
    <!-- SHOW  CANDIDATE MODAL  END-->
@endsection

@section('script')
    <script>
        /*-----------------------------------/
            /*Diplay candidates
            /*----------------------------------*/

        /********** Some Variable Initial Value **************/



        /*****  Show Candidate  *******/
        $(document).on("click", ".show-candidate", function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            var action = $(this).data("action");
            var i = 0;
            $.ajax({
                url: action,
                method: "GET",
                beforeSend: function() {
                    // setting a timeout
                    $(".preloader").fadeIn();
                    i++;
                },
                success: function(data) {

                    var parent = "#show-candidate";
                    var candidate = data.candidate === null ? {} : data.candidate;
                    var guardian = data.guardian === null ? {} : data.guardian;
                    var subjects = data.subjects === null ? {} : data.subjects;
                    var paid_fee = data.paid_fee === null ? {} : data.paid_fee;
                    $(".select-readonly").remove();
                    $(`form${parent} #candidate-information input,form${parent} #candidate-information select, form${parent} #candidate-information textarea`)
                        .each(
                            function(index) {
                                var input = $(this);
                                var type = input.prop('type');
                                var name = input.attr('name');
                                var readonlySelects = ['gender', 'session', 'level', 'sponser', 'type',
                                    'district'
                                ];
                                console.log(type);
                                if (type == "select-one") {
                                    $(`form${parent} [name='${name}']`).val(candidate[name])
                                    if (readonlySelects.indexOf(name) >= 0) {
                                        setReadonly(`form${parent} [name='${name}']`);
                                    }

                                } else {
                                    $(`form${parent} [name='${name}']`).val(candidate[name])

                                }

                            }
                        );
                    //guardian
                    $(`form${parent} #candidate-guardian input,form${parent} #candidate-guardian select, form${parent} #candidate-guardian textarea`)
                        .each(
                            function(index) {
                                var input = $(this);
                                var type = input.prop('type');
                                var guardian_prifix_length = "guardian_".length;
                                var name = input.attr('name').slice(guardian_prifix_length);
                                var readonlySelects = ['guardian_type', 'guardian_district'];
                                if (type == "select-one") {
                                    $(`form${parent} [name='guardian_${name}']`).val(guardian
                                        .hasOwnProperty(name) ? guardian[name] : '');
                                    if (name == "type") {
                                        var type = `guardian_${name}`
                                        $(`form${parent} [name='guardian_${name}']`).val(
                                            `${guardian[type]}`)
                                    }
                                    if (readonlySelects.indexOf(name) >= 0) {
                                        setReadonly(
                                            `form${parent} #candidate-guardian [name='guardian_${name}']`
                                        );
                                    }

                                } else {
                                    $(`form${parent} #candidate-guardian [name='guardian_${name}']`)
                                        .val(guardian.hasOwnProperty(name) ? guardian[name] : '')

                                }

                            }
                        );
                    //Subjects
                    $(`form${parent} #candidate-subjects ul`).html("");
                    $.each(subjects, function(key, subject) {
                        var doubleSubjectOption = ['0178', '0181'];
                        var option = doubleSubjectOption.includes(subject.subject_code) ?
                            subject.description : '';
                        $(`form${parent} #candidate-subjects ul`).append(`
                                                             <li class = "list-group-item"> ${subject.subject_code }  : ${subject.subject_name}  ${option}</li>
                                                            `);

                    });
                    // Paid
                    $(`form${parent} [name='amount']`).val(paid_fee);
                    $(".show-candidate-modal").modal("show");
                },
                complete: function() {
                    i--;
                    if (i <= 0) {
                        $(".preloader").fadeOut();
                    }
                },
            });
        });
        /*****Show Update Candidate Endr *******/

        var candidates_filter = $("#candidates_filter").val();
        var candidates_sort = $("#candidates_sort").val();
        var search_txt = "";

        /**********  Candidates Sorting Start    **************/
        $("#candidates_sort").on("change", function() {
            var candidates_sort = $(this).val();
            load_candidates(
                candidates_filter,
                candidates_sort,
                search_txt
            );


        });
        /**********  Candidates Sorting End    **************/

        /**********  Candidates filter Start    **************/
        $("#candidates_filter").on("change", function() {
            var candidates_filter = $(this).val();
            load_candidates(
                candidates_filter,
                candidates_sort,
                search_txt
            );

        });
        /**********  Candidates filter  End    **************/

        /**********  Candidates Main Search Start    **************/
        $("#search_txt").keyup(function() {
            var search = $(this).val();

            load_candidates(
                candidates_filter,
                candidates_sort,
                search
            );
        });
        /**********  Candidates Main Search End   **************/

        /*****  Retrieve Value When Page First Load  *******/

        load_candidates(
            candidates_filter,
            candidates_sort,
            search_txt
        );



        /****  AJAX Main Function Who Perform All Tasks Start *******/
        function load_candidates(
            candidates_filter,
            candidates_sort,
            search_txt
        ) {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: '{{ route('center.registered') }}',
                method: "POST",
                data: {
                    candidates_filter: candidates_filter,
                    candidates_sort: candidates_sort,
                    search_txt: search_txt,
                },
                success: function(data) {
                    $(".candidateInfo").html(data.table);
                    $(".candidateInfoPrivate").html(data.private_table);
                    $(".candidateInfoUser").html(data.candidate_user);
                },
            });
        }



        function setReadonly(selectElement) {

            $(`${selectElement}`).each(function() {
                var selectElement = $(this);
                var parent = selectElement.parent();
                var textValue = selectElement.find(":selected").text();
                if (!parent.length) {
                    parent = selectElement.parent();
                    textValue = selectElement.find(":selected").text();
                }
                var input = $("<input>");
                input.attr("id", selectElement.attr("id"));

                input.attr("type", "text");
                input.attr("value", textValue.trim());
                input.css({
                    background: "#eee",
                    opacity: 1
                });
                input.addClass("form-control select-readonly");
                input.attr("readonly", true);
                parent.append(input);
                selectElement.hide();
            });
        }


        /****  Payement  statement Start *******/
        paymentStatement();

        function paymentStatement() {
            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
            });
            $.ajax({
                url: "/center/payment-statement",
                type: 'GET',
                dataType: 'json', // added data type
                success: function(data) {
                    console.log(data)
                    var sponsors=data.sponsors;
                    var total_candidates=data.total_candidates;
                    var total_amount=data.total_overdue
                    var balance=data.balance

                    $(".total-canidates").html(total_candidates);
                    $(".total-amount").html(total_amount.toFixed(2));
                    var candidate_per_sponsor=""
                    var sponsor_overdue =""
                    for (const sponsor in sponsors) {
                        candidate_per_sponsor +=`<span> ${sponsor}:<span class='badge'>${sponsors[sponsor].total_candidate}</span></span>`
                        sponsor_overdue +=`<span> ${sponsor}: <span class='badge'>LSL ${sponsors[sponsor].sponsor_overdue.toFixed(2)}</span></span>`
                    }
                    $(".canidates-per-sponsor").html(candidate_per_sponsor);
                    $(".sponsor_overdue").html(sponsor_overdue);
                    $(".balance").html("Balance : LSL"+parseFloat(balance).toFixed(2));

                }
            });

        }
        /****  Payement  statement End *******/
        /****  AJAX Main Function Who Perform All Tasks End *******/
    </script>
@endsection
