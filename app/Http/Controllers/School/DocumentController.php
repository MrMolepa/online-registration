<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\AdminUser;
use App\Models\DocumentCategory;
use App\Models\DocumentMetaDatas;
use App\Models\DocumentRolePermissions;
use App\Models\Documents;
use App\Models\DocumentUserPermissions;
use App\Models\DocumentVersions;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $documents = $this->getAssignedDocuments();
            return DataTables::of($documents)
                ->editColumn('document_name', function ($row) {

                    return $this->getIsDownloadFlag($row->id) ? "<a href='" . route('center.documents.download', ['id' => $row->id, 'isVersion' => 0]) . "' title='Download' class='dropdown-item download-document'>$row->name</a>" : $row->name;
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

                    $actions = "";

                    if ($this->getIsDownloadFlag($row->id)) {
                        $actions .= "<a href='" . route('center.documents.download', ['id' => $row->id, 'isVersion' => 0]) . "' class='dropdown-item download-document'><i class='fa fa-download' aria-hidden='true'></i>Download</a>";
                    }
                    return  $actions == "" ? "" : "<div class='list-icons'>
                                                        <div class='list-icons-item dropdown'>
                                                                <a href='#' class='list-icons-item dropdown-toggle caret-0' data-toggle='dropdown'><i class='fa fa-ellipsis-v' aria-hidden='true'></i></a>
                                                                <div class='dropdown-menu  dropdown-menu-left' x-placement='top-end' style='position: absolute; transform: translate3d(-164px, -166px, 0px); top:18rem; left: 11em; will-change: transform;'>
                                                            $actions
                                                                </div>
                                                            </div>
                                                        </div>";
                })
                ->rawColumns(['actions', 'document_name', 'created_by', 'expired_date'])
                ->make();
        }

        $categories = DocumentCategory::with('childs')->where('parent_id', '=', null)->get();
        return view('school.documents.index',compact('categories'));
    }
    public function store(Request $request)
    {

         ini_set('memory_limit','2048M');


        $validator = Validator::make($request->all(), [
            'name' => "required",
            'document_upload' => "required",
            'category_id' => "required",
            'description' => "required",

        ]);
        //document_meta_datas
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
        try {
            $userId = Auth::user()->document_user_profile->id;
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
            DocumentUserPermissions::create(array(
                'document_id' =>   $document_id,
                'user_id' =>  $userId,
                'is_allow_download' => true,
                'is_time_bound'=>false
            ));
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
                                    $date = date('Y-m-d H:i');
                                    $query->where('document_user_permissions.is_time_bound', '=', '1')
                                        ->where('document_user_permissions.start_date', '<=',    $date)
                                        ->where('document_user_permissions.end_date', '>=',    $date);
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
                                    $date = date('Y-m-d H:i');
                                    $query->where('document_role_permissions.is_time_bound', '=', '1')
                                        ->where('document_role_permissions.start_date', '<=',    $date)
                                        ->where('document_role_permissions.end_date', '>=',    $date);
                                });
                        });
                });
            })
            ->whereYear('documents.created_date', date('Y'))
            ->get();

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


    public function getDocumentbyId($id)
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
                                    $date = date('Y-m-d H:i');
                                    $query->where('document_user_permissions.is_time_bound', '=', '1')
                                        ->where('document_user_permissions.start_date', '<=', $date )
                                        ->where('document_user_permissions.end_date', '>=', $date );
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
                                        $date = date('Y-m-d H:i');
                                        $query->where('document_role_permissions.is_time_bound', '=', '1')
                                            ->where('document_role_permissions.start_date', '<=',  $date)
                                            ->where('document_role_permissions.end_date', '>=',   $date);
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
                        $date = date('Y-m-d H:i');
                        $query->where('document_user_permissions.is_time_bound', '=', '1')
                            ->where('document_user_permissions.start_date', '<=', $date)
                            ->where('document_user_permissions.end_date', '>=',  $date);
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
                         $date = date('Y-m-d H:i');

                        $query->where('document_role_permissions.is_time_bound', '=', '1')
                            ->whereDate('document_role_permissions.start_date', '<=', $date)
                            ->whereDate('document_role_permissions.end_date', '>=', $date);
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

        if ($this->getIsDownloadFlag($id)) {

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
                    ->header('Content-Type','application-x/force-download"')
                    ->header('Content-length', strlen($file_contents))
                    ->header('Content-Disposition', 'attachment; filename=' . $file->name . '.' . $fileExtension[1])
                    ->header('Content-Transfer-Encoding', 'binary');
                    exit;
                }
            } catch (\Exception $e) {
                return response()->json([
                    $e->getMessage()

                ]);
            }
        } else {
            return response()->json([
                'message' => 'Error in downloading.',
            ], 409);
        }
    }


    public function descargarPDF($curso_prog_cod = null)
    {
        $curso_prog = DB::table('curso_prog')->join('curso', 'curso_prog.curso_cod', '=', 'curso.curso_cod')->select('curso_prog.', 'curso.')->where('curso_prog.curso_prog_cod', $curso_prog_cod)->first();
        $file_contents = $curso_prog->curso_pdf;
        return response($file_contents)->header('Cache-Control', 'no-cache private')
        ->header('Content-Description', 'File Transfer')
        ->header('Content-Type', 'application/octet-stream')
        ->header('Content-length', strlen($file_contents))
        ->header('Content-Disposition', 'attachment; filename=' . $curso_prog->curso_nombre . '.pdf');
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
                                    $date = date('Y-m-d');
                                    $startDate = Carbon::createFromFormat('Y-m-d', $date)->startOfDay();
                                    $endDate = Carbon::createFromFormat('Y-m-d', $date)->endOfDay();
                                    $query->where('document_user_permissions.is_time_bound', '=', '1')
                                        ->whereDate('document_user_permissions.start_date', '<=', $startDate)
                                        ->whereDate('document_user_permissions.end_date', '>=', $endDate);
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
                                    $date = date('Y-m-d');
                                    $startDate = Carbon::createFromFormat('Y-m-d', $date)->startOfDay();
                                    $endDate = Carbon::createFromFormat('Y-m-d', $date)->endOfDay();
                                    $query->where('document_role_permissions.is_time_bound', '=', '1')
                                        ->whereDate('document_role_permissions.start_date', '<=', $startDate)
                                        ->whereDate('document_role_permissions.end_date', '>=', $endDate);
                                });
                        });
                });
            });


        $count = $query->count();
        return $count > 0 ? true : false;
    }
}
