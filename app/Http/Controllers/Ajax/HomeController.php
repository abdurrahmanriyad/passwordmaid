<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Responses\AjaxResponse;
use App\Services\ProjectService;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    private $projectService;

    public function __construct(ProjectService $projectService)
    {
        $this->middleware('auth');
        $this->projectService = $projectService;
    }

    public function getDashboardData()
    {
        $stats = $this->projectService->getUserStats(auth()->user());
        return AjaxResponse::render('success', $stats);
    }
}
