<?php

namespace App\Services;

use App\Repositories\UserRepository;;

class UserService extends Service
{
    private $userRepository = null;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function find($id)
    {
        return $this->userRepository->find($id);
    }

    public function allUsers()
    {
        return $this->userRepository->all();
    }

    public function searchUsers($q)
    {
        return $this->userRepository->search($q);
    }
}