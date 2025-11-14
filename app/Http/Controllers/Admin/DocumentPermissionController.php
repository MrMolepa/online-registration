<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentRolePermissions;
use App\Models\DocumentUserPermissions;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class DocumentPermissionController extends Controller
{
    //


    public function index(Request $request, $id)
    {
        if ($request->ajax()) {
            $documentRolePermission = DocumentRolePermissions::where('document_id', '=', $id)
                ->with('role')
                ->has('role')
                ->get();
            $documentRolePermissionList = $documentRolePermission->map(function ($item) {
                $item->type = 'Role';
                return $item;
            });
            $documentUserPermission = DocumentUserPermissions::where('document_id', '=', $id)
                ->with('user')
                ->has('user')
                ->get();
            $documentUserPermissionList = $documentUserPermission->map(function ($item) {
                $item->type = 'User';
                return $item;
            });
            $permissions = $documentRolePermissionList->concat($documentUserPermissionList);

            return DataTables::of($permissions)

                ->editColumn('type', function ($row) {
                    return   $row->type;
                })
                ->editColumn('is_allow_download', function ($row) {
                    return   $row->is_allow_download == 1 ? "Yes" : "No";
                })
                ->editColumn('name', function ($row) {
                    if ($row->type == 'User') {
                        return substr($row->user->document_user_type::find($row->user->document_user_id)->email, 0, strrpos($row->user->document_user_type::find($row->user->document_user_id)->email, '@'));
                    } else {
                        return   $row->role->display_name;
                    }
                })
                ->editColumn('email', function ($row) {
                    if ($row->type == 'User') {
                        return   $row->user->document_user_type::find($row->user->document_user_id)->email;
                    } else {
                        return   '--';
                    }
                })
                ->editColumn('start_date', function ($row) {
                    return  $row->is_time_bound == 1 ? date('d-m-Y H:i:s', strtotime($row->start_date)) : '--';
                })
                ->editColumn('end_date', function ($row) {
                    return  $row->is_time_bound == 1 ? date('d-m-Y H:i:s', strtotime($row->end_date)) : '--';
                })
                ->editColumn('actions', function ($row) {
                    $action = "";
                    if ($row->type == 'User') {
                        $url = route('admin.documents.permissions.users.destroy', $row->id);
                        $action = "<button type='button' data-url=' $url' class='btn btn-danger btn-sm delete-permission'>Restrict <i class='fas fa-user-lock'></i></button>";
                    } else {
                        $url = route('admin.documents.permissions.roles.destroy', $row->id);
                        $action = "<button type='button' data-url=' $url' class='btn btn-danger btn-sm delete-permission'>Restrict <i class='fas fa-user-lock'></i></button>";
                    }
                    return $action;
                })
                ->rawColumns(['actions', 'name', 'type', 'is_allow_download', 'email'])
                ->make();
        }
    }

    public function documentRolePermission(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'document_role_Permission' => ['required', 'array'],
            'document_role_Permission.roles' => ['required', 'array'],
            'document_role_Permission.start_date' => ['required_with:document_role_Permission.is_time_bound', 'nullable', 'date_format:Y-m-d\TH:i', 'before:document_role_Permission.end_date'],
            'document_role_Permission.end_date' => ['required_with:document_role_Permission.is_time_bound', 'nullable', 'date_format:Y-m-d\TH:i', 'after:document_role_Permission.end_date'],

        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
        try {
            DB::beginTransaction();
            $document_id = $id;
            $docuemntroles = (object)$request->document_role_Permission;
            $rolePermissionsArray = array();
            if (isset($docuemntroles->roles)) {
                $startDate = 0;
                $endDate = 0;
                $is_allow_download = isset($docuemntroles->is_allow_download) ? $docuemntroles->is_allow_download : 0;
                $is_time_bound = isset($docuemntroles->is_time_bound) ? $docuemntroles->is_time_bound : 0;

                if (isset($docuemntroles->is_time_bound)) {
                    $startdate = date('Y-m-d\TH:i', strtotime($docuemntroles->start_date));
                    $enddate = date('Y-m-d\TH:i', strtotime($docuemntroles->end_date));
                    $startDate = $startdate;
                    $endDate = $enddate;
                }
                //roles
                foreach ($docuemntroles->roles as $roleId) {
                    DocumentRolePermissions::create([
                        'document_id' => $document_id,
                        'is_allow_download' => $is_allow_download,
                        'is_time_bound' => $is_time_bound,
                        'role_id' => $roleId,
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                    ]);
                    $documentUserIds = DB::table('document_users')
                        ->select(
                            [
                                'id',
                                'document_user_id',
                                'role_id',
                            ],
                        )->where('role_id', $roleId)
                        ->get();

                    foreach ($documentUserIds as $documentUserId) {
                        array_push($rolePermissionsArray, [
                            'document_id' => $document_id,
                            'user_id' => $documentUserId->id,
                        ]);
                    }
                }
            }
            DB::commit();
            return response()->json(['success' => 'Successfully saved the records']);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                $e->getMessage()
            ]);
        }
    }

    public function documentUserPermission(Request $request, $id)
    {

        //
        $validator = Validator::make($request->all(), [
            'document_user_Permission' => ['required', 'array'],
            'document_user_Permission.start_date' => ['required_with:document_user_Permission.is_time_bound', 'nullable', 'date_format:Y-m-d\TH:i', 'before:document_user_Permission.end_date'],
            'document_user_Permission.end_date' => ['required_with:document_user_Permission.is_time_bound', 'nullable', 'date_format:Y-m-d\TH:i', 'after:document_user_Permission.start_date'],
            'document_user_Permission.users' => ['required', 'array'],
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
        try {
            DB::beginTransaction();
            $document_id = $id;
            $documentUserPermissions = (object)$request->document_user_Permission;
            if (isset($documentUserPermissions->users)) {
                $startDate = 0;
                $endDate = 0;
                $is_allow_download = isset($documentUserPermissions->is_allow_download) ? $documentUserPermissions->is_allow_download : 0;
                $is_time_bound = isset($documentUserPermissions->is_time_bound) ? $documentUserPermissions->is_time_bound : 0;

                if (isset($documentUserPermissions->is_time_bound)) {
                    $startdate = date('Y-m-d\TH:i', strtotime($documentUserPermissions->start_date));
                    $enddate = date('Y-m-d\TH:i', strtotime($documentUserPermissions->end_date));
                    $startDate =  $startdate;
                    $endDate = $enddate;
                }
                //Users
                foreach ($documentUserPermissions->users as  $userid) {
                    DocumentUserPermissions::create([
                        'document_id' => $document_id,
                        'is_allow_download' => $is_allow_download,
                        'is_time_bound' => $is_time_bound,
                        'user_id' => $userid,
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                    ]);
                }
            }
            DB::commit();
            return response()->json(['success' => 'Successfully saved the records']);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                $e->getMessage()
            ]);
        }
    }

    public function deleteDocumentUserPermission($id)
    {
        DocumentUserPermissions::destroy($id);
        return response()->json(['success' => 'Successfully deleted the records']);
    }

    public function deleteDocumentRolePermission($id)
    {
        DocumentRolePermissions::destroy($id);
        return response()->json(['success' => 'Successfully deleted the records']);
    }

    // public function multipleDocumentsToUsersAndRoles($request)
    // {
    //     try {
    //         DB::beginTransaction();

    //         $permissionsArray = array();
    //         foreach ($request['documents'] as $document) {
    //             if ($request['isTimeBound']) {
    //                 $startdate = date('Y-m-d', strtotime(str_replace('/', '-', $request['startDate'])));
    //                 $endDate = date('Y-m-d', strtotime(str_replace('/', '-', $request['endDate'])));
    //                 if ($request['roles']) {
    //                     foreach ($request['roles'] as $role) {
    //                         DocumentRolePermissions::create([
    //                             'documentId' => $document,
    //                             'endDate' => $endDate ?? '',
    //                             'isAllowDownload' => $request['isAllowDownload'],
    //                             'isTimeBound' => $request['isTimeBound'],
    //                             'roleId' => $role,
    //                             'startDate' => $startdate ?? ''
    //                         ]);

    //                         $userIds = UserRoles::select('userId')
    //                             ->where('roleId', $role)
    //                             ->get();


    //                         foreach ($userIds as $userIdObject) {
    //                             array_push($permissionsArray, [
    //                                 'documentId' => $document,
    //                                 'userId' => $userIdObject['userId']
    //                             ]);
    //                         }
    //                     }
    //                 }
    //                 if ($request['users']) {
    //                     foreach ($request['users'] as $user) {
    //                         DocumentUserPermissions::create([
    //                             'documentId' => $document,
    //                             'endDate' => $endDate ?? '',
    //                             'isAllowDownload' => $request['isAllowDownload'],
    //                             'isTimeBound' => $request['isTimeBound'],
    //                             'userId' => $user,
    //                             'startDate' => $startdate ?? ''
    //                         ]);

    //                         array_push($permissionsArray, [
    //                             'documentId' => $document,
    //                             'userId' => $user
    //                         ]);
    //                     }
    //                 }
    //             } else {
    //                 if ($request['roles']) {
    //                     foreach ($request['roles'] as $role) {
    //                         DocumentRolePermissions::create([
    //                             'documentId' => $document,
    //                             'isAllowDownload' => $request['isAllowDownload'],
    //                             'isTimeBound' => $request['isTimeBound'],
    //                             'roleId' => $role,
    //                         ]);

    //                         $userIds = UserRoles::select('userId')
    //                             ->where('roleId', $role)
    //                             ->get();

    //                         $userIds = UserRoles::select('userId')
    //                             ->where('roleId', $role)
    //                             ->get();


    //                         foreach ($userIds as $userIdObject) {
    //                             array_push($permissionsArray, [
    //                                 'documentId' => $document,
    //                                 'userId' => $userIdObject['userId']
    //                             ]);
    //                         }
    //                     }
    //                 }
    //                 if ($request['users']) {
    //                     foreach ($request['users'] as $user) {
    //                         DocumentUserPermissions::create([
    //                             'documentId' => $document,
    //                             'isAllowDownload' => $request['isAllowDownload'],
    //                             'isTimeBound' => $request['isTimeBound'],
    //                             'userId' => $user,
    //                         ]);

    //                         array_push($permissionsArray, [
    //                             'documentId' => $document,
    //                             'userId' => $user
    //                         ]);
    //                     }
    //                 }
    //             }
    //         }

    //         $permissions = array_unique($permissionsArray, SORT_REGULAR);
    //         foreach ($permissions as $permission) {
    //             UserNotifications::create([
    //                 'documentId' => $permission['documentId'],
    //                 'userId' => $permission['userId']
    //             ]);
    //         }
    //         DB::commit();

    //         return [];
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return response()->json([
    //             'message' => 'Error in saving data.',
    //         ], 409);
    //     }
    // }

    // public function getIsDownloadFlag($id, $isPermission)
    // {
    //     $userId = Auth::parseToken()->getPayload()->get('userId');
    //     $userRoles = UserRoles::select('roleId')
    //         ->where('userId', $userId)
    //         ->get();
    //     $query = Documents::where('documents.id',  '=', $id)
    //         ->where(function ($query) use ($userId, $userRoles) {
    //             $query->whereExists(function ($query) use ($userId) {
    //                 $query->select(DB::raw(1))
    //                     ->from('document_user_permissions')
    //                     ->whereRaw('document_user_permissions.document_id = documents.id')
    //                     ->where('documentUserPermissions.userId', '=', $userId)
    //                     ->where('documentUserPermissions.isAllowDownload', '=', 1)
    //                     ->where(function ($query) {
    //                         $query->where('documentUserPermissions.isTimeBound', '=', '0')
    //                             ->orWhere(function ($query) {
    //                                 $date = date('Y-m-d');
    //                                 $startDate = Carbon::createFromFormat('Y-m-d', $date)->startOfDay();
    //                                 $endDate = Carbon::createFromFormat('Y-m-d', $date)->endOfDay();
    //                                 $query->where('documentUserPermissions.isTimeBound', '=', '1')
    //                                     ->whereDate('documentUserPermissions.startDate', '<=', $startDate)
    //                                     ->whereDate('documentUserPermissions.endDate', '>=', $endDate);
    //                             });
    //                     });
    //             })->orWhereExists(function ($query) use ($userRoles) {
    //                 $query->select(DB::raw(1))
    //                     ->from('documentRolePermissions')
    //                     ->whereRaw('documentRolePermissions.documentId = documents.id')
    //                     ->where('documentRolePermissions.isAllowDownload', '=', true)
    //                     ->whereIn('documentRolePermissions.roleId', $userRoles)
    //                     ->where(function ($query) {
    //                         $query->where('documentRolePermissions.isTimeBound', '=', '0')
    //                             ->orWhere(function ($query) {
    //                                 $date = date('Y-m-d');
    //                                 $startDate = Carbon::createFromFormat('Y-m-d', $date)->startOfDay();
    //                                 $endDate = Carbon::createFromFormat('Y-m-d', $date)->endOfDay();
    //                                 $query->where('documentRolePermissions.isTimeBound', '=', '1')
    //                                     ->whereDate('documentRolePermissions.startDate', '<=', $startDate)
    //                                     ->whereDate('documentRolePermissions.endDate', '>=', $endDate);
    //                             });
    //                     });
    //             });
    //         });


    //     $count = $query->count();
    //     return $count > 0 ? true : false;
    // }

}
