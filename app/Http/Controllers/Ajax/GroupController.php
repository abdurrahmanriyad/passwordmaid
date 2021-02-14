<?php

namespace App\Http\Controllers\Ajax;

use App\Events\GroupShared;
use App\Exceptions\AlreadyExists;
use App\Exceptions\InvalidItem;
use App\Exceptions\InvalidResourceAssignment;
use App\Exceptions\ItemNotFound;
use App\Group;
use App\Project;
use App\Responses\AjaxResponse;
use App\Services\GroupService;
use App\Services\ProjectService;
use App\Services\UserService;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;

class GroupController extends Controller
{
    private $groupService = null;
    private $projectService = null;
    private $userService = null;

    public function __construct(GroupService $groupService, ProjectService $projectService, UserService $userService)
    {
        $this->groupService = $groupService;
        $this->projectService = $projectService;
        $this->userService = $userService;
    }

    public function getAll($projectId)
    {
        try {
            $groups = $this->groupService->getAuthProjectGroups($projectId);
            return AjaxResponse::render('success', $groups);
        } catch (ItemNotFound $e) {
            return AjaxResponse::render('error', [], $e->getMessage(), 400);
        } catch (\Exception $e) {
            return AjaxResponse::render('error', [], "Something went wrong!", 400);
        }
    }

    public function authGetAll(Project $project)
    {
        try {
            if (auth()->user()->can('modify', $project)) {
                $groups = $this->groupService->getAuthProjectGroups($project->id);
            } else {
                if ($project->active == '0') throw new ItemNotFound('Project not exists!');
                $groups = $this->groupService->getSharedProjectGroups($project, auth()->user()->id);
            }
            return AjaxResponse::render('success', [
                'project_name' => $project->name,
                'groups' => $groups
            ]);
        } catch (ItemNotFound $e) {
            return AjaxResponse::render('error', [], $e->getMessage(), 400);
        } catch (\Exception $e) {
            return AjaxResponse::render('error', [], "Something went wrong!", 400);
        }
    }


    public function authFind(Group $group)
    {
        try {
            $isAuthorized = $this->groupService->isUserGroup($group->id, auth()->user()->id);
            if (!$isAuthorized) return AjaxResponse::render('error', [], 'Unauthorized!', 400);
            return AjaxResponse::render('success', $group->load('project.owner'));
        } catch (ItemNotFound $e) {
            return AjaxResponse::render('error', [], $e->getMessage(), 400);
        } catch (\Exception $e) {
            return AjaxResponse::render('error', [], "Something went wrong!", 400);
        }
    }

    public function getGroupUsers($groupId)
    {
        try {
            $users = $this->groupService->getGroupUsers($groupId);
            return AjaxResponse::render('success', $users);
        } catch (ItemNotFound $e) {
            return AjaxResponse::render('error', [], $e->getMessage(), 400);
        } catch (\Exception $e) {
            return AjaxResponse::render('error', [], 'Invalid data!', 400);
        }
    }

    public function getAuthGroupUsers(Group $group)
    {
        try {
            // todo: get group here and pass group to getGroup users
            $this->authorize('modify', $group);
            $users = $this->groupService->getGroupUsers($group->id);
            return AjaxResponse::render('success', $users);
        } catch (ItemNotFound $e) {
            return AjaxResponse::render('error', [], $e->getMessage(), 400);
        } catch (\Exception $e) {
            return AjaxResponse::render('error', [], 'Invalid data!', 400);
        }
    }

    public function addGroupUser(Request $request, Group $group)
    {
        $validatedData = $request->validate([
            'id' => 'required|exists:users,id'
        ]);

        try {
            $this->authorize('modify', $group);

            if ($this->groupService->isUserGroup($group->id, $validatedData['id'])) {
                throw new InvalidResourceAssignment('Already owner of the group!');
            }

            $groupShared = $this->groupService->addGroupUser($group->id, $validatedData['id']);
            if ($groupShared) {
                event(new GroupShared(auth()->user(), $this->userService->find($validatedData['id'])));
                return AjaxResponse::render('success', [], "Successfully shared!");
            }

            return AjaxResponse::render('error', [], "Couldn't share group", 400);
        } catch (InvalidResourceAssignment $e) {
            return AjaxResponse::render('error', [], $e->getMessage(), 400);
        } catch (AlreadyExists $e) {
            return AjaxResponse::render('error', [], $e->getMessage(), 400);
        } catch (\Exception $e) {
            return AjaxResponse::render('error', [], $e->getMessage(), 400);
            return AjaxResponse::render('error', [], "Something went wrong!", 400);
        }
    }

