@extends('layouts.admin')

@section('title', 'Fun Walk Payments')

@section('content')
<div class="main">
    <div class="main-content">
        <div class="container-fluid">
            <h3 class="page-title">Fun Walk Payments</h3>

            <div class="panel panel-headline">
                <div class="panel-body">
                   
                    <table class="table table-striped" id="funWalkPaymentsTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Ticket Number</th>
                                <th>Full Name</th>
                                <th>Registration ID</th>
                                <th>Amount</th>
                                <th>Payment Method</th>
                                <th>Transaction Ref</th>
                                <th>Status</th>
                                <th>Paid At</th>
                                <th>Created At</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function(){
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    let table = $('#funWalkPaymentsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{!! route("admin.fun-walk-payments.index") !!}',
        columns: [
            {data:'id', name:'id'},
            {data:'registration_ticket', name:'registration.ticket_number'},
            {data:'registration_full_name', name:'registration_full_name'},
            {data:'registration_id', name:'registration_id'},
            {data:'amount', name:'amount'},
            {data:'payment_method', name:'payment_method'},
            {data:'transaction_ref', name:'transaction_ref'},
            {data:'status', name:'status'},
            {data:'paid_at', name:'paid_at'},
            {data:'created_at', name:'created_at'},
        ],
        order: [[0, 'desc']]
    });
});
</script>
@endpush