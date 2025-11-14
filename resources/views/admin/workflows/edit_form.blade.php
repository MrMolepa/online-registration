<form id="workflow-edit-form" method="POST" action="{{ route('admin.workflows.update', $workflow) }}">
    @csrf
    @method('PUT')
    @include('admin.workflows._form', [
        'workflow' => $workflow,
        'entityTypes' => $entityTypes,
        'roles' => $roles,
        'users' => $users,
    ])
</form>

