@extends('layouts.admin')

@section('title', 'Fun Walk Management')

@section('content')
<!-- MAIN -->
<div class="main">
    <!-- MAIN CONTENT -->
    <div class="main-content">
        <div class="container-fluid">
            <h3 class="page-title">Fun Walk Management</h3>
            <div class="row">
                <div class="col-md-12">
                    <!-- PANEL NO CONTROLS -->
                    <div class="panel">
                        <div class="panel-heading">
                            <h3 class="panel-title">Fun Walk Management</h3>
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
                            
                            <!-- Statistics Cards - Now visible on ALL tabs -->
                            <div class="row mb-4">
                                <div class="col-md-3">
                                    <div class="panel panel-headline">
                                        <div class="panel-body">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <i class="fa fa-users fa-3x text-primary"></i>
                                                </div>
                                                <div class="col-md-9 text-right">
                                                    <h3 class="mb-0" id="total-registrations">0</h3>
                                                    <p class="text-muted">Total Registrations</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="panel panel-headline">
                                        <div class="panel-body">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <i class="fa fa-check-circle fa-3x text-success"></i>
                                                </div>
                                                <div class="col-md-9 text-right">
                                                    <h3 class="mb-0" id="paid-registrations">0</h3>
                                                    <p class="text-muted">Paid</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="panel panel-headline">
                                        <div class="panel-body">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <i class="fa fa-clock-o fa-3x text-warning"></i>
                                                </div>
                                                <div class="col-md-9 text-right">
                                                    <h3 class="mb-0" id="pending-registrations">0</h3>
                                                    <p class="text-muted">Pending Payment</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="panel panel-headline">
                                        <div class="panel-body">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <i class="fa fa-money fa-3x text-info"></i>
                                                </div>
                                                <div class="col-md-9 text-right">
                                                    <h3 class="mb-0" id="total-revenue">M0.00</h3>
                                                    <p class="text-muted">Total Revenue</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="custom-tabs-line tabs-line-bottom left-aligned">
                                <ul class="nav" role="tablist">
                                    <li class="active">
                                        <a href="#fun-walks" role="tab" data-toggle="tab">Fun Walks</a>
                                    </li>
                                    <li>
                                        <a href="#registrations" role="tab" data-toggle="tab">Registrations</a>
                                    </li>
                                    <li>
                                        <a href="#payments" role="tab" data-toggle="tab">Payments</a>
                                    </li>
                                </ul>
                            </div>
                            
                            <div class="tab-content">
                                <!-- Fun Walks Tab -->
                                <div class="tab-pane fade in active" id="fun-walks">
                                    @include('admin.fun-walk-management._fun-walks')
                                </div>

                                <!-- Registrations Tab -->
                                <div class="tab-pane fade" id="registrations">
                                    @include('admin.fun-walk-management._registrations')
                                </div>

                                <!-- Payments Tab -->
                                <div class="tab-pane fade" id="payments">
                                    @include('admin.fun-walk-management._payments')
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- END PANEL NO CONTROLS -->
                </div>
            </div>
        </div>
    </div>
    <!-- END MAIN CONTENT -->
</div>
<!-- END MAIN -->

<!-- Include all modals -->
@include('admin.fun-walk._form')
@include('admin.fun-walk-registration._view-modal')
@include('admin.fun-walk-registration._edit-modal')

@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

