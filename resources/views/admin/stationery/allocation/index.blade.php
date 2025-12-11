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
                                    
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="center_no">Center <span class="text-danger">*</span></label>
                                            <select name="center_no" id="center_no" class="form-control select2" required disabled>
                                                <option value="">-- Select Center --</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="component_id">Component</label>
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
                                            <dt>Level:</dt>
                                            <dd id="reportLevel"></dd>
                                            
                                            <dt>Financial Year:</dt>
                                            <dd id="reportFinancialYear"></dd>
                                            
                                            <dt>Session:</dt>
                                            <dd id="reportSession"></dd>
                                            
                                            <dt>Center:</dt>
                                            <dd><strong id="reportCenterName"></strong> (<span id="reportCenterNo"></span>)</dd>
                                        </dl>
                                    </div>
                                    <div class="col-md-6">
                                        <dl class="dl-horizontal">
                                            <dt>Unique Candidates:</dt>
                                            <dd id="reportCandidates"></dd>
                                            
                                            <dt>Component Registrations:</dt>
                                            <dd id="reportComponentRegistrations"></dd>
                                            
                                            <dt>Invigilators:</dt>
                                            <dd id="reportInvigilators"></dd>
                                            
                                            <dt id="componentLabel" style="display: none;">Selected Component:</dt>
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
                                                <th>Candidates</th>
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
let allFinancialYears = @json($financialYears);

