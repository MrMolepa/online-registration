@extends('layouts.admin')

@section('title', 'View Allocations')

@section('content')
<div class="main">
    <div class="main-content">
        <div class="container-fluid">
            <h3 class="page-title">View Stock Allocations</h3>
            
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h4>Filter Allocations</h4>
                        </div>
                        <div class="panel-body">
                            <form id="filterForm">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="filter_center">Center</label>
                                            <select class="form-control" id="filter_center">
                                                <option value="">All Centers</option>
                                                @foreach($centers as $center)
                                                    <option value="{{ $center->center_no }}">
                                                        {{ $center->center_no }} - {{ $center->center_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="filter_session">Session</label>
                                            <select class="form-control" id="filter_session">
                                                <option value="">All Sessions</option>
                                                @foreach($sessions as $session)
                                                    <option value="{{ $session->id }}">{{ $session->session }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="filter_status">Status</label>
                                            <select class="form-control" id="filter_status">
                                                <option value="">All Status</option>
                                                <option value="pending">Pending</option>
                                                <option value="allocated">Allocated</option>
                                                <option value="dispatched">Dispatched</option>
                                                <option value="received">Received</option>
                                                <option value="cancelled">Cancelled</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <button type="button" class="btn btn-primary" id="applyFilterBtn">
                                    <i class="fa fa-filter"></i> Apply Filter
                                </button>
                                
                                <a href="{{ route('admin.stationery.allocation.index') }}" class="btn btn-default">
                                    <i class="fa fa-arrow-left"></i> Back to Allocation
                                </a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-success">
                        <div class="panel-heading">
                            <h4>Saved Allocations</h4>
                        </div>
                        <div class="panel-body">
                            <table class="table table-striped table-bordered" id="allocationsDataTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Center</th>
                                        <th>Session</th>
                                        <th>Component</th>
                                        <th>Stock Item</th>
                                        <th>Allocated</th>
                                        <th>Dispatched</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Breakdown Modal -->
<div class="modal fade" id="breakdownModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Allocation Breakdown</h4>
            </div>
            <div class="modal-body" id="breakdownContent"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Dispatch Modal -->
<div class="modal fade" id="dispatchModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Mark as Dispatched</h4>
            </div>
            <form id="dispatchForm">
                <input type="hidden" id="dispatch_id">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="dispatched_qty">Dispatched Quantity</label>
                        <input type="number" class="form-control" id="dispatched_qty" name="dispatched_qty" step="0.01">
                        <small class="form-text text-muted">Leave empty to dispatch full quantity</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Mark Dispatched</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let table;

$(document).ready(function() {
    $.ajaxSetup({
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
    });

    // Initialize DataTable
    table = $('#allocationsDataTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("admin.stationery.allocation.view") }}',
            data: function(d) {
                d.center_no = $('#filter_center').val();
                d.session_id = $('#filter_session').val();
                d.status = $('#filter_status').val();
            }
        },
        columns: [
            {data: 'id', name: 'id'},
            {data: 'center_name', name: 'center.center_name'},
            {data: 'session_name', name: 'session.session'},
            {data: 'component_name', name: 'component.component_name'},
            {data: 'stock_item_name', name: 'stockItem.name'},
            {data: 'quantity_allocated', name: 'quantity_allocated'},
            {data: 'quantity_dispatched', name: 'quantity_dispatched'},
            {data: 'status_badge', name: 'status'},
            {data: 'actions', name: 'actions', orderable: false, searchable: false}
        ]
    });

    // Apply Filter
    $('#applyFilterBtn').click(function() {
        table.ajax.reload();
    });

    // View Breakdown
    $(document).on('click', '.view-breakdown-btn', function() {
        let id = $(this).data('id');
        
        $.ajax({
            url: '{{ url("admin/stationery/allocation") }}/' + id + '/breakdown',
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    let breakdown = response.data.breakdown;
                    let html = '<ul class="list-group">';
                    
                    for (let key in breakdown) {
                        let label = key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                        html += `<li class="list-group-item">${label}: <strong>${breakdown[key]}</strong></li>`;
                    }
                    
                    html += '</ul>';
                    $('#breakdownContent').html(html);
                    $('#breakdownModal').modal('show');
                }
            },
            error: function(xhr) {
                toastr.error(xhr.responseJSON?.message || 'Error loading breakdown');
            }
        });
    });

    // Dispatch
    $(document).on('click', '.dispatch-btn', function() {
        let id = $(this).data('id');
        $('#dispatch_id').val(id);
        $('#dispatchModal').modal('show');
    });

    $('#dispatchForm').submit(function(e) {
        e.preventDefault();
        let id = $('#dispatch_id').val();
        
        $.ajax({
            url: '{{ url("admin/stationery/allocation") }}/' + id + '/dispatch',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    $('#dispatchModal').modal('hide');
                    table.ajax.reload();
                }
            },
            error: function(xhr) {
                toastr.error(xhr.responseJSON?.message || 'Error dispatching allocation');
            }
        });
    });

    // Cancel
    $(document).on('click', '.cancel-btn', function() {
        if (!confirm('Are you sure you want to cancel this allocation? Stock will be returned to inventory.')) {
            return;
        }
        
        let id = $(this).data('id');
        
        $.ajax({
            url: '{{ url("admin/stationery/allocation") }}/' + id + '/cancel',
            type: 'DELETE',
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    table.ajax.reload();
                }
            },
            error: function(xhr) {
                toastr.error(xhr.responseJSON?.message || 'Error cancelling allocation');
            }
        });
    });
});
</script>
@endpush