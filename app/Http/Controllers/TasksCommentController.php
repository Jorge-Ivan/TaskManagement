<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\TasksComment;
use App\Task;
use Log;

class TasksCommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $task_id)
    {
        $task = Task::findOrFail($task_id);
        $comments = $task->comments()->orderBy('created_at', 'desc')->paginate(20);

        return view('tasks.comments', ['task'=>$task, 'comments'=>$comments]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $task_id)
    {
        $request->validate([
            'content' => 'required|string|min:10'
        ]);

        $task = Task::findOrFail($task_id);

        try {
            $comment = new TasksComment();
            $comment->fill($request->except('_method'));
            $comment->user_id = auth()->id();
            $comment->task_id = $task_id;

            $comment->save();

            return response()->json(['message'=>'Comentario agregado', 'comment_id'=>$comment->id]);
        } catch (\Throwable $th) {
            Log::error($th);
            return response()->json(['message'=>'No se pudo agregar el comentario, contacte al admin.'], 500);
        }
    }
}
