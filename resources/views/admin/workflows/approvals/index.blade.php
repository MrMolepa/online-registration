@extends('layouts.admin')

@section('content')
    <div class="main">
        <!-- MAIN CONTENT -->
        <div class="main-content">
            <div class="container-fluid">
                <h3 class="page-title">Pending Approvals</h3>
                <div class="row">
                    <div class="col-md-12">
                        <!-- PANEL NO CONTROLS -->
                        <div class="panel">
                            <div class="panel-heading">
                                <h3 class="panel-title">Pending Approvals<b></b></h3>
                            </div>
                            <div class="panel-body">
                                <div class="mb-2">
                                    <a href="{{ route('admin.workflows.approvals.index') }}" class="btn btn-default btn-xs {{ empty(request('all')) ? 'active' : '' }}">My Approvals</a>
                                    <a href="{{ route('admin.workflows.approvals.index', ['all' => 1]) }}" class="btn btn-default btn-xs {{ !empty(request('all')) ? 'active' : '' }}">All Pending</a>
                                </div>
                                <table class="table table-striped" id="approvals-data-table">
                                    <thead>
                                        <tr>
                                            <th>Workflow</th>
                                            <th>Entity</th>
                                            <th>Current Step</th>
                                            <th>Status</th>
                                            <th>Created</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($approvals as $approval)
                                            <tr>
                                                <td>{{ $approval->workflow->name }}</td>
                                                <td>{{ class_basename($approval->entity_type) }} #{{ $approval->entity_id }}</td>
                                                <td>
                                                    @php
                                                        $currentStep = $approval->steps->where('status', 'pending')->first();
                                                    @endphp
                                                    @if($currentStep)
                                                        {{ $currentStep->step->name ?? 'Unknown Step' }}
                                                    @else
                                                        <span class="text-muted">No pending steps</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="label label-{{ $approval->status === 'completed' ? 'success' : ($approval->status === 'rejected' ? 'danger' : 'warning') }}">
                                                        {{ ucfirst($approval->status) }}
                                                    </span>
                                                </td>
                                                <td>{{ $approval->created_at->format('M d, Y H:i') }}</td>
                                                <td>
                                                    <a href="{{ route('admin.workflows.instances.show', $approval) }}" 
                                                       class="btn btn-primary btn-xs" 
                                                       title="View Details">
                                                        <i class="glyphicon glyphicon-eye-open"></i>
                                                    </a>
                                                    <a href="{{ route('admin.workflows.approvals.history', $approval) }}" 
                                                       class="btn btn-info btn-xs" 
                                                       title="View History">
                                                        <i class="glyphicon glyphicon-time"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">
                                                    <em class="text-muted">No pending approvals found.</em>
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

    @push('scripts')
        <script>
            $(document).ready(function() {
                // Initialize DataTable
                $('#approvals-data-table').DataTable({
                    "paging": true,
                    "searching": true,
                    "ordering": true,
                    "info": true,
                    "order": [[3, 'desc']] // Sort by created date, newest first
                });
                
                $("#approvals-data-table").css("width", "98.5%");
            });
        </script>
    @endpush
@endsection