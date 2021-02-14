<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $appends = array('total_credentials', 'total_users');

    protected $guarded = [];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function getTotalUsersAttribute()
    {
        return $this->users()->count();
    }

    public function credentials()
    {
        return $this->hasMany(Credential::class);
    }

    public function getTotalCredentialsAttribute()
    {
       return $this->credentials()->count();
    }
}