$(document).ready(function() {
    $.ajaxSetup({
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
    });

    // Initialize select2
    $('.select2').select2({
        placeholder: 'Select an option',
        allowClear: true,
        width: '100%'
    });

    // When Level changes, load Financial Years
    $('#level').on('change', function() {
        const selectedLevel = $(this).val();
        
        // Reset dependent fields
        $('#financial_year').prop('disabled', true).html('<option value="">-- Select Financial Year --</option>');
        $('#session_id').prop('disabled', true).html('<option value="">-- Select Session --</option>');
        $('#financial_year').prop('disabled', true).html('<option value="">-- Select Financial Year --</option>');
        $('#center_no').prop('disabled', true).html('<option value="">-- Select Center --</option>');
        $('#component_id').prop('disabled', true).html('<option value="">-- All Components --</option>');
        
        if (!selectedLevel) {
            return;
        }

        // Enable and populate financial years
        $('#financial_year').prop('disabled', false);
        let fyOptions = '<option value="">-- Select Financial Year --</option>';
        allFinancialYears.forEach(fy => {
            fyOptions += `<option value="${fy}">${fy}</option>`;
        });
        $('#financial_year').html(fyOptions);
    });

    // When Financial Year changes, load Sessions
    $('#financial_year').on('change', function() {
        const selectedLevel = $('#level').val();
        const selectedFinancialYear = $(this).val();
        
        // Reset dependent fields
        $('#session_id').prop('disabled', true).html('<option value="">-- Select Session --</option>');
        $('#center_no').prop('disabled', true).html('<option value="">-- Select Center --</option>');
        $('#component_id').prop('disabled', true).html('<option value="">-- All Components --</option>');
        
        if (!selectedFinancialYear) {
            return;
        }

        // Enable and load sessions
        $('#session_id').prop('disabled', false);
        loadSessionsByFilters(selectedLevel, selectedFinancialYear);
    });

    // When Session changes, load Centers
    $('#session_id').on('change', function() {
        const selectedLevel = $('#level').val();
        const selectedFinancialYear = $('#financial_year').val();
        const selectedSession = $(this).val();
        
        // Reset dependent fields
        $('#center_no').prop('disabled', true).html('<option value="">-- Select Center --</option>');
        $('#component_id').prop('disabled', true).html('<option value="">-- All Components --</option>');
        
        if (!selectedSession) {
            return;
        }
        
        // Enable and load centers
        $('#center_no').prop('disabled', false);
        loadCentersByFilters(selectedLevel, selectedFinancialYear, selectedSession);
    });

    // When Center changes, load Components
    $('#center_no').on('change', function() {
        const selectedLevel = $('#level').val();
        const selectedFinancialYear = $('#financial_year').val();
        const selectedSession = $('#session_id').val();
        const selectedCenter = $(this).val();
        
        // Reset component field
        $('#component_id').prop('disabled', true).html('<option value="">-- All Components --</option>');
        
        if (!selectedCenter) {
            return;
        }
        
        // Enable and load components
        $('#component_id').prop('disabled', false);
        loadComponentsByFilters(selectedLevel, selectedFinancialYear, selectedSession, selectedCenter);
    });

    // Function to load sessions by filters
    function loadSessionsByFilters(level, financialYear) {
        $.ajax({
            url: "{{ route('admin.stationery.allocation.sessions-by-filters') }}",
            method: 'GET',
            data: { 
                level: level,
                financial_year: financialYear 
            },
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

    // Function to load centers by filters
    function loadCentersByFilters(level, financialYear, sessionId) {
        $.ajax({
            url: "{{ route('admin.stationery.allocation.centers-by-filters') }}",
            method: 'GET',
            data: { 
                level: level,
                financial_year: financialYear,
                session_id: sessionId 
            },
            success: function(response) {
                if (response.success) {
                    let centerOptions = '<option value="">-- Select Center --</option>';
                    response.centers.forEach(center => {
                        centerOptions += `<option value="${center.center_no}">${center.center_no} - ${center.center_name}</option>`;
                    });
                    $('#center_no').html(centerOptions);
                    
                    if (response.centers.length === 0) {
                        toastr.warning('No centers found with candidates for this level, session, and financial year');
                    }
                }
            },
            error: function(xhr) {
                console.error('Error loading centers:', xhr);
                toastr.error('Error loading centers');
            }
        });
    }

    // Function to load components by filters
    function loadComponentsByFilters(level, financialYear, sessionId, centerNo) {
        $.ajax({
            url: "{{ route('admin.stationery.allocation.components-by-filters') }}",
            method: 'GET',
            data: { 
                level: level,
                financial_year: financialYear,
                session_id: sessionId,
                center_no: centerNo
            },
            success: function(response) {
                if (response.success) {
                    let componentOptions = '<option value="">-- All Components --</option>';
                    response.components.forEach(comp => {
                        const paddedSubjectCode = String(comp.subject_code).padStart(4, '0'); 
                        const paddedComponentCode = String(comp.component_code).padStart(2, '0'); 
                        componentOptions += `<option value="${comp.id}">${comp.component_name} (${paddedSubjectCode}-${paddedComponentCode}) - ${comp.candidate_count} candidates</option>`;
                    });
                    $('#component_id').html(componentOptions);
                    
                    if (response.components.length === 0) {
                        toastr.info('No components found with candidate registrations');
                    }
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
        const financialYear = $('#financial_year').val();
        const sessionId = $('#session_id').val();
        const centerNo = $('#center_no').val();
        const componentId = $('#component_id').val();

        if (!level || !financialYear || !sessionId || !centerNo) {
            toastr.error('Please select Level, Financial Year, Session, and Center');
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
                level: level,
                financial_year: financialYear,
                session_id: sessionId,
                center_no: centerNo,
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
                
                if (xhr.responseJSON?.debug) {
                    console.log('Debug Info:', xhr.responseJSON.debug);
                }
            }
        });
    });

    function displayAllocationReport(data) {
        // Clear previous results
        $('#allocationTableBody').empty();
        
        // Update header information
        $('#reportLevel').text(data.level ? data.level.description : '-');
        $('#reportFinancialYear').text(data.session.financial_year);
        $('#reportSession').text(data.session.session);
        $('#reportCenterName').text(data.center.center_name);
        $('#reportCenterNo').text(data.center.center_no);
        $('#reportCandidates').text(data.num_candidates);
        $('#reportComponentRegistrations').text(data.total_component_registrations || '-');
        $('#reportInvigilators').text(data.num_invigilators);
        
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
                            <small class="text-muted">${allocation.component.full_code}</small>
                        </td>
                        <td class="text-center">
                            <span class="badge badge-info">${allocation.component.candidates_registered}</span>
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
                    <td colspan="10" class="text-center">
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

        const unallocatable = allocationData.allocations.filter(a => !a.can_allocate);
        if (unallocatable.length > 0) {
            if (!confirm('Some items have insufficient stock. Do you want to proceed anyway?')) {
                return;
            }
        }

        const allocations = allocationData.allocations.map(a => ({
            component_id: a.component.id,
            stock_item_id: a.stock_item.id,
            allocated_qty: a.required_qty,
            breakdown: a.breakdown
        }));

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
        
        $('#session_id').prop('disabled', true).html('<option value="">-- Select Session --</option>');
        $('#center_no').prop('disabled', true).html('<option value="">-- Select Center --</option>');
        $('#component_id').prop('disabled', true).html('<option value="">-- All Components --</option>');
    });
});
</script>
@endpush