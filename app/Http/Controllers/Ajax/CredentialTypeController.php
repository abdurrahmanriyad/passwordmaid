<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Responses\AjaxResponse;
use App\Services\CredentialTypeService;

class CredentialTypeController extends Controller
{
    private $credTypeService = null;

    public function __construct(CredentialTypeService $credTypeService)
    {
        $this->credTypeService = $credTypeService;
    }

    public function getAll()
    {
        $credTypes =  $this->credTypeService->getAllCredentials();
        return AjaxResponse::render('success', $credTypes);
    }
}
