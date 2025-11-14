<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Documents;
use App\Models\DocumentVersions;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class DocumentVersionController extends Controller
{
    //



    public function index(Request $request)
    {






        if ($request->ajax()) {
            $id = $request->id;
            $document = Documents::with('users.document_user')->findOrFail($id);
            if ($document == null) {
                return   $document;
            }

            $documentsVersions = DocumentVersions::select([
                'document_versions.id',
                'document_versions.url',
                'document_versions.modified_date',
                'document_users.document_user_type',
                'document_users.document_user_id'
            ])
                ->leftJoin('document_users', 'document_versions.created_by', '=', 'document_users.id')
                ->where('document_versions.document_id', $id)
                ->orderBy('document_versions.modified_date', 'desc')
                ->get();

            $documentsVersions->map(function ($item) {
                $item->created_by = $item->document_user_type::find($item->document_user_id);
                return $item;
            });


            $modified_date = date('Y-m-d H:i:s e', strtotime($document->modified_date));
            $results = $documentsVersions->push((object)[
                'is_current_version' => true,
                'id' => $document->id,
                'url' => $document->url,
                'modified_date' =>   $modified_date,
                'created_by' =>  $document->users->document_user->email,
                'document_user_type' => '',
                'document_user_id' => ''
            ]);
            $documentsVersions = collect($results)->sortByDesc('modified_date')->values();

            return DataTables::of($documentsVersions)
                ->editColumn('created_by', function ($row) {
                    return $row->created_by;
                })
                ->editColumn('modified_date', function ($row) {
                    return $row->modified_date;
                })
                ->editColumn('is_current_version', function ($row) {
                    return   $row->is_current_version;
                })
                ->rawColumns(['created_by', 'modified_date', 'is_current_version'])
                ->make();
        }
    }

    public function store($request, $path)
    {
        try {
            DB::beginTransaction();
            $documentModel = Documents::findOrFail($request->documentId);
            // if ($documentModel == null) {
            //     throw new RepositoryException('Document Not Found.');
            // }

            $model = DocumentVersions::create([
                'url' => $documentModel->url,
                'documentId' => $documentModel->id,
                'createdBy' => $documentModel->createdBy,
                'modifiedBy' => $documentModel->modifiedBy,
                'location' => $documentModel->location,
            ]);

            $model->createdDate = $documentModel->createdDate;
            $model->modifiedDate = $documentModel->modifiedDate;

            $model->save();

            $userId = Auth::parseToken()->getPayload()->get('userId');
            $documentModel->url = $path;
            $documentModel->createdDate = Carbon::now()->addSeconds(2);
            $documentModel->modifiedDate = Carbon::now()->addSeconds(2);
            $documentModel->createdBy = $userId;

            $documentModel->save();

            $result = $this->parseResult($model);

            DB::commit();
            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error in saving data.',
            ], 409);
        }
    }

    public function restoreDocumentVersion($id, $versionId)
    {
        try {
            DB::beginTransaction();
            $documentModel = Documents::findOrFail($id);
            // if ($documentModel == null) {
            //     throw new RepositoryException('Document Not Found.');
            // }

            $version = DocumentVersions::findOrFail($versionId);

            // if ($version  == null) {
            //     throw new RepositoryException('Document version Not Found.');
            // }

            $newVersion = DocumentVersions::create([
                'url' => $documentModel->url,
                'documentId' => $documentModel->id,
                'createdBy' => $documentModel->createdBy,
                'modifiedBy' => $documentModel->modifiedBy,
                'location' => $documentModel->location,
            ]);

            $newVersion->createdDate = $documentModel->createdDate;
            $newVersion->modifiedDate = $documentModel->modifiedDate;

            $newVersion->save();

            $userId = Auth::parseToken()->getPayload()->get('userId');
            $documentModel->url = $version->url;
            $documentModel->createdBy = $userId;
            $documentModel->modifiedDate = Carbon::now()->addSeconds(2);
            $documentModel->createdDate = Carbon::now()->addSeconds(2);

            $documentModel->save();

            $result = $this->parseResult($newVersion);

            DB::commit();
            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error in saving data.',
            ], 409);
        }
    }
}
