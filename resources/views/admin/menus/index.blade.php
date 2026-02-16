@extends('layouts.admin')




@section('content')
    <div class="main">
        <!-- MAIN CONTENT -->
        <div class="main-content">
            <div class="container-fluid">
                <h3 class="page-title">Menu Management</h3>
                <div class="row">
                    <div class="col-md-12">
                        <!-- PANEL NO CONTROLS -->
                        <div class="panel panel-headline">
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <!-- View Toggle Buttons -->
                                        {{-- <div class="btn-group mb-3" role="group"> --}}
                                        {{-- <button type="button" class="btn btn-default active" id="table-view-btn">
                                            <i class="fa fa-list"></i> Table View
                                        </button>
                                        <button type="button" class="btn btn-default" id="tree-view-btn">
                                            <i class="fa fa-sitemap"></i> Tree View (Drag & Drop)
                                        </button>
                                        </div>
                                        <button type="button" class="btn btn-primary" id="addMenuBtn">
                                            <i class="fa fa-plus"></i> Add Menu
                                        </button> --}}




                                        <div class="custom-tabs-line tabs-line-bottom left-aligned">
                                            <ul class="nav" role="tablist">
                                                <li class="active"><a href="#table-view" role="tab" data-toggle="tab"
                                                        id="table-view-btn">Table View
                                                    </a></li>
                                                <li><a href="#tree-view" role="tab" data-toggle="tab"
                                                        id="tree-view-btn">Tree View (Drag & Drop)</a></li>
                                            </ul>

                                            <div class="pull-right"
                                                style="width: 300px; display: flex; align-items: center; gap: 6px;">
                                                <label for="guardFilterSelect" style="font-size: 10px; margin-bottom: 0;">
                                                    Filter by Guard:
                                                </label>
                                                <select class="form-control input-sm" id="guardFilterSelect"
                                                    style="width: 200px;">
                                                    <option value="">All Guards</option>
                                                    <option value="admin">Admin</option>
                                                    <option value="candidate">Candidate</option>
                                                    <option value="sponsor">Sponsor</option>
                                                    <option value="web">Web</option>
                                                </select>
                                            </div>


                                        </div>




                                        <div class="clearfix" style="margin-bottom: 0px;"></div>


                                        <div class="tab-content">
                                            <!-- TABLE VIEW -->
                                            <div class="tab-pane fade in active" id="table-view">
                                                <button type="button" class="btn btn-primary pull-left" id="addMenuBtn" style="margin-bottom: 10px;">
                                                    <i class="fa fa-plus"></i> Add Menu
                                                </button>
                                                <table class="table table-striped" id="menusTable">
                                                    <thead>
                                                        <tr>
                                                            <th>ID</th>
                                                            <th>Name</th>
                                                            <th>Route</th>
                                                            <th>Icon</th>
                                                            <th>Order</th>
                                                            <th>Parent</th>
                                                            <th>Guard</th>
                                                            <th>Status</th>
                                                            <th>Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                </table>
                                            </div>




                                            <!-- TREE VIEW (Drag & Drop) -->
                                            <div id="tree-view" class="tab-pane fade-in">
                                                <div class="alert alert-info">
                                                    <i class="fa fa-hand-rock-o"></i> <strong>Drag and drop</strong> menus
                                                    to reorder them. Changes are saved automatically. You can also drag
                                                    child menus between parents.
                                                </div>
                                                <div id="menu-tree" class="menu-tree">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <span class="handle" style="cursor: grab;">☰</span>
                                                    </div>
                                                    <div class="text-center" style="padding: 100px;">
                                                        <i class="fa fa-spinner fa-spin fa-3x"></i>
                                                        <p>Loading menus...</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- </div> --}}
                                    </div>
                                </div>
                            </div>
                            <!-- END PANEL NO CONTROLS -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- END MAIN CONTENT -->




        @include('admin.menus._form')
        @include('admin.menus._permissions')
    @endsection




    @push('styles')
        <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <style>
            .menu-item {
                transition: opacity 0.3s ease;
            }


            .menu-item.filtered-out {
                display: none;
            }
        </style>
    @endpush




    @push('scripts')
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
        <script>
            $(document).ready(function() {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });




                let table = $('#menusTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('admin.menus.index') }}",
                    columns: [{
                            data: 'id',
                            name: 'id'
                        },
                        {
                            data: 'name',
                            name: 'name'
                        },
                        {
                            data: 'route',
                            name: 'route'
                        },
                        {
                            data: 'icon',
                            name: 'icon'
                        },
                        {
                            data: 'order',
                            name: 'order'
                        },
                        {
                            data: 'parent_name',
                            name: 'parent.name'
                        },
                        {
                            data: 'guard_name',
                            name: 'guard_name'
                        },
                        {
                            data: 'status',
                            name: 'status'
                        },
                        {
                            data: 'actions',
                            name: 'actions',
                            orderable: false,
                            searchable: false
                        }
                    ]
                });




                // Guard filter for table view
                $('#guardFilterSelect').on('change', function() {
                    let selectedGuard = $(this).val();


                    // Add search to guard_name column
                    table.column(6).search(selectedGuard).draw();
                    // Filter tree view if visible
                    if ($('#tree-view').is(':visible')) {
                        filterTreeView(selectedGuard);
                    }
                });




                // Filter tree view by guard
                function filterTreeView(guardName) {
                    if (guardName === '') {
                        // Show all menu items
                        $('.menu-item').removeClass('filtered-out').show();
                    } else {
                        // Hide all first
                        $('.menu-item').addClass('filtered-out').hide();


                        // Show only matching guard items
                        $('.menu-item[data-guard="' + guardName + '"]').removeClass('filtered-out').show();


                        // Handle parent-child relationships
                        $('.menu-item[data-guard="' + guardName + '"]').each(function() {
                            // Show parent if it has visible children
                            let $parent = $(this).closest('.menu-children').closest('.menu-item');
                            if ($parent.length > 0) {
                                $parent.removeClass('filtered-out').show();
                            }
                        });
                    }
                }




                // View toggle functionality
                $('#table-view-btn').click(function() {
                    $('#table-view').show();
                    $('#tree-view').hide();
                    $(this).addClass('active');
                    $('#tree-view-btn').removeClass('active');
                });




                $('#tree-view-btn').click(function() {
                    $('#table-view').hide();
                    $('#tree-view').show();
                    $(this).addClass('active');
                    $('#table-view-btn').removeClass('active');
                    loadMenuTree();
                });




                // Load menu tree for drag and drop
                function loadMenuTree() {
                    $.get('{{ route('admin.menus.tree') }}', function(response) {
                        if (response.success) {
                            renderMenuTree(response.menus);
                            initSortable();


                            // Apply current filter if any
                            let currentFilter = $('#guardFilterSelect').val();
                            if (currentFilter) {
                                filterTreeView(currentFilter); // Apply filter to tree view
                            }
                        } else {
                            $('#menu-tree').html('<div class="alert alert-warning">No menus found</div>');
                        }
                    }).fail(function() {
                        $('#menu-tree').html('<div class="alert alert-danger">Error loading menus</div>');
                    });
                }




                // Render menu tree
                function renderMenuTree(menus) {
                    let html = '<ul class="menu-tree">';




                    menus.forEach(function(menu) {
                        html += renderMenuItem(menu);
                    });




                    html += '</ul>';
                    $('#menu-tree').html(html);
                }




                // Render single menu item
                function renderMenuItem(menu) {
                    let html = `
                <li class="menu-item list-group-item" data-id="${menu.id}" data-parent="${menu.parent_id || ''}" data-guard="${menu.guard_name}">
                    <div class="menu-item-header clearfix">




                        <div class="menu-item-actions pull-right">
                            <button class="btn btn-primary btn-xs edit-btn" data-url="${menu.edit_url}" title="Edit">
                                <i class="fa fa-pen"></i>
                            </button>
                            <button class="btn btn-warning btn-xs permissions-btn"
                                    data-url="${menu.permissions_url}"
                                    data-name="${menu.name}"
                                    title="Permissions">
                                <i class="fa fa-lock"></i>
                            </button>
                            <button class="btn btn-danger btn-xs delete-btn" data-url="${menu.delete_url}" title="Delete">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>




                        <div class="menu-item-info">
                            <span class="drag-handle">
                                <i class="fa fa-bars"></i>
                            </span>
                            <i class="${menu.icon || 'fa fa-circle-o'} menu-item-icon"></i>
                            <span class="menu-item-name">${menu.name}</span>
                            <span class="badge" style="margin-left: 10px;">${menu.guard_name}</span>
                        </div>




                    </div>
                    ${menu.children && menu.children.length > 0 ? renderChildren(menu.children) : ''}
                </li>
            `;
                    return html;
                }




                // Render children
                function renderChildren(children) {
                    let html = '<ul class="menu-children lsit-group " style="margin-left: 20px; margin-top: 10px;">';
                    children.forEach(function(child) {
                        html += renderMenuItem(child);
                    });
                    html += '</ul>';
                    return html;
                }




                // Initialize sortable
                function initSortable() {
                    $('.menu-tree, .menu-children').sortable({
                        handle: '.drag-handle',
                        placeholder: 'ui-sortable-placeholder',
                        connectWith: '.menu-tree, .menu-children',
                        tolerance: 'pointer',
                        cursor: 'move',
                        update: function(event, ui) {
                            saveOrder();
                        }
                    });
                }




                // Save menu order
                function saveOrder() {
                    let order = [];




                    // Get parent menus
                    $('.menu-tree > .menu-item').each(function(index) {
                        let menuId = $(this).data('id');
                        order.push({
                            id: menuId,
                            parent_id: null,
                            order: index + 1
                        });




                        // Get children
                        $(this).find('.menu-children > .menu-item').each(function(childIndex) {
                            order.push({
                                id: $(this).data('id'),
                                parent_id: menuId,
                                order: childIndex + 1
                            });
                        });
                    });




                    $.ajax({
                        url: '{{ route('admin.menus.reorder') }}',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            order: order
                        },
                        success: function(response) {
                            if (response.success) {
                                toastr.success('Menu order updated successfully');
                                table.ajax.reload(null, false); // Reload table without resetting pagination
                            }
                        },
                        error: function() {
                            toastr.error('Failed to update menu order');
                            loadMenuTree(); // Reload tree on error
                        }
                    });
                }




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
                $('#addMenuBtn').click(function() {
                    $('#menuForm')[0].reset();
                    $('#menu_id').val('');
                    $('#menuModalTitle').text('Add Menu');
                    $('.form-control').removeClass('is-invalid');
                    $('.invalid-feedback').text('');
                    $('#menuForm').attr('action', '{{ route('admin.menus.store') }}');
                    $('#menuModal').modal('show');
                });




                // Open modal for Edit (delegated for tree view)
                $(document).on('click', '.edit-btn', function(e) {
                    e.preventDefault();
                    let url = $(this).data('url');




                    $.ajax({
                        url: url,
                        type: 'GET',
                        success: function(response) {
                            if (response.menu) {
                                const menu = response.menu;
                                fillForm(menu, '#menuForm');
                                $('#menu_id').val(menu.id);
                                $('#menuModalTitle').text('Edit Menu');
                                $('.form-control').removeClass('is-invalid is-valid');
                                $('.invalid-feedback').text('');
                                $('#menuForm').attr('action', response.url);




                                $('#menuModal').modal('show');
                            } else {
                                toastr.error('Menu not found');
                            }
                        },
                        error: function(xhr) {
                            toastr.error('Error loading menu data');
                        }
                    });
                });




                // Submit Add/Edit
                $('#menuForm').submit(function(e) {
                    e.preventDefault();
                    let id = $('#menu_id').val();
                    let method = id ? 'PUT' : 'POST';
                    let url = $('#menuForm').attr('action');




                    $('.form-control').removeClass('is-invalid');
                    $('.invalid-feedback').text('');




                    $.ajax({
                        url: url,
                        type: method,
                        data: $(this).serialize(),
                        success: function(response) {
                            $('#menuModal').modal('hide');
                            table.ajax.reload();
                            if ($('#tree-view').is(':visible')) {
                                loadMenuTree();
                            }
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
                                toastr.error(xhr.responseJSON?.message || 'Error saving menu');
                            }
                        }
                    });
                });




                // Delete Menu (delegated for tree view)
                $(document).on('click', '.delete-btn', function(e) {
                    e.preventDefault();




                    if (!confirm('Are you sure you want to delete this menu?')) {
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
                            table.ajax.reload();
                            if ($('#tree-view').is(':visible')) {
                                loadMenuTree();
                            }
                            toastr.success(response.message);
                        },
                        error: function(xhr) {
                            toastr.error(xhr.responseJSON?.message || 'Error deleting menu');
                        }
                    });
                });
            });
        </script>
    @endpush
