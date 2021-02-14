<?php

namespace App\Repositories;

use App\User;

class UserRepository implements \App\Contracts\Repositories\UserRepository
{
    public function find($id)
    {
        return User::find($id);
    }

    public function all()
    {
        return User::all();
    }

    public function search($q)
    {
        return User::where('name', 'like', "%{$q}%")
            ->orWhere('email', 'like', "%{$q}%")
            ->get();
    }
}
