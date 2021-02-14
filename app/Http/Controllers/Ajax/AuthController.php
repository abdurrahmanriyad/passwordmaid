<?php

namespace App\Http\Controllers\Ajax;

use App\Enums\Credential;
use App\Exceptions\ItemNotFound;
use App\Group;
use App\Responses\AjaxResponse;
use App\Services\CredentialService;
use App\Services\GroupService;
use App\Services\ProjectService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function checkLogin()
    {
        if (auth()->check()) {
            return AjaxResponse::render('success', [], 'Logged in');
        }

        return AjaxResponse::render('error', [], 'Not logged in', 401);
    }
}
