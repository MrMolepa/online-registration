@extends('layouts.admin')
@section('content')
    <div class="main">
        <!-- MAIN CONTENT -->
        <div class="main-content">
            <div class="container-fluid">
                <h3 class="page-title">Invitations</h3>
                <div class="row">
                    <div class="col-md-12">
                        <!-- PANEL NO CONTROLS -->
                        <div class="panel panel-headline">
                            <div class="panel-body">
                                <div class="row">

                                    <div class="col-md-3">
                                        <div class="metric">
                                            <span class="icon"><i class="lnr lnr-users"></i></span>
                                            <p>
                                                <span class="number total-recipients"></span>
                                                <span class="title">Total Recipients</span>
                                            </p>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="metric">
                                            <span class="icon"><i class="lnr lnr-bubble"></i></span>
                                            <p>
                                                <span class="number total-invitations"></span>
                                                <span class="title">Sent Invitations </span>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="metric">
                                            <span class="icon"><i class="far fa-chart-bar"></i></span>
                                            <p>
                                                <span class="number sent-invitations"></span>
                                                <span class="title">Pending Invitations</span>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="metric">
                                            <span class="icon"><i class="lnr lnr-checkmark-circle"></i></span>
                                            <p>
                                                <span class="number completed-invitations"></span>
                                                <span class="title">Accepted Invitations</span>
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <fieldset id="filters">
                                            <legend>Filter</legend>
                                            <div class="pull-left col-md-3">
                                                <div class="input-group">
                                                    <span class="input-group-btn">
                                                        <button class="btn secondary" type="button">Level</button>
                                                    </span>
                                                    <select class="form-control status-dropdown" id="level">
                                                        @foreach ($levels as $level)
                                                            <option value="{{ $level->level }}"
                                                                @if ($level->level == 'LGCSE') selected @endif>
                                                                {{ $level->level }}</option>
                                                        @endforeach
                                                    </select>

                                                </div>
                                            </div>
                                            <div class="pull-left col-md-3">
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
                                            <div class="pull-right col-md-3">
                                                <div class="input-group">
                                                    <span class="input-group-btn">
                                                        <button class="btn secondary" type="button">Roles</button>
                                                    </span>
                                                    <select class="form-control status-dropdown" id="role">
                                                        <option value=""> Role</option>
                                                        @foreach ($roles as $role)
                                                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                                                        @endforeach
                                                    </select>

                                                </div>

                                            </div>
                                            <div class="pull-right col-md-3">
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

                                            <div class="custom-tabs-line tabs-line-bottom left-aligned">
                                                <ul class="nav" role="tablist">
                                                    <li class="active"><a href="#tab-invitations" role="tab"
                                                            data-toggle="tab">Invitations</a>
                                                    </li>
                                                    <li>
                                                        <a href="#tab-roles" role="tab" data-toggle="tab">Roles</a>
                                                    </li>
                                                    <li>
                                                        <a href="#tab-script-fee" role="tab" data-toggle="tab">Script
                                                            Fee</a>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="tab-content">
                                                <div class="tab-pane fade in active" id="tab-invitations">
                                                    <button type="button" class="btn btn-primary" data-toggle="modal"
                                                        data-target="#add-recipient-modal">
                                                        New Invitation
                                                    </button>

                                                    <button type="button" class="btn btn-primary" data-toggle="modal"
                                                        data-target="#import-recipient-modal">
                                                        Import CSV
                                                    </button>

                                                    <button type="button" id="send-invitations" class="btn btn-success">
                                                        Send Invitations
                                                    </button>
                                                    <button type="button" id="export-invitations"
                                                        class="btn btn-primary">Export Invitations</button>


                                                    <table class="table table-striped"id="invitations-data-table">
                                                        <thead>
                                                            <tr>
                                                                <th><label><input type="checkbox" id="select-all"
                                                                            name="select-all-recipients"></label></th>
                                                                <th></th>
                                                                <th>First Name</th>
                                                                <th>Last Name</th>
                                                                <th>Email</th>
                                                                <th>Phone no</th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="tab-pane fade" id="tab-roles">
                                                    <button type="button" class="btn btn-primary" data-toggle="modal"
                                                        data-target="#add-role-modal">
                                                        New Role
                                                    </button>
                                                    <table class="table table-striped"id="roles-data-table">
                                                        <thead>
                                                            <tr>
                                                                <th>ID</th>
                                                                <th>Name</th>
                                                                <th>Description</th>
                                                                <th># Fields</th>
                                                                <th>Designer</th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                        </tbody>
                                                    </table>

                                                </div>
                                                <div class="tab-pane fade" id="tab-script-fee">
                                                    <button type="button" class="btn btn-primary" data-toggle="modal"
                                                        data-target="#add-script-fee-modal">
                                                        New Fee
                                                    </button>
                                                    <table class="table table-striped"id="script-fee-data-table">
                                                        <thead>
                                                            <tr>
                                                                <th>Component Code</th>
                                                                <th>Subject Code</th>
                                                                <th>Component Number</th>
                                                                <th>Subject Name</th>
                                                                <th>Session</th>
                                                                <th>Financial Year</th>
                                                                <th>Script Fee</th>
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
                            </div>
                        </div>
                    </div>
                    <!-- END PANEL NO CONTROLS -->
                </div>

            </div>


        </div>
    </div>
    <!-- END MAIN CONTENT -->

    <!-- MODAL COPY POSITIONS-->
    <div class="modal fade" id="copy-positions-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close resetform" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h3 class="modal-title">Copy Field and Position</h3>
                </div>
                <div class="modal-body">
                    <form id="copy-positions-form" method="post" action="">
                        @csrf
                        <div class="form-group">
                            <label for="source_role_id">Source Role</label>
                            <select name="source_role_id" id="source_role_id" class="form-control">
                                <option value="">Select Source Role</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}">
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="clearfix"></div>
                    </form>

                </div>
                <div class="modal-footer">
                    <button type="submit" name="copy-positions" class="btn btn-primary" id="save-copy-positions">Copy
                        positions</button>
                    <button type="button" class="btn btn-danger resetform" id="close"
                        data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!--END MODAL  COPY POSITIONS -->

    <!-- MODAL NEW SCRIPT FEE-->
    <div class="modal fade" id="add-script-fee-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close resetform" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h3 class="modal-title">New Script Fee</h3>
                </div>
                <div class="modal-body">
                    <form id="script-fee-add-form" method="post"
                        action="{{ route('admin.invitations.script-fee.store') }}">
                        @csrf
                        <div class="form-group">
                            <label for="subject_code">Subject Code</label>
                            <select name="subject_code" id="subject_code" class="form-control">
                                <option value="">Subject Code</option>
                                @foreach ($subjects as $subject)
                                    <option value="{{ $subject->subject_code }}">
                                        {{ $subject->subject_name }}({{ $subject->subject_code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="component_no">Component Number</label>
                            <input type="text" class="form-control" placeholder="eg .01" name="component_no"
                                id="component_no" value="" />
                        </div>
                        <div class="form-group">
                            <label for="financial_year">Financial Year</label>
                            <select name="financial_year" id="financial_year" class="form-control">
                                <option value="">Select Financial Year</option>
                                @foreach ($years as $year)
                                    <option value="{{ $year }}">{{ $year }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="session">Session</label>
                            <select name="session" id="session" class="form-control invitation-role">
                                <option value="">Select Session</option>
                                @foreach ($sessions as $session)
                                    <option value="{{ $session->session }}">{{ $session->session }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="script_fee">Script Fee</label>
                            <input type="text" class="form-control" name="script_fee" id="script_fee"
                                value="" />
                        </div>
                        <div class="clearfix"></div>
                    </form>

                </div>
                <div class="modal-footer">
                    <button type="submit" name="new-role" class="btn btn-primary" id="new-fee">Save</button>
                    <button type="button" class="btn btn-danger resetform" id="close"
                        data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!--END  NEW MODAL SCRIPT FEE--->

    <!-- MODAL UPDATE SCRIPT FEE-->
    <div class="modal fade" id="edit-script-fee-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close resetform" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h3 class="modal-title">Update Script Fee</h3>
                </div>
                <div class="modal-body">
                    <form id="script-fee-edit-form" method="post" action="">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="subject_code">Subject Code</label>
                            <select name="subject_code" id="subject_code" class="form-control">
                                <option value="">Subject Code</option>
                                @foreach ($subjects as $subject)
                                    <option value="{{ $subject->subject_code }}">
                                        {{ $subject->subject_name }}({{ $subject->subject_code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="component_no">Component Number</label>
                            <input type="text" class="form-control" name="component_no" id="component_no"
                                value="" />
                        </div>
                        <div class="form-group">
                            <label for="financial_year">Financial Year</label>
                            <select name="financial_year" id="financial_year" class="form-control">
                                <option value="">Select Financial Year</option>
                                @foreach ($years as $year)
                                    <option value="{{ $year }}">{{ $year }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="session">Session</label>
                            <select name="session" id="session" class="form-control invitation-role">
                                <option value="">Select Session</option>
                                @foreach ($sessions as $session)
                                    <option value="{{ $session->session }}">{{ $session->session }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="script_fee">Script Fee</label>
                            <input type="text" placeholder="eg 5.5" class="form-control" name="script_fee"
                                id="script_fee" value="" />
                        </div>
                        <div class="clearfix"></div>
                    </form>

                </div>
                <div class="modal-footer">
                    <button type="submit" name="new-role" class="btn btn-primary" id="update-fee">Save</button>
                    <button type="button" class="btn btn-danger resetform" id="close"
                        data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>

    </div>
    <!--END   UPDATE MODAL SCRIPT FEE--->


    <!-- MODAL NEW ROLE-->
    <div class="modal fade" id="add-role-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close resetform" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h3 class="modal-title">New Role</h3>
                </div>
                <div class="modal-body">
                    <form id="role-add-form" method="post" enctype="multipart/form-data"
                        action="{{ route('admin.invitations.roles.store') }}">
                        @csrf
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" placeholder="eg .Mrker" class="form-control" name="name"
                                id="name" value="" />
                        </div>
                        <div class="form-group">
                            <label for="type">Type</label>
                            <select name="type" id="type" class="form-control">
                                <option value="">Select Type</option>
                                @foreach ($types as $type)
                                    <option value="{{ $type }}">{{ $type }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea name="description" placeholder="eg .Marks scripts/papers for a subject" class="form-control"
                                id="description" cols="30" rows="2"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="contract_template">Contract template</label>
                            <input type="file" name="contract_template" class="form-control"
                                id="contract_template" />
                        </div>
                        <div class="form-group">
                            <label for="fields">Fields</label>
                            <table class="fields-table table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Label</th>
                                        <th>Name</th>
                                        <th>Type</th>
                                        <th>Options (for Select / Radio / Checkbox)</th>
                                        <th>Required</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>

                            <button type="button" class="add-fieldbtn btn btn-sm btn-success">
                                <i class="glyphicon glyphicon-plus"></i> Add Field
                            </button>

                            <hr>

                            <!-- Live Preview -->
                            <h4>Form Preview</h4>
                            <div class="well form-preview">
                                <em class="text-muted">Preview will appear here as you add fields</em>
                            </div>

                            <hr>

                        </div>


                        <div class="clearfix"></div>
                    </form>

                </div>
                <div class="modal-footer">
                    <button type="submit" name="new-role" class="btn btn-primary" id="new-role">Save</button>
                    <button type="button" class="btn btn-danger resetform" id="close"
                        data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!--END MODAL NEW ROLE-->
    <!-- MODAL EDIT ROLE -->
    <div class="modal fade" id="edit-role-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close resetform" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h3 class="modal-title">Edit Role</h3>
                </div>
                <div class="modal-body">
                    <form id="role-edit-form" method="post" action="" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <input type="hidden" name="role_id" id="edit-role-id">
                        <div class="form-group">
                            <label for="edit-name">Role Name</label>
                            <input type="text" class="form-control" name="name" id="edit-name"
                                placeholder="eg .marker">
                            <span class="help-block text-danger" id="error-edit-name"></span>
                        </div>
                        <div class="form-group">
                            <label for="edit-type">Type</label>
                            <select name="type" id="edit-type" class="form-control">
                                <option value="">Select Type</option>
                                @foreach ($types as $type)
                                    <option value="{{ $type }}">{{ $type }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="edit-description">Description</label>
                            <textarea name="description" placeholder="eg .Marks scripts/papers for a subject" class="form-control"
                                id="edit-description" cols="30" rows="2"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="contract_template">Contract template</label>
                            <input type="file" name="contract_template" class="form-control"
                                id="contract_template" />
                        </div>
                        <div class="form-group">
                            <label>Dynamic Fields</label>
                            <table class="fields-table table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Label</th>
                                        <th>Name</th>
                                        <th>Type</th>
                                        <th>Options (Select / Radio / Checkbox)</th>
                                        <th>Required</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>

                            <button type="button" class="add-fieldbtn btn btn-success">
                                <i class="glyphicon glyphicon-plus"></i> Add Field
                            </button>

                            <hr>

                            <h4>Form Preview</h4>
                            <div class="form-preview well">
                                <em class="text-muted">Preview will appear here as you add fields</em>
                            </div>

                            <hr>
                        </div>
                    </form>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="update-role">Update</button>
                    <button type="button" class="btn btn-danger resetform" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!--END MODAL EDIT ROLE-->

    <!-- NEW RECIPIENT MODAL -->
    <div class="modal fade" id="add-recipient-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close resetform" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h3 class="modal-title">New Recipient</h3>
                </div>
                <div class="modal-body">
                    <form id="recipient-add-form" method="POST" action="{{ route('admin.invitations.store') }}">
                        @csrf

                        <!-- Tabs -->
                        <div class="custom-tabs-line tabs-line-bottom left-aligned">
                            <ul class="nav" role="tablist">
                                <li class="active"><a href="#tab-basic" role="tab" data-toggle="tab">Basic Info</a>
                                </li>
                                <li>
                                    <a href="#tab-session" role="tab" data-toggle="tab">Session & Role</a>
                                </li>
                            </ul>
                        </div>

                        <div class="tab-content">
                            <!-- Basic Info Tab -->
                            <div class="tab-pane fade in active" id="tab-basic">
                                <!-- Recipient Basic Info Panel -->
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <strong>Recipient Basic Info</strong>
                                    </div>
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="first_name">First Name</label>
                                                    <input type="text" name="first_name" id="first_name"
                                                        placeholder="eg .John" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="last_name">Last Name</label>
                                                    <input type="text" name="last_name" id="last_name"
                                                        placeholder="eg. Doe" class="form-control">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="national_id">National ID</label>
                                                    <input type="text" name="national_id" id="national_id"
                                                        placeholder="eg .05124842078" class="form-control" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="email">Email</label>
                                                    <input type="email" name="email" id="email"
                                                        placeholder="eg .exmaple@ecol.org.ls" class="form-control">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="phone_number">Phone Number</label>
                                                    <input type="text" name="phone_number" id="phone_number"
                                                        placeholder="e.g.59027917" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Session & Role Tab -->
                            <div class="tab-pane fade" id="tab-session">
                                <!-- Session & Financial Year Panel -->
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <strong>Session & Financial Year</strong>
                                    </div>
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="financial_year">Financial Year</label>
                                                    <select name="financial_year" id="financial_year"
                                                        class="form-control">
                                                        <option value="">Select Financial Year</option>
                                                        @foreach ($years as $year)
                                                            <option value="{{ $year }}">{{ $year }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="session">Session</label>
                                                    <select name="session" id="session" class="form-control">
                                                        <option value="">Select Session</option>
                                                        @foreach ($sessions as $session)
                                                            <option value="{{ $session->session }}">
                                                                {{ $session->session }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="level">Level</label>
                                                    <select name="level" id="level" class="form-control level">
                                                        <option value="">Select level</option>
                                                        @foreach ($levels as $level)
                                                            <option value="{{ $level->level }}">
                                                                {{ $level->level }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="center_no">Center</label>
                                                    <select name="center_no" id="center_no" class="form-control">
                                                        <option value="">Select Center</option>

                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="start_date">Start Date</label>
                                                    <input type="date" name="start_date" id="start_date"
                                                        class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="end_date">End Date</label>
                                                    <input type="date" name="end_date" id="end_date"
                                                        class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Ro Workflow Panel -->
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <strong>Workflow</strong>
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <label for="workflow">Workflow</label>
                                            <select name="workflow" id="workflow" class="form-control">
                                                <option value="">Select workflow</option>
                                                @foreach ($workflows as $workflow)
                                                    <option value="{{ $workflow->id }}">{{ $workflow->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Role Selection & Dynamic Fields Panel -->
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <strong>Role & Custom Fields</strong>
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <label for="role_id">Role</label>
                                            <select name="role_id" id="role_id" class="form-control invitation-role">
                                                <option value="">Select Role</option>
                                                @foreach ($roles as $role)
                                                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <!-- Dynamic Role Fields -->
                                        <div id="dynamic-fields">
                                            <!-- Role-specific fields will appear here via JS -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group text-right" style="margin-top:2px;">
                            <button type="submit" class="btn btn-primary" id="new-recipient">Save Recipient</button>
                            <button type="button" class="btn btn-danger resetform" data-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--END NEW RECIPIENT MODAL -->


    <!-- IMPORT RECIPIENT MODAL -->
    <div class="modal fade" id="import-recipient-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close resetform" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h3 class="modal-title">Import Recipients via CSV</h3>
                </div>
                <div class="modal-body">
                    <form id="csv-import-form" method="POST" action="{{ route('admin.invitations.importCsv') }}">
                        @csrf

                        <div class="form-group">
                            <label for="csv_file">Upload CSV File</label>
                            <input type="file" name="csv_file" id="csv_file" class="form-control" accept=".csv">
                        </div>

                        <div class="form-group">
                            <label for="template_role_id">Role for Template</label>
                            <select name="template_role_id" id="template_role_id" class="form-control">
                                <option value="">Select Role</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <a href="#" class="btn btn-success" id="download-template" target="_blank">
                                <i class="fa fa-download"></i> Download Template
                            </a>
                        </div>

                        <hr>
                        <div id="csv-errors" class="alert alert-danger"
                            style="display:none; max-height:200px; overflow:auto;">
                            <ul></ul>
                        </div>

                        <div class="form-group text-right">
                            <button type="submit" class="btn btn-primary" id="import-recipient">Import</button>
                            <button type="button" class="btn btn-danger resetform" data-dismiss="modal">Close</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
    <!--END IMPORT RECIPIENT  MODAL -->

    <!-- UPDATE RECIPIENT MODAL -->
    <div class="modal fade" id="edit-recipient-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close resetform" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h3 class="modal-title" id="recipient-modal-title">Update Recipient</h3>
                </div>
                <div class="modal-body">
                    <form id="recipient-form-edit" method="POST">
                        @csrf
                        @method('PUT')
                        <!-- Recipient Basic Info Panel -->
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <strong>Recipient Basic Info</strong>
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="first_name">First Name</label>
                                            <input type="text" name="first_name" id="first_name"
                                                class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="last_name">Last Name</label>
                                            <input type="text" name="last_name" id="last_name" class="form-control">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="national_id">National ID</label>
                                            <input type="text" name="national_id" id="national_id"
                                                class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="email">Email</label>
                                            <input type="email" name="email" id="email" class="form-control">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="phone_number">Phone Number</label>
                                            <input type="text" name="phone_number" id="phone_number"
                                                class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group text-right" style="margin-top:2px;">
                            <button type="submit" class="btn btn-primary" id="update-recipient">Save Recipient</button>
                            <button type="button" class="btn btn-danger resetform" data-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--END UPDATE RECIPIENT MODAL -->
    <!-- UPDATE INVITATION MODAL -->
    <div class="modal fade" id="edit-invitation-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close resetform" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h3 class="modal-title" id="invitation-modal-title">Update Invitation</h3>
                </div>
                <div class="modal-body">
                    <form id="invitation-form-edit" method="POST">
                        @csrf
                        @method('PUT')
                        <!-- Session & Financial Year Panel -->
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <strong>Session & Financial Year</strong>
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="financial_year">Financial Year</label>
                                            <select name="financial_year" id="financial_year" class="form-control">
                                                <option value="">Select Financial Year</option>
                                                @foreach ($years as $year)
                                                    <option value="{{ $year }}">{{ $year }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="session">Session</label>
                                            <select name="session" id="session" class="form-control">
                                                <option value="">Select Session</option>
                                                @foreach ($sessions as $session)
                                                    <option value="{{ $session->session }}">
                                                        {{ $session->session }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="level">Level</label>
                                            <select name="level" id="level" class="form-control level">
                                                <option value="">Select level</option>
                                                @foreach ($levels as $level)
                                                    <option value="{{ $level->level }}">
                                                        {{ $level->level }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="center_no">Center</label>
                                            <select name="center_no" id="center_no" class="form-control">
                                                <option value="">Select Center</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="start_date">Start Date</label>
                                            <input type="date" name="start_date" id="start_date"
                                                class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="end_date">End Date</label>
                                            <input type="date" name="end_date" id="end_date" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Role Selection & Dynamic Fields Panel -->
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <strong>Role & Custom Fields</strong>
                            </div>
                            <div class="panel-body">
                                <div class="form-group">
                                    <label for="role_id">Role</label>
                                    <select name="role_id" id="role_id" class="form-control invitation-role">
                                        <option value="">Select Role</option>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Dynamic Role Fields -->
                                <div id="dynamic-fields">
                                    <!-- Role-specific fields will appear here via JS -->
                                </div>
                            </div>
                        </div>
                        <div class="form-group text-right" style="margin-top:2px;">
                            <button type="submit" class="btn btn-primary" id="update-invitation">Save
                                Invitation</button>
                            <button type="button" class="btn btn-danger resetform" data-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--END UPDATE INVITATION MODAL -->


    @push('scripts')
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

            $(document).ready(function() {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                let fieldIndex = 0; // Global index for both modals
                let level = $('#filters #level').val();
                let session = $('#filters #session').val();
                let financial_year = $('#filters #year').val();
                let role = $('#filters #role').val();

                dashboard(level, session, financial_year, role);


                $('#filters select').on('change', function() {
                    // Get values from all selects
                    level = $('#filters #level').val();
                    session = $('#filters #session').val();
                    financial_year = $('#filters #year').val();
                    role = $('#filters #role').val();
                    // Call your dashboard function with current values
                    dashboard(level, session, financial_year, role);
                    $('#script-fee-data-table').DataTable().ajax.reload();
                    $('#invitations-data-table').DataTable().ajax.reload();
                });


                $('form select#level').on('change', function() {
                    var formId = $(this).closest('form').attr('id');
                    getCenters(`${formId}`, $(`#${formId} #level`).val())

                });



                ///



                $('#export-invitations').on('click', function() {
                    $.ajax({
                        url: "{{ route('admin.invitations.exportCsv') }}",
                        method: 'GET',
                        data: {
                            financial_year: $('#filters #year').val(),
                            level: $('#filters #level').val(),
                            session: $('#filters #session').val(),
                            role: $('#filters #role').val()
                        },
                        success: function(res) {
                            console.log(res);
                            if (res.success && res.url) {
                                // Trigger file download
                                const link = document.createElement('a');
                                link.href = res.url;
                                link.download = '';
                                document.body.appendChild(link);
                                link.click();
                                document.body.removeChild(link);
                            } else {
                                console.log(res);
                                alert('Failed to generate CSV.');
                            }
                        },
                        error: function() {
                            alert('Something went wrong while generating CSV.');
                        }
                    });
                });



                // ID selector on Master Checkbox
                var checkedAll = "#select-all";
                var checkedItems = "[name='recipients[]']";
                $(document).on("change", checkedAll, function() {
                    $(checkedItems).prop("checked", $(this).prop("checked"));
                });
                $(document).on("click", checkedItems, function() {
                    let inputs = $(checkedItems).length;
                    let inputs_checked = $(checkedItems + ":checked").length;
                    if (inputs_checked <= 0) {
                        $(checkedAll).prop("checked", false);
                        $(checkedAll).prop("indeterminate", null);
                    } else if (inputs == inputs_checked) {
                        $(checkedAll).prop("checked", true);
                        $(checkedAll).prop("indeterminate", false);
                    } else {
                        $(checkedAll).prop("checked", true);
                        $(checkedAll).prop("indeterminate", true);
                    }
                });




                // Roles
                var table_role = $('#roles-data-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('admin.invitations.roles.index') }}",
                    columns: [{
                            data: 'id',
                            name: 'id'
                        },
                        {
                            data: 'name',
                            name: 'name'
                        },
                        {
                            data: 'description',
                            name: 'description'
                        },

                        {
                            data: 'fields_count',
                            name: 'fields_count',
                            searchable: false
                        },
                        {
                            data: 'designer',
                            name: 'designer',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        },

                    ]
                });
                $("#roles-data-table").css("width", "98.5%");
                //Add New Role
                initDynamicFields('#role-add-form', '.fields-table', '.form-preview');

                $('#new-role').on('click', function(event) {
                    event.preventDefault();
                    var addForm = $("#role-add-form");
                    var url = addForm.attr('action');
                    var formData = new FormData(addForm[0]); // ✅ Handles file + normal inputs
                    $.ajax({
                        type: "POST",
                        url: url,
                        data: formData,
                        processData: false, // ✅ Needed for FormData
                        contentType: false, // ✅ Needed for FormData
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(data) {
                            console.log(data)
                            if ($.isEmptyObject(data.errors)) {
                                $('#add-role-modal').modal('hide');
                                toastr.success(data.success);
                                $('#roles-data-table').DataTable().ajax.reload();

                                // ✅ Reset form after success
                                addForm[0].reset();
                            } else {
                                printErrorMsg('#role-add-form', data.errors);
                            }
                        },
                        error: function(xhr) {
                            toastr.error("Something went wrong. Please try again.");

                        }
                    });
                });


                //Edit Role
                $(document).on('click', '.edit-role', function(ev) {
                    ev.preventDefault();
                    var url = $(this).attr('href');
                    $.ajax({
                        type: "GET",
                        url: url,
                        success: function(data) {
                            var role = data.role;
                            var action = data.url;

                            // Set role basic info
                            $('#edit-name').val(role.name);
                            $('#edit-type').val(role.type);
                            $('#edit-description').val(role.description);

                            // Init dynamic fields only once
                            initDynamicFields('#role-edit-form', '.fields-table', '.form-preview');

                            // Always clear table + preview before filling
                            $('#role-edit-form .fields-table tbody').empty();
                            $('#role-edit-form .form-preview').empty();


                            // Load fields dynamically
                            role.fields.forEach(f => {
                                $('#role-edit-form .add-fieldbtn').trigger('click');
                                let row = $('#role-edit-form .fields-table tbody tr')
                                    .last();
                                // Hidden ID field
                                row.append(
                                    `<input type="hidden" class="field-id" name="fields[${row.index()}][id]" value="${f.id || ''}">`
                                );

                                // Positions
                                if (f.positions && Array.isArray(f.positions)) {
                                    f.positions.forEach((pos, posIndex) => {
                                        row.append(
                                            `<input type="hidden" name="fields[${row.index()}][positions][${posIndex}][id]" value="${pos.id || ''}">`
                                        );
                                    });
                                }
                                row.find('.field-label').val(f.label);
                                row.find('.field-name').val(f.name);
                                row.find('.field-type').val(f.type).trigger('change');
                                if (f.required) row.find('.field-required').prop('checked',
                                    true);



                                if (f.source) {
                                    row.find('.options-source').val(f.source).trigger(
                                        'change');
                                    if (f.key_column) row.find('.key-column').val(f
                                        .key_column);
                                    if (f.value_column) row.find('.value-column').val(f
                                        .value_column);
                                }
                                // Options
                                if (f.options) {
                                    let optionsArr = [];
                                    if (typeof f.options === 'object' && !Array.isArray(f
                                            .options)) {
                                        optionsArr = Object.entries(f.options).map(([key,
                                            value
                                        ]) => ({
                                            key,
                                            value
                                        }));
                                    } else if (Array.isArray(f.options)) {
                                        optionsArr = f.options.map(o => ({
                                            key: o,
                                            value: o
                                        }));
                                    }

                                    optionsArr.forEach(o => {
                                        row.find('.add-option').trigger('click');
                                        let lastOption = row.find('.option-value')
                                            .last();
                                        lastOption.val(o.value);
                                        if (f.source && f.source !== 'manual') {
                                            lastOption.attr('data-key', o.key);
                                        }
                                    });
                                }

                            });

                            // Set form action
                            $('#role-edit-form').attr('action', action);

                            // Update preview
                            updatePreview('#role-edit-form', '.fields-table', '.form-preview');

                            // Show modal
                            $('#edit-role-modal').modal('show');
                        },
                        error: function(data) {
                            console.log('Error:', data);
                        }
                    });
                });

                // Update Role
                $(document).on('click', '#update-role', function(ev) {
                    ev.preventDefault();
                    var editForm = $("#role-edit-form");
                    var url = editForm.attr('action');
                    var formData = new FormData(editForm[0]); // ✅ Handles file + normal inputs
                    $.ajax({
                        type: "POST",
                        url: url,
                        data: formData,
                        processData: false, // ✅ Needed for FormData
                        contentType: false, // ✅ Needed for FormData
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(data) {
                            console.log(data)
                            if ($.isEmptyObject(data.errors)) {
                                $('#edit-role-modal').modal('hide');
                                toastr.success(data.success);
                                $('#roles-data-table').DataTable().ajax.reload();

                                // ✅ Reset form after success
                                editForm[0].reset();
                            } else {
                                printErrorMsg('#role-edit-form', data.errors);
                            }
                        },
                        error: function(xhr) {

                            toastr.error("Something went wrong. Please try again.");
                            console.error(xhr.responseText);
                        }
                    });
                });

                //Delete Role
                $(document).on('click', '.delete-role', function(ev) {
                    ev.preventDefault();
                    var url = $(this).attr('href');

                    if (!confirm("Are you sure you want to delete this role?")) {
                        return; // Stop if user cancels
                    }

                    $.ajax({
                        type: "DELETE",
                        url: url,
                        success: function(data) {
                            // Refresh DataTable
                            toastr.success(data.success);
                            $('#roles-data-table').DataTable().ajax.reload();
                        },
                        error: function(data) {
                            console.log('Error:', data);
                        }
                    });
                });
                // Select Role
                $(document).on('change', '.invitation-role', function() {
                    let roleId = $(this).val();
                    let formId = $(this).closest("form").attr("id");
                    let container = $(`#${formId } #dynamic-fields`);
                    let level = $(`#${formId } #level`).val();
                    container.empty(); // clear previous fields
                    if (!roleId) return;
                    let url = "{{ route('admin.invitations.roles.fields', ['role' => 'ROLE_ID']) }}".replace(
                        'ROLE_ID', roleId);
                    container.html('<p>Loading fields...</p>');
                    $.ajax({
                        url: url,
                        data: {
                            level: level
                        },
                        async: false, // ⚠ synchronous request
                        type: 'GET',

                        success: function(fields) {
                            container.empty();

                            if (fields.length === 0) {
                                container.html('<p>No custom fields for this role.</p>');
                                return;
                            }

                            fields.forEach(function(field) {
                                let input = '';
                                let options = [];

                                // If options is an object (DB source), convert to array of {key, value}
                                if (field.options && typeof field.options === 'object') {
                                    options = Object.entries(field.options).map(([key,
                                        value
                                    ]) => ({
                                        key,
                                        value
                                    }));
                                }
                                // If options is array from manual
                                else if (field.options && Array.isArray(field.options)) {
                                    options = field.options.map(o => ({
                                        key: o,
                                        value: o
                                    }));
                                }

                                switch (field.type) {
                                    case 'number':
                                        input =
                                            `<input type="number" name="fields[${field.id}]" class="form-control" ${field.required ? 'required' : ''}>`;
                                        break;
                                    case 'text':
                                        input =
                                            `<input type="text" name="fields[${field.id}]" class="form-control" ${field.required ? 'required' : ''}>`;
                                        break;

                                    case 'textarea':
                                        input =
                                            `<textarea name="fields[${field.id}]" class="form-control" ${field.required ? 'required' : ''}></textarea>`;
                                        break;

                                    case 'select':
                                        let selectOptions = options.map(o =>
                                            `<option value="${o.key}">${o.value}-${o.key}</option>`
                                        ).join('');
                                        input = `<select name="fields[${field.id}]" class="form-control" ${field.required ? 'required' : ''}>
                                                 <option value="">Select</option>
                                            ${selectOptions}</select>`;
                                        break;

                                    case 'checkbox':
                                        let checkboxOptions = options.map(o =>
                                            `<label class="mr-2"><input type="checkbox" name="fields[${field.id}][]" value="${o.key}"> ${o.value}</label>`
                                        ).join(' ');
                                        input = `<div>${checkboxOptions}</div>`;
                                        break;

                                    case 'radio':
                                        let radioOptions = options.map(o =>
                                            `<label class="mr-2"><input type="radio" name="fields[${field.id}]" value="${o.key}"> ${o.value}</label>`
                                        ).join(' ');
                                        input = `<div>${radioOptions}</div>`;
                                        break;
                                }

                                container.append(`<div class="form-group">
                                    <label>${field.label}${field.required ? ' *' : ''}</label>
                                    ${input}
                                  </div>`);
                            });
                        },
                        error: function() {
                            container.html('<p>Error loading fields. Please try again.</p>');
                        }
                    });
                });

                // Copy Select Positions
                $(document).on('click', '.copy-position', function(ev) {
                    ev.preventDefault();
                    var url = $(this).attr('href');
                    var id = $(this).data('id');
                    $(`#source_role_id option`).show();
                    $(`#source_role_id option[value="${id}"]`).hide();
                    // Set form action
                    $('#copy-positions-form').attr('action', url);
                    // Show modal
                    $('#copy-positions-modal').modal('show');
                });

                //Save Copied Positions
                $('#save-copy-positions').on('click', function(ev) {
                    ev.preventDefault();
                    var copyForm = $("#copy-positions-form");
                    var url = copyForm.attr('action');
                    $.ajax({
                        url: url,
                        type: "POST",
                        data: copyForm.serialize(),
                        success: function(data) {

                            if ($.isEmptyObject(data.errors)) {
                                $('#script-fee-data-table').DataTable().ajax.reload();
                                $('#copy-positions-modal').modal('hide');
                                // ✅ Reset form after success
                                copyForm[0].reset();
                                toastr.success(data.success);
                            } else {
                                printErrorTabs('#copy-positions-form', data.errors);
                            }

                        },
                        error: function(xhr) {
                            alert(xhr.responseJSON?.error || 'Failed to copy positions.');
                        }
                    });
                });

                var table_fee = $('#script-fee-data-table').DataTable({
                    processing: true,
                    serverSide: true,
                    "lengthMenu": [
                        [20, 50, 100, 200, 400, -1],
                        [20, 50, 100, 200, 400, "All"]
                    ],
                    ajax: {
                        url: "{{ route('admin.invitations.script-fee.index') }}",
                        data: function(d) {
                            d.year = $("#filters #year").val();
                            d.session = $('#filters #session').val();
                            d.level = $('#filters #level').val()
                            d.financial_year = $('#filters #year').val();
                        }
                    },
                    columns: [{
                            data: 'component_code',
                            name: 'component_code'
                        },
                        {
                            data: 'subject_code',
                            name: 'subject_code'
                        },
                        {
                            data: 'component_no',
                            name: 'component_no'
                        },
                        {
                            data: 'subject.subject_name',
                            name: 'subject.subject_name'
                        },

                        {
                            data: 'session',
                            name: 'session'
                        },
                        {
                            data: 'financial_year',
                            name: 'financial_year'
                        },

                        {
                            data: 'script_fee',
                            name: 'script_fee'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        },

                    ]
                });
                $("#script-fee-data-table").css("width", "98.5%");
                //Add New Fee
                $('#new-fee').on('click', function(event) {
                    event.preventDefault();
                    var addForm = $("#script-fee-add-form");
                    var url = addForm.attr('action');
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: addForm.serialize(),
                        success: function(data) {

                            if ($.isEmptyObject(data.errors)) {
                                $('#script-fee-data-table').DataTable().ajax.reload();
                                $('#add-script-fee-modal').modal('hide');
                                // ✅ Reset form after success
                                addForm[0].reset();
                                toastr.success(data.success);
                            } else {
                                printErrorTabs('#script-fee-add-form', data.errors);
                            }
                        }
                    });
                });

                //Edit Fee
                $(document).on('click', '.edit-fee', function(ev) {
                    ev.preventDefault();
                    var url = $(this).attr('href');
                    $.ajax({
                        type: "GET",
                        url: url,
                        success: function(data) {

                            var fee = data.fee;
                            var action = data.url;
                            var form = '#script-fee-edit-form'


                            $(`${form} input, ${form} select`).each(
                                function(index) {
                                    var input = $(this);
                                    var name = input.attr('name');
                                    $(`${form} #${name}`).val(fee[name]);

                                }
                            );
                            // Set form action
                            $('#script-fee-edit-form').attr('action', action);

                            // Show modal
                            $('#edit-script-fee-modal').modal('show');
                        },
                        error: function(data) {
                            console.log('Error:', data);
                        }
                    });
                });
                //Add Update Fee
                $('#update-fee').on('click', function(event) {
                    event.preventDefault();
                    var editForm = $("#script-fee-edit-form");
                    var url = editForm.attr('action');
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: editForm.serialize(),
                        success: function(data) {
                            console.log(data);
                            if ($.isEmptyObject(data.errors)) {
                                $('#script-fee-data-table').DataTable().ajax.reload();
                                $('#edit-script-fee-modal').modal('hide');
                                // ✅ Reset form after success
                                editForm[0].reset();
                                toastr.success(data.success);
                            } else {
                                printErrorTabs('#script-fee-edit-form', data.errors);
                            }
                        }
                    });
                });

                //Delete Fee
                $(document).on('click', '.delete-fee', function(ev) {
                    ev.preventDefault();
                    var url = $(this).attr('href');

                    if (!confirm("Are you sure you want to delete this script fee?")) {
                        return; // Stop if user cancels
                    }

                    $.ajax({
                        type: "DELETE",
                        url: url,
                        success: function(data) {
                            // Refresh DataTable
                            if (data.success) {
                                toastr.success(data.success);
                                $('#script-fee-data-table').DataTable().ajax.reload();
                            } else {
                                toastr.error(data.error);
                            };
                        },
                        error: function(data) {
                            console.log('Error:', data);
                        }
                    });
                });

                // Recipient
                var table_recipients = $('#invitations-data-table').DataTable({
                    processing: true,
                    serverSide: true,
                    "lengthMenu": [
                        [20, 50, 100, 200, 400, -1],
                        [20, 50, 100, 200, 400, "All"]
                    ],
                    ajax: {
                        url: "{{ route('admin.invitations.index') }}",
                        data: function(d) {
                            d.session = $('#filters #session').val();
                            d.level = $('#filters #level').val()
                            d.role = $('#filters #role').val();
                            d.financial_year = $('#filters #year').val();
                        }
                    },
                    columns: [{
                            data: 'checkbox',
                            name: 'checkbox',
                            orderable: false,
                            searchable: false
                        },
                        {
                            "className": 'dt-control',
                            orderable: false,
                            data: null,
                            defaultContent: ''
                        },
                        {
                            data: 'first_name',
                            name: 'first_name'
                        },
                        {
                            data: 'last_name',
                            name: 'last_name'
                        },
                        {
                            data: 'email',
                            name: 'email'
                        },
                        {
                            data: 'phone_number',
                            name: 'phone_number'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        },

                    ]
                });
                $("#invitations-data-table").css("width", "98.5%");
                //Add New Recipient
                $('#new-recipient').on('click', function(event) {
                    event.preventDefault();
                    var addForm = $("#recipient-add-form");
                    var url = addForm.attr('action');
                    var $btn = $(this);
                    // Store original button text
                    var originalText = $btn.html();
                    // Disable button and show loader
                    $btn.prop('disabled', true).html(
                        '<i class="fas fa-spinner fa-spin"></i> importing...'
                    );
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: addForm.serialize(),
                        success: function(data) {
                            console.log(data);
                            if ($.isEmptyObject(data.errors)) {
                                $('#invitations-data-table').DataTable().ajax.reload();
                                $('#add-recipient-modal').modal('hide');
                                // ✅ Reset form after success
                                addForm[0].reset();
                                toastr.success(data.success);
                            } else {
                                printErrorTabs('#recipient-add-form', data.errors);
                            }
                        },
                        complete: function() {
                            // Restore original button text and re-enable
                            $btn.prop('disabled', false).html(originalText);
                        }
                    });
                });


                $('#template_role_id').on('change', function() {
                    let roleId = $(this).val();
                    if (roleId) {
                        $('#download-template').show()
                        let url = "{{ route('admin.invitations.downloadTemplate') }}" + "?role_id=" + roleId;
                        url = url.replace(':roleId', roleId);
                        $('#download-template').attr('href', url);
                    } else {
                        $('#download-template').hide()
                        $('#download-template').attr('href', '#');
                    }
                });

                //import-recipient
                $('#import-recipient').on('click', function(event) {
                    event.preventDefault();
                    let form = $("#csv-import-form")[0];
                    let formData = new FormData(form);
                    var url = $("#csv-import-form").attr('action');
                    $("#csv-errors").hide().find("ul").empty();

                    var $btn = $(this);
                    // Store original button text
                    var originalText = $btn.html();
                    // Disable button and show loader
                    $btn.prop('disabled', true).html(
                        '<i class="fas fa-spinner fa-spin"></i> importing...'
                    );
                    $.ajax({
                        url: url,
                        type: "POST",
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function(res) {
                            if (res.success) {
                                $('#invitations-data-table').DataTable().ajax.reload();
                                $('#import-recipient-modal').modal('hide');
                                // ✅ Reset form after success
                                toastr.success(data.success)
                                $("#csv_file").val("");
                            }
                        },
                        error: function(xhr) {
                            let response = xhr.responseJSON;

                            if (response && response.errors) {
                                $("#csv-errors").show();
                                $.each(response.errors, function(row, messages) {
                                    $("#csv-errors ul").append(
                                        `<li><strong>Row ${row}:</strong> ${messages.join(", ")}</li>`
                                    );
                                });
                            } else if (response && response.error) {
                                $("#csv-errors").show().find("ul").append(
                                    `<li>${response.error}</li>`
                                );
                            }
                        },
                        complete: function() {
                            // Restore original button text and re-enable
                            $btn.prop('disabled', false).html(originalText);
                        }
                    });
                });

                //Edit Recipient
                $(document).on('click', '.edit-recipient', function(ev) {
                    ev.preventDefault();
                    var url = $(this).attr('href');

                    $.ajax({
                        type: "GET",
                        url: url,
                        success: function(data) {

                            var recipient = data.recipient;
                            var action = data.url;
                            var form = '#recipient-form-edit'


                            $(`${form} input, ${form} select`).each(
                                function(index) {
                                    var input = $(this);
                                    console.log('Type: ' + input.attr('type') + 'Name: ' + input
                                        .attr(
                                            'name') +
                                        'Value: ' + input.val());
                                    var name = input.attr('name');

                                    $(`${form} #${name}`).val(recipient[name]);


                                }
                            );
                            // Set form action
                            $('#recipient-form-edit').attr('action', action);

                            // Show modal
                            $('#edit-recipient-modal').modal('show');
                        },
                        error: function(data) {
                            console.log('Error:', data);
                        }
                    });
                });
                //Update Recipient
                $(document).on('click', '#update-recipient', function(ev) {

                    ev.preventDefault();
                    var editForm = $("#recipient-form-edit");
                    var url = editForm.attr('action');

                    $.ajax({
                        type: "POST",
                        data: editForm.serialize(),
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        url: url,
                        success: function(data) {
                            console.log(data);
                            if ($.isEmptyObject(data.errors)) {
                                $('#edit-recipient-modal').modal('hide');
                                toastr.success(data.success);
                                $('#invitations-data-table').DataTable().ajax.reload();
                                // ✅ Reset form after success
                                editForm[0].reset();
                            } else {
                                printErrorMsg('#recipient-form-edit', data.errors);
                            }


                        }


                    });
                });
                //Delete Recipient
                $(document).on('click', '.delete-recipient', function(ev) {
                    ev.preventDefault();
                    var url = $(this).attr('href');

                    if (!confirm("Are you sure you want to delete this recipient?")) {
                        return; // Stop if user cancels
                    }

                    $.ajax({
                        type: "DELETE",
                        url: url,
                        success: function(data) {
                            // Refresh DataTable
                            if (data.success) {
                                toastr.success(data.success);
                                $('#invitations-data-table').DataTable().ajax.reload()
                            } else {
                                toastr.error(data.error);


                            };
                        },
                        error: function(data) {
                            console.log('Error:', data);
                        }
                    });
                });
                // Edit Invitations
                $(document).on('click', '.edit-invitation', function(ev) {
                    ev.preventDefault();
                    var url = $(this).attr('href');
                    var form = '#invitation-form-edit';
                    $.ajax({
                        type: "GET",
                        url: url,
                        success: function(data) {
                            var invitation = data.invitation;
                            console.log(invitation);

                            // Reset the form first
                            $(form)[0].reset();

                            // Populate standard fields
                            $(`${form} input, ${form} select, ${form} textarea`).each(function() {
                                var input = $(this);
                                var name = input.attr('name');
                                if (!name) return;

                                if (input.is(':checkbox')) {
                                    input.prop('checked', !!invitation[name]);
                                } else if (input.is(':radio')) {
                                    input.prop('checked', input.val() == invitation[name]);
                                } else if (invitation[name] !== undefined) {
                                    $(`${form} [name="${name}"]`).val(invitation[name]);
                                }
                            });
                            // Specific role select (if you have one)
                            $('#invitation-form-edit .invitation-role')
                                .val(invitation.role_id)
                                .trigger("change");


                            // Populate dynamic fields (invitationFields)
                            if (invitation.invitation_fields) {
                                invitation.invitation_fields.forEach(function(field) {

                                    var selector =
                                        `${form} [name="fields[${field.field_id}]"]`;
                                    var input = $(selector);
                                    console.log(input)

                                    if (input.length) {
                                        if (input.is(':checkbox')) {
                                            input.prop('checked', !!field.field_value);
                                        } else if (input.is(':radio')) {
                                            input.prop('checked', input.val() == field
                                                .field_value);
                                        } else {
                                            input.val(field.field_value);
                                        }
                                    }
                                });
                            }


                            // Set form action
                            $(form).attr('action', data.url);

                            // Show modal
                            $('#edit-invitation-modal').modal('show');
                        },
                        error: function(xhr) {
                            console.error('Error:', xhr.responseText);
                            alert('Failed to load invitation details.');
                        }
                    });
                });

                // Update Invitation
                $(document).on('click', '#update-invitation', function(ev) {
                    ev.preventDefault();
                    var editForm = $("#invitation-form-edit");
                    var url = editForm.attr('action');
                    $.ajax({
                        type: "POST",
                        data: editForm.serialize(),
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        url: url,
                        success: function(data) {
                            console.log(data);
                            if ($.isEmptyObject(data.errors)) {
                                $('#edit-invitation-modal').modal('hide');
                                toastr.success(data.success);
                                $('#invitations-data-table').DataTable().ajax.reload();
                                // ✅ Reset form after success
                                editForm[0].reset();
                            } else {
                                printErrorMsg('#invitation-form-edit', data.errors);
                            }


                        }


                    });
                });

                //Delete Invitation
                $(document).on('click', '.delete-invitation', function(ev) {
                    ev.preventDefault();
                    var url = $(this).attr('href');
                    if (!confirm("Are you sure you want to delete this invitation?")) {
                        return; // Stop if user cancels
                    }

                    $.ajax({
                        type: "DELETE",
                        url: url,
                        success: function(data) {
                            // Refresh DataTable
                            if (data.success) {
                                toastr.success(data.success);
                                $('#invitations-data-table').DataTable().ajax.reload()
                            } else {
                                toastr.error(data.error);


                            };
                        },
                        error: function(data) {
                            console.log('Error:', data);
                        }
                    });
                });
                //Resend

                $(document).on('click', '.resend-invitation', function(ev) {
                    ev.preventDefault();



                    var url = $(this).attr('href');
                    var $btn = $(this);

                    // Store original button text
                    var originalText = $btn.html();


                    // Disable button and show loader
                    $btn.prop('disabled', true).html(
                        '<i class="fas fa-spinner fa-spin"></i> Sending...'
                    );

                    $.ajax({
                        type: "POST",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        url: url,
                        success: function(data) {
                            toastr.success(data.success || 'Invitation resent successfully.');
                        },
                        error: function(xhr) {
                            toastr.error(xhr.responseJSON?.error || 'Something went wrong.');
                        },
                        complete: function() {
                            // Restore original button text and re-enable
                            $btn.prop('disabled', false).html(originalText);
                        }
                    });
                });



                // Resend Invitation
                $(document).on('click', '#send-invitations', function(ev) {
                    ev.preventDefault();
                    level = $('#filters #level').val();
                    session = $('#filters #session').val();
                    financial_year = $('#filters #year').val();
                    if (!level) {
                        toastr.error("Level is required.");
                        return;
                    }

                    if (!session) {
                        toastr.error("Session is required.");
                        return;
                    }

                    if (!financial_year) {
                        toastr.error("Financial year is required.");
                        return;
                    }
                    if (confirm("Are You sure send invitation to selected recipients?")) {
                        var recipients = [];
                        $("[name='recipients[]']:checked").each(function(i) {
                            recipients[i] = $(this).val();
                        });
                        if (recipients.length === 0) {
                            toastr.error("Please select atleast one recipients");
                        } else {


                            var $btn = $(this);

                            // Store original button text
                            var originalText = $btn.html();

                            // Disable button and show loader
                            $btn.prop('disabled', true).html(
                                '<i class="fas fa-spinner fa-spin"></i> Sending...'
                            );


                            $.ajax({
                                type: "POST",
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                url: "{{ route('admin.invitations.bulk-resend') }}",
                                data: {
                                    level: level,
                                    session: session,
                                    financial_year: financial_year,
                                    recipients: recipients,
                                },
                                success: function(data) {
                                    toastr.success(data.success ||
                                        'Invitations resent successfully.');
                                },
                                error: function(xhr) {
                                    console.log(xhr)
                                    toastr.error(xhr.responseJSON?.error ||
                                        'Something went wrong.');
                                },
                                complete: function() {
                                    // Restore original button text and re-enable
                                    $btn.prop('disabled', false).html(originalText);
                                }
                            });


                        }
                    }

                });








                function getCenters(formParent, level) {
                    var data = {
                        level: level,
                        center_filter: 1
                    }
                    $.ajax({
                        type: "GET",
                        url: "{{ route('admin.invitations.index') }}",
                        data: data,
                        success: function(data) {
                            var centers = data.centers;

                            $(`#${formParent}  #center_no`).html(`<option value="">
                                   Select school
                              </option>`);

                            $.each(centers, function(center_no, center_name) {
                                $(`#${formParent} #center_no`).append(
                                    $('<option>')
                                    .val(center_no) // use center_no as value
                                    .text(center_no + ' - ' + center_name) // display nicely
                                );
                            });






                        },
                        error: function(data) {


                            console.log('Error:', data);
                        }
                    });

                }

                function dashboard(level, session, financial_year, role) {
                    var data = {
                        level: level,
                        session: session,
                        financial_year: financial_year,
                        role: role,
                        analysis: 1
                    }
                    $.ajax({
                        type: "GET",
                        url: "{{ route('admin.invitations.index') }}",
                        data: data,
                        success: function(data) {
                            $('.completed-invitations').html(data.completedInvitations);
                            $('.total-recipients').html(data.totalRecipients);
                            $('.total-invitations').html(data.totalInvitations);
                            $('.completed-invitations').html(data.completedInvitations);
                            $('.sent-invitations').html(data.sentInvitations);

                            // byRole
                            // :
                            // {Marker: 106}
                            // completedInvitations
                            // :
                            // 0
                            // monthlyInvitations
                            // :
                            // 2025-09
                            // :
                            // 106
                            // [[Prototype]]
                            // :
                            // Object
                            // totalInvitations
                            // :
                            // 106
                            // totalRecipients
                            // :
                            // 106
                            console.log(data)

                        },
                        error: function(data) {
                            console.log('Error:', data);
                        }
                    });
                }


                // Add event listener for opening/closing details
                $('#invitations-data-table tbody').on('click', 'td.dt-control', function() {
                    let tr = $(this).closest('tr');
                    let row = table_recipients.row(tr);

                    if (row.child.isShown()) {
                        row.child.hide();
                        tr.removeClass('shown');
                    } else {
                        row.child(format(row.data())).show();
                        tr.addClass('shown');
                    }
                });

                // Ensure initDynamicFields only runs once
                function initDynamicFields(parentForm, tableSelector, previewSelector) {
                    if ($(parentForm).data('dynamic-initialized')) {
                        return; // already bound, skip
                    }
                    $(parentForm).data('dynamic-initialized', true);

                    let localFieldIndex = fieldIndex;

                    // Add Field
                    $(document).on('click', `${parentForm} .add-fieldbtn`, function() {
                        let row = `<tr data-row="${localFieldIndex}">
                        <td><input type="text" class="form-control field-label" name="fields[${localFieldIndex}][label]" required></td>
                        <td><input type="text" class="form-control field-name" name="fields[${localFieldIndex}][name]" required></td>
                        <td>
                            <select name="fields[${localFieldIndex}][type]" class="form-control field-type" data-index="${localFieldIndex}" required>
                                <option value="text">Text</option>
                                <option value="number">Number</option>
                                <option value="date">Date</option>
                                <option value="select">Select</option>
                                <option value="checkbox">Checkbox</option>
                                <option value="radio">Radio</option>
                                <option value="file">File</option>
                            </select>
                        </td>
                        <td>
                            <div class="options-box" data-index="${localFieldIndex}" style="display:none;">
                                <label>
                                    <select name="fields[${localFieldIndex}][source]" class="options-source form-control" data-index="${localFieldIndex}">
                                        <option value="manual">Manual Options</option>
                                        <option value="subjects">Subjects Table</option>
                                        <option value="centers">Centers Table</option>
                                    </select>
                                </label>
                                <div class="manual-options" data-index="${localFieldIndex}">
                                    <button type="button" class="btn btn-xs btn-info add-option" data-index="${localFieldIndex}">
                                        <i class="glyphicon glyphicon-plus"></i> Add Option
                                    </button>
                                    <ul class="options-list list-unstyled" data-index="${localFieldIndex}"></ul>
                                </div>

                                <div class="db-options" data-index="${localFieldIndex}" style="display:none; margin-top:5px;">
                                    <input type="text" class="form-control key-column" name="fields[${localFieldIndex}][key_column]" placeholder="Key column (e.g. id)">
                                    <input type="text" class="form-control value-column" name="fields[${localFieldIndex}][value_column]" placeholder="Value column (e.g. name)">
                                </div>
                            </div>
                        </td>
                        <td class="text-center">
                            <input type="checkbox" class="field-required" name="fields[${localFieldIndex}][required]" value="1">
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger btn-xs remove-field">
                                <i class="glyphicon glyphicon-remove"></i> Remove
                            </button>
                        </td>
                    </tr>`;

                        $(`${parentForm} ${tableSelector} tbody`).append(row);
                        localFieldIndex++;
                        fieldIndex = localFieldIndex; // update global index
                        updatePreview(parentForm, tableSelector, previewSelector);
                    });

                    // Delegate field change/input/remove events
                    $(document).on('change input', tableSelector + ' .field-label, ' + tableSelector +
                        ' .field-name, ' + tableSelector + ' .field-required, ' + tableSelector +
                        ' .option-value, ' + tableSelector + ' .field-type',
                        function() {
                            updatePreview(parentForm, tableSelector, previewSelector);
                        });

                    $(document).on('click', tableSelector + ' .remove-field', function() {
                        $(this).closest('tr').remove();
                        updatePreview(parentForm, tableSelector, previewSelector);
                    });

                    $(document).on('change', tableSelector + ' .field-type', function() {
                        let index = $(this).data('index');
                        let type = $(this).val();
                        if (type === 'select' || type === 'checkbox' || type === 'radio') {
                            $(`${tableSelector} .options-box[data-index="${index}"]`).show();
                        } else {
                            $(`${tableSelector} .options-box[data-index="${index}"]`).hide();
                        }
                        updatePreview(parentForm, tableSelector, previewSelector);
                    });

                    $(document).on('change', tableSelector + ' .options-source', function() {
                        let index = $(this).data('index');
                        if ($(this).val() === 'manual') {
                            $(`${tableSelector} .manual-options[data-index="${index}"]`).show();
                            $(`${tableSelector} .db-options[data-index="${index}"]`).hide();
                        } else {
                            $(`${tableSelector} .manual-options[data-index="${index}"]`).hide();
                            $(`${tableSelector} .db-options[data-index="${index}"]`).show();
                            $(`${tableSelector} .options-list[data-index="${index}"]`).empty();
                        }
                        updatePreview(parentForm, tableSelector, previewSelector);
                    });

                    $(document).on('click', tableSelector + ' .add-option', function() {
                        let index = $(this).data('index');
                        let optionInput = `<li class="m-t-xs">
                                                <div class="input-group input-group-sm">
                                                    <input type="text" class="form-control option-value" name="fields[${index}][options][]" placeholder="Option value" required data-index="${index}">
                                                    <span class="input-group-btn">
                                                        <button type="button" class="btn btn-danger remove-option">
                                                            <i class="glyphicon glyphicon-remove"></i>
                                                        </button>
                                                    </span>
                                                </div>
                                            </li>`;
                        $(`${tableSelector} .options-list[data-index="${index}"]`).append(optionInput);
                        updatePreview(parentForm, tableSelector, previewSelector);
                    });

                    $(document).on('click', tableSelector + ' .remove-option', function() {
                        $(this).closest('li').remove();
                        updatePreview(parentForm, tableSelector, previewSelector);
                    });
                }




                // Generic preview function
                function updatePreview(parentForm, tableSelector, previewSelector) {
                    let previewBox = $(`${parentForm} ${previewSelector}`);

                    let rows = $(`${parentForm} ${tableSelector} tbody tr`);
                    if (rows.length === 0) {
                        previewBox.html('<em class="text-muted">Preview will appear here as you add fields</em>');
                        return;
                    }

                    let html = '';
                    rows.each(function() {
                        let label = $(this).find('.field-label').val() || 'Label';
                        let type = $(this).find('.field-type').val();
                        let required = $(this).find('.field-required').is(':checked') ? 'required' : '';
                        let index = $(this).data('row');

                        if (type === 'text' || type === 'number' || type === 'date' || type === 'file') {
                            html += `<div class="form-group">
                        <label>${label}${required?' *':''}</label>
                        <input type="${type}" class="form-control" ${required}>
                     </div>`;
                        } else if (type === 'select') {
                            let options = $(this).find('.option-value').map(function() {
                                return `<option>${$(this).val()}</option>`;
                            }).get().join('');
                            html += `<div class="form-group">
                        <label>${label}${required?' *':''}</label>
                        <select class="form-control" ${required}>${options}</select>
                     </div>`;
                        } else if (type === 'checkbox') {
                            let options = $(this).find('.option-value').map(function() {
                                return `<label class="checkbox-inline"><input type="checkbox" ${required}> ${$(this).val()}</label>`;
                            }).get().join(' ');
                            html +=
                                `<div class="form-group"><label>${label}${required?' *':''}</label><br>${options}</div>`;
                        } else if (type === 'radio') {
                            let options = $(this).find('.option-value').map(function() {
                                return `<label class="radio-inline"><input type="radio" name="preview_${index}" ${required}> ${$(this).val()}</label>`;
                            }).get().join(' ');
                            html +=
                                `<div class="form-group"><label>${label}${required?' *':''}</label><br>${options}</div>`;
                        }
                    });

                    previewBox.html(html);
                }

                function format(d) {

                    let html = '';

                    // Custom Fields
                    if (d.recipient_fields && d.recipient_fields.length > 0) {
                        html += '<h5>Custom Fields</h5>';
                        html += '<table class="table table-sm">';
                        d.recipient_fields.forEach(f => {
                            html +=
                                `<tr><td><strong>${f.field_key}</strong></td><td>${f.field_value ?? ''}</td></tr>`;
                        });
                        html += '</table>';
                    }
                    // Now invitationsArray is a normal array
                    let invitations = JSON.parse(d.invitations); // returns array of objects
                    // Invitations
                    if (invitations && invitations.length > 0) {
                        html += '<h5>Invitations</h5>';
                        html += '<table class="table table-sm">';
                        html +=
                            '<thead><tr><th>Center No</th><th>Session</th><th>Year</th><th>Role</th><th>Status</th><th>Sent At</th><th>Responded At</th><th>Action</th></tr></thead><tbody>';

                        invitations.forEach(inv => {
                            html += `<tr>
                                        <td>${inv.center_no}</td>
                                        <td>${inv.session}</td>
                                        <td>${inv.financial_year}</td>
                                        <td>${inv.role }</td>
                                        <td>${inv.status}</td>
                                        <td>${inv.sent_at ?? ''}</td>
                                        <td>${inv.responded_at ?? ''}</td>
                                        <td>${inv.action}</td>
                                    </tr>`;
                        });

                        html += '</tbody></table>';
                    } else {
                        html += '<em>No invitations sent</em>';
                    }

                    return html;
                }




                /****  Print errors*******/
                function printErrorMsg(parent, msg) {
                    // Clear old errors
                    $(`${parent} .help-block`).remove();
                    $(`${parent} .has-error`).removeClass('has-error');

                    // Loop through Laravel errors
                    $.each(msg, function(key, errors) {
                        errors.forEach(function(value) {
                            // Convert dot-notation to bracket notation for dynamic table inputs
                            const fieldName = key.split('.') // ["fields","0","label"]
                                .map((part, i) => i === 0 ? part : `[${part}]`)
                                .join(''); // "fields[0][label]"
                            const selector =
                                `${parent} [name="${key}"], ${parent} [name="${fieldName}"]`;

                            // Wait until the element exists in the DOM
                            const $selector = $(selector);
                            if ($selector.length === 0) {
                                console.warn(
                                    `Field ${key} not found in DOM yet. Skipping error display.`);
                                return;
                            }

                            // Highlight .form-group (normal input) or <td> (table cell)
                            const $formGroup = $selector.closest('.form-group');
                            const $td = $selector.closest('td');
                            const $tr = $selector.closest('tr'); // optional: highlight whole row

                            // Apply error class
                            $formGroup.add($td).add($tr).addClass('has-error');

                            // Insert error message
                            if ($selector.attr('type') === 'radio' || $selector.attr('type') ===
                                'checkbox') {
                                // Append to last element’s parent for groups
                                $selector.last().parent().append(
                                    `<span class='help-block'>${value}</span>`);
                            } else {
                                // Insert after the input/field
                                $(`<span class='help-block'>${value}</span>`).insertAfter($selector);
                            }
                        });
                    });
                }

                function printErrorTabs(parent, msg) {
                    // Clear old errors
                    $(`${parent} .help-block`).remove();
                    $(`${parent} .has-error`).removeClass('has-error');
                    $(`${parent} .nav li a`).removeClass('text-danger'); // reset tab labels

                    let firstErrorTab = null;

                    // Loop through Laravel errors
                    $.each(msg, function(key, errors) {
                        errors.forEach(function(value) {
                            // Convert dot-notation to bracket notation for dynamic table inputs
                            const fieldName = key.split('.') // ["fields","0","label"]
                                .map((part, i) => i === 0 ? part : `[${part}]`)
                                .join(''); // "fields[0][label]"
                            const selector =
                                `${parent} [name="${key}"], ${parent} [name="${fieldName}"]`;

                            // Wait until the element exists in the DOM
                            const $selector = $(selector);
                            if ($selector.length === 0) {
                                console.warn(
                                    `Field ${key} not found in DOM yet. Skipping error display.`);
                                return;
                            }

                            // Highlight .form-group (normal input) or <td> (table cell)
                            const $formGroup = $selector.closest('.form-group');
                            const $td = $selector.closest('td');
                            const $tr = $selector.closest('tr'); // optional: highlight whole row

                            // Apply error class
                            $formGroup.add($td).add($tr).addClass('has-error');

                            // Insert error message
                            if ($selector.attr('type') === 'radio' || $selector.attr('type') ===
                                'checkbox') {
                                $selector.last().parent().append(
                                    `<span class='help-block'>${value}</span>`);
                            } else {
                                $(`<span class='help-block'>${value}</span>`).insertAfter($selector);
                            }

                            // ---- NEW: Highlight tab ----
                            const $tabPane = $selector.closest('.tab-pane');
                            if ($tabPane.length) {
                                const tabId = $tabPane.attr('id');
                                const $tabLink = $(`${parent} .nav a[href="#${tabId}"]`);
                                $tabLink.addClass('text-danger'); // red tab label

                                // Store first tab with error
                                if (!firstErrorTab) {
                                    firstErrorTab = $tabLink;
                                }
                            }
                        });
                    });

                    // Auto-switch to first tab with error
                    if (firstErrorTab) {
                        firstErrorTab.tab('show');
                    }
                }

                /****  Print errors End*******/
            });
        </script>
    @endpush
@endsection
