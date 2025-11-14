@extends('layouts.admin')
@section('content')
    <div class="main">
        <!-- MAIN CONTENT -->
        <div class="main-content">
            <div class="container-fluid">
                <h3 class="page-title">Manage Invigilator Contracts</h3>
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel">
                            <div class="panel-heading">

                            </div>
                            <div class="panel-body">
                                @if (session()->has('success'))
                                    <div class="alert alert-success alert-dismissible" role="alert">
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                                aria-hidden="true">&times;</span></button>
                                        <i class="fa fa-check-circle"></i> {{ session('success') }}
                                    </div>
                                    &nbsp;
                                @endif
                                <form id="center-filter-form" action="{{ route('admin.invigilations.invigilationReport') }}"
                                    method="GET">
                                    @csrf
                                    <fieldset class="row  fieldset-border">
                                        <legend class="fieldset-border">Filter Invigilators</legend>
                                        <div class="form-group col-md-4">
                                            <label for="">By Level</label>
                                            <select class="form-control level" name="level" id="level">
                                                <option value="">Select Level</option>
                                                @foreach ($levels as $level)
                                                    <option value="{{ $level->level }}">{{ $level->level }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="year">Year</label>
                                            <select class="form-control status-dropdown year" name="year" id="year">
                                                @foreach ($years as $year)
                                                    <option value="{{ $year }}">
                                                        {{ $year }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="">By Session</label>
                                            <select class="form-control session" name="session" id="session">
                                                <option value="">Please Select Session</option>
                                                @foreach ($sessions as $session)
                                                    <option value="{{ $session->session }}">{{ $session->session }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="sponsor" class="col-form-label">Sponsor</label>
                                            <select class="form-control sponsor" name="sponsor" id="sponsor">
                                                <option value="">Select</option>
                                                @foreach ($sponsors as $sponsor)
                                                    <option value="{{ $sponsor->sponser }}">{{ $sponsor->sponser }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="">By Catergories</label>
                                            <select class="form-control catergory" name="catergory" id="catergory">
                                                <option value="">Please Select Catergories</option>
                                                @foreach ($catergories as $catergory)
                                                    <option value="{{ $catergory->id }}">
                                                        {{ $catergory->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="center_no" class="col-form-label">Center Number</label>
                                            <select class="form-control center_no" name="center_no" id="center_no">
                                                <option value="">Select</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="status">Status</label>
                                            <select class="form-control status-dropdown filter-selected status"
                                                name="status" id="status">
                                                <option value="">Please Status</option>
                                                @foreach ($invigilation_status as $status)
                                                    <option value="{{ $status->id }}">
                                                        {{ $status->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="">File Type</label>
                                            <select class="form-control formstyler" name="file_type">
                                                <option value="1">Invigilators List(CSV)</option>
                                                <option value="2">Signed Contracts (PDF)</option>
                                                <option value="3">Invigilators Totals(CSV)</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-12">
                                            <button type="submit" class=" btn btn-primary btn-block">Export Report
                                            </button>
                                        </div>
                                    </fieldset>
                                </form>

                            </div>
                        </div>
                        <!-- PANEL NO CONTROLS -->
                        <div class="panel">
                            <div class="panel-heading">
                                <h3 class="panel-title">Invigilator Contracts <b></b></h3>
                            </div>
                            <div class="panel-body">

                                <div class="custom-tabs-line tabs-line-bottom left-aligned">
                                    <ul class="nav" role="tablist">
                                        <li class="active"><a href="#tab-bottom-left2" role="tab"
                                                data-toggle="tab">Invigilator
                                                Report</a>
                                        </li>
                                        <li>
                                            <a href="#tab-bottom-left1" role="tab" data-toggle="tab">Application
                                                progress
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="tab-content">
                                    <div class="tab-pane fade in active" id="tab-bottom-left2">

                                        <table class="table table-striped"id="invigilator-table-total">
                                            <thead>
                                                <tr>
                                                    <th>Centre No.</th>
                                                    <th width='30%'>Centre Name</th>
                                                    <th>#.Candidates</th>
                                                    <th>Chief Invigilator</th>
                                                    <th>Invigilator</th>
                                                    <th>Assistant Invigilator</th>
                                                    <th>Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>

                                    </div>
                                    <div class="tab-pane fade" id="tab-bottom-left1">
                                        <button type="button" class="btn btn-primary" data-toggle="modal"
                                            data-target="#add-center-modal">
                                            Add Invigilator
                                        </button>
                                        <button type="button" class="btn btn-primary" data-toggle="modal"
                                            data-target="#seek-approval-modal">
                                            Seek Approval
                                        </button>
                                        <div class="pull-right">
                                            <div class='status-tag auxiliar-low accepted'>
                                                <i class='highlight auxiliar-low'></i>
                                                <p class='status-tag__txt bac-l-stack-xs'>Accepted: {{ $acceptedNumber }}
                                                </p>
                                            </div>
                                            <div class='status-tag auxiliar-low pending'>
                                                <i class='highlight auxiliar-low'></i>
                                                <p class='status-tag__txt bac-l-stack-xs'>Pending: {{ $pendingNumber }}
                                                </p>
                                            </div>
                                            <div class='status-tag auxiliar-low declined'>
                                                <i class='highlight auxiliar-low'></i>
                                                <p class='status-tag__txt bac-l-stack-xs'>Declined: {{ $declinedNumber }}
                                                </p>
                                            </div>
                                        </div>
                                        <div id="invigilator">
                                            <table class="table table-striped"id="data-table-invigilation">
                                                <thead>
                                                    <tr>
                                                        <th>Center</th>
                                                        <th>Invigilator</th>
                                                        <th>ID Number</th>
                                                        <th>Surname</th>
                                                        <th>Other Names</th>
                                                        <th>Email</th>
                                                        <th>Status</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                </div>


                            </div>
                        </div>
                        <!-- END PANEL NO CONTROLS -->
                    </div>
                </div>
            </div>
            <!-- Modal add center-->
            <div class="modal fade" id="add-center-modal" tabindex="-1" role="dialog"
                aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Add Invigilator</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="center-add-form" method="POST"
                                action="{{ route('admin.invigilations.contracts.store') }}">
                                @csrf
                                @method('POST')
                                <fieldset class="row  fieldset-border">
                                    <legend class="fieldset-border">Personal Information</legend>
                                    <div class="row">
                                        <div class="form-group col-md-4">
                                            <label for="level" class="col-form-label">level</label>
                                            <select class="form-control level" name="level" id="level">
                                                <option value="">Select</option>
                                                @foreach ($levels as $level)
                                                    <option value="{{ $level->level }}">{{ $level->level }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="sponsor" class="col-form-label">Sponsor</label>
                                            <select class="form-control sponsor" name="sponsor" id="sponsor">
                                                <option value="">Select</option>
                                                @foreach ($sponsors as $sponsor)
                                                    <option value="{{ $sponsor->sponser }}">{{ $sponsor->sponser }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="catergory" class="col-form-label">Catergories</label>
                                            <select class="form-control catergory" name="catergory" id="catergory">
                                                <option value="">Select</option>
                                                @foreach ($catergories as $catergory)
                                                    <option value="{{ $catergory->id }}">
                                                        {{ $catergory->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-4">
                                            <label for="center_no" class="col-form-label">Center Number</label>
                                            <select class="form-control center_no" name="center_no" id="center_no">
                                                <option value="">Select</option>

                                            </select>
                                        </div>

                                        <div class="form-group col-md-4">
                                            <label for="invigilation_type" class="col-form-label">Invigilation
                                                Type</label>

                                            <select class="form-control invigilation_role_id" name="invigilation_role_id"
                                                id="invigilation_role_id">
                                                <option value="">Select</option>


                                            </select>
                                        </div>

                                        <div class="form-group col-md-4">
                                            <label for="invigilator_number" class="col-form-label">National ID</label>
                                            <input type="number" class="form-control" name="national_id"
                                                id="national_id" placeholder="National ID">

                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            <label for="invigilator_number" class="form-label">Surname</label>
                                            <input type="text" class="form-control" name="surname" id="surname"
                                                placeholder="Surname">

                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="invigilator_number" class="form-label">Other Names
                                            </label>
                                            <input type="text" class="form-control" name="other_names"
                                                id="other_names" placeholder="Other names">

                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            <label for="invigilator_number" class="col-form-label">Phone
                                                Number</label>
                                            <input type="number" class="form-control" name="phone_number"
                                                id="phone_number" placeholder="">

                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="invigilator_number" class="col-form-label">Email</label>

                                            <input type="text" class="form-control" name="email" id="email"
                                                placeholder="email address">

                                        </div>
                                    </div>
                                </fieldset>
                                <fieldset class="row  fieldset-border">
                                    <legend class="fieldset-border">Selection Criteria</legend>

                                    <div class="form-group row">
                                        <label for="invigilation_type" class="col-md-2 form-label">Experience</label>
                                        <div class="col-md-4">
                                            <select class="form-control " name="experience_id" id="experience_id">
                                                <option value="">Select</option>
                                                @foreach ($invigilator_experiences as $invigilator_experience)
                                                    <option value="{{ $invigilator_experience->id }}">
                                                        {{ $invigilator_experience->years }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-check col-md-6">
                                            <label for="description" class="col-md-5 form-label">Invigilator
                                                Accessibility</label>
                                            <input class="form-check-input" type="checkbox" value="1"
                                                name="accessibility" id="accessibility">
                                        </div>

                                    </div>
                                    <div class="form-group row">

                                        <div class="form-check col-md-6">
                                            <label for="description" class="col-md-4 col-form-label">Invigilator
                                                Integrity</label>
                                            <input class="form-check-input" type="checkbox" value="1"
                                                name="integrity" id="integrity">
                                        </div>
                                        <div class="form-check col-md-6">
                                            <label for="description" class="col-md-5 form-label">Invigilator
                                                Induction</label>
                                            <input class="form-check-input" type="checkbox" value="1"
                                                name="workshop" id="workshop">
                                        </div>
                                    </div>

                                </fieldset>
                                <fieldset class="row  fieldset-border">
                                    <legend class="fieldset-border">Declaration</legend>
                                    <div class="form-group row col-md-12">
                                        <p> I declare that:
                                            <li>I declare that I have appointed above person to invigilate at out
                                                center.</li>
                                            <br>
                                        </p>

                                        <div class="row col-sm-8">
                                            <input type="checkbox" class="col-sm-1" name="principal_declare"
                                                value="1" id="principal_declare" />
                                            <div class="col-sm-7"> I agree with terms and
                                                conditions </div>
                                        </div>
                                    </div>
                                </fieldset>
                                <div class="clearfix"></div>
                            </form>
                        </div>
                        <div class="modal-footer ">
                            <button type="button" class="btn btn-primary" id="add-center">
                                 <i
                                    class="fas fa-spinner fa-spin hidden loadingSpinnersave"></i><span> Save
                                </span></button>

                            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Modal Edit center-->
            <div class="modal fade" id="center-edit-modal" tabindex="-1" role="dialog"
                aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog  modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Update Invigilator</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="center-edit-form" method="POST" action="">
                                @csrf
                                @method('PUT')
                                <fieldset class="row  fieldset-border">
                                    <legend class="fieldset-border">Personal Information</legend>
                                    <div class="row">
                                        <div class="form-group col-md-4">
                                            <label for="level" class="col-form-label">level</label>
                                            <select class="form-control level" name="level" id="level">
                                                <option value="">LGCSE</option>
                                                @foreach ($levels as $level)
                                                    <option value="{{ $level }}">
                                                        {{ $level->level }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group col-md-4">
                                            <label for="sponsor" class="col-form-label">Sponsor</label>
                                            <select class="form-control sponsor" name="sponsor" id="sponsor">
                                                <option value="">O</option>
                                                @foreach ($sponsors as $sponsor)
                                                    <option value="{{ $sponsor->sponser }}">
                                                        {{ $sponsor->sponser }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="catergory" class="col-form-label ">Catergories</label>
                                            <select class="form-control catergory" name="catergory" id="catergory">
                                                <option value="">Select</option>
                                                @foreach ($catergories as $catergory)
                                                    <option value="{{ $catergory->id }}">
                                                        {{ $catergory->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-4">
                                            <label for="center_no" class="col-form-label">Center Number</label>
                                            <select class="form-control center_no" name="center_no" id="center_no">
                                                <option value="">Select</option>
                                                @foreach ($centers as $center)
                                                    <option value="{{ $center->center_no }}">
                                                        {{ $center->center_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="invigilation_type" class="col-form-label">Invigilation
                                                Type</label>
                                            <select class="form-control invigilation_role_id" name="invigilation_role_id"
                                                id="invigilation_role_id">
                                                <option value="">Select</option>
                                                {{-- @foreach ($invigilator_types as $invigilator_type)
                                                <option value="{{$invigilator_type->id}}">{{$invigilator_type->name}}</option>
                                                @endforeach --}}

                                            </select>
                                        </div>

                                        <div class="form-group col-md-4">
                                            <label for="invigilator_number" class="col-form-label">National ID</label>
                                            <input type="number" class="form-control" name="national_id"
                                                id="national_id" placeholder="National ID">

                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            <label for="invigilator_number" class="form-label">Surname</label>
                                            <input type="text" class="form-control" name="surname" id="surname"
                                                placeholder="Surname">

                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="invigilator_number" class="form-label">Other Names
                                            </label>
                                            <input type="text" class="form-control" name="other_names"
                                                id="other_names" placeholder="Other names">

                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            <label for="invigilator_number" class="col-form-label">Phone
                                                Number</label>
                                            <input type="number" class="form-control" name="phone_number"
                                                id="phone_number" placeholder="">

                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="invigilator_number" class="col-form-label">Email</label>

                                            <input type="text" class="form-control" name="email" id="email"
                                                placeholder="email address">

                                        </div>
                                    </div>
                                </fieldset>
                                <fieldset class="row  fieldset-border">
                                    <legend class="fieldset-border">Selection Criteria</legend>

                                    <div class="form-group row">
                                        <label for="invigilation_type" class="col-md-2 form-label">Experience</label>
                                        <div class="col-md-4">
                                            <select class="form-control " name="experience_id" id="experience_id">
                                                <option value="">Select</option>
                                                @foreach ($invigilator_experiences as $invigilator_experience)
                                                    <option value="{{ $invigilator_experience->id }}">
                                                        {{ $invigilator_experience->years }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-check col-md-6">
                                            <label for="description" class="col-md-5 form-label">Invigilator
                                                Accessibility</label>
                                            <input class="form-check-input" type="checkbox" value="1"
                                                name="accessibility" id="accessibility">
                                        </div>

                                    </div>
                                    <div class="form-group row">

                                        <div class="form-check col-md-6">
                                            <label for="description" class="col-md-4 col-form-label">Invigilator
                                                Integrity</label>
                                            <input class="form-check-input" type="checkbox" value="1"
                                                name="integrity" id="integrity">
                                        </div>
                                        <div class="form-check col-md-6">
                                            <label for="description" class="col-md-5 form-label">Invigilator
                                                Induction</label>
                                            <input class="form-check-input" type="checkbox" value="1"
                                                name="workshop" id="workshop">
                                        </div>
                                    </div>

                                </fieldset>
                                {{-- Resend email --}}
                                <fieldset class="row  fieldset-border">
                                    <legend class="fieldset-border">Resend Invigilator Offer</legend>
                                    <div class="row">
                                        <div class="form-group">
                                            <label class="col-md-2"> Resend Email</label>
                                            <input type="checkbox" class="col-md-1" name="resend_token"
                                                id="resend_token" value="1" />
                                        </div>
                                        <br>
                                        <label class="col-md-12">
                                            You will send Invigilator Offer via email by tick above box.
                                        </label>



                                    </div>
                                </fieldset>
                                {{-- Resend email --}}
                                <fieldset class="row  fieldset-border">
                                    <legend class="fieldset-border">Declaration</legend>
                                    <div class="form-group row col-md-6">
                                        <p> I declare that:
                                            <li>I declare that I have appointed above person to invigilate at our
                                                center.</li>
                                            <br>
                                        </p>

                                        <div class="row col-sm-12">
                                            <input type="checkbox" class="col-sm-1" name="principal_declare"
                                                value="1" id="principal_declare" />
                                            <div class="col-sm-7"> I agree with terms and
                                                conditions </div>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="status">Status</label>
                                        <select class="form-control status-dropdown" name="progress_status_id"
                                            id="progress_status_id">
                                            <option value="">Please Status</option>
                                            @foreach ($invigilation_status as $status)
                                                <option value="{{ $status->id }}">
                                                    {{ $status->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                </fieldset>

                                <div class="clearfix"></div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" id="update-center"><i
                                    class="fa fa-spinner fa-spin hidden loadingSpinnersave"></i><span> Update
                                </span></button>
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>


            <div class="modal fade" id="seek-approval-modal" tabindex="-1" role="dialog"
                aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Seek approval</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="seek-approval-form" method="POST">
                                <div class="form-group">
                                    <label for="subject" class="form-label">Subject
                                    </label>
                                    <input type="text" class="form-control" name="subject" id="subject"
                                        placeholder="subject">
                                </div>
                                <div class="form-group">
                                    <label for="email_to" class="form-label">To
                                    </label>
                                    <input type="text" class="form-control" name="email_to" id="email_to"
                                        placeholder="Email">
                                </div>
                                <div class="form-group">
                                    <label for="body" class="form-label">Body
                                    </label>
                                    <textarea name="body" class="form-control" id="body" cols="30" rows="5"></textarea>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer ">
                            <button type="button" class="btn btn-primary" id="seak-approval"> <i
                                    class="fas fa-spinner fa-spin hidden"></i><span> Send
                                </span></button>
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </div>
    <!-- END MAIN CONTENT -->
    </div>


    @push('scripts')
        <script>
            // TOASTER AND NOTIFICATION SETUP
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
            $('.modal').on('hidden.modal', function(e) {
                $('form').trigger("reset");
            });
            $(document).ready(function() {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var level = null;
                var sponsor = null;
                var catergory = null
                var center_no = null;
                var year = null;
                var session = null;
                var status = null
                // datatable

                var table = $('#data-table-invigilation').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('admin.invigilations.contracts.index') }}",
                        data: function(data) {
                            data.level = $('#center-filter-form .level').val();
                            data.sponsor = $('#center-filter-form .sponsor ').val();
                            data.catergory = $('#center-filter-form .catergory').val();
                            data.center_no = $('#center-filter-form .center_no').val();
                            data.year = $('#center-filter-form .year').val();
                            data.session = $('#center-filter-form .session').val();
                            data.status = $('#center-filter-form .status').val();
                        }
                    },
                    columns: [{
                            data: 'center_no',
                            name: 'center_no'
                        },
                        {
                            data: 'invigilation_role.invigilation_type.name',
                            name: 'invigilation_role.invigilation_type.name'
                        },
                        {
                            data: 'national_id',
                            name: 'national_id'
                        },

                        {
                            data: 'surname',
                            name: 'surname'
                        },
                        {
                            data: 'other_names',
                            name: 'other_names'
                        },

                        {
                            data: 'email',
                            name: 'email'
                        },
                        {
                            data: 'status',
                            name: 'status'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        },

                    ]
                });
                $("#data-table-invigilation").css("width", "100%");
                //Summary datatables


                var tables = $('#invigilator-table-total').DataTable({
                    processing: true,
                    serverSide: true,

                    ajax: {
                        url: "{{ route('admin.invigilations.contracts.index') }}",
                        data: function(data) {
                            data.invigilations_total = 1;
                            data.level = $('#center-filter-form .level').val();
                            data.sponsor = $('#center-filter-form .sponsor ').val();
                            data.catergory = $('#center-filter-form .catergory').val();
                            data.center_no = $('#center-filter-form .center_no').val();
                            data.year = $('#center-filter-form .year').val();
                            data.session = $('#center-filter-form .session').val();
                            data.status = $('#center-filter-form .status').val();
                        }
                    },
                    columns: [

                        {
                            data: 'center_no',
                            name: 'center_no'
                        },
                        {
                            data: 'center_name',
                            name: 'center_name'
                        },
                        {
                            data: 'candidates',
                            name: 'candidates'
                        },

                        {
                            data: 'Chief_Invigilator_School',
                            name: 'Chief_Invigilator_School'
                        },
                        {
                            data: 'Invigilator_School',
                            name: 'Invigilator_School'
                        },

                        {
                            data: 'Assistant_Invigilator_School',
                            name: 'Assistant_Invigilator_School'
                        },
                        {
                            data: 'total',
                            name: 'total'
                        },

                    ]
                });
                $("#invigilator-table-total").css("width", "100%");
                // Filter on dropdown change

                //add
                $('#add-center').on('click', function(event) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    var addForm = $("#center-add-form");
                    var url = addForm.attr('action');
                    var captionsave = $('span', this).html();
                    var $button = $(this);
                    $button.prop('disabled', true);
                    var i = 0;
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: addForm.serialize(),
                        beforeSend: function() {
                            $(".loadingSpinnersave").removeClass('hidden');
                            $button.find("span").html('Saving..');
                            $button.prop('disabled', true);
                            i++;
                        },
                        success: function(data) {
                            if ($.isEmptyObject(data.errors)) {
                                $('#add-center-modal').modal('hide');
                                toastr.success(data.success);
                                $('#data-table-invigilation').DataTable().ajax.reload();
                                $('#center-add-form').reset();
                            } else {

                                printErrorMsg('#add-center-modal', data.errors);

                            }
                            $(".loadingSpinnersave").addClass('hidden');
                        },
                        complete: function() {
                            i--;
                            if (i <= 0) {
                                $(".loadingSpinnersave").addClass('hidden');
                                $button.find("span").html(captionsave);
                                $button.prop('disabled', false);
                            }
                        },

                    });
                });
                //edit
                $(document).on('click', '.edit-center', function() {
                    var url = $(this).data("url");
                    $.ajax({
                        type: "GET",
                        url: url,
                        success: function(data) {
                            $('#center-edit-modal').modal('show');
                            var invigilation = data.invigilation;
                            var catergory = data.catergory;
                            console.log(data);
                            var url = data.url;
                            var form = '#center-edit-form';


                            $(form).attr('action', url);
                            $(`${form} input, ${form} select`).each(
                                function(index) {
                                    var input = $(this);
                                    console.log('Type: ' + input.attr('type') + 'Name: ' + input
                                        .attr(
                                            'name') +
                                        'Value: ' + input.val());
                                    var name = input.attr('name');
                                    if (input.attr('type') == "checkbox") {
                                        $(`${form} #${name}`).attr("checked", invigilation[
                                            name] == 1 ? true : false);
                                    } else {
                                        $(`${form} #${name}`).val(invigilation[name]);
                                    }


                                }
                            );
                            $(`${form} .catergory`).val(catergory.id);


                            $(`${form} .center_no`).trigger('change');

                            console.log(invigilation.invigilation_role_id)

                            $(`${form} .invigilation_role_id`).val(invigilation
                                .invigilation_role_id);








                        },
                        error: function(data) {
                            console.log('Error:', data);
                        }
                    });
                });
                // Update
                $(document).on('click', '#update-center', function(e) {
                    var editForm = $("#center-edit-form");
                    var url = editForm.attr('action');

                    $.ajax({
                        type: "POST",
                        data: editForm.serializeArray(),
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        url: url,
                        success: function(data) {
                            if ($.isEmptyObject(data.errors)) {
                                $('#center-edit-modal').modal('hide');
                                toastr.success(data.success);
                                $('#responseMessage').DataTable().ajax.reload();
                            } else {
                                printErrorMsg('#center-edit-modal', data.errors);
                            }


                        }
                    });
                });
                // Delete
                $(document).on('click', '.delete-center', function() {
                    var url = $(this).data("url");
                    $.ajax({
                        type: "DELETE",
                        url: url,
                        success: function(data) {
                            //Refresh table
                            table.draw();
                        },
                        error: function(data) {
                            console.log('Error:', data);
                        }
                    });
                });

                // seak-approval
                $(document).on('click', '#seak-approval', function() {
                    var url = $("#center-filter-form").attr('action');
                    var filter_data = $("#center-filter-form").serialize();
                    var seek_approval = $("#seek-approval-form").serializeArray();
                    var emailData = "";
                    $(seek_approval).each(function(i, field) {
                        emailData += `&${field.name }=${field.value}`;
                    });
                    filter_data += emailData;
                    filter_data += `&send=1`;
                    var caption = $(this).html();
                    var i=0;
                    $.ajax({
                        type: "GET",
                        data: filter_data,
                        url: url,
                        beforeSend: function() {
                            $(this).prop('disabled', true).html("Sending....");
                            i++;

                        },
                        success: function(data) {
                            if ($.isEmptyObject(data.errors)) {
                                $('#seek-approval-modal').modal('hide');
                                toastr.success(data.success);

                            } else {
                                printErrorMsg('#seek-approval-form', data.errors);

                            }
                             $(this).prop('disabled', false).html(caption);
                        },
                        complete: function() {
                            i--;
                            if (i <= 0) {
                                $(this).prop('disabled', false).html(caption);
                            }
                        },
                        error: function(data) {
                            $(this).log('Error:', data);
                        }
                    });
                });

                // /**********  Main Search Start filter    **************/
                $(`.level`).on("change", function() {
                    var parent = `#${$(this).closest("form").attr('id')}`;
                    level = $(this).val();
                    console.log(level)
                    sponsor = $(`${parent} .sponsor`).val();
                    catergory = $(`${parent} .catergory`).val()
                    center_no = $(`${parent} .center_no`).val();
                    year = $(`${parent} .year`).val();
                    session = $(`${parent} .session`).val();
                    status = $(`${parent} .status`).val();
                    centers(
                        level,
                        sponsor,
                        catergory,
                        center_no,
                        year,
                        session,
                        status,
                        this
                    );
                    $('#invigilator-table-total').DataTable().ajax.reload();
                    $('#data-table-invigilation').DataTable().ajax.reload();
                });
                $(`.sponsor`).on("change", function() {
                    var parent = `#${$(this).closest("form").attr('id')}`;
                    level = $(`${parent} .level`).val();
                    sponsor = $(`${parent} .sponsor`).val();
                    catergory = $(`${parent} .catergory`).val()
                    center_no = $(`${parent} .center_no`).val();
                    year = $(`${parent} .year`).val();
                    session = $(`${parent} .session`).val();
                    status = $(`${parent} .status`).val();
                    centers(
                        level,
                        sponsor,
                        catergory,
                        center_no,
                        year,
                        session,
                        status,
                        this
                    );
                    $('#invigilator-table-total').DataTable().ajax.reload();
                    $('#data-table-invigilation').DataTable().ajax.reload();
                });

                $(`.catergory`).on("change", function() {
                    var parent = `#${$(this).closest("form").attr('id')}`;
                    level = $(`${parent} .level`).val();
                    sponsor = $(`${parent} .sponsor`).val();
                    catergory = $(`${parent} .catergory`).val()
                    center_no = $(`${parent} .center_no`).val();
                    year = $(`${parent} .year`).val();
                    session = $(`${parent} .session`).val();
                    status = $(`${parent} .status`).val();
                    centers(
                        level,
                        sponsor,
                        catergory,
                        center_no,
                        year,
                        session,
                        status,
                        this
                    );
                    $('#invigilator-table-total').DataTable().ajax.reload();
                    $('#data-table-invigilation').DataTable().ajax.reload();
                });
                $(`.center_no`).on("change", function() {
                    var parent = `#${$(this).closest("form").attr('id')}`;
                    level = $(`${parent} .level`).val();
                    sponsor = $(`${parent} .sponsor`).val();
                    catergory = $(`${parent} .catergory`).val()
                    center_no = $(`${parent} .center_no`).val();
                    year = $(`${parent} .year`).val();
                    session = $(`${parent} .session`).val();
                    status = $(`${parent} .status`).val();

                    centers(
                        level,
                        sponsor,
                        catergory,
                        center_no,
                        year,
                        session,
                        status,
                        this
                    );
                    $('#invigilator-table-total').DataTable().ajax.reload();
                    $('#data-table-invigilation').DataTable().ajax.reload();
                    // Simulating ajax

                });

                $('.session').change(function() {
                    var parent = `#${$(this).closest("form").attr('id')}`;
                    level = $(`${parent} .level`).val();
                    sponsor = $(`${parent} .sponsor`).val();
                    catergory = $(`${parent} .catergory`).val()
                    center_no = $(`${parent} .center_no`).val();
                    year = $(`${parent} .year`).val();
                    session = $(`${parent} .session`).val();
                    status = $(`${parent} .status`).val();
                    centers(
                        level,
                        sponsor,
                        catergory,
                        center_no,
                        year,
                        session,
                        status,
                        this
                    );
                    $('#invigilator-table-total').DataTable().ajax.reload();
                    $('#data-table-invigilation').DataTable().ajax.reload();
                });
                $('.year').change(function() {
                    var parent = `#${$(this).closest("form").attr('id')}`;
                    level = $(`${parent} .level`).val();
                    sponsor = $(`${parent} .sponsor`).val();
                    catergory = $(`${parent} .catergory`).val()
                    center_no = $(`${parent} .center_no`).val();
                    year = $(`${parent} .year`).val();
                    session = $(`${parent} .session`).val();
                    status = $(`${parent} .status`).val();
                    centers(
                        level,
                        sponsor,
                        catergory,
                        center_no,
                        year,
                        session,
                        status,
                        this
                    );
                    $('#invigilator-table-total').DataTable().ajax.reload();
                    $('#data-table-invigilation').DataTable().ajax.reload();
                });
                $('.status').change(function() {
                    var parent = `#${$(this).closest("form").attr('id')}`;
                    level = $(`${parent} .level`).val();
                    sponsor = $(`${parent} .sponsor`).val();
                    catergory = $(`${parent} .catergory`).val()
                    center_no = $(`${parent} .center_no`).val();
                    year = $(`${parent} .year`).val();
                    session = $(`${parent} .session`).val();
                    status = $(`${parent} .status`).val();
                    centers(
                        level,
                        sponsor,
                        catergory,
                        center_no,
                        year,
                        session,
                        status,
                        this
                    );
                    $('#invigilator-table-total').DataTable().ajax.reload();
                    $('#data-table-invigilation').DataTable().ajax.reload();
                });
                /****  AJAX Main Function Who Perform All Tasks Start *******/
                function centers(
                    level,
                    sponsor,
                    catergory,
                    center_no,
                    year,
                    session,
                    status,
                    element = null
                ) {
                    $.ajax({
                        url: "{{ route('admin.invigilations.contracts.index') }}",
                        method: "GET",
                        async: false,
                        data: {
                            level: level,
                            sponsor: sponsor,
                            catergory: catergory,
                            center_no: center_no,
                            year: year,
                            session: session,
                            status: status,
                            center_filter: '1',
                        },
                        success: function(data) {
                            var parent = `#${$(element).closest("form").attr('id')}`;
                            if (!$.isEmptyObject(data.centers) && $(element).attr('name') != 'center_no') {
                                var centers = data.centers;
                                $(`${parent} .center_no`).html(`<option value="">
                                 Select Center number
                              </option>`);
                                $(`${parent} .center_no`).html(`<option value="">
                                 Select Center number
                              </option>`);

                                centers.forEach(center => {
                                    $(`${parent}  .center_no`).append($('<option>').val(
                                        center.center_no).text(
                                        center
                                        .center_no +
                                        ' - ' + center.center_name));
                                });
                            }
                            if (!$.isEmptyObject(data.invigilation_types)) {
                                var invigilation_types = data.invigilation_types;
                                $(`${parent}  .invigilation_role_id`).html(`<option value="">
                                    Select invigilator
                                    </option>`);
                                invigilation_types.forEach(invigilation_type => {
                                    $(`${parent}  .invigilation_role_id`).append($(
                                        '<option>').val(
                                        invigilation_type.id).text(invigilation_type
                                        .catergory_name +
                                        ' - ' + invigilation_type.name));
                                });
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
            });
        </script>
    @endpush
@endsection
