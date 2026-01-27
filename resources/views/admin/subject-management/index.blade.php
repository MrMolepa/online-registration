@extends('layouts.admin')

@section('content')
    <div class="main">
        <div class="main-content">
            <div class="container-fluid">
                <h3 class="page-title">Subject Management</h3>

                <div class="row">
                    <div class="col-md-12">
                        <div class="panel">
                            <div class="panel-heading">
                                <h3 class="panel-title">Manage Subject Groups & Validation Rules</h3>
                            </div>
                            <div class="panel-body">
                                <!-- Tabs Navigation -->
                                <div class="custom-tabs-line tabs-line-bottom left-aligned">
                                    <ul class="nav" role="tablist">
                                        <li class="active">
                                            <a href="#subject-groups-tab" role="tab" data-toggle="tab">
                                                Subject Groups
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#validation-rules-tab" role="tab" data-toggle="tab">
                                                Validation Rules
                                            </a>
                                        </li>

                                    </ul>
                                    <div class="pull-right"  style="width: 300px; display: flex; align-items: center; gap: 6px;">
                                            <label for="filter_level" style="font-size: 12px; margin-bottom: 0;">
                                                Filter:
                                            </label>
                                            <select id="filter_level" class="form-control">
                                                <option value="">All Levels</option>
                                                @foreach ($levels as $level)
                                                    <option value="{{ $level->id }}">{{ $level->level }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tab Content -->
                                <div class="tab-content" style="margin-top: 0px;">
                                    <!-- Subject Groups Tab -->
                                    <div class="tab-pane fade in active" id="subject-groups-tab">
                                        @include('admin.subject-groups.index-content')
                                    </div>
                                    <!-- Validation Rules Tab -->
                                    <div class="tab-pane fade" id="validation-rules-tab">
                                        @include('admin.subject-group-rules.index-content')
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        // Prevent hash from being added to URL when clicking tabs
        $('.nav-tabs a, .nav a').on('click', function(e) {
            e.preventDefault();
            $(this).tab('show');
        });

        // Handle tab switching without hash
        $(document).ready(function() {
            // Initialize first tab's content if needed
            var activeTab = $('.nav li.active a').attr('href');
            $(activeTab).addClass('active in');
        });
    </script>
@endsection
