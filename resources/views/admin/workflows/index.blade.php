@extends('layouts.admin')

@section('content')
    <div class="main">
        <!-- MAIN CONTENT -->
        <div class="main-content">
            <div class="container-fluid">
                <h3 class="page-title">Workflow Management</h3>
                <div class="row">
                    <div class="col-md-12">
                        <!-- PANEL NO CONTROLS -->
                        <div class="panel">
                            <div class="panel-heading">
                                <h3 class="panel-title">Workflows<b></b></h3>
                            </div>
                            <div class="panel-body">
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#createWorkflowModal">
                                    Create Workflow
                                </button>
                                <table class="table table-striped" id="workflows-data-table">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Entity Type</th>
                                            <th>Steps</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($workflows as $workflow)
                                            <tr>
                                                <td>{{ $workflow->name }}</td>
                                                <td>{{ class_basename($workflow->entity_type) }}</td>
                                                <td>{{ $workflow->steps_count }} steps</td>
                                                <td>
                                                    <span class="label label-{{ $workflow->is_active ? 'success' : 'default' }}">
                                                        {{ $workflow->is_active ? 'Active' : 'Inactive' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="{{ route('admin.workflows.steps', $workflow) }}"
                                                       class="btn btn-primary btn-xs"
                                                       title="Manage Steps">
                                                        <i class="glyphicon glyphicon-list"></i>
                                                    </a>
                                                    <a href="#"
                                                       class="btn btn-warning btn-xs edit-workflow"
                                                       data-edit-url="{{ route('admin.workflows.edit', $workflow) }}"
                                                       data-workflow-id="{{ $workflow->id }}"
                                                       title="Edit">
                                                        <i class="glyphicon glyphicon-pencil"></i>
                                                    </a>
                                                    <a href="{{ route('admin.workflows.destroy', $workflow) }}"
                                                       class="btn btn-danger btn-xs delete-workflow"
                                                       title="Delete"
                                                       data-workflow-id="{{ $workflow->id }}">
                                                        <i class="glyphicon glyphicon-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">
                                                    <em class="text-muted">No workflows found.</em>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- END PANEL NO CONTROLS -->
                    </div>
                </div>
            </div>
        </div>
        <!-- END MAIN CONTENT -->
    </div>

    <!-- Create Workflow Modal -->
    <div class="modal fade" id="createWorkflowModal" tabindex="-1" role="dialog" aria-labelledby="createWorkflowModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close resetform" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="createWorkflowModalLabel">Create Workflow</h4>
                </div>
                <div class="modal-body">
                    <form id="workflow-create-form" method="POST" action="{{ route('admin.workflows.store') }}">
                        @include('admin.workflows._form', [
                            'workflow' => new \App\Models\Workflow(),
                            'entityTypes' => $entityTypes ?? [],
                            'roles' => $roles ?? [],
                        ])
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="save-workflow">Save Workflow</button>
                    <button type="button" class="btn btn-danger resetform" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Workflow Modal -->
    <div class="modal fade" id="editWorkflowModal" tabindex="-1" role="dialog" aria-labelledby="editWorkflowModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close resetform" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="editWorkflowModalLabel">Edit Workflow</h4>
                </div>
                <div class="modal-body" id="edit-workflow-modal-body">
                    <div class="text-center" id="edit-modal-loader" style="display:none;">
                        <span class="label label-default">Loading...</span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="update-workflow">Update Workflow</button>
                    <button type="button" class="btn btn-danger resetform" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                // Initialize DataTable if needed
                $('#workflows-data-table').DataTable({
                    "paging": true,
                    "searching": true,
                    "ordering": true,
                    "info": true
                });

                $("#workflows-data-table").css("width", "98.5%");

                // Delete Workflow
                $(document).on('click', '.delete-workflow', function(ev) {
                    ev.preventDefault();
                    var url = $(this).attr('href');

                    if (!confirm("Are you sure you want to delete this workflow?")) {
                        return;
                    }

                    $.ajax({
                        type: "POST",
                        url: url,
                        data: { _method: 'DELETE' },
                        headers: { 'X-Requested-With': 'XMLHttpRequest' },
                        dataType: 'json',
                        success: function(data) {
                            toastr.success(data.success || 'Workflow deleted successfully');

                        },
                        error: function(xhr) {
                            var msg = 'Error deleting workflow';
                            try {
                                var res = xhr && xhr.responseJSON;
                                if (res && res.message) msg = res.message;
                            } catch (e) {}
                            toastr.error(msg);
                            console.log('Error:', xhr);
                        }
                    });
                });

                // Auto-open modal if redirected from create route or if there are validation errors
                @if(session('openCreateModal') || $errors->any())
                    $('#createWorkflowModal').modal('show');
                @endif

                // Handle Edit button click: load form via AJAX into modal
                $(document).on('click', '.edit-workflow', function(ev) {
                    ev.preventDefault();
                    var url = $(this).data('edit-url');
                    $('#edit-workflow-modal-body').empty();
                    $('#edit-modal-loader').show();
                    $('#editWorkflowModal').modal('show');
                    $.ajax({
                        url: url,
                        method: 'GET',
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    }).done(function(html) {
                        $('#edit-modal-loader').hide();
                        $('#edit-workflow-modal-body').html(html);
                        // Remove any save/cancel buttons from the loaded form
                        $('#edit-workflow-modal-body').find('button[type="submit"], .btn-save, .btn-cancel').remove();
                        if (window.initWorkflowForm) {
                            try { window.initWorkflowForm(); } catch (e) { console.error(e); }
                        }
                    }).fail(function() {
                        $('#edit-modal-loader').hide();
                        $('#editWorkflowModal').modal('hide');
                        toastr.error('Failed to load edit form');
                    });
                });

                // AJAX submit for create and edit forms
                function clearFormErrors(form){
                    form.find('.is-invalid').removeClass('is-invalid');
                    form.find('.invalid-feedback.ajax-error').remove();
                    form.find('.has-error').removeClass('has-error');
                    form.find('.help-block').remove();
                }

                function clearLoadingState(btn){
                    try{
                        btn.prop('disabled', false);
                        btn.find('.spinner-border, i.glyphicon-refresh').remove();
                    }catch(e){ console.error('clearLoadingState error', e); }
                }

                function showValidationErrors(form, errors){
                    Object.keys(errors).forEach(function(field){
                        var selector = '[name="'+field+'"]';
                        if (field.indexOf('.') !== -1){
                            var alt = field.replace(/\.(\d+)\.(\w+)/g, '[$1][$2]').replace(/\./g, '][');
                            selector = '[name="'+field+'"],' + '[name="'+alt+'"]';
                        }
                        var input = form.find(selector).first();
                        if (!input || input.length === 0){
                            input = form.find('[name^="'+field.split('.')[0]+'["]');
                        }
                        if (input && input.length){
                            input.addClass('is-invalid');
                            var $formGroup = input.closest('.form-group');
                            $formGroup.addClass('has-error');
                            var msg = errors[field].join('<br>');
                            input.first().after('<span class="help-block ajax-error">'+msg+'</span>');
                        }
                    });
                }

                function submitWorkflowForm(form, submitBtn) {
                    var url = form.attr('action');
                    var method = (form.find('input[name=_method]').val() || form.attr('method') || 'POST').toUpperCase();

                    clearFormErrors(form);
                    submitBtn.prop('disabled', true).append(' <i class="glyphicon glyphicon-refresh glyphicon-spin"></i>');

                    $.ajax({
                        url: url,
                        method: method,
                        data: form.serialize(),
                        dataType: 'json'
                    }).done(function(resp){
                        clearLoadingState(submitBtn);
                        var message = (resp && resp.success) ? resp.success : 'Workflow saved successfully';
                        toastr.success(message);
                        form.closest('.modal').modal('hide');

                        // Reset form after successful submission
                        form[0].reset();


                    }).fail(function(xhr){
                        clearLoadingState(submitBtn);
                        if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors){
                            showValidationErrors(form, xhr.responseJSON.errors);
                            toastr.error('Please fix the highlighted errors.');
                        } else {
                            var msg = 'An error occurred while saving the workflow.';
                            try { if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message; } catch(e){}
                            toastr.error(msg);
                            console.error(xhr);
                        }
                    });
                }

                // Save workflow button click handler (Create)
                $(document).on('click', '#save-workflow', function(ev){
                    ev.preventDefault();
                    var btn = $(this);
                    var form = $('#workflow-create-form');
                    if (form && form.length) {
                        submitWorkflowForm(form, btn);
                    }
                });

                // Update workflow button click handler (Edit)
                $(document).on('click', '#update-workflow', function(ev){
                    ev.preventDefault();
                    var btn = $(this);
                    var form = $('#workflow-edit-form');
                    if (form && form.length) {
                        submitWorkflowForm(form, btn);
                    }
                });

                // Remove any old submit handlers and prevent form submission
                $(document).on('submit', '#workflow-create-form, #workflow-edit-form', function(ev){
                    ev.preventDefault();
                    return false;
                });

                // Auto-open edit modal for a specific workflow if flagged in session
                @if(session('openEditModalId'))
                    (function(){
                        var id = {{ (int) session('openEditModalId') }};
                        var rowBtn = $("a.edit-workflow[data-workflow-id='"+id+"']");
                        if (rowBtn.length) {
                            rowBtn.trigger('click');
                        }
                    })();
                @endif
            });
        </script>
    @endpush
@endsection
