<?php

namespace App\Http\Controllers;

use App\Project;
use Illuminate\Http\Request;
use Log;

class ProjectController extends Controller
{
    private $rules = [
        'name'=>'required|string|min:5|max:255',
        'description'=>'required|string|min:10|max:500',
        'start_date'=>'required|date',
    ];
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $projects = auth()->user()->projects()->orderBy('name')->get();

        return view('projects.index', ['projects'=>$projects]);
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
            $project = new Project();
            $project->fill($request->except('_method'));
            $project->user_id = auth()->id();
            $project->save();

            return response()->json(['message'=>'Proyecto: '.$project->name.' creado', 'project_id'=>$project->id]);
        } catch (\Throwable $th) {
            Log::error($th);
            return response()->json(['message'=>'No se puedo crear el proyecto, contacte al admin.'], 500);
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
        $project = Project::where('id', $id)->where('user_id', auth()->id())->firstOrFail();
        return response()->json($project);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param integer  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $project = Project::where('id', $id)->where('user_id', auth()->id())->firstOrFail();

        return view('projects.edit', ['project'=>$project]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param integer  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate($this->rules);

        try {
            $project = Project::where('id', $id)->where('user_id', auth()->id())->firstOrFail();
            $project->fill($request->except('_method'));
            $project->save();

            return response()->json(['message'=>'Proyecto: '.$project->name.' actualizado', 'project_id'=>$project->id]);
        } catch (\Throwable $th) {
            Log::error($th);
            return response()->json(['message'=>'No se pudo actualizar el proyecto, contacte al admin.'], 500);
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
            $project = Project::where('id', $id)->where('user_id', auth()->id())->firstOrFail();
            $project->tasks()->delete();

            $project->delete();

            return response()->json(['message'=>'Proyecto: '.$project->name.' eliminado']);
        } catch (\Throwable $th) {
            Log::error($th);
            return response()->json(['message'=>'No se pudo eliminar el proyecto, contacte al admin.'], 500);
        }
    }
}
