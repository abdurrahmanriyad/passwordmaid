<?php

namespace App\Repositories;

use App\CredentialType;
use App\CustomField;
use App\Project;
use Illuminate\Support\Str;

class CustomFieldRepository implements \App\Contracts\Repositories\CustomFieldRepository
{
    public function find($id)
    {
        return CustomField::find($id);
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
        return CustomField::create($data);
    }

    public function deleteCredCustomFields($credId)
    {
        return CustomField::where('credential_id', $credId)->delete();
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