    public function deleteGroupUser(UserService $userService, Group $group, $userId)
    {
        try {
            $this->authorize('modify', $group);

            if (!$userService->find($userId)) throw new InvalidItem('Invalid data!');

            $groupShared = $this->groupService->deleteGroupUser($group->id, $userId);
            if ($groupShared) {
                return AjaxResponse::render('success', [], "Successfully deleted!");
            }

            return AjaxResponse::render('error', [], "Couldn't delete.", 400);
        } catch (InvalidItem $e) {
            return AjaxResponse::render('error', [], "Something went wrong!", 400);
        } catch (\Exception $e) {
            return AjaxResponse::render('error', [], "Something went wrong!", 400);
        }
    }

    public function getGroupSharableUsers(Request $request, Group $group)
    {
        // todo: refactor to take it to a facade
        try {
            $this->authorize('modify', $group);
            if ($request->has('forSelect2')) {
                if ($request->has('q')) {
                    $users = $this->groupService->searchGroupSharableUsersForSelect2($group->id, $request->q);
                } else {
                    $users = $this->groupService->getGroupSharableUsersForSelect2($group->id);
                }
            } else {
                if ($request->has('q')) {
                    $users = $this->groupService->searchGroupSharableUsers($group->id, $request->q);
                } else {
                    $users = $this->groupService->getGroupSharableUsers($group->id);
                }
            }
            return AjaxResponse::render('success', $users);
        } catch (\Exception $e) {
            return AjaxResponse::render('error', [], 'Invalid data!', 400);
        }
    }

    public function store(Request $request, Project $project)
    {
        $validatedData = $request->validate([
            'name' => 'required|max:150'
        ]);

        try {
            $this->authorize('modify', $project);

            $group = $this->groupService->createGroup($validatedData, $project->id);
            if ($group) {
                return AjaxResponse::render('success', $group, "Successfully created!");
            }
            return AjaxResponse::render('error', [], "Couldn't create group", 400);
        } catch (AlreadyExists $e) {
            return AjaxResponse::render('error', [], $e->getMessage(), 400);
        } catch (\Exception $e) {
            return AjaxResponse::render('error', [], "Something went wrong!", 400);
        }
    }

    public function update(Request $request, Project $project, Group $group)
    {
        $validatedData = $request->validate([
            'name' => 'required|max:150',
            'active' => ['nullable', Rule::in([0, 1])]
        ]);

        try {
            $this->authorize('modify', $project);

            if (!$this->groupService->isProjectGroup($project->id, $group->id)) if (!$project) {
                return AjaxResponse::render('error', [], "Unauthorized!");
            }

            $group = $this->groupService->updateAuthGroup($validatedData, $group->id);
            if ($group) {
                return AjaxResponse::render('success', [], 'Updated successfully!');
            }
            return AjaxResponse::render('error', [], "Couldn't update group", 400);
        } catch (InvalidItem $e) {
            return AjaxResponse::render('error', [], $e->getMessage(), 400);
        } catch (\Exception $e) {
            return AjaxResponse::render('error', [], "Something went wrong!", 400);
        }
    }

    public function updateStatus(Request $request, $id)
    {
        $validatedData = $request->validate([
            'active' => ['required', 'numeric', Rule::in([0, 1])]
        ]);

        try {
            $group = $this->groupService->updateAuthProjectStatus($validatedData['active'], $id);
            if ($group) {
                return AjaxResponse::render('success', [], 'Updated successfully!');
            }
            return AjaxResponse::render('error', [], "Couldn't update status", 400);
        } catch (InvalidItem $e) {
            return AjaxResponse::render('error', [], $e->getMessage(), 400);
        } catch (\Exception $e) {
            return AjaxResponse::render('error', [], "Something went wrong", 400);
        }
    }
}
