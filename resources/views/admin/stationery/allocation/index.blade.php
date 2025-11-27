@extends('layouts.admin')

@section('title', 'Center Stock Allocation')

@section('content')
<div class="main">
    <div class="main-content">
        <div class="container-fluid">
            <h3 class="page-title">Center Stock Allocation</h3>
            
            <!-- Selection Form -->
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h4>Generate Allocation Report</h4>
                        </div>
                        <div class="panel-body">
                            <form id="allocationForm">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="center_no">Center <span class="text-danger">*</span></label>
                                            <select class="form-control" id="center_no" name="center_no">
                                                <option value="">Select Center</option>
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
                                            <label for="session_id">Session <span class="text-danger">*</span></label>
                                            <select class="form-control" id="session_id" name="session_id">
                                                <option value="">Select Session</option>
                                                @foreach($sessions as $session)
                                                    <option value="{{ $session->id }}">{{ $session->session }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="subject_code">Subject</label>
                                            <select class="form-control" id="subject_code" name="subject_code">
                                                <option value="">All Subjects</option>
                                                @foreach($subjects as $subject)
                                                    <option value="{{ $subject->subject_code }}">
                                                        {{ $subject->subject_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>   
                                
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-calculator"></i> Generate Allocation Report
                                </button>
                                
                                <a href="{{ route('admin.stationery.allocation.view') }}" class="btn btn-info">
                                    <i class="fa fa-list"></i> View Saved Allocations
                                </a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Allocation Report -->
            <div class="row" id="reportSection" style="display: none;">
                <div class="col-md-12">
                    <div class="panel panel-success">
                        <div class="panel-heading">
                            <h4>Allocation Report</h4>
                        </div>
                        <div class="panel-body">
                            <!-- Center Info -->
                            <div class="alert alert-info">
                                <h4 id="centerInfo"></h4>
                                <p id="candidateInfo"></p>
                            </div>
                            
                            <!-- Allocations Table -->
                            <table class="table table-striped table-bordered" id="allocationsTable">
                                <thead>
                                    <tr>
                                        <th>Component</th>
                                        <th>Stock Item</th>
                                        <th>Required Qty</th>
                                        <th>Available Stock</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                            
                            <button type="button" class="btn btn-success btn-lg" id="saveAllocationBtn">
                                <i class="fa fa-save"></i> Save Allocation
                            </button>
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
                <h4 class="modal-title">Calculation Breakdown</h4>
            </div>
            <div class="modal-body" id="breakdownContent"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let allocationData = null;

$(document).ready(function() {
    $.ajaxSetup({
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
    });

    // Toastr configuration
    if (typeof toastr !== 'undefined') {
        toastr.options = {
            closeButton: true,
            progressBar: true,
            positionClass: "toast-top-right",
            timeOut: "5000"
        };
    }

    // Generate Report
    $('#allocationForm').submit(function(e) {
        e.preventDefault();
        
        console.log('Form submitted');
        
        // Show loading indicator
        let submitBtn = $(this).find('button[type="submit"]');
        let originalText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Generating...');
        
        $.ajax({
            url: '{{ route("admin.stationery.allocation.generate") }}',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                console.log('Success response:', response);
                
                if (response.success) {
                    allocationData = response.data;
                    displayReport(response.data);
                    
                    if (typeof toastr !== 'undefined') {
                        toastr.success('Allocation report generated successfully');
                    } else {
                        alert('Allocation report generated successfully');
                    }
                } else {
                    if (typeof toastr !== 'undefined') {
                        toastr.error(response.message || 'Error generating report');
                    } else {
                        alert(response.message || 'Error generating report');
                    }
                }
                
                submitBtn.prop('disabled', false).html(originalText);
            },
            error: function(xhr) {
                console.error('Error response:', xhr);
                
                let errorMessage = 'Error generating report';
                
                if (xhr.responseJSON) {
                    errorMessage = xhr.responseJSON.message || errorMessage;
                    
                    // Display validation errors
                    if (xhr.responseJSON.errors) {
                        let errors = xhr.responseJSON.errors;
                        errorMessage = Object.values(errors).flat().join('<br>');
                    }
                }
                
                if (typeof toastr !== 'undefined') {
                    toastr.error(errorMessage);
                } else {
                    alert(errorMessage);
                }
                
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });

    // Display Report
    function displayReport(data) {
        console.log('Displaying report with data:', data);
        
        // Update center info
        $('#centerInfo').html(`<strong>${data.center.center_no} - ${data.center.center_name}</strong>`);
        $('#candidateInfo').html(`Candidates: <strong>${data.num_candidates}</strong> | Invigilators: <strong>${data.num_invigilators}</strong> | Session: <strong>${data.session.session}</strong>`);
        
        // Build table rows
        let tbody = '';
        
        if (data.allocations && data.allocations.length > 0) {
            data.allocations.forEach(function(alloc, index) {
                console.log('Processing allocation:', alloc);
                
                let statusBadge = alloc.can_allocate 
                    ? '<span class="label label-success"><i class="fa fa-check"></i> Available</span>' 
                    : '<span class="label label-danger"><i class="fa fa-times"></i> Insufficient Stock</span>';
                
                let componentName = alloc.component.component_name || 'N/A';
                let stockItemName = alloc.stock_item.name || 'N/A';
                let requiredQty = alloc.required_qty || 0;
                let availableStock = alloc.available_stock || 0;
                let remainingStock = alloc.remaining_stock || 0;
                
                tbody += `
                    <tr data-index="${index}">
                        <td>${componentName}</td>
                        <td>${stockItemName}</td>
                        <td class="text-right"><strong>${requiredQty}</strong></td>
                        <td class="text-right">${availableStock}</td>
                        <td class="text-center">${statusBadge}</td>
                        <td class="text-center">
                            <button class="btn btn-info btn-sm view-breakdown" 
                                data-index="${index}"
                                title="View Calculation Breakdown">
                                <i class="fa fa-calculator"></i> Breakdown
                            </button>
                        </td>
                    </tr>
                `;
            });
        } else {
            tbody = '<tr><td colspan="6" class="text-center text-danger"><i class="fa fa-exclamation-triangle"></i> No allocations found. Please ensure allocation rules are configured for the selected components.</td></tr>';
        }
        
        $('#allocationsTable tbody').html(tbody);
        
        $('#reportSection').slideDown();
        
        // Scroll to report
        $('html, body').animate({
            scrollTop: $("#reportSection").offset().top - 100
        }, 500);
        
        // Enable/disable save button based on allocations
        if (data.allocations && data.allocations.length > 0) {
            $('#saveAllocationBtn').prop('disabled', false);
        } else {
            $('#saveAllocationBtn').prop('disabled', true);
        }
    }

    // View Breakdown
    $(document).on('click', '.view-breakdown', function() {
        console.log('View breakdown clicked');
        
        let index = $(this).data('index');
        
        if (!allocationData || !allocationData.allocations[index]) {
            alert('No breakdown data available');
            return;
        }
        
        let allocation = allocationData.allocations[index];
        let breakdown = allocation.breakdown;
        
        console.log('Breakdown data:', breakdown);
        
        let html = '<div class="panel panel-default">';
        html += '<div class="panel-heading"><strong>' + allocation.stock_item.name + ' - Calculation Breakdown</strong></div>';
        html += '<div class="panel-body">';
        html += '<ul class="list-group">';
        
        if (breakdown && typeof breakdown === 'object') {
            for (let key in breakdown) {
                let label = key.replace(/_/g, ' ')
                    .replace(/\b\w/g, l => l.toUpperCase())
                    .replace('Step ', 'Step ');
                html += `<li class="list-group-item">
                    <strong>${label}:</strong> 
                    <span class="pull-right badge badge-primary">${breakdown[key]}</span>
                </li>`;
            }
        } else {
            html += '<li class="list-group-item">No breakdown available</li>';
        }
        
        html += '</ul>';
        html += '<hr>';
        html += '<p><strong>Formula:</strong> (Base Qty × Count × Multiplier) + Fixed Extras + Per Candidate Extras + Percentage Extras</p>';
        html += '</div></div>';
        
        $('#breakdownContent').html(html);
        $('#breakdownModal').modal('show');
    });

    // Save Allocation
    $('#saveAllocationBtn').click(function() {
        console.log('Save allocation clicked');
        
        if (!allocationData) {
            alert('No allocation data to save');
            return;
        }
        
        if (!allocationData.allocations || allocationData.allocations.length === 0) {
            alert('No allocations to save');
            return;
        }
        
        // Confirm before saving
        if (!confirm('Are you sure you want to save these allocations? This will deduct stock from inventory.')) {
            return;
        }
        
        let allocations = allocationData.allocations.map(function(alloc) {
            return {
                component_id: alloc.component.id,
                stock_item_id: alloc.stock_item.id,
                allocated_qty: alloc.required_qty,
                breakdown: alloc.breakdown
            };
        });
        
        console.log('Saving allocations:', allocations);
        
        // Show loading
        let btn = $(this);
        let originalText = btn.html();
        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');
        
        $.ajax({
            url: '{{ route("admin.stationery.allocation.save") }}',
            type: 'POST',
            data: {
                center_no: allocationData.center.center_no,
                session_id: allocationData.session.id,
                allocations: allocations
            },
            success: function(response) {
                console.log('Save response:', response);
                
                if (response.success) {
                    if (typeof toastr !== 'undefined') {
                        toastr.success(response.message);
                    } else {
                        alert(response.message);
                    }
                    
                    // Reset form and hide report
                    $('#reportSection').slideUp();
                    $('#allocationForm')[0].reset();
                    allocationData = null;
                    
                    // Optionally redirect to view allocations
                    setTimeout(function() {
                        if (confirm('Would you like to view the saved allocations?')) {
                            window.location.href = '{{ route("admin.stationery.allocation.view") }}';
                        }
                    }, 1000);
                } else {
                    if (typeof toastr !== 'undefined') {
                        toastr.error(response.message || 'Error saving allocation');
                    } else {
                        alert(response.message || 'Error saving allocation');
                    }
                }
                
                btn.prop('disabled', false).html(originalText);
            },
            error: function(xhr) {
                console.error('Save error:', xhr);
                
                let errorMessage = 'Error saving allocation';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                if (typeof toastr !== 'undefined') {
                    toastr.error(errorMessage);
                } else {
                    alert(errorMessage);
                }
                
                btn.prop('disabled', false).html(originalText);
            }
        });
    });
});
</script>
@endpush