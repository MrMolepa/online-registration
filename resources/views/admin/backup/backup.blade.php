@extends('layouts.admin')

@section('content')
    <!-- ============================================================== -->
    <!-- Start Page Content -->
    <!-- ============================================================== -->
    <!-- MAIN -->

    <div class="main">
        <!-- MAIN CONTENT -->
        <div class="main-content backup_restore">
            <div class="container-fluid">
                <h3 class="page-title">Backup & Restore Database</h3>
                <div class="row ">
                    <div class="col-md-12">
                        <!-- BUTTONS -->
                        <div class="panel">
                            <div class="panel-heading">
                                <h3 class="panel-title">Backup & Restore Database</h3>
                            </div>


                            <div class="panel-body">

                                @permission('backup-create')
                                    <a href="{{ route('admin.backup.create') }}" class="btn  btn-primary">+
                                        Create BackUp</a>
                                @endpermission
                                @if (session()->has('success'))
                                    <br>
                                    <br>
                                    <div class="alert alert-success alert-dismissible" role="alert">
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                                aria-hidden="true">&times;</span></button>
                                        <strong>Success! </strong> {{ session('success') }}
                                    </div>
                                @endif
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th width="45%">File Name</th>
                                            <th>File Size</th>
                                            <th>Last modified</th>
                                            <th colspan="3">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @foreach ($backups as $key => $backup)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ $backup['file_name'] }}</td>
                                                <td>{{ $backup['file_size'] }}</td>
                                                <td>{{ $backup['last_modified'] }}</td>


                                                <td>

                                                    @permission('backup-download')
                                                        <a href="{{ route('admin.backup.download', $backup['file_name']) }}"
                                                            class="btn btn-secondary"> <i class="fas fa-download"></i>
                                                            download</a>
                                                    @endpermission
                                                    @permission('backup-delete')
                                                        <a href="{{ route('admin.backup.delete', $backup['file_name']) }}"
                                                            class="btn btn-danger"> <i class="fas fa-trash-alt"></i>
                                                            remove</a>
                                                    @endpermission



                                                </td>


                                            </tr>
                                        @endforeach



                                    </tbody>
                                </table>



                            </div>
                        </div>
                        <!-- END BUTTONS -->

                    </div>

                </div>
            </div>
        </div>
        <!-- END MAIN CONTENT -->
    </div>
    <!-- ENTER PASSWORD  MODEL-->
    {{-- <div class="modal fade bd-modal-sm" id="check-password" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog modal-sm">
				<div class="modal-content">
					<div class="modal-header">

						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
						<h3 class="modal-title"> Password </h3>
					</div>



					<form action="" method="post" id="varifyPasswordForm">
						<div class="modal-body">
							<!-- <div class="errors">
						</div> -->

							<div class="form-group">
								<label for="inputAddress">Password</label>
								<input type="password" name="password" class="form-control">
							</div>

						</div>
						<div class="modal-footer">

							<input type="submit" class="btn btn-primary backupbtn" name="backup" value="Submit">
							<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>

						</div>
					</form>
				</div>
			</div>

		</div> --}}
    <!-- END ENTER PASSWORD -->
    <!-- END MAIN -->
    <div class="clearfix"></div>
    <!-- ============================================================== -->
    <!-- End PAge Content -->
    <!-- ============================================================== -->
@endsection
