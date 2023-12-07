<?php

namespace App\Http\Controllers;

use App\Task;
use Illuminate\Http\Request;
use Log;

class TaskController extends Controller
{
    private $rules = [
        'title'=>'required|string|min:5|max:255',
        'description'=>'required|string|min:10|max:500',
        'expire_date'=>'nullable|date',
        'user_id'=>'required|exists:users,id',
        'status_id'=>'required|exists:status,id',
    ];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tasks = Task::where('id', $id)->where('user_id', auth()->id())->get();

        return view('tasks.index', ['tasks'=>$tasks]);
    }

    /**
     * validation for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function validateCreate(Request $request)
    {
        $request->validate($this->rules);

        return response()->json($request->except('_method'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate($this->rules);

        try {
            $task = new Task();
            $task->fill($request->except('_method'));
            $task->user_id = $request->user_id;
            $task->project_id = $request->project_id;
            $task->save();

            return response()->json(['message'=>'Tarea: '.$task->title.' creada', 'task_id'=>$task->id]);
        } catch (\Throwable $th) {
            Log::error($th);
            return response()->json(['message'=>'No se puedo crear la tarea, contacte al admin.'], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param integer  $id
     * @return \Illuminate\Http\Response
     */
    
    public function show($id)
    {
        $task = Task::where('id', $id)->where('user_id', auth()->id())->firstOrFail();
        return response()->json($task);
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param integer  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $request->validate($this->rules);

        try {
            $task = Task::where('id', $request->id)->where('user_id', auth()->id())->firstOrFail();
            $task->fill($request->except('_method'));
            $task->save();

            return response()->json(['message'=>'Tarea: '.$task->name.' actualizada', 'task_id'=>$task->id]);
        } catch (\Throwable $th) {
            Log::error($th);
            return response()->json(['message'=>'No se pudo actualizar la tarea, contacte al admin.'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param integer  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $task = Task::where('id', $id)->where('user_id', auth()->id())->firstOrFail();

            $task->delete();

            return response()->json(['message'=>'Tarea: '.$task->name.' eliminada']);
        } catch (\Throwable $th) {
            Log::error($th);
            return response()->json(['message'=>'No se pudo eliminar la tarea, contacte al admin.'], 500);
        }
    }
}
