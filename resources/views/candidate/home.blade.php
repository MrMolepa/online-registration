@extends('layouts.candidate')

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-7 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Registered Subjects </h4>

                        <ul class="list-group">
                            @foreach ($subjects as $subject)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ $subject->subject_name }}
                                    <span class="badge badge-primary badge-pill">{{ $subject->subject_code }} {{ '  ' }} - {{ '  ' }}
                                        {{ $subject->subject_option }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-5 col-sm-6 col-md-6 col-lg-4">
                <div class="card">
                    <div class="card-body">


                        <h4 class="card-title">{{$subjects->first()->center_name}}</h4>
                        <h6 class="card-subtitle">{{$subjects->first()->center_no}}</h6>
                        <ul class="list-group">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Total registered subjects:
                                <span class="badge badge-subjects badge-pill">{{ count($subjects) }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Total fee to pay:
                                <span class="badge badge-subjects badge-pill">LSL {{number_format((float)$total_amount, 2, '.', '') }}</span>
                            </li>

                        </ul>
                          @if ($timetable =="")
                              <a href="{{ route('candidate.payment') }}"class="card-link text-danger my-2 ">Pay now</a>
                          @else
                              {!! $timetable !!}
                          @endif
                       <ul class="list-group">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <h4 class="card-title">Amount Paid: {{number_format((float)$amount_paid, 2, '.', '') }}</h4>


                            </li>
                        </ul>
                    </div>
                    <div class="card-body">



                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
