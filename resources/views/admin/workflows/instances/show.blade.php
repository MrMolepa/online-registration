@extends('layouts.admin')

@section('content')
    <div class="main">
        <!-- MAIN CONTENT -->
        <div class="main-content">
            <div class="container-fluid">
                <h3 class="page-title">Workflow Instance Details</h3>
                <div class="row">
                    <div class="col-md-12">
                        <!-- PANEL NO CONTROLS -->
                        <div class="panel">
                            <div class="panel-heading">
                                <h3 class="panel-title">Workflow: {{ $instance->workflow->name }}<b></b></h3>
                                <div class="right">
                                    <a href="{{ route('admin.workflows.approvals.index') }}" class="btn btn-default btn-sm">
                                        <i class="glyphicon glyphicon-arrow-left"></i> Back to Approvals
                                    </a>
                                </div>
                            </div>
                            <div class="panel-body">
                                <p class="text-muted">Entity: {{ class_basename($instance->entity_type) }} #{{ $instance->entity_id }}</p>
                                
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <strong>Status:</strong> 
                                            <span class="label label-{{ $instance->status === 'completed' ? 'success' : ($instance->status === 'rejected' ? 'danger' : 'warning') }}">
                                                {{ ucfirst($instance->status) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <strong>Created:</strong> {{ $instance->created_at->format('M d, Y H:i') }}
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <strong>Created By:</strong> {{ $instance->creator->name ?? 'Unknown' }}
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <strong>Last Updated:</strong> {{ $instance->updated_at->format('M d, Y H:i') }}
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                <h4>Workflow Steps Progress</h4>
                                <table class="table table-striped" id="workflow-instance-steps-table">
                                    <thead>
                                        <tr>
                                            <th>Step</th>
                                            <th>Role</th>
                                            <th>Status</th>
                                            <th>Action By</th>
                                            <th>Action Date</th>
                                            <th>Comments</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($instance->steps as $step)
                                            <tr>
                                                <td>{{ $step->step->name ?? 'Unknown Step' }}</td>
                                                <td>{{ $step->step->role->name ?? 'Unknown Role' }}</td>
                                                <td>
                                                    <span class="label label-{{ $step->status === 'approved' ? 'success' : ($step->status === 'rejected' ? 'danger' : 'warning') }}">
                                                        {{ ucfirst($step->status) }}
                                                    </span>
                                                </td>
                                                <td>{{ $step->actor->name ?? 'N/A' }}</td>
                                                <td>{{ $step->action_at ? $step->action_at->format('M d, Y H:i') : 'N/A' }}</td>
                                                <td>{{ $step->comments ?? 'N/A' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                                @php
                                    $currentStep = $instance->steps->where('status', 'pending')->first();
                                @endphp
                                
                                @if($currentStep && auth()->user()->hasRole($currentStep->step->role->name))
                                    <hr>
                                    <h4>Take Action</h4>
                                    <form action="{{ route('admin.workflows.approvals.process', $instance) }}" method="POST">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="comments">Comments</label>
                                                    <textarea class="form-control" id="comments" name="comments" rows="3"></textarea>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>&nbsp;</label>
                                                    <div>
                                                        <button type="submit" name="action" value="approve" class="btn btn-success">
                                                            <i class="glyphicon glyphicon-ok"></i> Approve
                                                        </button>
                                                        <button type="submit" name="action" value="reject" class="btn btn-danger">
                                                            <i class="glyphicon glyphicon-remove"></i> Reject
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                @endif
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
                $('#workflow-instance-steps-table').DataTable({
                    "paging": false,
                    "searching": false,
                    "ordering": false,
                    "info": false
                });
                
                $("#workflow-instance-steps-table").css("width", "98.5%");
            });
        </script>
    @endpush
@endsection