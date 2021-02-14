<?php

namespace App\Repositories;

use App\Credential;
use App\CredentialType;
use App\Project;
use Illuminate\Support\Str;

class CredentialRepository implements \App\Contracts\Repositories\CredentialTypeRepository
{
    public function find($id)
    {
        return Credential::find($id);
    }

    public function getAll()
    {
        return CredentialType::latest()->get();
    }

    public function getGroupCredentials($groupId)
    {
        return Credential::where('group_id', $groupId)->with('customFields')->get();
    }

    public function getSharedGroupCredentials($groupId)
    {
        return Credential::where('group_id', $groupId)
            ->where('is_private', "0")
            ->with('customFields')
            ->get();
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
        return Credential::create($data);
    }

    public function delete($id)
    {
        return Credential::where('id', $id)->delete();
    }

    public function updatedAccessibility($isPrivate, $id)
    {
        return Credential::where('id', $id)->update(['is_private' => (string)$isPrivate]);
    }

    public function update($data, $credentialId)
    {
        return Credential::where('id', $credentialId)->update($data);
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
