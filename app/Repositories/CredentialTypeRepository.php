<?php

namespace App\Repositories;

use App\CredentialType;
use App\Project;
use Illuminate\Support\Str;

class CredentialTypeRepository implements \App\Contracts\Repositories\CredentialTypeRepository
{
    public function find($id)
    {
        return CredentialType::find($id);
    }

    public function getAll()
    {
        return CredentialType::latest()->get();
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

    public function updateAuth($data, $id)
    {
        return Project::where('id', $id)
            ->where('user_id', auth()->user()->id)
            ->update($data);
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
}
