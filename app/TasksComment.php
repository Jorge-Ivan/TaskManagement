<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TasksComment extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'content'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'task_id', 'user_id',
    ];
    
    /**
    * Get the task associated with the task.
    */
    public function task()
    {
        return $this->belongsTo(Task::class);
    }
    
    /**
    * Get the user associated with the task.
    */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
