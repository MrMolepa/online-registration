@extends('layouts.admin')

@section('content')
    <!-- ============================================================== -->
    <!-- Start Page Content -->
    <!-- MAIN -->
    <div class="main">
        <!-- MAIN CONTENT -->
        <div class="main-content">
            <div class="container-fluid">
                <h3 class="page-title">All Permissions</h3>
                <div class="row">
                    <div class="col-md-12">
                        <!-- PANEL NO CONTROLS -->
                        <div class="panel">
                            <div class="panel-heading">
                                <h3 class="panel-title">All Permissions</h3>
                            </div>
                            <div class="panel-body">
                                @if (session()->has('success'))
                                
                                    <div class="alert alert-success alert-dismissible" role="alert">
                                        <button type="button" class="close" data-dismiss="alert"
                                            aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                        <strong>Success! </strong> {{ session('success') }}
                                    </div>

                                @endif
                                <a href="{{ route('admin.permissions.create') }}" class="btn  btn-primary">+
                                    NEW
                                    PERMISSION</a>

                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th> Name</th>
                                            <th>Display Name</th>
                                            <th>Description</th>

                                            <th colspan="3">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($permissions as $permission)
                                            <tr>
                                                <td>{{ $permission->id }}</td>
                                                <td>{{ $permission->name }}</td>
                                                <td>{{ $permission->display_name }}</td>
                                                <td>{{ $permission->description }}</td>
                                                <td>

                                                    <a href="{{ route('admin.permissions.edit',$permission->id )}}" class="btn btn-primary"> <i class="fas fa-pencil-alt"></i>
                                                        Edit</a>
                                                    <a class="btn btn-danger"
                                                        onclick="event.preventDefault(); document.getElementById('delete-role-form-').submit();">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </a>
                                                    <form id="delete-role-form- " action="" method="post"
                                                        style="display: none">
                                                        @csrf
                                                        @method("DELETE")
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach


                                    </tbody>
                                </table>
                                <div class="d-flex justify-content-center">
                                    {!! $permissions->links() !!}
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
