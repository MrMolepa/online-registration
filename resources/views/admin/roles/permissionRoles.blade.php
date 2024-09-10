@extends('layouts.admin')

@section('content')
    <!-- ============================================================== -->
    <!-- Start Page Content -->
    <!-- ============================================================== -->
    <div class="main">
        <!-- MAIN CONTENT -->
        <div class="main-content">
            <div class="container-fluid">
                <h3 class="page-title">Permission</h3>
                <div class="row">
                    <div class="col-md-12">
                        <!-- PANEL NO CONTROLS -->
                        <div class="panel">
                            <div class="panel-heading">
                                <h3 class="panel-title">All Permissions <b>({{ $role->display_name }})</b></h3>
                            </div>
                            <div class="panel-body">
                                <form action="{{ route('admin.roles.updateRolePermission', $role->id) }}" method="post">
                                    @method('PUT')
                                    @csrf

                                    <div class="row">
                                        @php
                                            $count = 0;
                                            $permissionsArray = [];
                                            foreach ($permissions as $permission) {
                                                $display_name = explode(' ', $permission->display_name);
                                                $permissionsArray[$display_name[1]][] = ['id' => $permission->id, 'display_name' => $permission->display_name];
                                            }
                                            
                                        @endphp

                                        @foreach ($permissionsArray as $key => $permissions)
                                            <div class="col-md-3">
                                                <ul class="list">
                                                    <li class="item">
                                                        <label>
                                                            <input type="checkbox" />
                                                            <span>{{ $key }}</span>
                                                        </label>
                                                        <ul class="list">
                                                            @foreach ($permissions as $permission)
                                                                <li class="item">
                                                                    <label>
                                                                        <input type="checkbox"
                                                                            {{ $role->permissions->contains('id', $permission['id']) ? 'checked' : '' }}
                                                                            class="form-check-input parent"
                                                                            name="permissions[]"
                                                                            value="{{ $permission['id'] }}" />
                                                                        <span>{{ $permission['display_name'] }}</span>
                                                                    </label>
                                                                </li>
                                                            @endforeach

                                                        </ul>
                                                    </li>
                                                </ul>
                                            </div>

                                        @endforeach
                                    </div>
                                    <div class="form-group mt-2 mb-2">
                                        <input type="submit" class="btn btn-primary" value="Update">
                                    </div>

                                </form>

                            </div>
                        </div>
                        <!-- END PANEL NO CONTROLS -->
                    </div>

                </div>


            </div>
        </div>
        <!-- END MAIN CONTENT -->
    </div>
    <!-- ============================================================== -->
    <!-- End PAge Content -->
    <!-- ============================================================== -->
@endsection
