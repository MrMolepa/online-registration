@extends('layouts.admin')

@section('title', 'Fun Walk Registrations')

@section('content')
    <div class="main">
        <!-- MAIN CONTENT -->
        <div class="main-content">
            <div class="container-fluid">
                <h3 class="page-title">Fun Walk Registrations</h3>

                <!-- Statistics Cards -->
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

                <div class="row">
                    <div class="col-md-12">
                        <!-- PANEL -->
                        <div class="panel panel-headline">
                            <div class="panel-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover" id="funWalkRegistrationTable">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Ticket Number</th>
                                                <th>Full Name</th>
                                                <th>Fun Walk</th>
                                                <th>Email</th>
                                                <th>Phone</th>
                                                <th>Gender</th>
                                                <th>Date of Birth</th>
                                                <th>Registered At</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- END PANEL -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END MAIN CONTENT -->

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
    $(document).ready(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Update statistics
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

        // Initial statistics load
        updateStatistics();

        let table = $('#funWalkRegistrationTable').DataTable({
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
                        table.ajax.reload();
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
                                table.ajax.reload();
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
    });
</script>
@endpush