<?php

namespace App\Services;

use App\Repositories\CredentialTypeRepository;
use App\Repositories\ProjectRepository;
use Illuminate\Support\Str;

class CredentialTypeService extends Service
{
    private $credTypeRepository = null;

    public function __construct(CredentialTypeRepository $credTypeRepository)
    {
        $this->credTypeRepository = $credTypeRepository;
    }

    public function getAllCredentials()
    {
        return $this->credTypeRepository->getAll();
    }
}