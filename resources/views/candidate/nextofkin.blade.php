@extends('layouts.candidate')

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-8 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Next of Kin</h4>
                        <form class="forms-sample" id="candidate-guardian" method="POST">
                            @csrf
                            <div class="form-card">
                                <h3 class="headline">Personal Information</h3>
                                <div class="row">
                                    <input type="hidden" name="national_id" value="{{ $candidate->national_id }}">
                                    <input type="hidden" name="candidate_no" value="{{ $candidate->candidate_no }}">
                                    <div class="form-group col-12">
                                        <label for="guardian_type">Relationship
                                            Between</label>
                                        <select name="guardian_type" class="form-control form-control-sm"
                                            id="guardian_type">
                                            <option value="">Please select
                                                relationship</option>
                                            @foreach ($guardian_types as $guardian_type)
                                                <option value="{{ $guardian_type->id }}">
                                                    {{ $guardian_type->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="guardian_national_id">National
                                            Id</label>
                                        <input type="text" class="form-control  form-control-sm "
                                            id="guardian_national_id" name="guardian_national_id" placeholder="national id">
                                    </div>
                                    <div class="form-group col-6">
                                        <label for="guardian_name">Other
                                            Names</label>
                                        <input type="text" class="form-control form-control-sm" id="guardian_name"
                                            name="guardian_name" placeholder="Name">
                                    </div>
                                    <div class="form-group col-6">
                                        <label for="guardian_surname">Surname</label>
                                        <input type="text" class="form-control form-control-sm" id="guardian_surname"
                                            name="guardian_surname" placeholder="Surname">
                                    </div>
                                    <div class="form-group col-6">
                                        <label for="guardian_email">Email</label>
                                        <input type="text" class="form-control form-control-sm" id="guardian_email"
                                            name="guardian_email" placeholder="Email">
                                    </div>
                                    <div class="form-group col-6">
                                        <label for="guardian_phone">Phone
                                            Number</label>
                                        <input type="text" class="form-control form-control-sm"
                                            id="guardian_phone_number" name="guardian_phone_number"
                                            placeholder="Phone Number">
                                    </div>
                                </div>
                                <h3 class="headline">Address</h3>
                                <div class="row">
                                    <div class="form-group col-6">
                                        <label for="guardian_postal_address">Postal
                                            Address </label>
                                        <input type="text" class="form-control form-control-sm"
                                            id="guardian_postal_address" name="guardian_postal_address"
                                            placeholder="P.O.Box 2398">
                                    </div>
                                    <div class="form-group col-6">
                                        <label for="guardian_physical_address">Physical
                                            Address</label>
                                        <input type="text" class="form-control form-control-sm"
                                            id="guardian_physical_address" name="guardian_physical_address"
                                            placeholder="Qoaling">
                                    </div>

                                    <div class="form-group col-6">
                                        <label for="guardian_village">Village</label>
                                        <input type="text" class="form-control form-control-sm" id="guardian_village"
                                            name="guardian_village" placeholder="Ha Seoli">
                                    </div>
                                    <div class="form-group col-6">
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
                                </div>
                            </div>
                            <button type="submit"  id="save-records" class=" btn btn-block   btn-primary">Save</button>
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
                        var guardian = data.guardian === null ? {} : data.guardian;

                        //guardian
                        $(`form#candidate-guardian input,form#candidate-guardian select, form#candidate-guardian textarea`)
                            .each(
                                function(index) {
                                    var input = $(this);
                                    var type = input.prop('type');
                                    var guardian_prifix_length = "guardian_".length;
                                    var original_name = input.attr('name');
                                    var name = input.attr('name').slice(guardian_prifix_length);
                                    if (type != "hidden") {
                                        if (original_name == "guardian_type") {
                                            $(`form#candidate-guardian [name='${original_name}']`)
                                                .val(guardian.hasOwnProperty(original_name) ? guardian[
                                                    original_name] : '')
                                        } else {
                                            $(`form#candidate-guardian [name='guardian_${name}']`)
                                                .val(guardian.hasOwnProperty(name) ? guardian[name] : '')
                                        }

                                    }






                                }
                            );




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
                    data: $("#candidate-guardian").serialize() +
                        "&current_page" +
                        "=" +
                        2,
                    success: function(data) {
                        if ($.isEmptyObject(data.errors)) {
                            toastr.success(data.success);
                        } else {
                            var parent = "#candidate-guardian";
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
