@extends('layouts.admin')

@section('content')
        <div class="main">
            <div class="main-content">
                <div class="container-fluid">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h3>Visitor List</h3>
                        <button class="btn btn-primary" id="btnAddVisitor"data-bs-toggle="modal" data-bs-target="#visitorModal">+ Add Visitor</button>
                    </div>

                    <div class="card p-3 shadow-sm">
                        <table id="visitorTable" class="table table-bordered table-striped w-100">
                            <thead class="table-light">
                                <tr>
                                    <th>Purpose</th>
                                    <th>Meeting With</th>
                                    <th>Visitor Name</th>
                                    <th>Phone</th>
                                    <th>ID Card</th>
                                    <th>Number Of Person</th>
                                    <th>Date</th>
                                    <th>In Time</th>
                                    <th>Out Time</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>

                <!-- ADD / EDIT MODAL -->
                <div class="modal fade" id="visitorModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <form id="visitorForm" method="POST">
                            @csrf
                            <input type="hidden" id="visitor_id" name="visitor_id" value="">
                            
                            <div class="modal-header">
                                <h5 class="modal-title">Add Visitor</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>

                            <div class="modal-body row g-3">
                                

                                <div class="col-md-6">
                                    <label class="form-label">Purpose</label>
                                    <input type="text" name="purpose" id="purpose" class="form-control" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Meeting With</label>
                                    <input type="text" name="meeting_with" id="meeting_with" class="form-control" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Visitor Name</label>
                                    <input type="text" name="visitor_name" id="visitor_name" class="form-control" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Phone</label>
                                    <input type="text" name="phone" id="phone" class="form-control" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">ID Card</label>
                                    <input type="text" name="id_card" id="id_card" class="form-control">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Number Of Person</label>
                                    <input type="number" name="number_of_person" id="number_of_person" class="form-control" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Date</label>
                                    <input type="date" name="date" id="date" class="form-control" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">In Time</label>
                                    <input type="time" name="in_time" id="in_time" class="form-control" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Out Time</label>
                                    <input type="time" name="out_time" id="out_time" class="form-control">
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="submit" class="btn btn-success">Save</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
                
                
            </div>

            
            
            
    
        </div>
            
               
            
        </div>
       
        @push('styles')
        <!-- Bootstrap 5 CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- DataTables CSS -->
        <link href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css" rel="stylesheet">
        @endpush


        @push('scripts')
                <!-- Make sure Bootstrap JS & DataTables JS are loaded -->
                <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
                <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
                <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>

                <script>
                    $(function () {
    // Initialize modal
    const modalElement = document.getElementById('visitorModal');
    const modal = new bootstrap.Modal(modalElement);
    const form = $('#visitorForm');

    // Ensure modal is not trapped inside a container
    $('#visitorModal').appendTo('body');

    // Initialize DataTable
    const table = $('#visitorTable').DataTable({
        ajax: '/admin/visitors/data', // Adjust to your data route
        columns: [
            { data: 'purpose' },
            { data: 'meeting_with' },
            { data: 'visitor_name' },
            { data: 'phone' },
            { data: 'id_card' },
            { data: 'number_of_person' },
            { data: 'date' },
            { data: 'in_time' },
            { data: 'out_time' },
            {
                data: 'id',
                render: id => `
                    <button class="btn btn-sm btn-warning btnEdit" data-id="${id}">✏️</button>
                    <button class="btn btn-sm btn-danger btnDelete" data-id="${id}">🗑️</button>
                `,
                orderable: false,
                searchable: false
            }
        ]
    });

    // ----------------------------
    // ADD VISITOR
    // ----------------------------
    $('#btnAddVisitor').on('click', function () {
        form[0].reset();
        $('#visitor_id').val('');
        $('.modal-title').text('Add Visitor');
        modal.show();
    });

    // ----------------------------
    // EDIT VISITOR
    // ----------------------------
    $(document).on('click', '.btnEdit', function () {
    const id = $(this).data('id');

    // Make sure the modal form is reset before filling
    $('#visitorForm')[0].reset();
    $('#visitor_id').val(''); // clear old ID

    // Fetch visitor data via AJAX
    $.ajax({
        url: `/admin/visitors/${id}/edit`, // GET route returns JSON
        type: 'GET',
        success: function(data) {
            // Populate form fields dynamically
            for (let key in data) {
                const field = $(`#${key}`);
                if (field.length) {
                    field.val(data[key]);
                }
            }

            // Set hidden visitor ID
            $('#visitor_id').val(id);

            // Update modal title and show it
            $('.modal-title').text('Edit Visitor');
            modal.show();
        },
        error: function() {
            alert('Error fetching visitor data.');
        }
    });
});

    // ----------------------------
    // SAVE VISITOR (Add or Update)
    // ----------------------------
    form.on('submit', function (e) {
        e.preventDefault();

        const id = $('#visitor_id').val();
        const url = id ? `/admin/visitors/${id}` : `/admin/visitors/store`;
        const type = id ? 'PUT' : 'POST';

        $.ajax({
            url: url,
            type: type,
            data: form.serialize(),
            success: () => {
                modal.hide();
                table.ajax.reload();
            },
            error: () => {
                modal.hide();
                table.ajax.reload();
            }
        });
    });

    // ----------------------------
    // DELETE VISITOR
    // ----------------------------
    $(document).on('click', '.btnDelete', function () {
        if (!confirm('Delete this visitor?')) return;

        const id = $(this).data('id');

        $.ajax({
            url: `/admin/visitors/${id}`,
            type: 'DELETE',
            data: { _token: '{{ csrf_token() }}' },
            success: () => table.ajax.reload(),
            error: () => alert('Error deleting visitor.')
        });
    });
});

                </script>
        @endpush
@endsection