<script>
$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Initialize datepicker globally
    $('.datepicker').datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true
    });

    // Track which tables are initialized
    let tablesInitialized = {
        funWalks: false,
        registrations: false,
        payments: false
    };

    // Update statistics - now called on page load to show on all tabs
    function updateStatistics() {
        $.ajax({
            url: "{{ route('admin.fun-walk-registration.statistics') }}",
            type: 'GET',
            success: function(response) {
                $('#total-registrations').text(response.total || 0);
                $('#paid-registrations').text(response.paid || 0);
                $('#pending-registrations').text(response.pending || 0);
                $('#total-revenue').text('M' + parseFloat(response.revenue || 0).toFixed(2));
            },
            error: function(xhr) {
                console.error('Error loading statistics:', xhr);
            }
        });
    }

    // Load statistics on page load
    updateStatistics();

    // Initialize Fun Walks table immediately (first tab)
    initFunWalksTab();
    tablesInitialized.funWalks = true;

    // Initialize tables when tabs are shown
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        let target = $(e.target).attr("href");
        
        if (target === '#registrations' && !tablesInitialized.registrations) {
            initRegistrationsTab();
            tablesInitialized.registrations = true;
        } else if (target === '#payments' && !tablesInitialized.payments) {
            initPaymentsTab();
            tablesInitialized.payments = true;
        }
    });

    // ==================== FUN WALKS TAB ====================
    function initFunWalksTab() {
        let funWalkTable = $('#funWalkTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.fun-walk.index') }}",
            columns: [
                {data: 'id', name: 'id'},
                {data: 'title', name: 'title'},
                {data: 'date', name: 'date'},
                {data: 'location', name: 'location'},
                {data: 'price', name: 'price'},
                {data: 'description', name: 'description'},
                {data: 'status_badge', name: 'status'},
                {
                    data: 'actions',
                    name: 'actions',
                    orderable: false,
                    searchable: false
                }
            ]
        });
        $("#funWalkTable").css("width", "98.5%");

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

        // Open modal for Add
        $('#addFunWalkBtn').click(function() {
            $('#funWalkForm')[0].reset();
            $('#funWalk_id').val('');
            $('#funWalkModalTitle').text('Add Fun Walk');
            $('.form-control').removeClass('is-invalid');
            $('.invalid-feedback').text('');
            $('input[name="status"][value="active"]').prop('checked', true);
            $('#funWalkForm').attr('action', '{{ route("admin.fun-walk.store") }}');
            
            // Re-initialize datepickers after form reset
            $('.datepicker').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true
            });
            
            $('#funWalkModal').modal('show');
        });

        // Open modal for Edit
        $(document).on('click', '.edit-btn', function(e) {
            e.preventDefault();
            let url = $(this).data('url');

            $.ajax({
                url: url,
                type: 'GET',
                success: function(response) {
                    if (response.data) {
                        const funWalk = response.data;
                        fillForm(funWalk, '#funWalkForm');
                        $('#funWalk_id').val(funWalk.id);
                        $('#funWalkModalTitle').text('Edit Fun Walk');
                        $('.form-control').removeClass('is-invalid is-valid');
                        $('.invalid-feedback').text('');
                        $('#funWalkForm').attr('action', response.url);
                        
                        $('#funWalkModal').modal('show');
                    } else {
                        toastr.error('Fun Walk not found');
                    }
                },
                error: function(xhr) {
                    toastr.error('Error loading fun walk data');
                }
            });
        });

        // Submit Add/Edit
        $('#funWalkForm').submit(function(e) {
            e.preventDefault();
            let id = $('#funWalk_id').val();
            let method = id ? 'PUT' : 'POST';
            let url = $('#funWalkForm').attr('action');
            
            $('.form-control').removeClass('is-invalid');
            $('.invalid-feedback').text('');
            
            $.ajax({
                url: url,
                type: method,
                data: $(this).serialize(),
                success: function(response) {
                    $('#funWalkModal').modal('hide');
                    funWalkTable.ajax.reload();
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
                        toastr.error(xhr.responseJSON?.message || 'Error saving fun walk');
                    }
                }        
            });
        });

        // Delete Fun Walk
        $(document).on('click', '.delete-btn', function(e) {
            e.preventDefault();
            
            if (!confirm('Are you sure you want to delete this fun walk?')) {
                return;
            }
            
            let url = $(this).data('url');

            $.ajax({
                url: url,
                type: 'DELETE',
                success: function(response) {
                    funWalkTable.ajax.reload();
                    toastr.success(response.message);
                },
                error: function(xhr) {
                    toastr.error(xhr.responseJSON?.message || 'Error deleting fun walk');
                }
            });
        });
    }

    // ==================== REGISTRATIONS TAB ====================
    function initRegistrationsTab() {
        let registrationTable = $('#funWalkRegistrationTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.fun-walk-registration.index') }}",
            columns: [
                { data: 'id', name: 'id' },
                { data: 'ticket_number', name: 'ticket_number' },
                { data: 'full_name', name: 'full_name' },
                { data: 'fun_walk_title', name: 'fun_walk_title' },
                { data: 'email', name: 'email' },
                { data: 'phone', name: 'phone' },
                { data: 'gender_display', name: 'gender' },
                { data: 'date_of_birth', name: 'date_of_birth' },
                { data: 'created_at', name: 'created_at' },
                {
                    data: 'actions',
                    name: 'actions',
                    orderable: false,
                    searchable: false
                }
            ],
            order: [[0, 'desc']],
            drawCallback: function() {
                updateStatistics();
            }
        });
        $("#funWalkRegistrationTable").css("width", "98.5%");

        // View registration details
        $(document).on('click', '.view-btn', function () {
            const registrationId = $(this).data('id');
            
            $.ajax({
                url: `/admin/fun-walk-registration/${registrationId}`,
                type: 'GET',
                success: function (response) {
                    if (response.success && response.data) {
                        const reg = response.data;
                        
                        $('#view_ticket_number').text(reg.ticket_number || 'N/A');
                        $('#view_full_name').text((reg.first_name || '') + ' ' + (reg.last_name || ''));
                        $('#view_email').text(reg.email || 'N/A');
                        $('#view_phone').text(reg.phone || 'N/A');
                        $('#view_gender').text(reg.gender ? reg.gender.charAt(0).toUpperCase() + reg.gender.slice(1) : 'N/A');
                        $('#view_dob').text(reg.date_of_birth || 'N/A');
                        $('#view_fun_walk').text(reg.fun_walk ? reg.fun_walk.title : 'N/A');
                        $('#view_registered_at').text(reg.created_at ? new Date(reg.created_at).toLocaleString() : 'N/A');
                        
                        // Display QR Code
                        if (reg.qr_path) {
                            const qrUrl = reg.qr_path.startsWith('http') ? reg.qr_path : '/' + reg.qr_path;
                            $('#view_qr_code').html('<img src="' + qrUrl + '" alt="QR Code" style="max-width:200px;">');
                        } else {
                            $('#view_qr_code').html('<p class="text-muted">No QR Code available</p>');
                        }
                        
                        // Display payments
                        if (reg.payments && reg.payments.length > 0) {
                            let paymentsHtml = '<table class="table table-sm table-bordered"><thead><tr><th>Amount</th><th>Method</th><th>Transaction Ref</th><th>Status</th><th>Date</th></tr></thead><tbody>';
                            reg.payments.forEach(function(payment) {
                                paymentsHtml += '<tr>';
                                paymentsHtml += '<td>M' + parseFloat(payment.amount || 0).toFixed(2) + '</td>';
                                paymentsHtml += '<td>' + (payment.payment_method || 'N/A') + '</td>';
                                paymentsHtml += '<td>' + (payment.transaction_ref || 'N/A') + '</td>';
                                paymentsHtml += '<td><span class="label label-' + (payment.status === 'completed' ? 'success' : 'warning') + '">' + (payment.status || 'N/A') + '</span></td>';
                                paymentsHtml += '<td>' + (payment.paid_at ? new Date(payment.paid_at).toLocaleString() : 'N/A') + '</td>';
                                paymentsHtml += '</tr>';
                            });
                            paymentsHtml += '</tbody></table>';
                            $('#view_payments').html(paymentsHtml);
                        } else {
                            $('#view_payments').html('<p class="text-muted">No payments recorded</p>');
                        }
                        
                        $('#viewRegistrationModal').modal('show');
                    }
                },
                error: function (xhr) {
                    console.error('Error loading registration:', xhr);
                    toastr.error('Error loading registration details');
                }
            });
        });

        // Load fun walks for edit modal
        function loadFunWalks() {
            $.ajax({
                url: '/api/fun-walks',
                type: 'GET',
                success: function (data) {
                    let options = '<option value="">Select Fun Walk</option>';
                    if (data && data.length > 0) {
                        data.forEach(function (walk) {
                            options += '<option value="' + walk.id + '">' + walk.title + ' (' + (walk.date || 'N/A') + ')</option>';
                        });
                    }
                    $('#edit_fun_walk_id').html(options);
                },
                error: function (xhr) {
                    console.error('Error loading fun walks:', xhr);
                    toastr.error('Error loading fun walks list');
                }
            });
        }

        // Edit registration
        $(document).on('click', '.edit-btn', function () {
            const registrationId = $(this).data('id');
            
            // Load fun walks first
            loadFunWalks();
            
            $.ajax({
                url: `/admin/fun-walk-registration/${registrationId}/edit`,
                type: 'GET',
                success: function (response) {
                    if (response.success && response.data) {
                        const reg = response.data;
                        
                        $('#edit_registration_id').val(reg.id);
                        $('#edit_fun_walk_id').val(reg.fun_walk_id);
                        $('#edit_first_name').val(reg.first_name || '');
                        $('#edit_last_name').val(reg.last_name || '');
                        $('#edit_email').val(reg.email || '');
                        $('#edit_phone').val(reg.phone || '');
                        $('#edit_gender').val(reg.gender || '');
                        $('#edit_date_of_birth').val(reg.date_of_birth || '');
                        $('#edit_ticket_number').val(reg.ticket_number || '');
                        $('#edit_qr_path').val(reg.qr_path || '');
                        
                        // Clear errors
                        $('.form-control').removeClass('is-invalid');
                        $('.invalid-feedback').text('');
                        
                        // Initialize datepicker
                        $('#edit_date_of_birth').datepicker({
                            format: 'yyyy-mm-dd',
                            autoclose: true,
                            endDate: new Date()
                        });
                        
                        $('#editRegistrationModal').modal('show');
                    }
                },
                error: function (xhr) {
                    console.error('Error loading registration data:', xhr);
                    toastr.error('Error loading registration data');
                }
            });
        });

        // Submit edit form
        $('#editRegistrationForm').submit(function (e) {
            e.preventDefault();
            const registrationId = $('#edit_registration_id').val();
            
            // Clear previous errors
            $('.form-control').removeClass('is-invalid');
            $('.invalid-feedback').text('');
            
            $.ajax({
                url: `/admin/fun-walk-registration/${registrationId}`,
                type: 'PUT',
                data: $(this).serialize(),
                success: function (response) {
                    if (response.success) {
                        $('#editRegistrationModal').modal('hide');
                        registrationTable.ajax.reload();
                        updateStatistics();
                        toastr.success(response.message || 'Registration updated successfully');
                    }
                },
                error: function (xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        $.each(errors, function (key, value) {
                            $('#edit_' + key).addClass('is-invalid');
                            $('#edit_' + key).siblings('.invalid-feedback').text(value[0]);
                        });
                        toastr.error('Please fix the validation errors');
                    } else {
                        console.error('Error updating registration:', xhr);
                        toastr.error('Error updating registration');
                    }
                }
            });
        });

        // Delete registration
        $(document).on('click', '.delete-btn', function () {
            const registrationId = $(this).data('id');
            const ticketNumber = $(this).data('ticket');
            
            swal({
                title: "Are you sure?",
                text: "Delete registration " + ticketNumber + "? This action cannot be undone!",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    $.ajax({
                        url: `/admin/fun-walk-registration/${registrationId}`,
                        type: 'DELETE',
                        success: function (response) {
                            if (response.success) {
                                registrationTable.ajax.reload();
                                updateStatistics();
                                swal("Success!", response.message || "Registration deleted successfully", "success");
                            }
                        },
                        error: function (xhr) {
                            if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.message) {
                                swal("Error!", xhr.responseJSON.message, "error");
                            } else {
                                console.error('Error deleting registration:', xhr);
                                swal("Error!", "Failed to delete registration", "error");
                            }
                        }
                    });
                }
            });
        });

        // Clear modal on close
        $('#editRegistrationModal').on('hidden.bs.modal', function () {
            $('#editRegistrationForm')[0].reset();
            $('.form-control').removeClass('is-invalid');
            $('.invalid-feedback').text('');
        });

        $('#viewRegistrationModal').on('hidden.bs.modal', function () {
            // Clear view modal content
            $('#view_qr_code').html('');
            $('#view_payments').html('');
        });
    }

    // ==================== PAYMENTS TAB ====================
    function initPaymentsTab() {
        let paymentsTable = $('#funWalkPaymentsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.fun-walk-payments.index') }}",
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
        $("#funWalkPaymentsTable").css("width", "98.5%");
    }
});
</script>
@endpush