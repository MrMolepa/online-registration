<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentComments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DocumentCommentController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()) {

            $id = $request->id;
            $comments = DB::table('document_comments')
                ->select(
                    [
                        'document_comments.id',
                        'document_comments.created_by',
                        'document_comments.comment',
                        'document_comments.created_date',
                        'document_users.document_user_id',
                        'document_users.role_id',
                        'document_users.document_user_type'
                    ],
                )
                ->join('document_users', 'document_comments.created_by', '=', 'document_users.id')
                ->where('document_id', $id)
                ->get();
            $commentsHtml = '';
            foreach ($comments  as $comment) {
                $email = $comment->document_user_type::find($comment->document_user_id)->email;
                $created_date = date('M j, Y @ g:i A', strtotime($comment->created_date));
                $commentsHtml .= "<div class='comment-wrap'>
                                <div class='comment-block'>
                                    <p class='comment-text'>$comment->comment</p>
                                            <div class='bottom-comment'>
                                                <div class='comment-date'>$created_date</div>
                                                <ul class='comment-actions'>
                                                    <li class='complain'> $email</li>
                                                    <li class='reply'>Reply</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>";
            }
            $commentsHtml .= "<div class='comment-wrap'>
                            <div class='comment-block'>
                                    <textarea name='comment' id='comment' cols='30' rows='3' placeholder='Add comment...'></textarea>
                                    <input type='hidden' name='document_id' value='$id'>
                            </div>
                        </div>";

            return response()->json(['comments' =>  $commentsHtml]);
        }
    }
    public function store(Request $request)
    {


        $validator = Validator::make($request->all(), [
            'comment' => "required",
            'document_id' => "required",

        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
        $comment = new DocumentComments();
        $comment->comment = $request->comment;
        $comment->document_id = $request->document_id;
        $comment->save();
        return response()->json(['success' => 'Successfully saved the records']);
    }

    public function distroy($id)
    {

        return DocumentComments::destroy($id);
    }
}
