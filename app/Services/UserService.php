<?php

namespace App\Services;

use App\Project;
use App\User;
use Illuminate\Support\Collection;

class UserService extends Service
{
    public function find($id): User|null
    {
        return User::find($id);
    }

    public function allUsers(): Collection
    {
        return User::all();
    }

    public function searchUsers($q): Collection
    {
        return User::where('name', 'like', "%{$q}%")
            ->orWhere('email', 'like', "%{$q}%")
            ->get();
    }
}
