@extends('layouts.admin')

@section('title', 'Center Stock Allocation')

@section('content')
<div class="main">
    <div class="main-content">
        <div class="container-fluid">
            <h3 class="page-title">Center Stock Allocation</h3>
            
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h4>Generate Allocation Report</h4>
                        </div>
                        <div class="panel-body">
                            <form id="allocationForm">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="level">Level <span class="text-danger">*</span></label>
                                            <select name="level" id="level" class="form-control select2" required>
                                                <option value="">-- Select Level --</option>
                                                @foreach($levels as $level)
                                                    <option value="{{ $level->id }}">{{ $level->description ?? $level->id }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="center_no">Center <span class="text-danger">*</span></label>
                                            <select name="center_no" id="center_no" class="form-control select2" required disabled>
                                                <option value="">-- Select Center --</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="financial_year">Financial Year <span class="text-danger">*</span></label>
                                            <select name="financial_year" id="financial_year" class="form-control select2" required disabled>
                                                <option value="">-- Select Financial Year --</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="session_id">Session <span class="text-danger">*</span></label>
                                            <select name="session_id" id="session_id" class="form-control select2" required disabled>
                                                <option value="">-- Select Session --</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="component_id">Component (Optional)</label>
                                            <select name="component_id" id="component_id" class="form-control select2" disabled>
                                                <option value="">-- All Components --</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <button type="button" class="btn btn-primary" id="generateReportBtn">
                                    <i class="fa fa-calculator"></i> Generate Report
                                </button>
                                
                                <a href="{{ route('admin.stationery.allocation.view') }}" class="btn btn-info">
                                    <i class="fa fa-eye"></i> View Saved Allocations
                                </a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Loading Indicator -->
            <div id="loadingIndicator" style="display: none;">
                <div class="alert alert-info text-center">
                    <i class="fa fa-spinner fa-spin fa-2x"></i>
                    <h4>Generating allocation report...</h4>
                </div>
            </div>

            <!-- Report Results -->
            <div id="reportResults" style="display: none;">
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-success">
                            <div class="panel-heading">
                                <h4>Allocation Report</h4>
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <dl class="dl-horizontal">
                                            <dt>Center:</dt>
                                            <dd><strong id="reportCenterName"></strong> (<span id="reportCenterNo"></span>)</dd>
                                            
                                            <dt>Level:</dt>
                                            <dd id="reportLevel"></dd>
                                            
                                            <dt>Session:</dt>
                                            <dd id="reportSession"></dd>
                                            
                                            <dt>Financial Year:</dt>
                                            <dd id="reportFinancialYear"></dd>
                                        </dl>
                                    </div>
                                    <div class="col-md-6">
                                        <dl class="dl-horizontal">
                                            <dt>Candidates:</dt>
                                            <dd id="reportCandidates"></dd>
                                            
                                            <dt>Invigilators:</dt>
                                            <dd id="reportInvigilators"></dd>
                                            
                                            <dt id="componentLabel" style="display: none;">Component:</dt>
                                            <dd id="reportComponent" style="display: none;"></dd>
                                        </dl>
                                    </div>
                                </div>
                                
                                <hr>
                                
                                <h4>Stock Allocation Details</h4>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Component</th>
                                                <th>Stock Item</th>
                                                <th>Stock Type</th>
                                                <th class="text-center">Required</th>
                                                <th class="text-center">Available</th>
                                                <th class="text-center">Can Allocate?</th>
                                                <th class="text-center">Remaining</th>
                                                <th>Breakdown</th>
                                            </tr>
                                        </thead>
                                        <tbody id="allocationTableBody">
                                        </tbody>
                                    </table>
                                </div>
                                
                                <button type="button" class="btn btn-success" id="saveAllocationBtn">
                                    <i class="fa fa-save"></i> Save Allocation
                                </button>
                                
                                <button type="button" class="btn btn-default" id="clearReportBtn">
                                    <i class="fa fa-refresh"></i> Clear Report
                                </button>
                            </div>
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
            <div class="modal-body" id="breakdownDetails"></div>
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
let allSessions = @json($sessions);
let allComponents = @json($components);
let allCenters = @json($centers);

$(document).ready(function() {
    $.ajaxSetup({
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
    });

    // Initialize select2 for better dropdown experience
    $('.select2').select2({
        placeholder: 'Select an option',
        allowClear: true,
        width: '100%'
    });

    // When Level changes, load Centers and Financial Years
    $('#level').on('change', function() {
        const selectedLevel = $(this).val();
        
        // Reset dependent fields
        $('#center_no').prop('disabled', true).html('<option value="">-- Select Center --</option>');
        $('#financial_year').prop('disabled', true).html('<option value="">-- Select Financial Year --</option>');
        $('#session_id').prop('disabled', true).html('<option value="">-- Select Session --</option>');
        $('#component_id').prop('disabled', true).html('<option value="">-- All Components --</option>');
        
        if (!selectedLevel) {
            return;
        }

        // Enable center dropdown and load centers by level
        $('#center_no').prop('disabled', false);
        loadCentersByLevel(selectedLevel);
        
        // Enable financial year dropdown and load available financial years
        $('#financial_year').prop('disabled', false);
        loadFinancialYears();
        
        // Enable and load components filtered by level
        $('#component_id').prop('disabled', false);
        loadComponentsByLevel(selectedLevel);
    });

    // When Financial Year changes, load Sessions
    $('#financial_year').on('change', function() {
        const selectedFinancialYear = $(this).val();
        
        // Reset session
        $('#session_id').prop('disabled', true).html('<option value="">-- Select Session --</option>');
        
        if (!selectedFinancialYear) {
            return;
        }
        
        // Enable and load sessions by financial year
        $('#session_id').prop('disabled', false);
        loadSessionsByFinancialYear(selectedFinancialYear);
    });

    // Function to load centers by level via AJAX
    function loadCentersByLevel(level) {
        $.ajax({
            url: "{{ route('admin.stationery.allocation.centers-by-level') }}",
            method: 'GET',
            data: { level: level },
            success: function(response) {
                if (response.success) {
                    let centerOptions = '<option value="">-- Select Center --</option>';
                    response.centers.forEach(center => {
                        centerOptions += `<option value="${center.center_no}">${center.center_no} - ${center.center_name}</option>`;
                    });
                    $('#center_no').html(centerOptions);
                }
            },
            error: function(xhr) {
                console.error('Error loading centers:', xhr);
                toastr.error('Error loading centers');
            }
        });
    }

    // Function to load available financial years
    function loadFinancialYears() {
        // Get unique financial years from all sessions
        const uniqueYears = [...new Set(allSessions.map(s => s.financial_year).filter(y => y))];
        
        let fyOptions = '<option value="">-- Select Financial Year --</option>';
        uniqueYears.sort().forEach(year => {
            fyOptions += `<option value="${year}">${year}</option>`;
        });
        
        $('#financial_year').html(fyOptions);
    }

    // Function to load sessions by financial year via AJAX
    function loadSessionsByFinancialYear(financialYear) {
        $.ajax({
            url: "{{ route('admin.stationery.allocation.sessions-by-level') }}",
            method: 'GET',
            data: { financial_year: financialYear },
            success: function(response) {
                if (response.success) {
                    let sessionOptions = '<option value="">-- Select Session --</option>';
                    response.sessions.forEach(session => {
                        sessionOptions += `<option value="${session.id}">${session.session}</option>`;
                    });
                    $('#session_id').html(sessionOptions);
                }
            },
            error: function(xhr) {
                console.error('Error loading sessions:', xhr);
                toastr.error('Error loading sessions');
            }
        });
    }

    // Function to load components by level via AJAX
    function loadComponentsByLevel(level) {
        $.ajax({
            url: "{{ route('admin.stationery.allocation.components-by-filters') }}",
            method: 'GET',
            data: { level: level },
            success: function(response) {
                if (response.success) {
                    let componentOptions = '<option value="">-- All Components --</option>';
                    response.components.forEach(comp => {
                        const paddedSubjectCode = String(comp.subject_code).padStart(4, '0');
                        componentOptions += `<option value="${comp.id}">${comp.component_name} (${paddedSubjectCode}-${comp.component_code})</option>`;
                    });
                    $('#component_id').html(componentOptions);
                }
            },
            error: function(xhr) {
                console.error('Error loading components:', xhr);
                toastr.error('Error loading components');
            }
        });
    }

    // Generate Report button click
    $('#generateReportBtn').on('click', function() {
        const level = $('#level').val();
        const centerNo = $('#center_no').val();
        const financialYear = $('#financial_year').val();
        const sessionId = $('#session_id').val();
        const componentId = $('#component_id').val();

        if (!level || !centerNo || !financialYear || !sessionId) {
            toastr.error('Please select Level, Center, Financial Year, and Session');
            return;
        }

        // Show loading indicator
        $('#loadingIndicator').show();
        $('#reportResults').hide();

        // Make AJAX request
        $.ajax({
            url: "{{ route('admin.stationery.allocation.generate') }}",
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                center_no: centerNo,
                level: level,
                session_id: sessionId,
                financial_year: financialYear,
                component_id: componentId || null
            },
            success: function(response) {
                $('#loadingIndicator').hide();
                
                if (response.success) {
                    allocationData = response.data;
                    displayAllocationReport(response.data);
                    $('#reportResults').show();
                    toastr.success('Report generated successfully');
                } else {
                    toastr.error(response.message || 'Error generating report');
                }
            },
            error: function(xhr) {
                $('#loadingIndicator').hide();
                const errorMsg = xhr.responseJSON?.message || 'An error occurred';
                toastr.error(errorMsg);
                
                // Show debug info if available
                if (xhr.responseJSON?.debug) {
                    console.log('Debug Info:', xhr.responseJSON.debug);
                    
                    let debugMsg = xhr.responseJSON.debug.hint || '';
                    if (debugMsg) {
                        toastr.warning(debugMsg, 'Hint', {timeOut: 10000});
                    }
                }
            }
        });
    });

    function displayAllocationReport(data) {
        // Clear previous results
        $('#allocationTableBody').empty();
        
        // Update header information
        $('#reportCenterName').text(data.center.center_name);
        $('#reportCenterNo').text(data.center.center_no);
        $('#reportSession').text(data.session.session);
        $('#reportCandidates').text(data.num_candidates);
        $('#reportInvigilators').text(data.num_invigilators);
        
        // Display level
        if (data.level) {
            $('#reportLevel').text(data.level.description || data.level.level);
        } else {
            $('#reportLevel').text('-');
        }
        
        // Display financial year
        $('#reportFinancialYear').text(data.session.financial_year || '-');
        
        // Update component info if selected
        if (data.component) {
            const fullCode = String(data.component.subject_code).padStart(4, '0') + '-' + data.component.component_code;
            $('#reportComponent').text(data.component.component_name + ' (' + fullCode + ')');
            $('#componentLabel').show();
            $('#reportComponent').show();
        } else {
            $('#componentLabel').hide();
            $('#reportComponent').hide();
        }

        // Populate allocation table
        if (data.allocations && data.allocations.length > 0) {
            data.allocations.forEach(function(allocation, index) {
                const statusClass = allocation.can_allocate ? '' : 'danger';
                const row = `
                    <tr class="${statusClass}">
                        <td>${index + 1}</td>
                        <td>
                            ${allocation.component.component_name}<br>
                            <small class="text-muted">
                                ${allocation.component.full_code}
                            </small>
                        </td>
                        <td>${allocation.stock_item.name}</td>
                        <td>${allocation.stock_item.stock_type || '-'}</td>
                        <td class="text-center"><strong>${allocation.required_qty}</strong></td>
                        <td class="text-center">${allocation.available_stock}</td>
                        <td class="text-center">
                            <span class="label label-${allocation.can_allocate ? 'success' : 'danger'}">
                                ${allocation.can_allocate ? 'Yes' : 'No'}
                            </span>
                        </td>
                        <td class="text-center ${allocation.remaining_stock < 0 ? 'text-danger' : ''}">
                            ${allocation.remaining_stock}
                        </td>
                        <td>
                            <button class="btn btn-xs btn-info view-breakdown" 
                                data-breakdown='${JSON.stringify(allocation.breakdown)}'>
                                <i class="fa fa-eye"></i> View
                            </button>
                        </td>
                    </tr>
                `;
                $('#allocationTableBody').append(row);
            });
            
            // Check if all can be allocated
            const allCanAllocate = data.allocations.every(a => a.can_allocate);
            if (!allCanAllocate) {
                toastr.warning('Some items have insufficient stock and cannot be allocated');
            }
        } else {
            $('#allocationTableBody').append(`
                <tr>
                    <td colspan="9" class="text-center">
                        <div class="alert alert-warning">
                            <i class="fa fa-exclamation-triangle"></i>
                            No allocation rules configured for the selected component(s)
                        </div>
                    </td>
                </tr>
            `);
        }
    }

    // View breakdown details
    $(document).on('click', '.view-breakdown', function() {
        const breakdown = $(this).data('breakdown');
        
        let html = '<div class="list-group">';
        for (const [key, value] of Object.entries(breakdown)) {
            const label = key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
            html += `<div class="list-group-item">
                <strong>${label}:</strong> <span class="pull-right">${value}</span>
            </div>`;
        }
        html += '</div>';
        
        $('#breakdownDetails').html(html);
        $('#breakdownModal').modal('show');
    });

    // Save allocation
    $('#saveAllocationBtn').on('click', function() {
        if (!allocationData) {
            toastr.error('No allocation data to save');
            return;
        }

        // Check if any allocation cannot be fulfilled
        const unallocatable = allocationData.allocations.filter(a => !a.can_allocate);
        if (unallocatable.length > 0) {
            if (!confirm('Some items have insufficient stock. Do you want to proceed anyway?')) {
                return;
            }
        }

        // Prepare allocation data
        const allocations = allocationData.allocations.map(a => ({
            component_id: a.component.id,
            stock_item_id: a.stock_item.id,
            allocated_qty: a.required_qty,
            breakdown: a.breakdown
        }));

        // Save allocation
        $.ajax({
            url: "{{ route('admin.stationery.allocation.save') }}",
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                center_no: allocationData.center.center_no,
                session_id: allocationData.session.id,
                allocations: allocations
            },
            success: function(response) {
                if (response.success) {
                    toastr.success('Allocation saved successfully');
                    $('#clearReportBtn').click();
                } else {
                    toastr.error(response.message || 'Error saving allocation');
                }
            },
            error: function(xhr) {
                toastr.error(xhr.responseJSON?.message || 'Error saving allocation');
            }
        });
    });

    // Clear report
    $('#clearReportBtn').on('click', function() {
        $('#reportResults').hide();
        allocationData = null;
        $('#allocationForm')[0].reset();
        $('.select2').val(null).trigger('change');
        
        // Reset cascading dropdowns
        $('#center_no').prop('disabled', true).html('<option value="">-- Select Center --</option>');
        $('#financial_year').prop('disabled', true).html('<option value="">-- Select Financial Year --</option>');
        $('#session_id').prop('disabled', true).html('<option value="">-- Select Session --</option>');
        $('#component_id').prop('disabled', true).html('<option value="">-- All Components --</option>');
    });
});
</script>
@endpush