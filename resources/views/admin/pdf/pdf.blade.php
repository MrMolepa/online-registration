@extends('layouts.admin')

@section('content')
    <!-- ============================================================== -->
    <!-- Start Page Content -->
    <!-- MAIN -->

    <style>
        #pdf-canvas {
            width: 210mm;
            height: 297mm;
            position: relative;
            border: 1px solid #ccc;
            background-color: white;
            margin: 20px auto;
        }

        /* Grid System */
        #pdf-canvas {
            background-image:
                linear-gradient(to right, #f0f0f0 1px, transparent 1px),
                linear-gradient(to bottom, #f0f0f0 1px, transparent 1px);
            background-size: 20px 20px;
            /* Grid size */
            position: relative;
            width: 210mm;
            height: 297mm;
            margin: 20px auto;
            background-color: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        /* Grid Toggle Controls */
        .grid-controls {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: white;
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }

        /* Snap to Grid */
        .snap-to-grid {
            transition: all 0.1s ease;
        }

        /* Resizable element styling */
        .draggable-element {
            position: absolute;
            box-sizing: border-box;
            padding: 0.5em;
            border: .1px dashed #999;
            cursor: move;
            background-color: rgba(255, 255, 255, 0.8);
            overflow: hidden;
            transition: all 0.2s ease;
        }





        .resizable-handle {
            width: 10px;
            height: 10px;
            background-color: #333;
            position: absolute;
            right: -5px;
            bottom: -5px;
            cursor: se-resize;
        }

        /* Resizable handles styling */
        .ui-resizable-handle {
            position: absolute;
            background: #555;
            border: 1px solid #fff;
            width: 10px;
            height: 10px;
            background-color: #333;
            z-index: 2;
        }

        .ui-resizable-n {
            top: -4px;
            left: 0;
            right: 0;
            height: 8px;
            cursor: n-resize;
        }

        .ui-resizable-e {
            top: 0;
            right: -4px;
            width: 8px;
            bottom: 0;
            cursor: e-resize;
        }

        .ui-resizable-s {
            bottom: -4px;
            left: 0;
            right: 0;
            height: 8px;
            cursor: s-resize;
        }

        .ui-resizable-w {
            top: 0;
            left: -4px;
            width: 8px;
            bottom: 0;
            cursor: w-resize;
        }

        .ui-resizable-se {
            right: -4px;
            bottom: -4px;
            cursor: se-resize;
        }

        .ui-resizable-sw {
            left: -4px;
            bottom: -4px;
            cursor: sw-resize;
        }

        .ui-resizable-ne {
            right: -4px;
            top: -4px;
            cursor: ne-resize;
        }

        .ui-resizable-nw {
            left: -4px;
            top: -4px;
            cursor: nw-resize;
        }

        /* During resize */
        .draggable-element.resizing {
            opacity: 0.9;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
        }

        .rotated-element {
            transform-origin: center center;
            border: 1px dashed #0066cc;
        }

        /* Preview mode - non-rotated elements should ignore rotation */
        /* #pdf-canvas .draggable-element {
                                                                                                                        transform: none !important;
                                                                                                                    } */

        #properties-panel {
            position: fixed;
            right: 20px;
            top: 7em;
            width: 230px;
            background: white;
            padding: 10px;
            border: 1px solid #ccc;
            display: none;
            z-index: 1;
        }

        .fitlter-btn {
            display: none;
        }

        #filter-template {
            display: none;
        }

        .filter-item {
            display: flex;
        }



        .filter-item .filter-col {
            padding-right: 4px;
        }
    </style>
    <div class="main">
        <!-- MAIN CONTENT -->
        <div class="main-content">
            <div class="container-fluid">
                <h3 class="page-title">PDF Designer</h3>
                <div class="row">
                    <div class="col-md-12">
                        <!-- PANEL NO CONTROLS -->
                        <div class="panel">
                            <div class="panel-heading">
                                <h3 class="panel-title">PDF Designer</h3>
                            </div>
                            <div class="panel-body row d-flex justify-content-center">
                                <div class="col-md-3">
                                    <!-- PROPERTIES -->
                                    <div id="properties-panel">
                                        <h4>Element Properties</h4>
                                        <form id="element-properties-form">
                                            <div class="form-group">
                                                <input type="hidden" id="edit-element-id">
                                                <label>Content:</label>
                                                <textarea id="edit-content" class="form-control"></textarea>
                                            </div>

                                            <div class="form-group">
                                                <label>Font Family:</label>
                                                <select id="edit-font-family" class="form-control">
                                                    <option value="Arial">Arial</option>
                                                    <option value="Times">Times</option>
                                                    <option value="Courier">Courier</option>
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <label>Font Size:</label>
                                                <input type="number" id="edit-font-size" class="form-control"
                                                    value="12">
                                            </div>
                                            <div class="form-group">
                                                <label>Rotation (degrees):</label>
                                                <input type="range" id="edit-rotation" min="0" max="360"
                                                    value="0" class="form-control">
                                                <span id="rotation-value">0°</span>
                                            </div>


                                            <div class="form-check">
                                                <input type="checkbox" id="edit-is-rotated" class="form-check-input">
                                                <label class="form-check-label" for="edit-is-rotated">Enable
                                                    Rotation</label>
                                            </div>



                                            <button type="submit" class="btn btn-primary">Save Properties</button>
                                        </form>
                                    </div>
                                    <!-- END PROPERTIES -->
                                    <!-- GRID CONTROLS -->
                                    <div class="grid-controls">
                                        <label>
                                            <input type="checkbox" id="toggle-grid" checked> Show Grid
                                        </label>
                                        <label>
                                            <input type="checkbox" id="snap-to-grid"> Snap to Grid
                                        </label>
                                        <select id="grid-size">
                                            <option value="10">10px</option>
                                            <option value="20" selected>20px</option>
                                            <option value="25">25px</option>
                                            <option value="50">50px</option>
                                        </select>
                                    </div>
                                    <!-- END GRID CONTROLS -->
                                    <!-- NEW ELEMENT -->
                                    <div id="element-form"
                                        style="margin: 10px; padding: 15px; background: #f5f5f5; border-radius: 5px;">
                                        <h4>New Element</h4>
                                        <input type="hidden" id="template" class="form-control"
                                            value="{{ $template->id }}">
                                        <input type="hidden" id="table_name" class="form-control"
                                            value="{{ $template->data_source }}">
                                        <div class="form-group">
                                            <label for="element-type">Element Type:</label>
                                            <select id="element-type" class="form-control">
                                                <option value="text">Text</option>
                                                <option value="barcode">Barcode</option>
                                                <option value="table">Table</option>
                                                <option value="image">Image</option>
                                            </select>
                                        </div>
                                        <div class="form-group" id="columns-container" style="display: none;">
                                            <label>Select Columns:</label>
                                            <div id="columns-list"
                                                style="max-height: 150px; overflow-y: auto; border: 1px solid #ddd; padding: 5px;">
                                            </div>
                                        </div>

                                        <div class="form-group" id="text-content-container">
                                            <label for="element-content">Content:</label>
                                            <input type="text" id="element-content" class="form-control"
                                                value="New Text Element">
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" id="is_dynamic" class="form-check-input">
                                            <label class="form-check-label" for="is_dynamic">is dynamic</label>
                                        </div>

                                        <div class="form-group fitlter-btn ">
                                            <label>Filters</label>
                                            <button id="add-filter" class="btn btn-sm btn-secondary mb-2">Add
                                                Filter</button>
                                            <div id="filter-list" style="max-height: 200px; overflow-y: auto;"></div>
                                        </div>
                                        <div id="filter-template">
                                            <div class="filter-item">
                                                <div class="filter-col">
                                                    <select class="form-control form-control-sm filter-column">
                                                        <option value="">Select Column</option>
                                                    </select>
                                                </div>
                                                <div class="filter-col">
                                                    <select class="form-control form-control-sm filter-operator">
                                                        <option value="equals">Equals</option>
                                                        <option value="not_equals">Not Equals</option>
                                                        <option value="contains">Contains</option>
                                                        <option value="starts_with">Starts With</option>
                                                        <option value="ends_with">Ends With</option>
                                                        <option value="greater">Greater Than</option>
                                                        <option value="less">Less Than</option>
                                                        <option value="greater_or_equal">Greater or Equal</option>
                                                        <option value="less_or_equal">Less or Equal</option>
                                                        <option value="between">Between</option>
                                                        <option value="in">In List</option>
                                                        <option value="not_in">Not In List</option>
                                                        <option value="null">Is Null</option>
                                                        <option value="not_null">Is Not Null</option>
                                                    </select>
                                                </div>
                                                <div class="filter-col">
                                                    <button
                                                        class="btn btn-sm btn-danger remove-filter float-right">×</button>
                                                </div>
                                            </div>
                                        </div>


                                        <button id="add-element" class="btn btn-primary">Add Element</button>
                                    </div>
                                    <!-- END NEW ELEMENT -->
                                </div>
                                <div class="col-md-9">

                                    <div class="pull-right">
                                        <a href="{{ route('admin.pdf.templates.show', $template->id) }}"
                                            class="btn btn-info" data-id="">
                                            <i class="fas fa-file-download"></i> Sample
                                        </a>
                                    </div>

                                    <div class="clearfix"></div>
                                    <div id="pdf-canvas">
                                        @foreach ($elements as $element)
                                            <div class="draggable-element ui-widget-content  {{ $element->is_rotated ? 'rotated-element' : '' }}"
                                                data-id="{{ $element->id }}" data-rotation="{{ $element->rotation }}"
                                                data-is-rotated="{{ $element->is_rotated ? 'true' : 'false' }}"
                                                style="left: {{ $element->x_position }}px;
                                                      top: {{ $element->y_position }}px;
                                                    transform: {{ $element->is_rotated ? 'rotate(-' . $element->rotation . 'deg)' : '' }};
                                                      width: {{ $element->width }}px;
                                                      height: {{ $element->height }}px;
                                                      font-family: {{ $element->font_family }};
                                                   font-size: {{ $element->font_size }}px;
                                                   color: {{ $element->color }};
                                                   text-align: {{ $element->alignment == 'C' ? 'center' : ($element->alignment == 'R' ? 'right' : 'left') }};">
                                                {{ $element->content }}
                                            </div>
                                        @endforeach
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
    <div class="clearfix"></div>
