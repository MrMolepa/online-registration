@extends('layouts.admin')

@section('title', 'Stationery Management')

@section('content')
    <div class="main">
        <div class="main-content">
            <div class="container-fluid">
                <h3 class="page-title">Stationery Management</h3>
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel">
                            <div class="panel-heading">
                            </div>
                            <div class="panel-body">
                                @if (session()->has('success'))
                                    <div class="alert alert-success alert-dismissible" role="alert">
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                        <strong>Success! </strong> {{ session('success') }}
                                    </div>
                                @endif
                                @if (session()->has('error'))
                                    <div class="alert alert-danger alert-dismissible" role="alert">
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                        <strong>Error! </strong> {{ session('error') }}
                                    </div>
                                @endif

                                <div class="custom-tabs-line tabs-line-bottom left-aligned">
                                    <ul class="nav" role="tablist">
                                        <li class="active"><a href="#stock-types-tab" role="tab" data-toggle="tab">Stock
                                                Types</a></li>
                                        <li><a href="#stock-items-tab" role="tab" data-toggle="tab">Stock Items</a></li>
                                        <li><a href="#component-stock-tab" role="tab" data-toggle="tab">Component
                                                Stock</a></li>
                                        <li><a href="#allocation-tab" role="tab" data-toggle="tab">Allocation</a></li>
                                    </ul>
                                </div>

                                <div class="tab-content">
                                    <!-- STOCK TYPES TAB -->
                                    <div class="tab-pane fade in active" id="stock-types-tab">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <button type="button" class="btn btn-primary" id="addStockTypeBtn">
                                                    <i class="fa fa-plus"></i> Add Stock Type
                                                </button>

                                                <div class="mt-3">
                                                    <table class="table table-striped" id="stockTypeTable">
                                                        <thead>
                                                            <tr>
                                                                <th>ID</th>
                                                                <th>Name</th>
                                                                <th>Description</th>
                                                                <th>Status</th>
                                                                <th>Actions</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody></tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- STOCK ITEMS TAB -->
                                    <div class="tab-pane fade" id="stock-items-tab">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <button type="button" class="btn btn-primary" id="addStockItemBtn">
                                                    <i class="fa fa-plus"></i> Add Stock Item
                                                </button>

                                                <div class="mt-3">
                                                    <table class="table table-striped" id="stockItemTable">
                                                        <thead>
                                                            <tr>
                                                                <th>ID</th>
                                                                <th>Stock Type</th>
                                                                <th>Name</th>
                                                                <th>Stock Quantity</th>
                                                                <th>Status</th>
                                                                <th>Created</th>
                                                                <th>Actions</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody></tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- COMPONENT STOCK TAB -->
                                    <div class="tab-pane fade" id="component-stock-tab">
                                        <!-- Filters Section -->
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="panel panel-primary">
                                                    <div class="panel-heading">
                                                        <h4><i class="fa fa-filter"></i> Filter Components</h4>
                                                    </div>
                                                    <div class="panel-body">
                                                        <form id="componentFilterForm">
                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    <div class="form-group">
                                                                        <label for="comp_level">Level</label>
                                                                        <select name="comp_level" id="comp_level"
                                                                            class="form-control select2-comp">
                                                                            <option value="">Select Level</option>
                                                                            @foreach ($levels as $level)
                                                                                <option value="{{ $level->id }}">
                                                                                    {{ $level->description ?? $level->id }}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                                <div class="col-md-4">
                                                                    <div class="form-group">
                                                                        <label for="comp_financial_year">Financial
                                                                            Year</label>
                                                                        <select name="comp_financial_year"
                                                                            id="comp_financial_year"
                                                                            class="form-control select2-comp"disabled>
                                                                            <option value="">-- Select Financial Year
                                                                            </option>
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                                <div class="col-md-4">
                                                                    <div class="form-group">
                                                                        <label for="comp_session_id">Session</label>
                                                                        <select name="comp_session_id" id="comp_session_id"
                                                                            class="form-control select2-comp"disabled>
                                                                            <option value="">-- Select Session --
                                                                            </option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Component Selection Card -->
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="panel panel-primary">
                                                    <div class="panel-heading">
                                                        <h4><i class="fa fa-filter"></i> Select Component</h4>
                                                    </div>
                                                    <div class="panel-body">
                                                        <div class="form-group">
                                                            <label for="component_selector">Choose a component to manage
                                                                its allocation rules</label>
                                                            <select class="form-control" id="component_selector"
                                                                style="width: 100%;">
                                                                <option value="">-- Select a Component --</option>
                                                                @foreach ($components as $comp)
                                                                    @php
                                                                        $paddedSubject = str_pad(
                                                                            $comp->subject_code,
                                                                            4,
                                                                            '0',
                                                                            STR_PAD_LEFT,
                                                                        );
                                                                        $paddedComponent = str_pad(
                                                                            $comp->component_code,
                                                                            2,
                                                                            '0',
                                                                            STR_PAD_LEFT,
                                                                        );
                                                                        $componentKey =
                                                                            $paddedSubject . '-' . $paddedComponent;
                                                                        $subjectName = $comp->subject
                                                                            ? $comp->subject->subject_name
                                                                            : 'N/A';
                                                                    @endphp
                                                                    <option value="{{ $componentKey }}"
                                                                        data-key="{{ $componentKey }}"
                                                                        data-code="{{ $componentKey }}"
                                                                        data-name="{{ $comp->component_name }}"
                                                                        data-subject="{{ $subjectName }}">
                                                                        ({{ $componentKey }})
                                                                        - {{ $comp->component_name }} -
                                                                        {{ $subjectName }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Component Info Card -->
                                        <div class="row" id="componentInfoSection" style="display: none;">
                                            <div class="col-md-12">
                                                <div class="panel panel-info">
                                                    <div class="panel-heading">
                                                        <h4>Component Information</h4>
                                                    </div>
                                                    <div class="panel-body">
                                                        <div class="row">
                                                            <div class="col-md-3">
                                                                <strong>Component Code:</strong><br>
                                                                <span id="display_component_code">-</span>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <strong>Component Name:</strong><br>
                                                                <span id="display_component_name">-</span>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <strong>Subject:</strong><br>
                                                                <span id="display_subject">-</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Allocation Rules Table -->
                                        
                                        <div class="row" id="rulesTableSection" style="display: none;">
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
                                                        <tbody></tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- ALLOCATION TAB -->
                                    <div class="tab-pane fade" id="allocation-tab">
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
                                                                        <label for="level">Level <span
                                                                                class="text-danger">*</span></label>
                                                                        <select name="level" id="level"
                                                                            class="form-control select2" required>
                                                                            <option value="">-- Select Level --
                                                                            </option>
                                                                            @foreach ($levels as $level)
                                                                                <option value="{{ $level->id }}">
                                                                                    {{ $level->description ?? $level->id }}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                                <div class="col-md-3">
                                                                    <div class="form-group">
                                                                        <label for="financial_year">Financial Year <span
                                                                                class="text-danger">*</span></label>
                                                                        <select name="financial_year" id="financial_year"
                                                                            class="form-control select2" required disabled>
                                                                            <option value="">-- Select Financial Year
                                                                                --</option>
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                                <div class="col-md-3">
                                                                    <div class="form-group">
                                                                        <label for="session_id">Session <span
                                                                                class="text-danger">*</span></label>
                                                                        <select name="session_id" id="session_id"
                                                                            class="form-control select2" required disabled>
                                                                            <option value="">-- Select Session --
                                                                            </option>
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                                <div class="col-md-3">
                                                                    <div class="form-group">
                                                                        <label for="center_no">Center <span
                                                                                class="text-danger">*</span></label>
                                                                        <select name="center_no" id="center_no"
                                                                            class="form-control select2" required disabled>
                                                                            <option value="">-- Select Center --
                                                                            </option>
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                                <div class="col-md-12">
                                                                    <div class="form-group">
                                                                        <label for="component_id">Component</label>
                                                                        <select name="component_id" id="component_id"
                                                                            class="form-control select2" disabled>
                                                                            <option value="">-- All Components --
                                                                            </option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <button type="button" class="btn btn-primary"
                                                                id="generateReportBtn">
                                                                <i class="fa fa-calculator"></i> Generate Report
                                                            </button>

                                                            <a href="{{ route('admin.stationery.allocation.view') }}"
                                                                class="btn btn-info">
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
                                                                        <dd><strong id="reportCenterName"></strong> (<span
                                                                                id="reportCenterNo"></span>)</dd>
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

                                                                        <dt id="componentLabel" style="display: none;">
                                                                            Selected Component:</dt>
                                                                        <dd id="reportComponent" style="display: none;">
                                                                        </dd>
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
                                                                    <tbody id="allocationTableBody"></tbody>
                                                                </table>
                                                            </div>

                                                            <button type="button" class="btn btn-success"
                                                                id="saveAllocationBtn">
                                                                <i class="fa fa-save"></i> Save Allocation
                                                            </button>

                                                            <button type="button" class="btn btn-default"
                                                                id="clearReportBtn">
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include all modals -->
    @include('admin.stationery.stock-types._form')
    @include('admin.stationery.stock-items._form')
    @include('admin.stationery.stock-items._view')
    @include('admin.stationery.components._rule_form')
    @include('admin.stationery.components._calculator')

    <!-- Breakdown Modal for Allocation -->
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
        let stockTypeTable = null;
        let stockItemTable = null;
        let componentStockTable = null;
        let currentComponentKey = null;

        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Initialize select2
            $('.select2').select2({
                placeholder: 'Select an option',
                allowClear: true,
                width: '100%'
            });

            // Toaster setup
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

            // Tab change event - initialize tables on first view
            $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
                let target = $(e.target).attr("href");

                if (target === '#stock-types-tab' && !stockTypeTable) {
                    initStockTypesTable();
                } else if (target === '#stock-items-tab' && !stockItemTable) {
                    initStockItemsTable();
                } else if (target === '#component-stock-tab') {
                    if (componentStockTable) {
                        componentStockTable.columns.adjust();
                    }
                }
            });

            // Initialize first tab table
            initStockTypesTable();

            // Helper function to fill form fields
            function fillForm(data, formId) {
                const form = $(formId);
                $.each(data, function(key, value) {
                    let field = $('[name="' + key + '"]');

                    if (field.is(':checkbox')) {
                        field.prop('checked', !!value);
                    } else if (field.is(':radio')) {
                        $('input[name="' + key + '"][value="' + value + '"]').prop('checked', true);
                    } else {
                        field.val(value);
                    }
                });
            }

            // ========== STOCK TYPES TAB =============

            function initStockTypesTable() {
                stockTypeTable = $('#stockTypeTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('admin.stationery.stock-types.index') }}",
                    columns: [{
                            data: 'id',
                            name: 'id'
                        },
                        {
                            data: 'name',
                            name: 'name'
                        },
                        {
                            data: 'description',
                            name: 'description'
                        },
                        {
                            data: 'status_badge',
                            name: 'is_active'
                        },
                        {
                            data: 'actions',
                            name: 'actions',
                            orderable: false,
                            searchable: false
                        }
                    ]
                });
                $("#stockTypeTable").css("width", "98.5%");
            }

            $('#addStockTypeBtn').click(function() {
                $('#stockTypeForm')[0].reset();
                $('#stock_type_id').val('');
                $('#stockTypeModalTitle').text('Add Stock Type');
                $('.form-control').removeClass('is-invalid');
                $('.invalid-feedback').text('');
                $('#stockTypeForm').attr('action', '{{ route('admin.stationery.stock-types.store') }}');
                $('#is_active').prop('checked', true);
                $('#stockTypeModal').modal('show');
            });

            $(document).on('click', '#stockTypeTable .edit-btn', function(e) {
                e.preventDefault();
                let url = $(this).data('url');

                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        if (response.data) {
                            const stockType = response.data;
                            fillForm(stockType, '#stockTypeForm');
                            $('#stock_type_id').val(stockType.id);
                            $('#stockTypeModalTitle').text('Edit Stock Type');
                            $('.form-control').removeClass('is-invalid is-valid');
                            $('.invalid-feedback').text('');
                            $('#stockTypeForm').attr('action', response.url);
                            $('#stockTypeModal').modal('show');
                        } else {
                            toastr.error('Stock type not found');
                        }
                    },
                    error: function(xhr) {
                        toastr.error('Error loading stock type data');
                    }
                });
            });

            $('#stockTypeForm').submit(function(e) {
                e.preventDefault();
                let id = $('#stock_type_id').val();
                let method = id ? 'PUT' : 'POST';
                let url = $('#stockTypeForm').attr('action');

                $('.form-control').removeClass('is-invalid');
                $('.invalid-feedback').text('');

                $.ajax({
                    url: url,
                    type: method,
                    data: $(this).serialize(),
                    success: function(response) {
                        $('#stockTypeModal').modal('hide');
                        stockTypeTable.ajax.reload();
                        toastr.success(response.message);
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                $('#' + key).addClass('is-invalid');
                                $('#' + key).siblings('.invalid-feedback').text(value[
                                    0]);
                            });
                        } else {
                            toastr.error(xhr.responseJSON?.message ||
                                'Error saving stock type');
                        }
                    }
                });
            });

            $(document).on('click', '#stockTypeTable .delete-btn', function(e) {
                e.preventDefault();

                if (!confirm('Are you sure you want to delete this stock type?')) {
                    return;
                }

                let url = $(this).data('url');
                $.ajax({
                    url: url,
                    type: 'DELETE',
                    success: function(response) {
                        stockTypeTable.ajax.reload();
                        toastr.success(response.message);
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON?.message || 'Error deleting stock type');
                    }
                });
            });

            // ========== STOCK ITEMS TAB - COMPLETE CODE ==========
            let stockItemTable;

            // Load stock types function 
            function loadStockTypes(callback) {
                $.ajax({
                    url: "{{ route('admin.stationery.stock-types.options') }}",
                    type: 'GET',
                    success: function(response) {
                        console.log('Stock Types loaded:', response.data.length);

                        // Force find dropdown within the modal
                        let $dropdown = $('#stockItemModal').find('#stock_type_id');

                        if ($dropdown.length === 0) {
                            $dropdown = $('#stock_type_id');
                        }

                        console.log('Dropdown exists:', $dropdown.length > 0);
                        console.log('Dropdown is visible:', $dropdown.is(':visible'));
                        console.log('Dropdown HTML before:', $dropdown.html());

                        $dropdown.empty();
                        $dropdown.append('<option value="">Select Stock Type</option>');

                        if (response.data && response.data.length > 0) {
                            response.data.forEach(function(stockType) {
                                $dropdown.append('<option value="' + stockType.id + '">' +
                                    stockType.name + '</option>');
                            });
                            console.log('Dropdown populated with', response.data.length, 'options');
                            console.log('Dropdown HTML after:', $dropdown.html().substring(0, 200));
                        }

                        if (callback) callback();
                    },
                    error: function(xhr) {
                        console.error('Error loading stock types:', xhr);
                        toastr.error('Error loading stock types');
                    }
                });
            }
            // Helper function to fill form fields
            function fillStockItemForm(data, formId) {
                const form = $(formId);
                $.each(data, function(key, value) {
                    let field = form.find('[name="' + key + '"]');

                    if (field.is(':checkbox')) {
                        field.prop('checked', !!value);
                    } else if (field.is(':radio')) {
                        form.find('input[name="' + key + '"][value="' + value + '"]').prop('checked', true);
                    } else {
                        field.val(value);
                    }
                });
            }

            // Initialize DataTable
            function initStockItemsTable() {
                if (stockItemTable) {
                    stockItemTable.destroy();
                }

                stockItemTable = $('#stockItemTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('admin.stationery.stock-items.index') }}",
                        type: 'GET',
                        error: function(xhr, error, code) {
                            console.log('DataTables Error:', xhr, error, code);
                            toastr.error('Error loading data. Please refresh the page.');
                        }
                    },
                    columns: [{
                            data: 'id',
                            name: 'id',
                            width: '5%'
                        },
                        {
                            data: 'stock_type_name',
                            name: 'stockType.name',
                            width: '18%'
                        },
                        {
                            data: 'name',
                            name: 'name',
                            width: '25%'
                        },
                        {
                            data: 'stock_display',
                            name: 'stock_qty',
                            width: '15%',
                            orderable: true,
                            searchable: false
                        },
                        {
                            data: 'status_badge',
                            name: 'is_active',
                            width: '10%'
                        },
                        {
                            data: 'created_at',
                            name: 'created_at',
                            width: '12%'
                        },
                        {
                            data: 'actions',
                            name: 'actions',
                            orderable: false,
                            searchable: false,
                            width: '15%'
                        }
                    ],
                    order: [
                        [0, 'desc']
                    ],
                    pageLength: 10,
                    responsive: true
                });

                console.log('Stock Items DataTable initialized');
            }

            $(document).ready(function() {

                // ========== ADD STOCK ITEM BUTTON ==========
                $('#addStockItemBtn').on('click', function() {
                    console.log('Add Stock Item clicked');

            
                    $('#stock_item_id').val('');
                    $('#stockItemModalTitle').text('Add Stock Item');
                    $('.form-control').removeClass('is-invalid');
                    $('.invalid-feedback').text('');
                    $('#stockItemForm').attr('action',
                        '{{ route('admin.stationery.stock-items.store') }}');

                    // Show modal FIRST
                    $('#stockItemModal').modal('show');
                });

                // ========== EDIT STOCK ITEM BUTTON ==========
                $(document).on('click', '#stockItemTable .edit-btn', function(e) {
                    e.preventDefault();
                    console.log('Edit button clicked');

                    let url = $(this).data('url');

                    $.ajax({
                        url: url,
                        type: 'GET',
                        success: function(response) {
                            console.log('Stock item data received:', response);

                            if (response.data) {
                                const stockItem = response.data;

                                // Set form metadata
                                $('#stock_item_id').val(stockItem.id);
                                $('#stockItemModalTitle').text('Edit Stock Item');
                                $('.form-control').removeClass('is-invalid is-valid');
                                $('.invalid-feedback').text('');
                                $('#stockItemForm').attr('action', response.url);
                                $('#stockItemModal').modal('show');

                                // Wait for modal to be shown, then populate
                                $('#stockItemModal').one('shown.bs.modal', function() {
                                    setTimeout(function() {
                                        fillStockItemForm(stockItem,
                                            '#stockItemForm');
                                        console.log(
                                            'Form populated with stock item data'
                                        );
                                    }, 500);
                                });
                            } else {
                                toastr.error('Stock item not found');
                            }
                        },
                        error: function(xhr) {
                            console.error('Error loading stock item:', xhr);
                            toastr.error('Error loading stock item data');
                        }
                    });
                });

                // ========== VIEW STOCK ITEM BUTTON ==========
                $(document).on('click', '#stockItemTable .view-btn', function(e) {
                    e.preventDefault();
                    console.log('View button clicked');

                    let url = $(this).data('url');

                    $.ajax({
                        url: url,
                        type: 'GET',
                        success: function(response) {
                            if (response.data) {
                                const stockItem = response.data;

                                // Populate view modal
                                $('#view_name').text(stockItem.name || 'N/A');
                                $('#view_stock_type').text(stockItem.stock_type ?
                                    stockItem.stock_type.name : 'N/A');
                                $('#view_unit').text(stockItem.unit || 'N/A');
                                $('#view_stock_qty').text(Number(stockItem.stock_qty)
                                    .toFixed(2) + ' ' + stockItem.unit);
                                $('#view_supplier_info').text(stockItem.supplier_info ||
                                    'N/A');
                                $('#view_status').html(stockItem.is_active ?
                                    '<span class="label label-success">Active</span>' :
                                    '<span class="label label-danger">Inactive</span>'
                                );
                                $('#view_created_at').text(new Date(stockItem
                                    .created_at).toLocaleString());
                                $('#view_updated_at').text(new Date(stockItem
                                    .updated_at).toLocaleString());

                                // Populate linked components
                                let componentsList = $('#view_components');
                                componentsList.empty();

                                if (stockItem.component_stocks && stockItem
                                    .component_stocks.length > 0) {
                                    stockItem.component_stocks.forEach(function(cs) {
                                        if (cs.component) {
                                            componentsList.append('<li>' + cs
                                                .component.name + ' (' + cs
                                                .component.code + ')</li>');
                                        }
                                    });
                                } else {
                                    componentsList.append(
                                        '<li class="text-muted">Not linked to any components</li>'
                                    );
                                }

                                $('#viewStockItemModal').modal('show');
                            } else {
                                toastr.error('Stock item not found');
                            }
                        },
                        error: function(xhr) {
                            console.error('Error loading stock item:', xhr);
                            toastr.error('Error loading stock item data');
                        }
                    });
                });

                // ========== SUBMIT STOCK ITEM FORM ==========
                $(document).on('submit', '#stockItemForm', function(e) {
                    e.preventDefault();
                    console.log('Form submitted');

                    let id = $('#stock_item_id').val();
                    let method = id ? 'PUT' : 'POST';
                    let url = $(this).attr('action');

                    // Clear previous validation errors
                    $('.form-control').removeClass('is-invalid');
                    $('.invalid-feedback').text('');

                    $.ajax({
                        url: url,
                        type: method,
                        data: $(this).serialize(),
                        success: function(response) {
                            console.log('Save successful:', response);
                            $('#stockItemModal').modal('hide');

                            if (stockItemTable) {
                                stockItemTable.ajax.reload();
                            }

                            toastr.success(response.message ||
                                'Stock item saved successfully');
                        },
                        error: function(xhr) {
                            console.error('Save error:', xhr);

                            if (xhr.status === 422) {
                                // Validation errors
                                let errors = xhr.responseJSON.errors;
                                $.each(errors, function(key, value) {
                                    let field = $('#' + key);
                                    field.addClass('is-invalid');
                                    field.siblings('.invalid-feedback').text(
                                        value[0]);
                                });
                                toastr.error('Please fix the validation errors');
                            } else {
                                toastr.error(xhr.responseJSON?.message ||
                                    'Error saving stock item');
                            }
                        }
                    });
                });

                // ========== DELETE STOCK ITEM BUTTON ==========
                $(document).on('click', '#stockItemTable .delete-btn', function(e) {
                    e.preventDefault();
                    console.log('Delete button clicked');

                    if (!confirm('Are you sure you want to delete this stock item?')) {
                        return;
                    }

                    let url = $(this).data('url');

                    $.ajax({
                        url: url,
                        type: 'DELETE',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            console.log('Delete successful:', response);

                            if (stockItemTable) {
                                stockItemTable.ajax.reload();
                            }

                            toastr.success(response.message ||
                                'Stock item deleted successfully');
                        },
                        error: function(xhr) {
                            console.error('Delete error:', xhr);
                            toastr.error(xhr.responseJSON?.message ||
                                'Error deleting stock item');
                        }
                    });
                });

                // ========== MODAL SHOWN EVENT =============
                $('#stockItemModal').on('shown.bs.modal', function(e) {
                    console.log('Modal shown event triggered');

                    // NOW reset and populate after modal is fully shown
                    let isEditMode = $('#stock_item_id').val() !== '';

                    if (!isEditMode) {
                        // Add mode - reset form
                        $('#stockItemForm')[0].reset();
                        $('#is_active').prop('checked', true);
                    }

                    // Load stock types
                    loadStockTypes();
                });

                // ========== CLEAR VALIDATION ON MODAL HIDE ==========
                $('#stockItemModal').on('hidden.bs.modal', function() {
                    console.log('Modal hidden, clearing form');
                    // Don't reset here - it will be reset when modal opens again
                    $('.form-control').removeClass('is-invalid is-valid');
                    $('.invalid-feedback').text('');
                });

            });



            // ========== COMPONENT STOCK TAB ==========
            $('#component_selector').select2({
                placeholder: '-- Select a Component --',
                allowClear: true
            });


            // ========== COMPLETE COMPONENT STOCK TAB IMPLEMENTATION ==========

            // Initialize select2 for component filters
            $('.select2-comp').select2({
                placeholder: 'Select an option',
                allowClear: true,
                width: '100%'
            });

            // Component filter: Level change event
            $('#comp_level').on('change', function() {
                const selectedLevel = $(this).val();

                // Reset dependent dropdowns
                $('#comp_financial_year').prop('disabled', true).html(
                    '<option value="">-- Select Financial Year --</option>');
                $('#comp_session_id').prop('disabled', true).html(
                    '<option value="">-- Select Session --</option>');
                $('#component_selector').html('<option value="">-- Select a Component --</option>');

                // Hide component info and rules sections
                $('#componentInfoSection').hide();
                $('#rulesTableSection').hide();
                currentComponentKey = null;

                if (!selectedLevel) {
                    return;
                }

                // Enable financial year dropdown and populate it
                $('#comp_financial_year').prop('disabled', false);
                let fyOptions = '<option value="">-- Select Financial Year --</option>';
                allFinancialYears.forEach(fy => {
                    fyOptions += `<option value="${fy}">${fy}</option>`;
                });
                $('#comp_financial_year').html(fyOptions);
            });

            // Component filter: Financial Year change event
            $('#comp_financial_year').on('change', function() {
                const selectedLevel = $('#comp_level').val();
                const selectedFinancialYear = $(this).val();

                // Reset dependent dropdowns
                $('#comp_session_id').prop('disabled', true).html(
                    '<option value="">-- Select Session --</option>');
                $('#component_selector').html('<option value="">-- Select a Component --</option>');

                // Hide component info and rules sections
                $('#componentInfoSection').hide();
                $('#rulesTableSection').hide();
                currentComponentKey = null;

                if (!selectedFinancialYear) {
                    return;
                }

                // Enable session dropdown
                $('#comp_session_id').prop('disabled', false);
                loadComponentSessionsByFilters(selectedLevel, selectedFinancialYear);
            });

            // Component filter: Session change event
            $('#comp_session_id').on('change', function() {
                const selectedLevel = $('#comp_level').val();
                const selectedFinancialYear = $('#comp_financial_year').val();
                const selectedSession = $(this).val();

                // Reset component selector
                $('#component_selector').html('<option value="">-- Select a Component --</option>');

                // Hide component info and rules sections
                $('#componentInfoSection').hide();
                $('#rulesTableSection').hide();
                currentComponentKey = null;

                if (!selectedSession) {
                    return;
                }

                // Load filtered components
                loadFilteredComponents(selectedLevel, selectedFinancialYear, selectedSession);
            });

            // Function to load sessions based on level and financial year
            function loadComponentSessionsByFilters(level, financialYear) {
                $.ajax({
                    url: "{{ route('admin.stationery.component-stock.sessions-by-filters') }}",
                    method: 'GET',
                    data: {
                        level: level,
                        financial_year: financialYear
                    },
                    success: function(response) {
                        console.log('Sessions response:', response);
                        if (response.success) {
                            let sessionOptions = '<option value="">-- Select Session --</option>';
                            response.sessions.forEach(session => {
                                sessionOptions +=
                                    `<option value="${session.id}">${session.session}</option>`;
                            });
                            $('#comp_session_id').html(sessionOptions);
                        }
                    },
                    error: function(xhr) {
                        console.error('Error loading sessions:', xhr);
                        toastr.error('Error loading sessions');
                    }
                });
            }

            // Function to load components based on filters
            function loadFilteredComponents(level, financialYear, sessionId) {
                $.ajax({
                    url: "{{ route('admin.stationery.component-stock.components-by-filters') }}",
                    method: 'GET',
                    data: {
                        level: level,
                        financial_year: financialYear,
                        session_id: sessionId
                    },
                    success: function(response) {
                        console.log('Components response:', response);

                        if (response.success) {
                            let componentOptions = '<option value="">-- Select a Component --</option>';

                            if (response.components && response.components.length > 0) {
                                response.components.forEach(comp => {
                                    const paddedSubject = String(comp.subject_code).padStart(4,
                                        '0');
                                    const paddedComponent = String(comp.component_code)
                                        .padStart(2, '0');
                                    const componentKey = paddedSubject + '-' + paddedComponent;
                                    const subjectName = comp.subject_name || 'N/A';

                                    componentOptions += `<option value="${componentKey}" 
                            data-key="${componentKey}"
                            data-code="${componentKey}"
                            data-name="${comp.component_name}"
                            data-subject="${subjectName}">
                            (${componentKey}) - ${comp.component_name} - ${subjectName} 
                        </option>`;
                                });

                                toastr.success(`${response.components.length} component(s) found`);
                            } else {
                                toastr.info(
                                    'No components found with candidate registrations for the selected filters'
                                );
                            }

                            $('#component_selector').html(componentOptions);
                        }
                    },
                    error: function(xhr) {
                        console.error('Error loading components:', xhr);
                        toastr.error('Error loading components');
                    }
                });
            }

            // Component selector change event 
            $('#component_selector').on('change', function() {
                currentComponentKey = $(this).val();

                if (currentComponentKey) {
                    const selectedOption = $(this).find('option:selected');

                    $('#display_component_code').text(selectedOption.data('code'));
                    $('#display_component_name').text(selectedOption.data('name'));
                    $('#display_subject').text(selectedOption.data('subject'));

                    $('#componentInfoSection').show();
                    $('#rulesTableSection').show();

                    loadAllocationRules(currentComponentKey);
                } else {
                    $('#componentInfoSection').hide();
                    $('#rulesTableSection').hide();
                    if (componentStockTable) {
                        componentStockTable.clear().draw();
                    }
                }
            });

            // Load allocation rules with proper dynamic parameter handling
            function loadAllocationRules(componentKey) {
                if (componentStockTable) {
                    componentStockTable.destroy();
                }

                componentStockTable = $('#allocationRulesTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('admin.stationery.component-stock.index') }}",
                        data: function(d) {
                            // Use a function to dynamically get the current component key
                            d.component_key = currentComponentKey;
                            return d;
                        }
                    },
                    columns: [{
                            data: 'id',
                            name: 'id'
                        },
                        {
                            data: 'stock_type_name',
                            name: 'stockItem.stockType.name'
                        },
                        {
                            data: 'stock_item_name',
                            name: 'stockItem.name'
                        },
                        {
                            data: 'rule_display',
                            name: 'rule_type'
                        },
                        {
                            data: 'formula_summary',
                            name: 'base_qty'
                        },
                        {
                            data: 'test_calculation',
                            name: 'test_calculation',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'actions',
                            name: 'actions',
                            orderable: false,
                            searchable: false
                        }
                    ]
                });
            }

            function loadStockItems(callback) {
                $.ajax({
                    url: "{{ route('admin.stationery.stock-items.options') }}",
                    type: 'GET',
                    success: function(response) {
                        console.log('Stock Items loaded:', response.data.length);

                        let $dropdown1 = $('#ruleModal').find('#stock_item_id');
                        let $dropdown2 = $('#calculatorModal').find('#calc_stock_item_id');

                        // Fallback to global search if not found in modals
                        if ($dropdown1.length === 0) {
                            $dropdown1 = $('#stock_item_id');
                        }
                        if ($dropdown2.length === 0) {
                            $dropdown2 = $('#calc_stock_item_id');
                        }

                        console.log('Dropdown #stock_item_id exists:', $dropdown1.length > 0);
                        console.log('Dropdown #calc_stock_item_id exists:', $dropdown2.length > 0);
                        console.log('Dropdown #stock_item_id visible:', $dropdown1.is(':visible'));
                        console.log('Dropdown #calc_stock_item_id visible:', $dropdown2.is(':visible'));

                        let options = '<option value="">Select Stock Item</option>';

                        if (response.data && response.data.length > 0) {
                            response.data.forEach(function(item) {
                                let stockType = item.stock_type ? ' (' + item.stock_type.name +
                                    ')' : '';
                                options += '<option value="' + item.id + '">' + item.name +
                                    stockType + '</option>';
                            });
                        }

                        $dropdown1.html(options);
                        $dropdown2.html(options);

                        console.log('Dropdowns populated with', response.data.length, 'options');
                        console.log('Dropdown #stock_item_id HTML after:', $dropdown1.html().substring(
                            0, 100));
                        console.log('Dropdown #calc_stock_item_id HTML after:', $dropdown2.html()
                            .substring(0, 100));

                        if (callback) callback();
                    },
                    error: function(xhr) {
                        console.error('Error loading stock items:', xhr);
                        toastr.error('Error loading stock items');
                    }
                });
            }

            function toggleConditionalField() {
                if ($('#rule_type').val() === 'conditional') {
                    $('.conditional-field').show();
                } else {
                    $('.conditional-field').hide();
                }
            }

            $('#rule_type').change(toggleConditionalField);

            $('#addRuleBtn').click(function() {
                if (!currentComponentKey) {
                    toastr.warning('Please select a component first');
                    return;
                }

                $('#ruleForm')[0].reset();
                $('#rule_id').val('');
                $('#form_component_key').val(currentComponentKey);
                $('#ruleModalTitle').text('Add Allocation Rule');
                $('.form-control').removeClass('is-invalid');
                $('.invalid-feedback').text('');

                toggleConditionalField();

                $('#ruleModal').modal('show');
            });

            // Rule Modal shown event 
            $('#ruleModal').on('shown.bs.modal', function() {
                console.log('Rule modal shown, loading stock items');
                loadStockItems();
            });

            // Edit Rule Button 
            $(document).on('click', '.edit-rule-btn', function(e) {
                e.preventDefault();
                let id = $(this).data('id');

                let editUrl = '{{ route('admin.stationery.component-stock.edit', ':id') }}';
                editUrl = editUrl.replace(':id', id);

                $.ajax({
                    url: editUrl,
                    type: 'GET',
                    success: function(response) {
                        if (response.success) {
                            let rule = response.data;

                            // Set form metadata
                            $('#rule_id').val(rule.id);
                            $('#form_component_key').val(rule.component_id);
                            $('#ruleModalTitle').text('Edit Allocation Rule');
                            $('.form-control').removeClass('is-invalid');
                            $('.invalid-feedback').text('');

                            // Show modal first
                            $('#ruleModal').modal('show');

                            // Wait for modal to be shown, then load stock items and populate
                            $('#ruleModal').one('shown.bs.modal', function() {
                                loadStockItems(function() {
                                    // After stock items are loaded, populate the form
                                    $('#stock_item_id').val(rule.stock_item_id);
                                    $('#rule_type').val(rule.rule_type);
                                    $('#base_quantity').val(rule.base_qty);
                                    $('#multiplier').val(rule.multiplier);
                                    $('#extras_fixed').val(rule.extras_fixed ||
                                        0);
                                    $('#extras_percent').val(rule
                                        .extras_percentage || 0);
                                    $('#extras_per_candidate').val(rule
                                        .extras_per_candidate || 0);
                                    $('#condition_value').val(0);

                                    toggleConditionalField();
                                    console.log(
                                    'Form populated with rule data');
                                });
                            });
                        }
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON?.message || 'Error loading rule data');
                    }
                });
            });

            $('#testCalculatorBtn').click(function() {
                if (!currentComponentKey) {
                    toastr.warning('Please select a component first');
                    return;
                }

                $('#calc_result').hide();
                $('#calculatorModal').modal('show');
            });

            // Calculator Modal shown event 
            $('#calculatorModal').on('shown.bs.modal', function() {
                console.log('Calculator modal shown, loading stock items');
                loadStockItems();
            });

            // Calculate Test Button 
            $('#calculateTestBtn').click(function() {
                if (!currentComponentKey) {
                    toastr.warning('Please select a component first');
                    return;
                }

                let stockItemId = $('#calc_stock_item_id').val();
                let candidates = $('#calc_candidates').val();

                if (!stockItemId || !candidates) {
                    toastr.warning('Please select a stock item and enter number of candidates');
                    return;
                }

                $.ajax({
                    url: "{{ route('admin.stationery.component-stock.test') }}",
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        component_key: currentComponentKey,
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
                        console.error('Calculation error:', xhr);
                        console.error('Response:', xhr.responseText);
                        toastr.error(xhr.responseJSON?.message || 'Error calculating quantity');
                    }
                });
            });

 // ========================================================ALLOCATION TAB ============================================
            $('#level').on('change', function() {
                const selectedLevel = $(this).val();

                $('#financial_year').prop('disabled', true).html(
                    '<option value="">-- Select Financial Year --</option>');
                $('#session_id').prop('disabled', true).html(
                    '<option value="">-- Select Session --</option>');
                $('#center_no').prop('disabled', true).html(
                    '<option value="">-- Select Center --</option>');
                $('#component_id').prop('disabled', true).html(
                    '<option value="">-- All Components --</option>');

                if (!selectedLevel) {
                    return;
                }

                $('#financial_year').prop('disabled', false);
                let fyOptions = '<option value="">-- Select Financial Year --</option>';
                allFinancialYears.forEach(fy => {
                    fyOptions += `<option value="${fy}">${fy}</option>`;
                });
                $('#financial_year').html(fyOptions);
            });

            $('#financial_year').on('change', function() {
                const selectedLevel = $('#level').val();
                const selectedFinancialYear = $(this).val();

                $('#session_id').prop('disabled', true).html(
                    '<option value="">-- Select Session --</option>');
                $('#center_no').prop('disabled', true).html(
                    '<option value="">-- Select Center --</option>');
                $('#component_id').prop('disabled', true).html(
                    '<option value="">-- All Components --</option>');

                if (!selectedFinancialYear) {
                    return;
                }

                $('#session_id').prop('disabled', false);
                loadSessionsByFilters(selectedLevel, selectedFinancialYear);
            });

            $('#session_id').on('change', function() {
                const selectedLevel = $('#level').val();
                const selectedFinancialYear = $('#financial_year').val();
                const selectedSession = $(this).val();

                $('#center_no').prop('disabled', true).html(
                    '<option value="">-- Select Center --</option>');
                $('#component_id').prop('disabled', true).html(
                    '<option value="">-- All Components --</option>');

                if (!selectedSession) {
                    return;
                }

                $('#center_no').prop('disabled', false);
                loadCentersByFilters(selectedLevel, selectedFinancialYear, selectedSession);
            });

            $('#center_no').on('change', function() {
                const selectedLevel = $('#level').val();
                const selectedFinancialYear = $('#financial_year').val();
                const selectedSession = $('#session_id').val();
                const selectedCenter = $(this).val();

                $('#component_id').prop('disabled', true).html(
                    '<option value="">-- All Components --</option>');

                if (!selectedCenter) {
                    return;
                }

                $('#component_id').prop('disabled', false);
                loadComponentsByFilters(selectedLevel, selectedFinancialYear, selectedSession,
                    selectedCenter);
            });

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
                                sessionOptions +=
                                    `<option value="${session.id}">${session.session}</option>`;
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
                                centerOptions +=
                                    `<option value="${center.center_no}">${center.center_no} - ${center.center_name}</option>`;
                            });
                            $('#center_no').html(centerOptions);

                            if (response.centers.length === 0) {
                                toastr.warning(
                                    'No centers found with candidates for this level, session, and financial year'
                                );
                            }
                        }
                    },
                    error: function(xhr) {
                        console.error('Error loading centers:', xhr);
                        toastr.error('Error loading centers');
                    }
                });
            }

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
                                const paddedSubjectCode = String(comp.subject_code).padStart(4,
                                    '0');
                                const paddedComponentCode = String(comp.component_code)
                                    .padStart(2, '0');
                                componentOptions +=
                                    `<option value="${comp.id}">${comp.component_name} (${paddedSubjectCode}-${paddedComponentCode}) - ${comp.subject_name} - ${comp.candidate_count} candidates</option>`;
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

                $('#loadingIndicator').show();
                $('#reportResults').hide();

                $.ajax({
                    url: "{{ route('admin.stationery.allocation.generate') }}",
                    method: 'POST',
                    data: {
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
                    }
                });
            });

            function displayAllocationReport(data) {
                $('#allocationTableBody').empty();

                $('#reportLevel').text(data.level ? data.level.description : '-');
                $('#reportFinancialYear').text(data.session.financial_year);
                $('#reportSession').text(data.session.session);
                $('#reportCenterName').text(data.center.center_name);
                $('#reportCenterNo').text(data.center.center_no);
                $('#reportCandidates').text(data.num_candidates);
                $('#reportComponentRegistrations').text(data.total_component_registrations || '-');
                $('#reportInvigilators').text(data.num_invigilators);

                if (data.component) {
                    const fullCode = String(data.component.subject_code).padStart(4, '0') + '-' + data.component
                        .component_code;
                    $('#reportComponent').text(data.component.component_name + ' (' + fullCode + ')');
                    $('#componentLabel').show();
                    $('#reportComponent').show();
                } else {
                    $('#componentLabel').hide();
                    $('#reportComponent').hide();
                }

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
                    num_candidates: a.component.candidates_registered || 0,
                    breakdown: a.breakdown
                }));

                $.ajax({
                    url: "{{ route('admin.stationery.allocation.save') }}",
                    method: 'POST',
                    data: {
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

            $('#clearReportBtn').on('click', function() {
                $('#reportResults').hide();
                allocationData = null;
                $('#allocationForm')[0].reset();
                $('.select2').val(null).trigger('change');

                $('#session_id').prop('disabled', true).html(
                    '<option value="">-- Select Session --</option>');
                $('#center_no').prop('disabled', true).html(
                    '<option value="">-- Select Center --</option>');
                $('#component_id').prop('disabled', true).html(
                    '<option value="">-- All Components --</option>');
            });
        });
    </script>
@endpush
