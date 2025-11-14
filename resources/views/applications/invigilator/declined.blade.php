@extends('layouts.application')
@section('content')
    <div class="container" id="decline-application">
        <div class="form-wrap">
            <header class="header">
                <h1 id="title" class="text-danger">Are you sure you want to decline this offer?</b> </h1><br>
                <hr>
                <div id="description">
                    <li>You will loose this opportunity and it might affect you when you you apply again.</li>
                    <li>You will not be able to sign this Contract Form again.</li>
                </div>
            </header>


            <form id="application-edit-form" method="POST" action="{{ $url }}">
                @csrf
                @method('PUT')
                <div class="col-md-12">
                    <div class="form-group ">
                        <label class="form-check-label" for="flexCheckDefault">
                            <input class="form-check-input" type="checkbox" name="declined" value="1" id="declined">
                            I decline this offer.
                        </label>
                    </div>
                    <button type="submit" id="submit-application" name="add-appliation"
                        class="btn btn-danger btn-sm">Decline</button>
                </div>




            </form>

        </div>
    </div>


    @push('scripts')
        <script type="text/javascript">
            $(document).ready(function() {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                // Update
                $(document).on('click', '#submit-application', function(e) {
                    e.preventDefault();
                    var editForm = $("#application-edit-form");
                    var url = editForm.attr('action');
                    $.ajax({
                        type: "POST",
                        url: url,
                        data: editForm.serializeArray(),
                        success: function(data) {
                            if ($.isEmptyObject(data.errors)) {
                                // $('#decline-application').hide();
                                // $('.declined-wrapper').css({
                                //     "display": "flex"
                                // });
                                alert('Declined');

                            } else {
                                printErrorMsg('#decline-application', data.errors);
                            }
                            console.log(data);
                        },
                        error: function(data) {
                            console.log('Error:', data);
                        }
                    });
                });
            });
        </script>
    @endpush
@endsection
