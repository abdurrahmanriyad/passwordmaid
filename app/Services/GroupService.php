<?php

namespace App\Services;

use App\Exceptions\AlreadyExists;
use App\Exceptions\InvalidItem;
use App\Exceptions\ItemNotFound;
use App\Repositories\GroupRepository;
use App\Repositories\ProjectRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Enums\Cache as CacheEnum;

class GroupService extends Service
{
    private $groupRepository = null;
    private $projectRepository = null;
    private $userService = null;

    public function __construct(
        GroupRepository $groupRepository,
        ProjectRepository $projectRepository,
        UserService $userService
    )
    {
        $this->groupRepository = $groupRepository;
        $this->projectRepository = $projectRepository;
        $this->userService = $userService;
    }

    public function find($id)
    {
        return $this->groupRepository->find($id);
    }

    public function getAuthProjectGroups($projectId)
    {
        $project = $this->projectRepository->find($projectId);
        if (!$project) {
            throw new ItemNotFound('Item not found!');
        }
        $project = $project->load(['groups.users', 'groups.credentials']);
        return $project->groups;
    }

    public function getSharedProjectGroups($project, $userId)
    {
        $groups = $this->groupRepository->getSharedGroups($project, $userId);
        return $groups;
    }

    public function getGroupUsers($groupId)
    {
        return $this->groupRepository->getGroupUsers($groupId);
    }

    public function getProjectsRecentGroups($projectIds)
    {
        return Cache::remember(getAuthRecentGrpCacheKey(), CacheEnum::RECENT_GROUPS, function () use ($projectIds){
            return $this->groupRepository->getProjectsRecentGroups($projectIds);
        });
    }

    public function getRecentSharedByGroups($userId)
    {
        return Cache::remember(getAuthRecentSharedCacheKey(), CacheEnum::RECENT_GROUPS, function () use ($userId){
            return $this->groupRepository->getRecentSharedByGroups($userId);
        });
    }

    public function createGroup(array $data, $projectId)
    {
        $data['slug'] = Str::slug($data['name']);
        $data['project_id'] = Str::slug($projectId);

        if ($this->groupRepository->isProjectGroupExists($data['slug'], $projectId)) {
            throw new AlreadyExists('Already exists!');
        }

        DB::beginTransaction();
        $group = $this->groupRepository->create($data);
        if ($group) {
            DB::commit();
            return true;
        }

        DB::rollBack();
        return false;
    }

    public function addGroupUser($groupId, $userId)
    {
        $group = $this->find($groupId);
        $exists = $group->users->where('id', $userId)->count();

        if ($exists) {
            throw new AlreadyExists();
        }

        if ($group) {
            $this->groupRepository->addGroupUser($group, $userId);
            return true;
        }

        return false;
    }

    public function deleteGroupUser($groupId, $userId)
    {
        $group = $this->find($groupId);

        if ($group) {
            $this->groupRepository->deleteGroupUser($group, $userId);
            return true;
        }

        return false;
    }

    public function getGroupSharableUsers($groupId)
    {
        $users = $this->userService->allUsers();
        $group = $this->find($groupId);

        if (!$group) return [];

        return $users
            ->whereNotIn('id', $group->users->pluck('id')->toArray());
    }

    public function searchGroupSharableUsers($groupId, $q)
    {
        $users = $this->userService->searchUsers($q);
        $group = $this->find($groupId);

        if (!$group) return [];

        return $users
            ->whereNotIn('id', $group->users->pluck('id')->toArray());
    }

    public function getGroupSharableUsersForSelect2($groupId)
    {
        $response = [];
        $users = $this->getGroupSharableUsers($groupId);

        foreach ($users as $user) {
            $response['id'] = $user->id;
            $response['text'] = $user->name;
        }

        return $response;
    }

    public function searchGroupSharableUsersForSelect2($groupId, $q)
    {
        $response = [];
        $users = $this->searchGroupSharableUsers($groupId, $q);

        $arrayCount = 0;
        foreach ($users as $user) {
            $response[$arrayCount]['id'] = $user->id;
            $response[$arrayCount]['text'] = $user->name;
            $arrayCount++;
        }

        return $response;
    }

    public function isProjectGroup($projectId, $groupId)
    {
        return $this->groupRepository->isProjectGroup($projectId, $groupId);
    }

    public function updateAuthGroup(array $data, $id)
    {
        if (!$this->groupRepository->find($id)) {
            throw new InvalidItem('Invalid group!');
        }
        return $this->groupRepository->updateAuth($data, $id);
    }

    public function updateAuthProjectStatus($status, $id)
    {
        if (!$this->groupRepository->find($id)) {
            throw new InvalidItem('Invalid group!');
        }
        // todo: check if group has group inside
        return $this->groupRepository->updateAuth(['active' => (string)$status], $id);
    }

    public function isUserGroup($groupId, $userId)
    {
        if (!$this->groupRepository->isUserGroup($groupId,$userId)) return false;
        return true;
    }

    public function isSharedGroup($groupId, $userId)
    {
        if (!$this->groupRepository->isSharedGroup($groupId,$userId)) return false;
        return true;
    }
}