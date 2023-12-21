<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::middleware(['auth'])->prefix('proyectos')->group(function () {
    Route::get('/', 'ProjectController@index')->name('projects.index');
    Route::post('/create/validate', 'ProjectController@validateCreate')->name('projects.create.validate');
    Route::post('/create', 'ProjectController@store')->name('projects.store');
    Route::get('/{id}', 'ProjectController@show')->name('projects.show');
    Route::delete('/{id}', 'ProjectController@destroy')->name('projects.delete');
    Route::get('/edit/{id}', 'ProjectController@edit')->name('projects.edit');
    Route::put('/edit/{id}', 'ProjectController@update')->name('projects.update');
});

Route::middleware(['auth'])->prefix('tareas')->group(function () {
    Route::post('/create/validate', 'TaskController@validateCreate')->name('tasks.create.validate');
    Route::post('/', 'TaskController@store')->name('tasks.store');
    Route::get('/{id?}', 'TaskController@show')->name('tasks.show');
    Route::delete('/{id}', 'TaskController@destroy')->name('tasks.delete');
    Route::put('/', 'TaskController@update')->name('tasks.update');
});

Route::middleware(['auth'])->prefix('tareas/{task_id}/comentarios')->group(function () {
    Route::get('/', 'TasksCommentController@index')->name('comments.index');
    Route::post('/', 'TasksCommentController@store')->name('comments.store');
});

Route::middleware(['auth'])->prefix('usuarios')->group(function () {
    Route::get('/list', 'HomeController@listUsers')->name('users.list');
    Route::get('/mis-tareas', 'TaskController@index')->name('tasks.index');
    Route::post('/mis-tareas/estado/{id?}', 'TaskController@updateStatus')->name('tasks.status');
});
