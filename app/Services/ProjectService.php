<?php

namespace App\Services;

use App\Exceptions\AlreadyExists;
use App\Exceptions\InvalidItem;
use App\GroupUser;
use App\Repositories\GroupRepository;
use App\Repositories\ProjectRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use App\User;

class ProjectService extends Service {
    private $projectRepository = null;
    private $groupRepository = null;
    private $groupService = null;

    public function __construct(ProjectRepository $projectRepository, GroupRepository $groupRepository, GroupService $groupService)
    {
        $this->projectRepository = $projectRepository;
        $this->groupRepository = $groupRepository;
        $this->groupService = $groupService;
    }

    public function getAllProjects()
    {
        return $this->projectRepository->getAll();
    }

    public function getUserProjects($userId)
    {
        $userProjects = $this->projectRepository->getUserProjects($userId);
        return $userProjects->merge($this->projectRepository->getSharedProjects($userId))->unique();
    }

    public function find($id)
    {
        return $this->projectRepository->find($id);
    }

    public function createProject(array $data)
    {
        $data['slug'] = Str::slug($data['name']);
        $data['user_id'] = auth()->user()->id;
        if ($this->projectRepository->findBySlug($data['slug'])) {
            throw new AlreadyExists('Already exists!');
        }

        return $this->projectRepository->create($data);
    }

    public function updateUserProject(array $data, $id, $userId)
    {
        if (!$this->projectRepository->find($id)) {
            throw new InvalidItem('Invalid project!');
        }
        return $this->projectRepository->updateUserProject($data, $id, $userId);
    }

    public function updateUserProjectStatus($status, $id, $userId)
    {
        if (!$this->projectRepository->find($id)) {
            throw new InvalidItem('Invalid project!');
        }
        // todo: check if project has group inside
        return $this->projectRepository->updateUserProject(['active' => (string)$status], $id, $userId);
    }

    public function deleteUserProject($projectId, $userId)
    {
        return $this->projectRepository->deleteUserProject($projectId, $userId);
    }

    public function isUserProject($userId, $projectId)
    {
        return $this->projectRepository->isUserProject($userId, $projectId);
    }

    public function getUserStats(User $user)
    {
        $projects = $this->getUserProjects($user->id);
        $data = $this->getUserGroupsStatsFromProject($projects, $user->id);
        $data['total_projects'] = $projects->count();
        $data['recent_groups'] = $this->groupService->getProjectsRecentGroups($user->projects()->pluck('id')->toArray());
        $data['recent_shared_by_groups'] = $this->groupService->getRecentSharedByGroups($user->id);
        return $data;
    }

    public function getUserGroupsStatsFromProject(Collection $projects, $userId)
    {
        $totalGroups = 0;
        $sharedGroups = 0;
        $sharedGroupsWithMe = ($this->groupRepository->getAllSharedGroups($userId))->count();
        $sharedUsers = collect();
        foreach ($projects as $project) {
            // if owner
            if ($project->owner->id == $userId) {
                // get owner groups count
                $totalGroups += $project->groups->count();
                // get owner shared group count
                foreach ($project->groups as $group) {
                    $sharedUsers->merge($group->users);
                    if ($group->users->count()) {
                        $sharedGroups += 1;
                        $sharedUsers = $sharedUsers->merge($group->users);
                    };
                }
            }
        }

        return [
            'total_groups' => $totalGroups,
            'shared_groups' => $sharedGroups,
            'shared_with_me' => $sharedGroupsWithMe,
            'shared_users' => ($sharedUsers->unique('id'))->values()->all()
        ];
    }
}