<?php

namespace App\Services;

use App\Repositories\UserRepository;
use App\User;

;

class UserService extends Service
{
    public function __construct(private UserRepository $userRepository)
    {}

    public function find($id)
    {
        return User::find($id);
    }

    public function allUsers()
    {
        return User::all();
    }

    public function searchUsers($q)
    {
        return User::where('name', 'like', "%{$q}%")
            ->orWhere('email', 'like', "%{$q}%")
            ->get();
    }
}
