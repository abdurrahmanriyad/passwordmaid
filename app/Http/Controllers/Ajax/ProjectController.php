<?php

namespace App\Http\Controllers\Ajax;

use App\Exceptions\AlreadyExists;
use App\Exceptions\InvalidItem;
use App\Project;
use App\Responses\AjaxResponse;
use App\Services\ProjectService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;

class ProjectController extends Controller
{
    private $projectService = null;

    public function __construct(ProjectService $projectService)
    {
        $this->projectService = $projectService;
    }

    public function getAll()
    {
        $projects = $this->projectService->getAllProjects();
        return AjaxResponse::render('success', $projects);
    }

    public function authFind(Project $project)
    {
        try {
            if ($this->authorize('modify', $project)) {
                return AjaxResponse::render('success', $project);
            }
            return AjaxResponse::render('error',  $project, 'unauthorized!', 401);
        } catch (\Exception $e) {
            return AjaxResponse::render('error', $project, 'Something went wrong!', 401);
        }
    }

    public function authGetAll()
    {
        $projects = $this->projectService->getUserProjects(auth()->user()->id);
        return AjaxResponse::render('success', $projects);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|max:150'
        ]);

        try {
            $project = $this->projectService->createProject($validatedData);
            if ($project) {
                return AjaxResponse::render('success', $project, "Successfully created!");
            }
            return AjaxResponse::render('error', [], "Couldn't create project!", 400);
        } catch (AlreadyExists $e) {
            return AjaxResponse::render('error', [], $e->getMessage(), 400);
        } catch (\Exception $e) {
            return AjaxResponse::render('error', [], 'Something went wrong!', 400);
        }
    }

    public function update(Request $request, Project $project)
    {
        $validatedData = $request->validate([
            'name' => 'required|max:150',
            'active' => ['nullable', Rule::in([0, 1])]
        ]);
        try {
            $this->authorize('modify', $project);

            $project = $this->projectService->updateUserProject($validatedData, $project->id, auth()->user()->id);
            if ($project) {
                return AjaxResponse::render('success', [], 'Updated successfully!');
            }
            return AjaxResponse::render('error', [], "Couldn't update project.", 400);
        } catch (InvalidItem $e) {
            return AjaxResponse::render('error', [], $e->getMessage(), 400);
        } catch (\Exception $e) {
            return AjaxResponse::render('error', [], "Something went wrong!", 400);
        }
    }

    public function updateStatus(Request $request, Project $project)
    {
        $validatedData = $request->validate([
            'active' => ['required', 'numeric', Rule::in([0, 1])]
        ]);

        try {
            $this->authorize('modify', $project);

            $project = $this->projectService->updateUserProjectStatus($validatedData['active'], $project->id, auth()->user()->id);
            if ($project) {
                return AjaxResponse::render('success', [], 'Updated successfully!');
            }
            return AjaxResponse::render('error', [], "Couldn't update status", 400);
        } catch (InvalidItem $e) {
            return AjaxResponse::render('error', [], $e->getMessage(), 400);
        } catch (\Exception $e) {
            return AjaxResponse::render('error', [], "Something went wrong!", 400);
        }
    }

    public function delete(Project $project)
    {
        try {
            $this->authorize('modify', $project);

            $deleted = $this->projectService->deleteUserProject($project->id, auth()->user()->id);
            if ($deleted) {
                return AjaxResponse::render('success', [], 'Deleted successfully!');
            }
            return AjaxResponse::render('error', [], "Couldn't delete", 400);
        } catch (\Exception $e) {
            return AjaxResponse::render('error', [], "Something went wrong!", 400);
        }
    }
}
