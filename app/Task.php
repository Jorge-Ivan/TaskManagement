<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'description', 'expire_date', 'status_id',
    ];

    protected $cast = [
        'expire_date' => 'date'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'project_id',
    ];
    
    /**
    * Get the project associated with the task.
    */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
    
    /**
    * Get the user associated with the task.
    */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
