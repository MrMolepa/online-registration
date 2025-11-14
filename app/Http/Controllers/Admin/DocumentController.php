<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminUser;
use App\Models\DocumentCategory;
use App\Models\DocumentComments;
use App\Models\DocumentMetaDatas;
use App\Models\DocumentRolePermissions;
use App\Models\Documents;
use App\Models\DocumentUser;
use App\Models\DocumentUserPermissions;
use App\Models\DocumentVersions;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Doctrine\DBAL\Query;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class DocumentController extends Controller
{




    public function index(Request $request)
    {
        if ($request->ajax()) {
            $documents = Documents::with('categories', 'users.document_user');
            return DataTables::of($documents)
                ->editColumn('document_name', function ($row) {
                    return  " <a href='" . route('admin.documents.download', ['id' => $row->id, 'isVersion' => 0]) . "' class='dropdown-item download-document'>$row->name</a>";
                })
                ->editColumn('created_date', function ($row) {
                    return  date('d-m-Y', strtotime($row->created_date));
                })
                ->editColumn('actions', function ($row) {
                    return   "<div class='list-icons'>
                                <div class='list-icons-item dropdown'>
                                        <a href='#' class='list-icons-item dropdown-toggle caret-0' data-toggle='dropdown'><i class='fa fa-ellipsis-v' aria-hidden='true'></i></a>
                                        <div class='dropdown-menu  dropdown-menu-left' x-placement='top-end' style='position: absolute; transform: translate3d(-164px, -166px, 0px); top:7rem; left: 11em; will-change: transform;'>
                                            <a href='" . route('admin.documents.edit', $row->id) . "' class='dropdown-item edit-document'><i class='fas fa-pencil-alt' aria-hidden='true'></i>Edit</a>
                                            <a href='" . route('admin.documents.download', ['id' => $row->id, 'isVersion' => 0]) . "' class='dropdown-item download-document'><i class='fa fa-download' aria-hidden='true'></i>Download</a>
                                            <a href='" . route('admin.documents.edit', $row->id) . "' class='dropdown-item share-document'><i class='fas fa-share-alt' aria-hidden='true'></i>Share</a>
                                            <a href='" . route('admin.documents.edit', $row->id) . "'  class='dropdown-item version-document'><i class='fas fa-history' aria-hidden='true'></i>Version History</a>
                                            <a href='" . route('admin.documents-comments.index', ['id' => $row->id]) . "' class='dropdown-item  comments-document'><i class='fas fa-comments' aria-hidden='true'></i>Comments</a>
                                            <a href='" . route('admin.documents.destroy', $row->id) . "' class='dropdown-item  delete-document'><i class='fas fa-trash-alt' aria-hidden='true'></i>Delete</a>
                                        </div>
                                    </div>
                                </div>";
                })
                ->rawColumns(['actions', 'document_name'])
                ->make();
        }
        $categories = DocumentCategory::with('childs')->where('parent_id', '=', null)->get();
        $roles = Role::with('users')->get();
        return view('admin.documents.documents.index', compact('roles', 'categories', 'roles'));
    }


    public function assignedDocuments(Request $request)
    {
        if ($request->ajax()) {
            $documents = $this->getAssignedDocuments();
            return DataTables::of($documents)
                ->editColumn('document_name', function ($row) {

                    return  $this->getIsDownloadFlag($row->id) ? " <a href='" . route('admin.documents.download', ['id' => $row->id, 'isVersion' => 0]) . "' class='dropdown-item download-document'>$row->name</a>" : $row->name;
                })
                ->editColumn('created_date', function ($row) {
                    return  date('d-m-Y', strtotime($row->created_date));
                })
                ->editColumn('created_by', function ($row) {
                    return  $row->document_user_type::find($row->document_user_id)->email;
                })
                ->editColumn('expired_date', function ($row) {
                    $expired_date = [];
                    $expired_date[] = (!is_null($row->maxRolePermissionEndDate) ?: $row->maxRolePermissionEndDate);
                    $expired_date[] = (!is_null($row->maxUserPermissionEndDate) ?: $row->maxUserPermissionEndDate);
                    // $max = max(array_map('strtotime', $expired_date));
                    return   $row->maxRolePermissionEndDate > $row->maxUserPermissionEndDate ? $row->maxRolePermissionEndDate : $row->maxUserPermissionEndDate;
                })


                ->editColumn('actions', function ($row) {
                    return   "<div class='list-icons'>
                                <div class='list-icons-item dropdown'>
                                        <a href='#' class='list-icons-item dropdown-toggle caret-0' data-toggle='dropdown'><i class='fa fa-ellipsis-v' aria-hidden='true'></i></a>
                                        <div class='dropdown-menu  dropdown-menu-left' x-placement='top-end' style='position: absolute; transform: translate3d(-164px, -166px, 0px); top:7rem; left: 11em; will-change: transform;'>
                                            <a href='" . route('admin.documents.download', ['id' => $row->id, 'isVersion' => 0]) . "' class='dropdown-item download-document'><i class='fa fa-download' aria-hidden='true'></i>Download</a>
                                        </div>
                                    </div>
                                </div>";
                })
                ->rawColumns(['actions', 'document_name', 'created_by', 'expired_date'])
                ->make();
        }
        return view('admin.documents.documents.assigned');
    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => "required",
            'document_upload' => "required",
            'document_meta_datas' => ['required', 'array'],
            'document_meta_datas.*' => ['required', 'string', 'distinct'],
            'category_id' => "required",
            'description' => "required",
            'document_user_Permission.start_date' => ['required_with:document_user_Permission.is_time_bound', 'nullable', 'date_format:Y-m-d\TH:i', 'before:document_user_Permission.end_date'],
            'document_user_Permission.end_date' => ['required_with:document_user_Permission.is_time_bound', 'nullable', 'date_format:Y-m-d\TH:i', 'after:document_user_Permission.start_date'],
            'document_role_Permission.start_date' => ['required_with:document_role_Permission.is_time_bound', 'nullable', 'date_format:Y-m-d\TH:i', 'before:document_role_Permission.end_date'],
            'document_role_Permission.end_date' => ['required_with:document_role_Permission.is_time_bound', 'nullable', 'date_format:Y-m-d\TH:i', 'after:document_role_Permission.start'],
        ]);
        //document_meta_datas
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
        try {
            $location = $request->location ?? 'local';
            $path = $request->file('document_upload')->storeAs('documents', time() . '.' . $request->file('document_upload')->getClientOriginalExtension(),  $location);
            if ($path == null || $path == '') {
                return response()->json(['document_upload' => ['Error in storing document in ' . $location]]);
            }
            DB::beginTransaction();
            $document = new Documents();
            $document->url =  $path;
            $document->category_id = $request->category_id;
            $document->name = $request->name;
            $document->location = $request->location;
            $document->description = $request->description;
            $document->save();
            $document_id = $document->id;
            $metaDatas = $request->document_meta_datas;
            foreach ($metaDatas as $metaTag) {
                DocumentMetaDatas::create(array(
                    'document_id' =>    $document_id,
                    'metatag' =>  $metaTag,
                ));
            }
            $docuemntroles = (object)$request->document_role_Permission;
            $rolePermissionsArray = array();
            if (isset($docuemntroles->roles)) {
                $startDate = 0;
                $endDate = 0;
                $is_allow_download = isset($docuemntroles->is_time_bound) ? $docuemntroles->is_allow_download : 0;
                $is_time_bound = isset($docuemntroles->is_time_bound) ? $docuemntroles->is_time_bound : 0;
                if (isset($docuemntroles->is_time_bound)) {
                    $startdate = date('Y-m-d\TH:i', strtotime($docuemntroles->start_date));
                    $enddate = date('Y-m-d\TH:i', strtotime($docuemntroles->end_date));
                    $startDate = $startdate;
                    $endDate =  $enddate;
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
                    $endDate =  $enddate;
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
                    array_push($rolePermissionsArray, [
                        'document_id' => $document_id,
                        'user_id' => $userid
                    ]);
                }
            }

            $userId = Auth::user()->document_user_profile->id;
            $array = array_filter(isset($documentUserPermissions->users) ? $documentUserPermissions->users : [], function ($item) use ($userId) {
                return $item == $userId;
            });
            if (count($array) == 0) {

                DocumentUserPermissions::create(array(
                    'document_id' =>   $document_id,
                    'user_id' =>  $userId,
                    'is_allow_download' => true,
                    'is_time_bound' =>  0,
                ));
            }
            DB::commit();
            return response()->json(['success' => 'Successfully saved the records']);
        } catch (\Exception $e) {
            return response()->json([
                $e->getMessage()
            ]);
        }
    }


    public function update(Request  $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => "required",
            'document_meta_datas' => ['required', 'array'],
            'document_meta_datas.*' => ['required', 'string', 'distinct'],
            'category_id' => "required",
            'description' => "required",
        ]);
        //document_meta_datas
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        try {
            DB::beginTransaction();
            $document = Documents::find($id);
            $document->category_id = $request->category_id;
            $document->name = $request->name;
            $document->description = $request->description;
            $document->save();
            $documentMetadatas = DocumentMetaDatas::where('document_id', '=', $id)->get('id');
            DocumentMetaDatas::destroy($documentMetadatas);
            foreach ($request->document_meta_datas as $metaTag) {
                DocumentMetaDatas::create(array(
                    'document_id' =>  $id,
                    'metatag' =>  $metaTag,
                ));
            }

            DB::commit();
            return response()->json(['success' => 'Successfully saved the records']);
        } catch (\Exception $e) {
            return response()->json([
                $e->getMessage()

            ]);
        }
    }


    public function getRoleUser(Request $request)
    {

        $documentUser = [];
        if ($request->has('q')) {
            $email = $request->get('q');
            $documentUser = DocumentUser::with('document_user')
                ->whereHas('document_user', function ($query) use ($email) {
                    $query->whereNotNull('email')
                        ->where('email', 'LIKE', "%{$email}%");
                })
                ->get();
        }

        return response()->json($documentUser);
    }


    private function getAssignedDocuments()
    {
        $userId = Auth::user()->document_user_profile->id;
        $userRoles = DB::table('document_users')
            ->select(
                [
                    'id',
                    'document_user_id',
                    'role_id',
                ],
            )->where('id',  $userId)
            ->get()->pluck('role_id')->toArray();

        $userRolesSQL =  implode(',',  $userRoles);
        $documents = Documents::select([
            'documents.id',
            'documents.name',
            'documents.url',
            'documents.created_date',
            'documents.description',
            'document_categories.id as category_id',
            'document_categories.name as category_name',
            'documents.location',
            'document_users.document_user_type',
            'document_users.document_user_id',
            DB::raw("document_users.document_user_id as document_user_id "),
            DB::raw("(SELECT max(document_user_permissions.end_date) FROM document_user_permissions
                     WHERE document_user_permissions.document_id = documents.id and document_user_permissions.is_time_bound =1
                     AND document_user_permissions.user_id=$userId
                     GROUP BY document_user_permissions.document_id) as maxUserPermissionEndDate"),
            DB::raw("(SELECT max(document_role_permissions.end_date) FROM document_role_permissions
                     WHERE document_role_permissions.document_id = documents.id and document_role_permissions.is_time_bound =1
                     AND document_role_permissions.role_id IN ($userRolesSQL)
                     GROUP BY document_role_permissions.document_id) as maxRolePermissionEndDate"),
        ])
            ->join('document_categories', 'documents.category_id', '=', 'document_categories.id')
            ->join('document_users', function ($join) {
                $join->on('document_users.id', '=', 'documents.created_by')
                    ->where('document_users.document_user_type', User::class)
                    ->orOn('document_users.id', '=', 'documents.created_by')
                    ->where('document_users.document_user_type', AdminUser::class);
            })->where(function ($query) use ($userId, $userRoles) {
                $query->whereExists(function ($query) use ($userId) {
                    $query->select(DB::raw(1))
                        ->from('document_user_permissions')
                        ->whereRaw('document_user_permissions.document_id = documents.id')
                        ->where('document_user_permissions.user_id', '=', $userId)
                        ->where(function ($query) {
                            $query->where('document_user_permissions.is_time_bound', '=', '0')
                                ->orWhere(function ($query) {
                                    $date = date('Y-m-d h:i');
                                    $query->where('document_user_permissions.is_time_bound', '=', '1')
                                        ->where('document_user_permissions.start_date', '<=',  $date)
                                        ->where('document_user_permissions.end_date', '>=',  $date);
                                });
                        });
                })->orWhereExists(function ($query) use ($userRoles) {
                    $query->select(DB::raw(1))
                        ->from('document_role_permissions')
                        ->whereRaw('document_role_permissions.document_id = documents.id')
                        ->whereIn('document_role_permissions.role_id', $userRoles)
                        ->where(function ($query) {
                            $query->where('document_role_permissions.is_time_bound', '=', '0')
                                ->orWhere(function ($query) {
                                    $date = date('Y-m-d h:i');

                                    $query->where('document_role_permissions.is_time_bound', '=', '1')
                                        ->where('document_role_permissions.start_date', '<=',  $date)
                                        ->where('document_role_permissions.end_date', '>=', $date);
                                });
                        });
                });
            })->get();

        return $documents;
    }


    public function edit($id)
    {
        $document = Documents::select([
            'documents.id',
            'documents.name',
            'documents.url',
            'documents.created_date',
            'documents.description',
            'document_categories.id as category_id',
            'document_categories.name as category_name',
            'documents.location',
        ])->join('document_categories', 'documents.category_id', '=', 'document_categories.id')
            ->where('documents.id',  '=', $id)
            ->first();
        if ($document == null) {
            return response()->json(null);
        }
        $documentMetadatas = DocumentMetaDatas::where('document_id', '=', $id)->get()->pluck("metatag", "id")->toArray();
        $document['document_meta_datas'] =  $documentMetadatas;
        return response()->json($document);
    }


    public function show($id)
    {
        $userId = Auth::user()->document_user_profile->id;
        $userRoles = DB::table('document_users')
            ->select(
                [
                    'id',
                    'document_user_id',
                    'role_id',
                ],
            )->where('id',  $userId)
            ->get()->pluck('role_id')->toArray();

        $document = Documents::select([
            'documents.id',
            'documents.name',
            'documents.url',
            'documents.created_date',
            'documents.description',
            'document_categories.id as category_id',
            'document_categories.name as category_name',
            'documents.location',
        ])
            ->join('document_categories', 'documents.category_id', '=', 'document_categories.id')
            ->where(function ($query) use ($userId, $userRoles) {
                $query->whereExists(function ($query) use ($userId) {
                    $query->select(DB::raw(1))
                        ->from('document_user_permissions')
                        ->whereRaw('document_user_permissions.document_id = documents.id')
                        ->where('document_user_permissions.user_id', '=', $userId)
                        ->where(function ($query) {
                            $query->where('document_user_permissions.is_time_bound', '=', '0')
                                ->orWhere(function ($query) {
                                    $date = date('Y-m-d h:i');
                                    $query->where('document_user_permissions.is_time_bound', '=', '1')
                                        ->where('document_user_permissions.start_date', '<=',   $date)
                                        ->where('document_user_permissions.end_date', '>=',  $date);
                                });
                        });
                })
                    ->orWhereExists(function ($query) use ($userRoles) {
                        $query->select(DB::raw(1))
                            ->from('document_role_permissions')
                            ->whereRaw('document_role_permissions.document_id = documents.id')
                            ->whereIn('document_role_permissions.role_id', $userRoles)
                            ->where(function ($query) {
                                $query->where('document_role_permissions.is_time_bound', '=', '0')
                                    ->orWhere(function ($query) {
                                        $date = date('Y-m-d h:i');
                                        $query->where('document_role_permissions.is_time_bound', '=', '1')
                                            ->where('document_role_permissions.start_date', '<=',  $date)
                                            ->where('document_role_permissions.end_date', '>=',  $date);
                                    });
                            });
                    });
            })
            ->where('documents.id',  '=', $id)
            ->first();

        if ($document == null) {
            return response()->json(null);
        }

        $documentMetadatas = DocumentMetaDatas::where('document_id', '=', $id)->get()->pluck("metatag", "id")->toArray();
        $document['document_meta_datas'] =  $documentMetadatas;
        $docUserPermissionQuery = DocumentUserPermissions::where('document_user_permissions.document_id',  '=', $id)
            ->where('document_user_permissions.user_id', '=', $userId)
            ->where('document_user_permissions.is_allow_download', '=', true)
            ->where(function ($query) {
                $query->where('document_user_permissions.is_time_bound', '=', '0')
                    ->orWhere(function ($query) {
                        $date = date('Y-m-d h:i');
                        $query->where('document_user_permissions.is_time_bound', '=', '1')
                            ->where('document_user_permissions.start_date', '<=', $date)
                            ->where('document_user_permissions.end_date', '>=', $date);
                    });
            });

        $userPermissionCount = $docUserPermissionQuery->count();
        if ($userPermissionCount > 0) {
            $document['is_allow_download'] = true;
            return response()->json($document);
        }
        $docRolePermissionQuery = DocumentRolePermissions::where('document_role_permissions.document_id',  '=', $id)
            ->where('document_role_permissions.is_allow_download', '=', true)
            ->whereIn('document_role_permissions.role_id', $userRoles)
            ->where(function ($query) {
                $query->where('document_role_permissions.is_time_bound', '=', '0')
                    ->orWhere(function ($query) {
                        $date = date('Y-m-d h:i');
                        $query->where('document_role_permissions.is_time_bound', '=', '1')
                            ->where('document_role_permissions.start_date', '<=',   $date)
                            ->where('document_role_permissions.end_date', '>=',   $date);
                    });
            });

        $rolePermissionCount = $docRolePermissionQuery->count();




        if ($rolePermissionCount > 0) {
            $document['is_allow_download'] = true;
            return response()->json($document);
        } else {
            $document['is_allow_download'] = false;
            return response()->json($document);
        }
    }


    public function downloadDocument($id, $isVersion)
    {
        $bool = filter_var($isVersion, FILTER_VALIDATE_BOOLEAN);
        $file = null;
        if ($bool == true) {
            $file = DocumentVersions::withTrashed()->findOrFail($id);
        } else {
            $file = Documents::withTrashed()->findOrFail($id);
        }

        $fileupload = $file->url;


        $location = $file->location ?? 'local';

        try {
            if (Storage::disk($location)->exists($fileupload)) {
                $file_contents = Storage::disk($location)->get($fileupload);
                $fileType = Storage::mimeType($fileupload);

                $fileExtension = explode('.', $file->url);
                return response($file_contents)
                    ->header('Cache-Control', 'no-cache private')
                    ->header('Content-Description', 'File Transfer')
                    ->header('Content-Type', $fileType)
                    ->header('Content-length', strlen($file_contents))
                    ->header('Content-Disposition', 'attachment; filename=' . $file->name . '.' . $fileExtension[1])
                    ->header('Content-Transfer-Encoding', 'binary');
            }
        } catch (\Exception $e) {
            return response()->json([
                $e->getMessage()

            ]);
        }
    }



    private function getIsDownloadFlag($id)
    {
        $userId = Auth::user()->document_user_profile->id;
        $userRoles = DB::table('document_users')
            ->select(
                [
                    'id',
                    'document_user_id',
                    'role_id',
                ],
            )->where('id',  $userId)
            ->get()->pluck('role_id')->toArray();
        $query = Documents::where('documents.id',  '=', $id)
            ->where(function ($query) use ($userId, $userRoles) {
                $query->whereExists(function ($query) use ($userId) {
                    $query->select(DB::raw(1))
                        ->from('document_user_permissions')
                        ->whereRaw('document_user_permissions.document_id = documents.id')
                        ->where('document_user_permissions.user_id', '=', $userId)
                        ->where('document_user_permissions.is_allow_download', '=', 1)
                        ->where(function ($query) {
                            $query->where('document_user_permissions.is_time_bound', '=', '0')
                                ->orWhere(function ($query) {
                                    $date = date('Y-m-d h:i');
                                    $query->where('document_user_permissions.is_time_bound', '=', '1')
                                        ->where('document_user_permissions.start_date', '<=',  $date)
                                        ->where('document_user_permissions.end_date', '>=',   $date);
                                });
                        });
                })->orWhereExists(function ($query) use ($userRoles) {
                    $query->select(DB::raw(1))
                        ->from('document_role_permissions')
                        ->whereRaw('document_role_permissions.document_id = documents.id')
                        ->where('document_role_permissions.is_allow_download', '=', 1)
                        ->whereIn('document_role_permissions.role_id', $userRoles)
                        ->where(function ($query) {
                            $query->where('document_role_permissions.is_time_bound', '=', '0')
                                ->orWhere(function ($query) {
                                    $date = date('Y-m-d h:i');
                                    $query->where('document_role_permissions.is_time_bound', '=', '1')
                                        ->where('document_role_permissions.start_date', '<=', $date)
                                        ->where('document_role_permissions.end_date', '>=', $date);
                                });
                        });
                });
            });


        $count = $query->count();
        return $count > 0 ? true : false;
    }


    public function destroy($id)
    {
        try {

            DB::beginTransaction();
            $document = Documents::withoutGlobalScope('is_deleted')->withTrashed()->findOrFail($id);
            $medatDatas = DocumentMetaDatas::where('document_id', $id)->get();
            $comments = DocumentComments::where('document_id', $id)->get();
            $documentVersions = DocumentVersions::withoutGlobalScope('is_deleted')->where('document_id', $id)->get();
            $userPermissions = DocumentUserPermissions::where('document_id', $id)->get();
            $rolePermissions = DocumentRolePermissions::where('document_id', $id)->get();

            foreach ($medatDatas as $medatData) {
                $medatData->forceDelete();
            }
            foreach ($comments as $comment) {
                $comment->forceDelete();
            }


            foreach ($documentVersions as $documentVersion) {
                $documentVersion->forceDelete();
            }

            foreach ($userPermissions as $userPermission) {
                $userPermission->forceDelete();
            }

            foreach ($rolePermissions as $rolePermission) {
                $rolePermission->forceDelete();
            }



            $path = $document->url;
            $location = $document->location;
            $userId = Auth::user()->document_user_profile->id;
            $document->is_deleted = true;
            $document->is_permanent_delete = true;
            $document->deleted_by = $userId;
            $document->deleted_at = Carbon::now();
            $document->save();
            DB::commit();
            try {
                Storage::disk($location)->delete($path);
            } catch (\Throwable $th) {
            }


            foreach ($documentVersions as $documentVersion) {

                $versionUrl = $documentVersion->url;
                $versionLocation = $documentVersion->location;

                try {
                    Storage::disk($versionLocation)->delete($versionUrl);
                } catch (\Throwable $th) {
                }
            }

            return response()->json(['success' =>  'You have successfully deleted  the records']);
        } catch (\Exception $e) {
            return response()->json([
                $e->getMessage()
            ]);
        }
    }
}
