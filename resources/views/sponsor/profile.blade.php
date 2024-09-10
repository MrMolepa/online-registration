@extends('layouts.candidate')

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Profile</h4>
                        <form class="forms-sample">
                            <div class="form-group">
                                <label for="national_id">National ID</label>
                                <input type="text" class="form-control" readonly value="{{ $candidate->national_id }}"
                                    id="national_id" placeholder="Username">
                            </div>
                            <div class="form-group">
                                <label for="candidate_no">Candidite Number</label>
                                <input type="text" class="form-control" readonly value="{{ $candidate->candidate_no }}"
                                    id="candidate_no" placeholder="Username">
                            </div>
                            <div class="form-group">
                                <label for="exampleInputEmail1">Candidate Surname</label>
                                <input type="text" class="form-control" readonly
                                    value="{{ $candidate->candidate_surname }}" id="exampleInputEmail1" placeholder="Email">
                            </div>
                            <div class="form-group">
                                <label for="candidate_other_name">Candidate other name</label>
                                <input type="text" class="form-control" readonly
                                    value="{{ $candidate->candidate_other_name }}" id="candidate_other_name"
                                    placeholder="Candidate other name">
                            </div>
                            <div class="form-group">
                                <label for="date_of_birth">Date of birth</label>
                                <input type="text" class="form-control" readonly value="{{ $candidate->date_of_birth }}"
                                    id="date_of_birth" placeholder="Date of birth">
                            </div>
                            <div class="form-group">
                                <label>Gender</label>
                                <div class="form-check">
                                    <label class="form-check-label">
                                        <input type="radio" class="form-check-input" {{ ($candidate->gender=="M")? "checked" : "" }} name="gender" id="gender"
                                            value="M">
                                        Male
                                        <i class="input-helper"></i></label>
                                </div>
                                <div class="form-check">
                                    <label class="form-check-label">
                                        <input type="radio" class="form-check-input"  {{ ($candidate->gender=="F")? "checked" : "" }} name="gender" id="gender"
                                            value="F">Female
                                        <i class="input-helper"></i></label>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary me-2">Submit</button>

                        </form>

                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
