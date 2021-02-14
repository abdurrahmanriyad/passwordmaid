<?php

namespace App\Services;

class CustomFieldService extends Service
{
    private $credTypeRepository = null;

    public function getAllCredentials()
    {
        return $this->credTypeRepository->getAll();
    }
}