@endsection

@section('script')
    <script>
        $(function() {


            // Grid functionality
            const $pdfCanvas = $('#pdf-canvas');
            let gridSize = 20;
            let snapToGrid = false;
            let tableColumns = [];
            let elementFilters = null;




            //

            // Toggle grid visibility
            $('#toggle-grid').change(function() {
                if ($(this).is(':checked')) {
                    $pdfCanvas.css('background-image',
                        'linear-gradient(to right, #f0f0f0 1px, transparent 1px), ' +
                        'linear-gradient(to bottom, #f0f0f0 1px, transparent 1px)');
                } else {
                    $pdfCanvas.css('background-image', 'none');
                }
            });


            // Toggle snap to grid
            $('#snap-to-grid').change(function() {
                snapToGrid = $(this).is(':checked');
                $('.draggable-element').toggleClass('snap-to-grid', snapToGrid);
            });

            // Change grid size
            $('#grid-size').change(function() {
                gridSize = parseInt($(this).val());
                $pdfCanvas.css('background-size', `${gridSize}px ${gridSize}px`);
            });




            // Helper function to snap values to grid
            function snapToGridValue(value) {
                return snapToGrid ? Math.round(value / gridSize) * gridSize : value;
            }

            // Update Element
            var currentEditingElement = null;
            $(".draggable-element").dblclick(function() {
                currentEditingElement = $(this);
                $("#edit-element-id").val($(this).data('id'));


                var url = '{{ route('admin.pdf.designer.edit-element', ':id') }}';
                url = url.replace(':id', $(this).data('id'));
                $.ajax({
                    url: url,
                    method: 'GET',
                    success: function(response) {
                        var element = response.element;
                        $('#edit-rotation').val(element.rotation);
                        $('#rotation-value').text(element.rotation + '°');
                        $('#edit-is-rotated').prop('checked', element.is_rotated);
                        $("#edit-content").val(element.content);
                        $("#edit-font-family").val(element.font_family);
                        $("#edit-font-size").val(element.font_size);
                        $("#properties-panel").show();



                    }
                });




            });

            // Rotation controls
            $('#edit-rotation').on('input', function() {
                $('#rotation-value').text($(this).val() + '°');
                if (currentEditingElement) {
                    currentEditingElement.css('transform', `rotate(${$(this).val()}deg)`);
                }
            });

            $('#edit-is-rotated').change(function() {
                if (currentEditingElement) {
                    currentEditingElement.toggleClass('rotated-element', $(this).is(':checked'));
                }
            });

            $("#element-properties-form").submit(function(e) {
                e.preventDefault();
                if (!currentEditingElement) return;

                var url = '{{ route('admin.pdf.designer.update-element', ':id') }}';
                url = url.replace(':id', $("#edit-element-id").val());
                const rotation = $('#edit-rotation').val();
                const isRotated = $('#edit-is-rotated').is(':checked');
                const content = $("#edit-content").val();
                const font_family = $("#edit-font-family").val();
                const font_size = $("#edit-font-size").val();
                $.ajax({
                    url: url,
                    method: 'PUT',
                    data: {
                        rotation: rotation,
                        is_rotated: isRotated ? 1 : 0,
                        font_family: font_family,
                        font_size: font_size,
                        content: content,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        console.log(response)
                        currentEditingElement.text($("#edit-content").val());
                        currentEditingElement.css({
                            'font-family': $("#edit-font-family").val(),
                            'font-size': $("#edit-font-size").val() + 'px'
                        });
                        currentEditingElement
                            .data('rotation', rotation)
                            .data('is-rotated', isRotated)
                            .css('transform', isRotated ? `rotate(${rotation}deg)` : '');
                    }
                });
            });




            // Show/hide appropriate form fields based on element type
            $('#element-type').change(function() {
                const type = $(this).val();
                // Hide all optional containers
                $('#text-content-container, #columns-container').hide();
                if (type === 'text' || type === 'barcode') {
                    $('#text-content-container').show();
                    $('.fitlter-btn').hide();
                    getColumns(type);
                } else if (type === 'table') {
                    $('#columns-container').show();
                    $('.fitlter-btn').show();
                    getColumns(type);
                }



                // if (element.type === 'table') {
                //     $('#table-properties').show();
                //     $('#data-source').val(element.dataSource || '');
                //     updateColumnList(element.columns);
                //     loadElementFilters(element.filters || []);
                // } else {
                //     $('#table-properties').hide();
                // }





            });

            // Load columns when table is selected
            $('#element-type').trigger('change');



            function getColumns(element_type) {
                const table = $("#table_name").val();
                if (table) {
                    var url = "{{ route('admin.pdf.designer.table-columns', ':table') }}";
                    url = url.replace(':table', table);
                    $.get(url, function(columns) {
                        const $columnsList = $('#columns-list');
                        $columnsList.empty();
                        var type = element_type == 'text' ? 'radio' : 'checkbox';
                        columns.forEach(function(column) {
                            $columnsList.append(`
                                    <div class="form-check">
                                        <input class="form-check-input column-checkbox"
                                           name="column-checkbox"
                                            type="${type}"
                                            value="${column}"
                                            id="col-${column}">
                                        <label class="form-check-label" for="col-${column}">
                                            ${column}
                                        </label>
                                    </div>
                                `);
                        });
                        tableColumns = columns;
                        $('#columns-container').show();
                    });
                } else {
                    $('#columns-container').hide();
                }

            }

            // Helper function to get selected columns
            function getSelectedColumns() {
                const selected = [];
                $('.column-checkbox:checked').each(function() {
                    selected.push($(this).val());
                });
                return selected.join(',');
            }



            // Handle the "Add Element" button click
            $('#add-element').click(function(e) {
                e.preventDefault();
                // Get form values
                const elementType = $('#element-type').val();
                const elementContent = $('#element-content').val();
                const template = $('#template').val();
                const dataSourceTable = $('#data-source-table').val();
                const dataSourceColumns = getSelectedColumns() != '' ?
                    getSelectedColumns() : '';
                const isDynamic = $('#is_dynamic').is(':checked');
                const filters = JSON.stringify(elementFilters || []);











                // // Default properties for new element
                const newElement = {
                    element_type: elementType,
                    template_id: template,
                    x_position: 50, // Default X position
                    y_position: 50, // Default Y position
                    width: 100, // Default width
                    height: 30, // Default height
                    content: elementType === 'text' ? elementContent : elementType === 'table' ?
                        '[]' : '',
                    font_family: 'Arial',
                    font_size: 12,
                    is_dynamic: isDynamic ? 1 : 0,
                    data_columns: dataSourceColumns,
                    filters: filters,
                    _token: '{{ csrf_token() }}'
                };

                // // Send AJAX request to save the element
                $.ajax({
                    url: "{{ route('admin.pdf.designer.store-element') }}",
                    method: 'POST',
                    data: newElement,
                    success: function(response) {
                        if (response.success) {
                            // Add the new element to the canvas
                            addElementToCanvas(response.element);
                        }
                    },
                    error: function(xhr) {
                        console.error('Error:', xhr.responseText);
                        alert('Failed to create element');
                    }
                });
            });

            // Function to add a new element to the canvas
            function addElementToCanvas(element) {
                const elementHtml = `
                            <div class="draggable-element ui-widget-content"
                                data-id="${element.id}"
                                style="left: ${element.x_position}px;
                                        top: ${element.y_position}px;
                                        width: ${element.width}px;
                                        height: ${element.height}px;
                                        font-family: ${element.font_family || 'Arial'};
                                        font-size: ${element.font_size || 12}px;
                                        color: ${element.color || '#000000'};
                                        text-align: ${element.alignment === 'C' ? 'center' :
                                                    (element.alignment === 'R' ? 'right' : 'left')};">
                                ${element.content || ''}
                                <div class="resizable-handle"></div>
                            </div>
                        `;

                // Append to canvas and make draggable/resizable
                $('#pdf-canvas').append(elementHtml);

                // Initialize draggable and resizable
                $('.draggable-element').last().draggable({
                    containment: "#pdf-canvas",
                    stop: function(event, ui) {
                        updateElementData($(this));
                    }
                }).resizable({
                    handles: 'n, e, s, w, ne, se, sw, nw',
                    containment: "#pdf-canvas",
                    stop: function(event, ui) {
                        updateElementData($(this));
                    }
                });
            }







            // Make elements draggable and resizable
            $(".draggable-element").draggable({
                containment: "#pdf-canvas",
                grid: snapToGrid ? [gridSize, gridSize] : false,
                start: function() {
                    if (snapToGrid) $(this).addClass('snap-to-grid');
                },
                stop: function(event, ui) {
                    if (snapToGrid) {
                        const snappedLeft = Math.round(ui.position.left / gridSize) * gridSize;
                        const snappedTop = Math.round(ui.position.top / gridSize) * gridSize;

                        $(this).animate({
                            left: snappedLeft,
                            top: snappedTop
                        }, 100, function() {
                            updateElementData($(this));
                        });
                    } else {
                        updateElementData($(this));
                    }
                }
            }).resizable({
                handles: 'n, e, s, w, ne, se, sw, nw',
                containment: "#pdf-canvas",
                grid: snapToGrid ? [gridSize, gridSize] : false,
                start: function() {
                    if (snapToGrid) $(this).addClass('snap-to-grid');
                },
                stop: function(event, ui) {
                    if (snapToGrid) {
                        const snappedWidth = Math.round(ui.size.width / gridSize) * gridSize;
                        const snappedHeight = Math.round(ui.size.height / gridSize) * gridSize;

                        $(this).animate({
                            width: snappedWidth,
                            height: snappedHeight
                        }, 100, function() {
                            updateElementData($(this));
                        });
                    } else {
                        updateElementData($(this));
                    }
                }
            });

            // Function to update element data when moved/resized
            function updateElementData(element) {

                element.data('x', element.position().left);
                element.data('y', element.position().top);
                element.data('width', element.outerWidth() + 0.4);
                element.data('height', element.outerHeight() + 0.4);

                var url = "{{ route('admin.pdf.designer.update-element', ':id') }}";
                url = url.replace(':id', element.data('id'));
                $.ajax({
                    url: url,
                    method: 'PUT',
                    data: {
                        x_position: element.position().left,
                        y_position: element.position().top,
                        width: element.outerWidth() + 0.4,
                        height: element.outerHeight() + 0.4,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        console.log(response);
                        console.log('Position updated');
                    }
                });
            }

            // Add new filter
            $('#add-filter').click(function() {
                const $filter = $('#filter-template').clone().removeAttr('id').show();
                $('#filter-list').append($filter);

                // Populate columns
                const $columnSelect = $filter.find('.filter-column');
                $columnSelect.empty().append('<option value="">Select Column</option>');
                tableColumns.forEach(col => {
                    $columnSelect.append(`<option value="${col}">${col}</option>`);
                });

                // Handle operator change
                $filter.find('.filter-operator').change(function() {
                    updateFilterValueInput($(this).closest('.filter-item'));
                });

                // Remove filter
                $filter.find('.remove-filter').click(function() {
                    $(this).closest('.filter-item').remove();
                    saveElementFilters();
                });
            });

            // Update filter value input based on operator
            function updateFilterValueInput($filter) {
                const operator = $filter.find('.filter-operator').val();
                // const $container = $filter.find('.filter-value-container');
                $container.empty();
                if (operator === 'null' || operator === 'not_null') {
                    return; // No value needed for these operators
                }

                if (operator === 'between') {
                    $container.html(`
                <div class="col-md-6">
                    <input type="text" class="form-control form-control-sm filter-value" placeholder="From">
                </div>
                <div class="col-md-6">
                    <input type="text" class="form-control form-control-sm filter-value" placeholder="To">
                </div>
            `);
                } else if (operator === 'in' || operator === 'not_in') {
                    $container.html(`
                <div class="col-md-12">
                    <textarea class="form-control form-control-sm filter-value"
                              placeholder="Comma-separated values"></textarea>
                </div>
            `);
                } else {
                    $container.html(`
                <div class="col-md-12">
                    <input type="text" class="form-control form-control-sm filter-value" placeholder="Value">
                </div>
            `);
                }
            }

            // Save filters to current element
            function saveElementFilters() {
                const filters = [];
                $('.filter-item').each(function() {
                    const $filter = $(this);
                    const column = $filter.find('.filter-column').val();
                    const operator = $filter.find('.filter-operator').val();

                    if (!column || !operator) return;
                    filters.push({
                        column: column,
                        operator: operator
                    });
                });
                elementFilters = filters;
            }

            // Load filters for selected element
            function loadElementFilters(filters = []) {
                $('#filter-list').empty();

                filters.forEach(filter => {
                    const $filter = $('#filter-template').clone().removeAttr('id').show();
                    $('#filter-list').append($filter);

                    // Populate columns
                    const $columnSelect = $filter.find('.filter-column');
                    $columnSelect.empty().append('<option value="">Select Column</option>');
                    tableColumns.forEach(col => {
                        $columnSelect.append(`<option value="${col}">${col}</option>`);
                    });

                    // Set values
                    $columnSelect.val(filter.column);
                    $filter.find('.filter-operator').val(filter.operator).trigger('change');

                    if (filter.value) {
                        if (filter.operator === 'between' && Array.isArray(filter.value)) {
                            $filter.find('.filter-value:eq(0)').val(filter.value[0]);
                            $filter.find('.filter-value:eq(1)').val(filter.value[1]);
                        } else if ((filter.operator === 'in' || filter.operator === 'not_in') && Array
                            .isArray(filter
                                .value)) {
                            $filter.find('.filter-value').val(filter.value.join(', '));
                        } else if (typeof filter.value === 'string') {
                            $filter.find('.filter-value').val(filter.value);
                        }
                    }

                    // Handle operator change
                    $filter.find('.filter-operator').change(function() {
                        updateFilterValueInput($(this).closest('.filter-item'));
                    });

                    // Remove filter
                    $filter.find('.remove-filter').click(function() {
                        $(this).closest('.filter-item').remove();
                        saveElementFilters();
                    });
                });
            }



            // Save filters when any filter input changes
            $(document).on('change', '.filter-column, .filter-operator', saveElementFilters);
            $(document).on('input', '.filter-value', saveElementFilters);







        });


        /****  Print errors End*******/
    </script>
@endsection
