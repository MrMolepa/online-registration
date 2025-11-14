@extends('layouts.admin')

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        .editor-wrapper {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .editor-toolbar {
            display: flex;
            gap: 15px;
            align-items: center;
            padding: 10px;
            background: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 6px;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .editor-page {
            position: relative;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .pdf-canvas {
            display: block;
            margin: auto;
            width: 100%;
        }

        .page-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
        }

        /* Field list (palette) */
        /* Field list */
        #field-list {
            display: flex;
            border: 1px solid #ccc;
            padding: 5px;
            background: #fafafa;
            overflow-y: auto;
        }
        .field-option {
            display: grid;
            align-items: center;

        }


        .field-option .field-btn{
            margin-right: 0.5em;
        }

        /* Grid overlay */
        .grid-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image:
                linear-gradient(to right, #e6e6e6 1px, transparent 1px),
                linear-gradient(to bottom, #e6e6e6 1px, transparent 1px);
            background-size: 20px 20px;
            z-index: 1;
            display: none;
            pointer-events: none;
        }


        /* Draggable fields */
        .draggable-element {
            position: absolute;
            box-sizing: border-box;
            cursor: move;
            padding: 0.5em;
            border: .1px dashed #999;
            cursor: move;
            transition: all 0.2s ease;
            background-color: rgba(255, 255, 255, 0.85);
            border-radius: 4px;
            overflow: hidden;
            z-index: 2;
            font-size: 14px;
            font-weight: 500;
            pointer-events: auto;
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

        .draggable-element {
            position: absolute;
            box-sizing: border-box;
            padding: 5px;
            border: 1px dashed #0066cc;
            background-color: rgba(255, 255, 255, 0.9);
            cursor: move;
            overflow: visible;
            /* important so remove button is not clipped */
            z-index: 10;
            /* make sure it’s above the PDF canvas */
        }

        .draggable-element .remove-field {
            position: absolute;
            top: -10px;
            right: -10px;
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 14px;
            line-height: 18px;
            text-align: center;
            cursor: pointer;
            z-index: 100;
            /* above everything in the draggable element */
            display: block;
            /* always visible, optional */
        }

        /* Optional: fade-in on hover instead of hidden by default */
        .draggable-element .remove-field {
            opacity: 0;
            transition: opacity 0.2s ease;
        }

        .draggable-element:hover .remove-field {
            opacity: 1;
        }
    </style>


    <div class="main">
        <!-- MAIN CONTENT -->
        <div class="main-content">
            <div class="container-fluid">
                <h3 class="page-title">Invitations</h3>
                <div class="row">
                    <div class="col-md-12">
                        <!-- PANEL NO CONTROLS -->
                        <div class="panel">
                            <div class="panel-heading">
                                <h3 class="panel-title">Invitations<b></b></h3>
                            </div>
                            <div class="panel-body">
                                <div class="editor-wrapper container">

                                    <!-- Toolbar -->
                                    <div class="editor-toolbar">

                                        <div id="field-list">
                                            @foreach ($role->fields as $field)
                                                <div class="field-option">
                                                    <button class="btn btn-sm btn-primary field-btn"
                                                        data-id="{{ $field->id }}" data-label="{{ $field->label }}">
                                                        {{ $field->label }}
                                                    </button>
                                                </div>
                                            @endforeach
                                        </div>

                                        <label><input type="checkbox" id="toggle-grid"> Show Grid</label>
                                        <label><input type="checkbox" id="snap-to-grid"> Snap to Grid</label>
                                        <label>Grid Size: <input type="number" id="grid-size" value="20" min="5"
                                                step="5" style="width:70px"></label>

                                    </div>

                                    <!-- PDF Pages will render here -->
                                    <div id="pdf-editor"></div>

                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- END PANEL NO CONTROLS -->
                </div>

            </div>


        </div>
    </div>
@endsection

@php
    $roleFieldsArray = $role->fields
        ->map(function ($f) {
            return [
                'id' => $f->id,
                'label' => $f->label,
                'type' => $f->type,
                'is_visible' => $f->is_visible,
                'positions' => $f->positions
                    ->map(function ($p) {
                        return [
                            'position_id' => $p->id,
                            'page' => $p->page,
                            'pos_x' => $p->pos_x,
                            'pos_y' => $p->pos_y,
                            'width' => $p->width,
                            'height' => $p->height,
                        ];
                    })
                    ->toArray(),
            ];
        })
        ->toArray();
@endphp

@push('scripts')
    <script>
        $(function() {
            // Pass fields to JS
            const roleFields = @json($roleFieldsArray);




            const url = "{{ $pdfUrl }}";
            const pdfContainer = document.getElementById("pdf-editor");
            let pdfDoc = null;

            // Load PDF
            pdfjsLib.getDocument(url).promise.then(function(pdf) {
                pdfDoc = pdf;
                for (let i = 1; i <= pdf.numPages; i++) {
                    renderPage(i);
                }
            });

            // Render each page
            function renderPage(num) {
                pdfDoc.getPage(num).then(function(page) {
                    const viewport = page.getViewport({
                        scale: 1.5
                    });
                    const canvas = document.createElement('canvas');
                    canvas.className = "pdf-canvas";
                    canvas.height = viewport.height;
                    canvas.width = viewport.width;

                    const pageWrapper = document.createElement("div");
                    pageWrapper.className = "editor-page";
                    pageWrapper.style.width = viewport.width + "px";
                    pageWrapper.style.height = viewport.height + "px";

                    // Overlay for fields
                    const overlay = document.createElement("div");
                    overlay.className = "page-overlay";
                    overlay.dataset.page = num;
                    overlay.style.width = viewport.width + "px";
                    overlay.style.height = viewport.height + "px";

                    // Grid overlay
                    const grid = document.createElement("div");
                    grid.className = "grid-overlay";
                    overlay.appendChild(grid);

                    pageWrapper.appendChild(canvas);
                    pageWrapper.appendChild(overlay);
                    pdfContainer.appendChild(pageWrapper);

                    const context = canvas.getContext('2d');
                    page.render({
                        canvasContext: context,
                        viewport: viewport
                    });

                    // Add draggable fields for this page
                    roleFields.forEach(field => {
                        field.positions.forEach(pos => {
                            if (pos.page != num || !field.is_visible) return;

                            const el = $(`
                                    <div class="draggable-element"
                                        data-position-id="${pos.position_id}"
                                        data-id="${field.id}"
                                        data-page="${pos.page}"
                                        style="left:${pos.pos_x}px; top:${pos.pos_y}px; width:${pos.width ?? 120}px; height:${pos.height ?? 50}px;">
                                        ${field.label}
                                        <button type="button" class="remove-field">×</button>
                                        <button type="button" class="copy-field">📄</button>
                                    </div>`);

                            $(overlay).append(el);
                            initDraggable(el);
                        });
                    });
                });
            }





            // Initialize draggable + resizable
            function initDraggable($el) {
                $el.draggable({
                    containment: "#pdf-editor",
                    stop: function(event, ui) {
                      
                        const $field = $(this);

                        let pageNum = null;
                        let $overlay = null;

                        // Find which page overlay the element is currently over
                        $(".page-overlay").each(function() {
                            const $o = $(this);
                            const offset = $o.offset();
                            const width = $o.width();
                            const height = $o.height();

                            if (
                                ui.offset.left >= offset.left &&
                                ui.offset.left <= offset.left + width &&
                                ui.offset.top >= offset.top &&
                                ui.offset.top <= offset.top + height
                            ) {
                                pageNum = $o.data("page");
                                $overlay = $o;
                            }
                        });

                        if (pageNum && $overlay) {
                            $field.attr("data-page", pageNum);

                            // Calculate relative position BEFORE appending to overlay
                            const offsetOverlay = $overlay.offset();
                            const offsetField = $field.offset();

                            const relativeX = offsetField.left - offsetOverlay.left;
                            const relativeY = offsetField.top - offsetOverlay.top;

                            // Update CSS to keep the element visually in place
                            $field.css({
                                left: relativeX,
                                top: relativeY,
                                position: "absolute"
                            });

                            // Append to the correct overlay
                            $overlay.append($field);

                            // Save to DB (position only)
                            saveField(
                                $field,
                                ui.position.left,
                                ui.position.top,
                                null, // width not changed
                                null, // height not changed
                                pageNum
                            );
                        }
                    },
                    drag: function(event, ui) {
                        if ($("#snap-to-grid").is(":checked")) {
                            const gridSize = parseInt($("#grid-size").val()) || 20;
                            ui.position.left = Math.round(ui.position.left / gridSize) * gridSize;
                            ui.position.top = Math.round(ui.position.top / gridSize) * gridSize;
                        }
                    }
                }).resizable({
                    handles: "n, e, s, w, ne, nw, se, sw",
                    stop: function(event, ui) {
                        const $field = $(this);
                        const $overlay = $field.closest(".page-overlay");
                        const pageNum = $overlay.data("page") || $field.data("page");

                        // Calculate relative position inside overlay
                        const offsetOverlay = $overlay.offset();
                        const offsetField = $field.offset();

                        const relativeX = offsetField.left - offsetOverlay.left;
                        const relativeY = offsetField.top - offsetOverlay.top;

                        saveField(
                            $field,
                            relativeX,
                            relativeY,
                            ui.size.width,
                            ui.size.height,
                            pageNum
                        );
                    }
                });
            }

            // Remove button handler
            $(document).on("click", ".remove-field", function(e) {
                e.stopPropagation();
                $(this).closest(".draggable-element").remove();
            });



            $(".field-btn").on("click", function() {
                const id = $(this).data("id");
                const label = $(this).data("label");
                const firstPageOverlay = $(".page-overlay").first();
                const page = firstPageOverlay.data("page");
                // Add field to PDF editor
                const $newField = $(`
                            <div class="draggable-element" id="field-${id}" data-page="1" data-id="${id}"
                            style="left:10px; top:10px; width:120px; height:40px;">
                                ${label}
                                <button type="button" class="copy-field">📄</button>
                                <button type="button" class="remove-field">×</button>
                            </div>
                                `);
                firstPageOverlay.append($newField);
                initDraggable($newField);
                // Optional save
                saveField($newField, 10, 10, 120, 40, 1);

            });


            $(document).on('click', '.copy-field', function() {
                const $field = $(this).closest('.draggable-element');
                const fieldId = $field.data('id');
                const currentPage = parseInt($field.data('page'));

                let targetPage = prompt("Copy to which page?", currentPage + 1);
                if (!targetPage) return;

                // Clone field
                const $clone = $field.clone();
                $clone.attr('data-page', targetPage);

                // Append into target page overlay
                $(`#overlay-page-${targetPage}`).append($clone);
                initDraggable($clone);

                // Save via AJAX
                $.post("/field-positions", {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    field_id: fieldId,
                    page: targetPage,
                    pos_x: parseFloat($clone.css("left")),
                    pos_y: parseFloat($clone.css("top")),
                    width: $clone.width(),
                    height: $clone.height(),
                });
            });





            // Remove field
            $(document).on('click', '.remove-field', function(e) {
                e.stopPropagation();
                const $el = $(this).closest('.draggable-element');
                const id = $el.data("id");
                removeField($el);
                $(`#field-${id}`).remove();
                // Uncheck checkbox too

            });

            // Toggle grid
            $('#toggle-grid').on('change', function() {
                $('.grid-overlay').toggle(this.checked);
            });


            // Save field
            function saveField($el, x, y, w, h, page_no, is_visible = true) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                const data = {
                    position_id: $el.data('position-id'), // optional
                    field_id: $el.data("id"),
                    page: page_no,
                    pos_x: x,
                    is_visible: is_visible,
                    pos_y: y,
                    _token: $('meta[name="csrf-token"]').attr('content')
                };


                if (w !== null) data.width = w;
                if (h !== null) data.height = h;
                $.ajax({
                    url: '{{ route('admin.invitations.roles.saveField') }}',
                    type: 'POST',
                    data: data,
                    success: function(data) {
                        var position = data.position;
                        console.log(typeof $el.data('position-id'));
                        if (typeof $el.data('position-id') === 'undefined') {
                            $el.data('position-id', position.id);
                        }
                        console.log(data);
                    }
                });


            };


            // Remove field
            function removeField($el) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                const data = {
                    position_id: $el.data('position-id'), // optional
                    _token: $('meta[name="csrf-token"]').attr('content')
                };
                $.ajax({
                    url: '{{ route('admin.invitations.roles.removeField') }}',
                    type: 'POST',
                    data: data,
                    success: function(data) {
                        console.log(data);
                    }
                });


            };
        });
    </script>
@endpush
