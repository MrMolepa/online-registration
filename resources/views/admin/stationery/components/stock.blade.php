@extends('layouts.admin')

@section('title', 'Manage Allocation Rules')

@section('content')
<div class="main">
    <div class="main-content">
        <div class="container-fluid">
            <div class="row mb-3">
                <div class="col-md-12">
                    @if(Route::has('admin.components-stock.index'))
                        <a href="{{ route('admin.components-stock.index', ['subject_code' => $component->subject_code]) }}" class="btn btn-default">
                            <i class="fa fa-arrow-left"></i> Back to Components
                        </a>
                    @else
                        <a href="{{ url()->previous() }}" class="btn btn-default">
                            <i class="fa fa-arrow-left"></i> Back
                        </a>
                    @endif
                </div>
            </div>

            <h3 class="page-title">Allocation Rules for: {{ $component->component_name }} ({{ $component->component_code }})</h3>
            
            <!-- Component Info Card -->
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <h4>Component Information</h4>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <strong>Component Code:</strong><br>
                                    {{ $component->component_code }}
                                </div>
                                <div class="col-md-6">
                                    <strong>Component Name:</strong><br>
                                    {{ $component->component_name }}
                                </div>
                                <div class="col-md-3">
                                    <strong>Subject:</strong><br>
                                    {{ $component->subject ? $component->subject->subject_name : 'N/A' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Allocation Rules Table -->
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-headline">
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <button type="button" class="btn btn-primary" id="addRuleBtn">
                                        <i class="fa fa-plus"></i> Add Allocation Rule
                                    </button>
                                    
                                    <button type="button" class="btn btn-info" id="testCalculatorBtn">
                                        <i class="fa fa-calculator"></i> Test Calculator
                                    </button>
                                    
                                    <div class="mt-3">
                                        <table class="table table-striped" id="allocationRulesTable">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Stock Type</th>
                                                    <th>Stock Item</th>
                                                    <th>Rule Type</th>
                                                    <th>Formula Summary</th>
                                                    <th>Test (50 Candidates)</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include Modals -->
@include('admin.stationery.components._rule_form')
@include('admin.stationery.components._calculator')

@endsection

@push('scripts')
<script>

    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        const componentId = '{{ $component->id }}';

        let table = $('#allocationRulesTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.stationery.component-stock.index', $component->id) }}",
            columns: [
                {data: 'id', name: 'id'},
                {data: 'stock_type_name', name: 'stockItem.stockType.name'},
                {data: 'stock_item_name', name: 'stockItem.name'},
                {data: 'rule_display', name: 'rule_type'},
                {data: 'formula_summary', name: 'base_qty'},
                {data: 'test_calculation', name: 'test_calculation', orderable: false, searchable: false},
                {data: 'actions', name: 'actions', orderable: false, searchable: false}
            ]
        });

        // Load stock items for dropdown
        function loadStockItems() {
            $.ajax({
                url: "{{ route('admin.stationery.stock-items.options') }}",
                type: 'GET',
                success: function(response) {
                    let options = '<option value="">Select Stock Item</option>';
                    response.data.forEach(function(item) {
                        let stockType = item.stock_type ? ' (' + item.stock_type.name + ')' : '';
                        options += '<option value="' + item.id + '">' + item.name + stockType + '</option>';
                    });
                    $('#stock_item_id').html(options);
                    $('#calc_stock_item_id').html(options);
                }
            });
        }

        // Show/hide conditional field
        function toggleConditionalField() {
            if ($('#rule_type').val() === 'conditional') {
                $('.conditional-field').show();
            } else {
                $('.conditional-field').hide();
            }
        }

        $('#rule_type').change(toggleConditionalField);

        // Add Rule
        $('#addRuleBtn').click(function() {
            $('#ruleForm')[0].reset();
            $('#rule_id').val('');
            $('#ruleModalTitle').text('Add Allocation Rule');
            $('.form-control').removeClass('is-invalid');
            $('.invalid-feedback').text('');
            $('#ruleForm').attr('action', '{{ route("admin.stationery.component-stock.store", $component->id) }}');
            
            loadStockItems();
            toggleConditionalField();
            
            $('#ruleModal').modal('show');
        });

        // Edit Rule
        $(document).on('click', '.edit-rule-btn', function(e) {
            e.preventDefault();
            let id = $(this).data('id');
            let url = $(this).data('url');
            
            // Get the edit URL
            let editUrl = '{{ route("admin.stationery.component-stock.edit", [$component->id, ":id"]) }}';
            editUrl = editUrl.replace(':id', id);

            $.ajax({
                url: editUrl,
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        let rule = response.data;
                        
                        // Load stock items first
                        loadStockItems();
                        
                        setTimeout(function() {
                            $('#rule_id').val(rule.id);
                            $('#stock_item_id').val(rule.stock_item_id);
                            $('#rule_type').val(rule.rule_type);
                            $('#base_quantity').val(rule.base_qty);
                            $('#multiplier').val(rule.multiplier);
                            $('#extras_fixed').val(rule.extras_fixed || 0);
                            $('#extras_percent').val(rule.extras_percentage || 0);
                            $('#extras_per_candidate').val(rule.extras_per_candidate || 0);
                            $('#condition_value').val(0);
                            
                            $('#ruleModalTitle').text('Edit Allocation Rule');
                            $('#ruleForm').attr('action', url);
                            $('.form-control').removeClass('is-invalid');
                            $('.invalid-feedback').text('');
                            
                            toggleConditionalField();
                            $('#ruleModal').modal('show');
                        }, 300);
                    }
                },
                error: function(xhr) {
                    toastr.error(xhr.responseJSON?.message || 'Error loading rule data');
                }
            });
        });

        // Submit Rule
        $('#ruleForm').submit(function(e) {
            e.preventDefault();
            let id = $('#rule_id').val();
            let method = id ? 'PUT' : 'POST';
            let url = $('#ruleForm').attr('action');
            
            $('.form-control').removeClass('is-invalid');
            $('.invalid-feedback').text('');
            
            $.ajax({
                url: url,
                type: method,
                data: $(this).serialize(),
                success: function(response) {
                    $('#ruleModal').modal('hide');
                    table.ajax.reload();
                    toastr.success(response.message);
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, value) {
                            $('#' + key).addClass('is-invalid');
                            $('#' + key).siblings('.invalid-feedback').text(value[0]);
                        });
                    } else {
                        toastr.error(xhr.responseJSON?.message || 'Error saving allocation rule');
                    }
                }
            });
        });

        // Delete Rule
        $(document).on('click', '.delete-rule-btn', function(e) {
            e.preventDefault();
            
            if (!confirm('Are you sure you want to delete this allocation rule?')) {
                return;
            }
            
            let url = $(this).data('url');
            $.ajax({
                url: url,
                type: 'DELETE',
                success: function(response) {
                    table.ajax.reload();
                    toastr.success(response.message);
                },
                error: function(xhr) {
                    toastr.error(xhr.responseJSON?.message || 'Error deleting allocation rule');
                }
            });
        });

        // Test Calculator
        $('#testCalculatorBtn').click(function() {
            loadStockItems();
            $('#calc_result').hide();
            $('#calculatorModal').modal('show');
        });

        // Calculate Test
        $('#calculateTestBtn').click(function() {
            let stockItemId = $('#calc_stock_item_id').val();
            let candidates = $('#calc_candidates').val();
            
            if (!stockItemId || !candidates) {
                toastr.warning('Please select a stock item and enter number of candidates');
                return;
            }
            
            $.ajax({
                url: '{{ route("admin.stationery.component-stock.test", $component->id) }}',
                type: 'POST',
                data: {
                    stock_item_id: stockItemId,
                    candidates: candidates,
                    centers: 1
                },
                success: function(response) {
                    $('#calc_result_qty').text(response.quantity);
                    
                    let breakdownHtml = '<ul>';
                    response.breakdown.forEach(function(step) {
                        breakdownHtml += '<li>' + step + '</li>';
                    });
                    breakdownHtml += '</ul>';
                    
                    $('#calc_breakdown').html(breakdownHtml);
                    $('#calc_result').show();
                },
                error: function(xhr) {
                    toastr.error(xhr.responseJSON?.message || 'Error calculating quantity');
                }
            });
        });
    });
</script>
@endpush