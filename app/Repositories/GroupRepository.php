<?php

namespace App\Repositories;

use App\Exceptions\ItemNotFound;
use App\Group;
use App\GroupUser;
use App\Project;
use Illuminate\Support\Str;

class GroupRepository implements \App\Contracts\Repositories\GroupRepository
{
    public function find($id)
    {
        return Group::find($id);
    }

    public function isUserGroup($groupId, $userId)
    {
        $group = $this->find($groupId);
        if (!$group) throw new ItemNotFound();
        return $group->project->owner->id == $userId;
    }

    public function isSharedGroup($groupId, $userId)
    {
        return GroupUser::where('group_id', $groupId)
            ->where('user_id', $userId)
            ->exists();
    }

    public function getAll()
    {
        return Group::latest()->get();
    }

    public function findBySlug($slug)
    {
        return Group::where('slug', Str::lower($slug))
            ->first();
    }

    public function isProjectGroupExists($slug, $projectId)
    {
        return Group::where('slug', Str::lower($slug))
            ->where('project_id', $projectId)
            ->first();
    }

    public function getGroupUsers($groupId)
    {
        $group = $this->find($groupId);
        if (!$group) throw new ItemNotFound();

        return $group->users;
    }

    public function getProjectsRecentGroups($projectIds)
    {
        return Group::whereIn('project_id', $projectIds)
            ->orderBy('id', 'DESC')
            ->take(5)
            ->get();
    }

    public function getRecentSharedByGroups($userId)
    {
        return Group::whereHas('users', function ($query) use ($userId) {
            $query->where('id', $userId);
        })->take(5)->get();
    }

    public function getSharedGroups(Project $project, $userId)
    {
        return Group::withCount('credentials')->whereHas('users', function ($query) use ($userId) {
            $query->where('id', $userId);
        })->where('project_id', $project->id)->get();
    }

    public function getAllSharedGroups($userId)
    {
        return Group::whereHas('users', function ($query) use ($userId) {
            $query->where('id', $userId);
        })->get();
    }

    public function create($data)
    {
        return Group::create($data);
    }

    public function update($data, $id)
    {
        return Group::where('id', $id)->update($data);
    }

    public function updateAuth($data, $id)
    {
        return Group::where('id', $id)
            ->update($data);
    }

    public function isProjectGroup($projectId, $groupId)
    {
        return Group::where('id', $groupId)
            ->where('project_id', $projectId)
            ->exists();
    }

    public function addGroupUser(Group $group, $userId)
    {
        $group->users()->attach($userId);
    }

    public function deleteGroupUser(Group $group, $userId)
    {
        $group->users()->detach($userId);
    }

    protected function loadUserRelationships($group)
    {
//        $group->load('subscriptions');

        return $group;
    }
}
