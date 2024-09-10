@extends('layouts.candidate')

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-8 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Personal Info</h4>
                        <form class="forms-sample" id="candidate-information" method="POST"
                            action="{{ route('candidate.profile.update') }}">
                            <div class="form-card">
                                @csrf
                                <h3 class="headline">Personal</h3>
                                <div class="row">
                                    <input type="hidden" name="session" value="{{ $candidate->session }}">
                                    <input type="hidden" name="financial_year" value="{{ $candidate->financial_year }}">
                                    <div class="form-group col-md-6">
                                        <label for="national_id">National ID</label>
                                        <input type="text" class="form-control" readonly
                                            value="{{ $candidate->national_id }}" id="national_id" placeholder="Username">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="candidate_no">Candidite Number</label>
                                        <input type="text" class="form-control" readonly
                                            value="{{ $candidate->candidate_no }}" id="candidate_no" placeholder="Username">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="candidate_surname">Candidate Surname</label>
                                        <input type="text" class="form-control" readonly
                                            value="{{ $candidate->candidate_surname }}" id="candidate_surname"
                                            placeholder="Surname">
                                    </div>
                                    <div class="form-group col-md-6 ">
                                        <label for="candidate_other_name">Candidate other name</label>
                                        <input type="text" class="form-control" readonly
                                            value="{{ $candidate->candidate_other_name }}" id="candidate_other_name"
                                            placeholder="Candidate other name">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="date_of_birth">Date of birth</label>
                                        <input type="text" class="form-control" readonly
                                            value="{{ $candidate->date_of_birth }}" id="date_of_birth"
                                            placeholder="Date of birth">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Gender</label>
                                        <input type="text" class="form-control" readonly
                                            value="{{ $candidate->gender == 'M' ? 'Male' : 'Female' }}" id="date_of_birth"
                                            placeholder="Gender">


                                    </div>

                                </div>
                                <input type="hidden" name="national_id" value="{{ $candidate->national_id }}">
                                <input type="hidden" name="candidate_no" value="{{ $candidate->candidate_no }}">
                                <div class="form-group col-md-6">
                                    <label for="candidate_email">Email</label>
                                    <input type="text" class="form-control  form-control-sm " id="candidate_email"
                                        name="candidate_email" placeholder="Email Address">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="cadidate_phone">Phone
                                        Number</label>
                                    <input type="text" class="form-control form-control-sm" id="candidate_phone_number"
                                        name="candidate_phone_number" placeholder="Phone Number">
                                </div>
                                <div class="form-group col-12">
                                    <label for="special_need">Special
                                        needs</label>
                                    <select name="special_need" class="form-control form-control-sm" id="special_need">
                                        <option value="">
                                            Please select special
                                            need(s)</option>
                                        @foreach ($specialNeeds as $specialNeed)
                                            <option value="{{ $specialNeed->id }}">
                                                {{ $specialNeed->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <h3 class="headline">Address</h3>
                                <div class="row">
                                    <div class="form-group col-6">
                                        <label for="candidate_postal_address">Postal
                                            Address </label>
                                        <input type="text" class="form-control form-control-sm"
                                            id="candidate_postal_address" name="candidate_postal_address"
                                            placeholder="P.O.Box 2398">

                                    </div>
                                    <div class="form-group col-6">
                                        <label for="candidate_physical_address">Physical
                                            Address</label>
                                        <input type="text" class="form-control form-control-sm "
                                            id="candidate_physical_address" name="candidate_physical_address"
                                            placeholder="Qoaling">
                                    </div>

                                    <div class="form-group col-6">
                                        <label for="candidate_village">Village</label>
                                        <input type="text" class="form-control form-control-sm" id="candidate_village"
                                            name="candidate_village" placeholder="Ha Seoli">
                                    </div>
                                    <div class="form-group col-6">
                                        <label for="candidate_district">District</label>
                                        <select class="form-control form-control-sm" name="candidate_district"
                                            id="candidate_district">
                                            <option value="">
                                                Please Select
                                                District</option>
                                            @foreach ($districts as $district)
                                                <option value="{{ $district->district }}">
                                                    {{ $district->district }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" id="save-records"
                                class=" btn btn-block   btn-primary me-2">Save</button>
                        </form>

                    </div>
                </div>
            </div>



        </div>
    </div>

    @push('scripts')
        <script>
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
            /*****  Show Candidate  *******/
            showCandidateInformation();

            function showCandidateInformation() {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                var i = 0;
                $.ajax({
                    url: "{{ route('candidate.profile.show') }}",
                    method: "GET",
                    success: function(data) {
                        console.log(data);

                        var parent = "#msform";
                        var candidate = data.candidate === null ? {} : data.candidate;
                        var guardian = data.guardian === null ? {} : data.guardian;
                        var paid_fee = data.paid_fee === null ? {} : data.paid_fee;
                        var special_need = data.specialNeed === null ? {} : data.specialNeed;
                        $(`form#candidate-information  input,form#candidate-information  select, form#candidate-information  textarea`)
                            .each(
                                function(index) {
                                    var input = $(this);
                                    var type = input.prop('type');
                                    console.log(type)
                                    var candidate_prifix_length = "candidate_".length;
                                    var name = input.attr('name')?.slice(candidate_prifix_length)

                                    if (type != "hidden") {

                                        $(`form#candidate-information  [name='candidate_${name}']`)
                                            .val(candidate.hasOwnProperty(name) ? candidate[name] : '')
                                    }

                                }
                            );

                        $(`form#candidate-information [name='candidate_${name}']`)
                            .val(candidate.hasOwnProperty(name) ? candidate[name] : '')

                        // specialNeed
                        $(`form#candidate-information [name='special_need']`)
                            .val(special_need.hasOwnProperty('arrangement_id') ? special_need.arrangement_id : '')





                    }
                });

            }

            /*****Show Update Candidate Endr *******/


            /****  Update Profile*******/
            $(document).on("click", "#save-records", function(ev) {
                ev.preventDefault()
                $.ajaxSetup({
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                    },
                });
                $.ajax({
                    url: "{{ route('candidate.profile.update') }}",
                    method: "POST",
                    cache: false,
                    data: $("#candidate-information").serialize() +
                        "&current_page" +
                        "=" +
                        1,
                    success: function(data) {

                        if ($.isEmptyObject(data.errors)) {
                            toastr.success(data.success);

                        } else {
                            var parent = "#candidate-information";
                            $(`${parent} input, ${parent} select, textarea`).each(function(index) {
                                $(`${parent} .invalid-feedback`).remove();
                                $(`${parent} .is-invalid`).removeClass('is-invalid');

                            });
                            $.each(data.errors, function(key, errors) {
                                for (const error in errors) {
                                    const value = errors[error];
                                    $(`[name='${key}']`).addClass('is-invalid');
                                    $(`<span class='invalid-feedback'>${value}</span>`).insertAfter(
                                        `${parent} [name='${key}']`)

                                }
                            });

                        }
                    },

                });


            });
            /****  End Update Profile********/






            /****  Print errors*******/
            function printErrorMsg(parent, msg) {
                $(`${parent} input, ${parent} select, textarea`).each(function(index) {
                    $(`${parent} .invalid-feedback`).remove();
                    $(`${parent} .is-invalid`).removeClass('is-invalid');
                    // console.log(input.attr('type') + 'Name: ' + input.attr('name') + '  Value: ' + input.val());
                });
                $.each(errors, function(key, errors) {
                    for (const error in errors) {
                        const value = errors[error];
                        $(`[name='${key}']`).addClass('is-invalid');
                        $(`<span class='invalid-feedback'>${value}</span>`).insertAfter(
                            `${parent} [name='${key}']`)

                    }
                });
            }
            /****  Print errors End*******/
        </script>
    @endpush
@endsection
