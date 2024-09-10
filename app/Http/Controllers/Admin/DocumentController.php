<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentCategory;
use App\Models\DocumentMetaDatas;
use App\Models\DocumentRolePermissions;
use App\Models\Documents;
use App\Models\DocumentUserPermissions;
use App\Models\Role;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DocumentController extends Controller
{




    public function index()
    {

       // dd(Auth::user()->document_user_profile->first()->id);
        $categories = DocumentCategory::with('childs')->where('parent_id', '=', null)->get();

        $roles = Role::with('admins', 'centers')->get();
        return view('admin.documents.documents.index', compact('roles', 'categories', 'roles'));
    }

    public function store(Request $request)
    {


        $validator = Validator::make($request->all(), [
            'name' => "required",
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }


        try {
            $location = $request->location ?? 'local';


            $path = $request->file('document_upload')->storeAs('documents', time() . '.' . $request->file('document_upload')->getClientOriginalExtension(),$location);
            // if ($path == null || $path == '') {
            //     return response()->json([
            //         'message' => 'Error in storing document in ' . $location,
            //     ], 409);
            // }
            // DB::beginTransaction();



            $document = new Documents();
            $document->url =  $path ;
            $document->category_id = $request->category_id;
            $document->name = $request->name;
            $document->location = $request->location;
            $document->description = $request->description;
            $document->save();
            $document_id = $document->id;
            $metaDatas = $request->document_meta_datas;



            foreach ($metaDatas as $metaTag) {
                DocumentMetaDatas::create(array(
                    'document_id' =>    $document_id ,
                    'metatag' =>  $metaTag,
                ));


            }
            $documentRolePermissions = $request->document_role_Permission;
            $rolePermissionsArray = array();
            foreach ($documentRolePermissions as $docuemntrole) {
                return response()->json([$docuemntrole]);
                $startDate = '';
                $endDate = '';
                if ($docuemntrole->isTimeBound) {
                    $startDate = Carbon::createFromFormat('Y-m-d', $docuemntrole->start_date)->startOfDay();
                    $endDate = Carbon::createFromFormat('Y-m-d', $docuemntrole->end_date)->endOfDay();
                }



                //roles
                foreach ($docuemntrole as $key => $value) {
                    DocumentRolePermissions::create([
                        'document_id' => $document_id,
                        'end_date' => $endDate  ?? '',
                        'is_allow_download' => $docuemntrole->is_allow_download,
                        'is_time_bound' => $docuemntrole->is_time_bound,
                        'role_id' => $docuemntrole->roleId,
                        'start_date' => $startDate ?? ''
                    ]);
                }




                // $userIds = UserRoles::select('userId')
                //     ->where('roleId', $docuemntrole->roleId)
                //     ->get();

                // foreach ($userIds as $userIdObject) {
                //     array_push($rolePermissionsArray, [
                //         'documentId' => $result->id,
                //         'userId' => $userIdObject->userId
                //     ]);
                // }
            }


            $documentUserPermissions = json_decode($request->document_user_permission);
            // foreach ($documentUserPermissions as $docuemntUser) {
            //     $startDate = '';
            //     $endDate = '';
            //     if ($docuemntUser->isTimeBound) {
            //         $startdate1 = date('Y-m-d', strtotime(str_replace('/', '-', $docuemntUser->startDate)));
            //         $enddate1 = date('Y-m-d', strtotime(str_replace('/', '-', $docuemntUser->endDate)));
            //         $startDate = Carbon::createFromFormat('Y-m-d', $startdate1)->startOfDay();
            //         $endDate = Carbon::createFromFormat('Y-m-d', $enddate1)->endOfDay();
            //     }

            //     DocumentUserPermissions::create([
            //         'documentId' => $result->id,
            //         'endDate' => $endDate  ?? '',
            //         'isAllowDownload' => $docuemntUser->isAllowDownload,
            //         'isTimeBound' => $docuemntUser->isTimeBound,
            //         'userId' => $docuemntUser->userId,
            //         'startDate' => $startDate ?? ''
            //     ]);




            //     array_push($rolePermissionsArray, [
            //         'documentId' => $result->id,
            //         'userId' => $docuemntUser->userId
            //     ]);
            // }




            DB::commit();
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Error in storing document in ' . $location,
            ], 409);
        }


        // try {




        //     // foreach (json_decode($metaDatas) as $metaTag) {
        //     //     DocumentMetaDatas::create(array(
        //     //         'documentId' =>   $result->id,
        //     //         'metatag' =>  $metaTag->metatag,
        //     //     ));
        //     }

        //     // $documentRolePermissions = json_decode($request->documentRolePermissions);
        //     // $rolePermissionsArray = array();
        //     // foreach ($documentRolePermissions as $docuemntrole) {
        //     //     $startDate = '';
        //     //     $endDate = '';
        //     //     if ($docuemntrole->isTimeBound) {
        //     //         $startdate1 = date('Y-m-d', strtotime(str_replace('/', '-', $docuemntrole->startDate)));
        //     //         $enddate1 = date('Y-m-d', strtotime(str_replace('/', '-', $docuemntrole->endDate)));
        //     //         $startDate = Carbon::createFromFormat('Y-m-d', $startdate1)->startOfDay();
        //     //         $endDate = Carbon::createFromFormat('Y-m-d', $enddate1)->endOfDay();
        //     //     }

        //     //     DocumentRolePermissions::create([
        //     //         'documentId' => $result->id,
        //     //         'endDate' => $endDate  ?? '',
        //     //         'isAllowDownload' => $docuemntrole->isAllowDownload,
        //     //         'isTimeBound' => $docuemntrole->isTimeBound,
        //     //         'roleId' => $docuemntrole->roleId,
        //     //         'startDate' => $startDate ?? ''
        //     //     ]);

        //     //     // DocumentAuditTrails::create([
        //     //     //     'documentId' => $result->id,
        //     //     //     'createdDate' =>  Carbon::now(),
        //     //     //     'operationName' => DocumentOperationEnum::Add_Permission->value,
        //     //     //     'assignToRoleId' => $docuemntrole->roleId
        //     //     // ]);

        //     //     $userIds = UserRoles::select('userId')
        //     //         ->where('roleId', $docuemntrole->roleId)
        //     //         ->get();

        //     //     foreach ($userIds as $userIdObject) {
        //     //         array_push($rolePermissionsArray, [
        //     //             'documentId' => $result->id,
        //     //             'userId' => $userIdObject->userId
        //     //         ]);
        //     //     }
        //     // }

        //     // $documentUserPermissions = json_decode($request->documentUserPermissions);
        //     // foreach ($documentUserPermissions as $docuemntUser) {
        //     //     $startDate = '';
        //     //     $endDate = '';
        //     //     if ($docuemntUser->isTimeBound) {
        //     //         $startdate1 = date('Y-m-d', strtotime(str_replace('/', '-', $docuemntUser->startDate)));
        //     //         $enddate1 = date('Y-m-d', strtotime(str_replace('/', '-', $docuemntUser->endDate)));
        //     //         $startDate = Carbon::createFromFormat('Y-m-d', $startdate1)->startOfDay();
        //     //         $endDate = Carbon::createFromFormat('Y-m-d', $enddate1)->endOfDay();
        //     //     }

        //     //     DocumentUserPermissions::create([
        //     //         'documentId' => $result->id,
        //     //         'endDate' => $endDate  ?? '',
        //     //         'isAllowDownload' => $docuemntUser->isAllowDownload,
        //     //         'isTimeBound' => $docuemntUser->isTimeBound,
        //     //         'userId' => $docuemntUser->userId,
        //     //         'startDate' => $startDate ?? ''
        //     //     ]);

        //     //     DocumentAuditTrails::create([
        //     //         'documentId' => $result->id,
        //     //         'createdDate' =>  Carbon::now(),
        //     //         'operationName' => DocumentOperationEnum::Add_Permission->value,
        //     //         'assignToUserId' => $docuemntUser->userId
        //     //     ]);


        //     //     array_push($rolePermissionsArray, [
        //     //         'documentId' => $result->id,
        //     //         'userId' => $docuemntUser->userId
        //     //     ]);
        //     // }


        //     // $rolePermissions = array_unique($rolePermissionsArray, SORT_REGULAR);
        //     // foreach ($rolePermissions as $rolePermission) {
        //     //     UserNotifications::create([
        //     //         'documentId' => $result->id,
        //     //         'userId' => $rolePermission['userId']
        //     //     ]);
        //     // }

        //     // $userId = Auth::parseToken()->getPayload()->get('userId');

        //     // $array = array_filter($documentUserPermissions, function ($item) use ($userId) {
        //     //     return $item->userId == $userId;
        //     // });

        //     // if (count($array) == 0) {
        //     //     DocumentUserPermissions::create(array(
        //     //         'documentId' =>   $result->id,
        //     //         'userId' =>  $userId,
        //     //         'isAllowDownload' => true
        //     //     ));
        //     // }
        //     DB::commit();
        //     return response()->json((string)$result->id);
        // } catch (\Exception $e) {
        //     DB::rollBack();
        //     return response()->json([
        //         'message' => 'Error in saving data.',
        //     ], 409);
        // }





    }



    public function getRoleUser(Request $request)
    {
        $roles = Role::with('admins', 'centers')->get();


        // $roles = [];
        if ($request->has('search')) {
            $center_name = $request->get('search');

            // $centers = Center::where('center_name', 'LIKE', "{$center_name}%")
            //     ->whereHas('levels', function ($query) use ($request) {
            //         $query->where('levels.id', '=', $request->level);
            //     })
            //     ->where('sessions', 'LIKE', "%$request->session%")
            //     ->where('status', '=', 1)
            //     ->limit(5)->get();
        }
        return response()->json($roles);
    }
}
