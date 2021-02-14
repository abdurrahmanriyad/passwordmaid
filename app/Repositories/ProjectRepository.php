<?php

namespace App\Repositories;

use App\Group;
use App\GroupUser;
use App\Project;
use App\User;
use Carbon\Carbon;
use function foo\func;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class ProjectRepository implements \App\Contracts\Repositories\ProjectRepository
{
    public function find($id)
    {
        $project = Project::find($id);
        return $project ?: null;
    }

    public function getAll()
    {
        return Project::with('owner')->latest()->get();
    }

    public function getUserProjects($userId)
    {
        return Project::with('owner')
            ->with('groups.credentials')
            ->where('user_id', $userId)
            ->latest()
            ->get();
    }

    public function getSharedProjects($userId)
    {
        $groupIds = GroupUser::where('user_id', $userId)->get()->pluck('group_id')->toArray();

        return Project::whereHas('groups', function ($query) use ($groupIds) {
            $query->whereIn('id', $groupIds);
        })->with(['groups' => function ($query) use ($groupIds) {
            $query->whereIn('id', $groupIds);
        }, 'groups.credentials' => function($query) {
            $query->where('is_private', '0');
        }])->get();


//        return Group::whereHas('project', function ($query) {
//            $query->where('active', '1');
//        })
//            ->with('project.owner')
//            ->whereIn('id', $groupIds)->get()
//            ->pluck('project');
    }

    public function findBySlug($slug)
    {
        $project = Project::where('slug', Str::lower($slug))
            ->where('user_id', auth()->user()->id)
            ->first();
        return $project ?: null;
    }

    public function create($data)
    {
        return Project::create($data);
    }

    public function update($data, $id)
    {
        return Project::where('id', $id)->update($data);
    }

    public function updateUserProject($data, $id, $userId)
    {
        return Project::where('id', $id)
            ->where('user_id', $userId)
            ->update($data);
    }

    public function deleteUserProject($projectId, $userId)
    {
        return Project::where('id', $projectId)
            ->where('user_id', $userId)
            ->delete();
    }

    public function isUserProject($userId, $projectId)
    {
        return Project::where('id', $projectId)
            ->where('user_id', $userId)
            ->exists();
    }

    protected function loadUserRelationships($project)
    {
//        $project->load('subscriptions');

        return $project;
    }

    public function isAuthProject($projectId)
    {
        return Project::where('user_id', auth()->user()->id)->where('id', $projectId)->first();
    }

    public function getUserStats(User $user)
    {
        $projects = $this->getUserProjects($user->id);
        dd($projects);
    }
}
