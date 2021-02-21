<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function groups()
    {
        return $this->hasMany(Group::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
