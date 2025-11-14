@extends('layouts.admin')

@section('content')
    <div class="main">
        <!-- MAIN CONTENT -->
        <div class="main-content">
            <div class="container-fluid">
                <h3 class="page-title">Workflow Steps: {{ $workflow->name }}</h3>
                <div class="row">
                    <div class="col-md-12">
                        <!-- PANEL NO CONTROLS -->
                        <div class="panel">
                            <div class="panel-heading">
                                <h3 class="panel-title">Manage Workflow Steps<b></b></h3>
                                <div class="right">
                                    <a href="{{ route('admin.workflows.index') }}" class="btn btn-default btn-sm">
                                        <i class="glyphicon glyphicon-arrow-left"></i> Back to Workflows
                                    </a>
                                </div>
                            </div>
                            <div class="panel-body">
                                @if($workflow->description)
                                    <p class="text-muted">{{ $workflow->description }}</p>
                                    <hr>
                                @endif
                                
                                <table class="table table-striped" id="workflow-steps-table">
                                    <thead>
                                        <tr>
                                            <th>Order</th>
                                            <th>Step Name</th>
                                            <th>Role</th>
                                            <th>Mandatory</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($workflow->steps as $step)
                                            <tr>
                                                <td>{{ $step->order }}</td>
                                                <td>{{ $step->name }}</td>
                                                <td>{{ $step->role->name ?? 'Unknown Role' }}</td>
                                                <td>
                                                    <span class="label label-{{ $step->is_mandatory ? 'success' : 'default' }}">
                                                        {{ $step->is_mandatory ? 'Yes' : 'No' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="{{ route('admin.workflows.edit', $workflow) }}" 
                                                       class="btn btn-warning btn-xs" 
                                                       title="Edit Workflow">
                                                        <i class="glyphicon glyphicon-pencil"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">
                                                    <em class="text-muted">No steps defined for this workflow.</em>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                                
                                <div class="clearfix" style="margin-top: 15px;">
                                    <a href="{{ route('admin.workflows.edit', $workflow) }}" class="btn btn-primary">
                                        <i class="glyphicon glyphicon-pencil"></i> Edit Workflow Steps

                                    </a>
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

    @push('scripts')
        <script>
            $(document).ready(function() {
                // Initialize DataTable if needed
                $('#workflow-steps-table').DataTable({
                    "paging": true,
                    "searching": true,
                    "ordering": true,
                    "info": true,
                    "order": [[0, 'asc']] // Sort by order column by default
                });
                
                $("#workflow-steps-table").css("width", "98.5%");
            });
        </script>
    @endpush
@endsection