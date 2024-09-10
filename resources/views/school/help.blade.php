@extends('layouts.school')

@section('content')
    <div id="page-wrapper">

        <div class="header">
            <h1 class="page-header">
                How to
            </h1>

            <ol class="breadcrumb">

                <li><a href="javascript:void();">Home</a></li>
                <li class="active"><a href="javascript:void();">How to</a></li>
            </ol>

        </div>

        <div id="page-inner" class="reports">

            <div class="row">
                <div class="col-md-12">
                    <!-- Panel -->
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Guide and Help
                        </div>
                        <div class="panel-body help-panel">
                            <p class="guide">
                                For a comprehensive guide on how to use this module, please download the guide
                                <a href="{{ asset('school/assets/download/SchoolCandidatesRegistrationGuide.pdf')}}" target="_blank">here...</a>
                            </p>
                            <div class="note">
                                <p>
                                    Note: This system will only register candidates if the uploaded csv containing
                                    the list of registered candidates is structured in a similar manner as the
                                    sample provided in the Candidates Registration section of the system.
                                </p>
                            </div>
                            <div class="important-guide">
                                <h5>How to add a single candidate</h5>
                                <ul>
                                    <li>
                                        Click on Amend Candidates and then click on Add Candidate.
                                    </li>
                                    <li>
                                        Enter the candidate number of the candidate that is being added
                                        (The information associated with that candidate will be shown).
                                    </li>
                                    <li>
                                        Confirm the information shown, with the candidate
                                        (to be certain that, that information belongs to the candidate).
                                    </li>
                                    <li>
                                        Select Type (this is the candidate type).
                                    </li>
                                    <li>
                                        Select Sponsor (this is the sponsor that is going to pay fees for the candidate).
                                    </li>
                                    <li>
                                        Select subjects that the candidate is going to write.
                                    </li>
                                    <li>
                                        Then click Add
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <!--End Panel -->
                </div>

                <div class="col-md-12">
                    <!-- Panel -->
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Errors
                        </div>
                        <div class="panel-body help-panel">
                            <p class="guide">
                                For a comprehensive interpretatiton of errors and what each error means, please download the
                                error guide
                                <a href="{{ asset('school/assets/download/schoolRegistrationModuleErrorGuide.pdf')}}" target="_blank">here...</a>
                            </p>
                            <div class="note">
                                <p>
                                    Note: All errors and warnings associated with the registration csv
                                    include a row entry that informs the user of where the
                                    problem was encountered in the csv.
                                </p>
                            </div>
                            <div class="important-guide">
                                <div class="container-fluid">
                                    <div class="row mb-4">
                                        <div class="col-lg-12 col-md-12">
                                            <div class="error-item">
                                                <h5>Sponsor must have a maximum/minimum characters of …</h5>
                                                <ul>
                                                    <li>
                                                        Check the sponsor entry at the row mentioned so as to confirm
                                                        whether the sponsor entry is correct.
                                                    </li>
                                                    <li>
                                                        If the sponsor entry is correct, check whether the number of subject
                                                        entries in that row is
                                                        equal to the number provided in the ‘subject count’ column on that
                                                        particular row.
                                                    </li>
                                                </ul>
                                            </div>

                                            <div class="error-item mt-3">
                                                <h5>Candidate number does not exist</h5>
                                                <ul>
                                                    <li>
                                                        The candidate number provided is not an existing candidate number
                                                        therefore
                                                        re-confirm with the candidate so as to get the correct candidate
                                                        number.
                                                    </li>
                                                </ul>
                                            </div>

                                            <div class="error-item mt-3">
                                                <h5>Invalid sponsor</h5>
                                                <ul>
                                                    <li>
                                                        Check the sponsor entry at the row mentioned so as to confirm
                                                        whether the sponsor entry is correct.
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>

                                        <div class="col-lg-12 col-md-12 error-col">
                                            <div class="errors-container">
                                                <div class="error-item">
                                                    <h5>Candidate’s surname does not match our records?</h5>
                                                    <ul>
                                                        <li>
                                                            To determine the cause of this error, click Amend Candidates and
                                                            then click Add Candidate.
                                                        </li>
                                                        <li>
                                                            Enter Candidate number of the candidate
                                                            (the system will automatically show information relating to the
                                                            candidate number).
                                                        </li>
                                                        <li>
                                                            Compare the information provided by the candidate with the
                                                            candidate’s information shown by the system.
                                                            If the candidate confirms the information as theirs, please fill
                                                            in the form to add the candidate.
                                                        </li>
                                                    </ul>
                                                    <p class="error-item-note">
                                                        Note: With regards to surnames that include an apostrophe (‘)
                                                        such as ‘Mamathe, please add the particular candidate in amend
                                                        candidate.
                                                    </p>
                                                </div>
                                                <div class="error-item mt-3">
                                                    <h5>Invalid email format</h5>
                                                    <ul>
                                                        <li>
                                                            Check the email provided so as to confirm whether it’s the
                                                            correct entry.
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <!--End Panel -->
                </div>
            </div>

        </div>
        <!-- /. PAGE INNER  -->

    </div>
    <!-- /. PAGE WRAPPER  -->
@endsection

@section('script')

@endsection
