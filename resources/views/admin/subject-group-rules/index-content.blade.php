{{-- Subject Group Rules Content (Without Layout) --}}
<button type="button" class="btn btn-success" id="openCreateRuleModal">
    <i class="fa fa-plus"></i> Validation Rule
</button>

<div class="clearfix" style="margin-bottom: 20px;"></div>

<div class="table-responsive">
    <table class="table table-striped" id="rules_table">
        <thead>
            <tr>
                <th>Rule Name</th>
                <th>Level</th>
                <th>Type</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
    </table>
</div>

{{-- Create Rule Modal --}}
@include('admin.subject-group-rules.create-modal')

{{-- Edit Rule Modal --}}
@include('admin.subject-group-rules.edit-modal')

@push('scripts')
<script>
toastr.options = {
    closeButton: true,
    progressBar: true,
    positionClass: "toast-top-center",
    timeOut: "5000"
};

var rulesTable;

// Only initialize when the tab is shown
$('a[href="#validation-rules-tab"]').on('shown.bs.tab', function(e) {
    initializeRulesDataTable();
});

$(document).ready(function() {
    // Open create modal
    $('#openCreateRuleModal').on('click', function() {
        $('#createRuleModal').modal('show');
    });

    // Open edit modal
    $(document).on('click', '.editRuleBtn', function() {
        var ruleId = $(this).data('id');
        loadRuleForEdit(ruleId);
    });

    // Auto-filter on dropdown change
    $('#filter_level').on('change', function() {
        if (rulesTable) {
            rulesTable.ajax.reload();
        }
    });

    // Delete button
    $(document).on('click', '.deleteRuleBtn', function() {
        if (!confirm('Are you sure you want to delete this rule?')) return;

        var url = $(this).data('url');

        $.ajax({
            url: url,
            method: "DELETE",
            data: {_token: "{{ csrf_token() }}"},
            success: function(data) {
                toastr.success(data.success);
                if (rulesTable) {
                    rulesTable.ajax.reload();
                }
            }
        });
    });
});

function initializeRulesDataTable() {
    if ($.fn.DataTable.isDataTable('#rules_table')) {
        $('#rules_table').DataTable().destroy();
    }
    
    rulesTable = $('#rules_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('admin.subject-group-rules.index') }}",
            data: function(d) {
                d.level_id = $('#filter_level').val();
            }
        },
        columns: [
            {data: 'rule_name', name: 'rule_name'},
            {data: 'level', name: 'level'},
            {data: 'type', name: 'type'},
            {data: 'is_active', name: 'is_active'},
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ]
    });
}

function loadRuleForEdit(ruleId) {
    console.log('=== LOAD RULE FOR EDIT ===');
    console.log('Rule ID:', ruleId);
    
    // Build the URL properly
    var baseUrl = "{{ route('admin.subject-group-rules.index') }}";
    var editUrl = baseUrl + '/' + ruleId + '/edit';
    
    console.log('Fetching from URL:', editUrl);
    
    $.ajax({
        url: editUrl,
        method: "GET",
        dataType: 'json',
        success: function(response) {
            console.log('=== AJAX SUCCESS ===');
            console.log('Full response:', response);
            
            if (response.success && response.rule) {
                console.log('Rule data:', response.rule);
                populateEditModal(response);
                $('#editRuleModal').modal('show');
            } else {
                toastr.error('Invalid response format');
            }
        },
        error: function(xhr, status, error) {
            console.error('=== AJAX ERROR ===');
            console.error('Status:', xhr.status);
            console.error('Response Text:', xhr.responseText);
            toastr.error('Error loading rule data: ' + error);
        }
    });
}
</script>
@endpush