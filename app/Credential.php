<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Credential extends Model
{
    protected $casts = [
      'is_private' => 'int'
    ];

    protected $guarded = [];

    public function customFields()
    {
        return $this->hasMany(CustomField::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}
