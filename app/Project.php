<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description', 'start_date',
    ];

    protected $cast = [
        'start_date' => 'date'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'user_id',
    ];
    
    /**
    * Get the user associated with the project.
    */
    public function user()
    {
        return $this->hasOne(User::class);
    }
    
    /**
     * The tasks that belong to the project.
     */
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
