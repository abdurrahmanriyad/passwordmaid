<?php

namespace App\Http\Controllers\Ajax;

use App\Enums\Credential;
use App\Exceptions\ItemNotFound;
use App\Group;
use App\Responses\AjaxResponse;
use App\Services\CredentialService;
use App\Services\GroupService;
use App\Services\ProjectService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;

class CredentialController extends Controller
{
    private $projectService = null;
    private $groupService = null;
    private $credentialService = null;

    public function __construct(
        ProjectService $projectService,
        GroupService $groupService,
        CredentialService $credentialService
    )
    {
        $this->projectService = $projectService;
        $this->groupService = $groupService;
        $this->credentialService = $credentialService;
    }

    public function getCredKeys()
    {
        return AjaxResponse::render('success', Credential::KEYS);
    }

    public function getAll($groupId)
    {
        try {
            $isOwner = $this->groupService->isUserGroup($groupId, auth()->user()->id);
            $wasShared = $this->groupService->isSharedGroup($groupId, auth()->user()->id);
            if (!($isOwner || $wasShared)) return AjaxResponse::render('error', [], 'Unauthorized!', 400);
            $credentials = $this->credentialService->getGroupCredentials($groupId);

            return AjaxResponse::render('success', $credentials);
        } catch (ItemNotFound $e) {
            return AjaxResponse::render('error', [], $e->getMessage(), 400);
        } catch (\Exception $e) {
            return AjaxResponse::render('error', [], 'Something went wrong!', 400);
        }
    }

    public function authGetAll(Group $group)
    {
        try {
            $isOwner = $this->groupService->isUserGroup($group->id, auth()->user()->id);
            $wasShared = $this->groupService->isSharedGroup($group->id, auth()->user()->id);
            if (!($isOwner || $wasShared)) return AjaxResponse::render('error', [], 'Unauthorized!', 400);

            if ($isOwner) {
                $credentials = $this->credentialService->getGroupCredentials($group->id);
            } else {
                $credentials = $this->credentialService->getSharedGroupCredentials($group->id);
            }

            return AjaxResponse::render('success', [
                'group_name' => $group->name,
                'credentials' => $credentials
            ]);
        } catch (ItemNotFound $e) {
            return AjaxResponse::render('error', [], $e->getMessage(), 400);
        } catch (\Exception $e) {
            return AjaxResponse::render('error', [], 'Something went wrong!', 400);
        }
    }

    public function store(Request $request, $groupId)
    {
        $validatedData = $request->validate([
            'title' => 'required',
            'is_private' => 'nullable',
            'keys' => 'required|array',
            'values' => 'required|array',
            'is_encrypted' => 'required|array',
        ]);
        $validatedData['group_id'] = $groupId;
        if (isset($validatedData['is_private'])) $validatedData['is_private'] = '1';

        try {
            // checks if all keys have values
            if ($this->credentialService->isInvalidCustomFields($validatedData['keys'], $validatedData['values'], $validatedData['is_encrypted'])) {
                return AjaxResponse::render('error', [], 'Invalid credentials!', 400);
            }

            $group = $this->groupService->find($groupId);
            if (is_null($group)) {
                return AjaxResponse::render('error', [], 'Invalid group!', 400);
            }

            $credential = $this->credentialService->createCredential($validatedData);
            return AjaxResponse::render('success', $credential, "Successfully created!");
        } catch (\Exception $e) {
            return AjaxResponse::render('error', [], $e->getMessage(), 400);
        }
    }

    public function update(Request $request, $groupId, $credentialId)
    {
        $validatedData = $request->validate([
            'title' => 'required',
            'is_private' => 'nullable',
            'keys' => 'required|array',
            'values' => 'required|array',
            'is_encrypted' => 'required|array',
        ]);
        $validatedData['group_id'] = $groupId;

        if (isset($validatedData['is_private'])) $validatedData['is_private'] = 1;

        try {
            // checks if all keys have values
            if ($this->credentialService->isInvalidCustomFields($validatedData['keys'], $validatedData['values'], $validatedData['is_encrypted'])) {
                return AjaxResponse::render('error', [], 'Invalid credentials!', 400);
            }

            $group = $this->groupService->find($groupId);
            if (is_null($group)) {
                return AjaxResponse::render('error', [], 'Invalid group!', 400);
            }

            $credential = $this->credentialService->updateCredential($validatedData, $credentialId);
            return AjaxResponse::render('success', $credential, "Successfully updated!");
        } catch (\Exception $e) {
            return AjaxResponse::render('error', [], "Something went wrong!", 400);
        }
    }

    public function delete($groupId, $credentialId)
    {
        try {
            $group = $this->groupService->find($groupId);
            if (is_null($group)) {
                return AjaxResponse::render('error', [], 'Invalid data!', 400);
            }

            $credential = $this->credentialService->find($credentialId);
            if (is_null($credential)) {
                return AjaxResponse::render('error', [], 'Invalid data!', 400);
            }

            $isAuthorized = $this->groupService->isUserGroup($groupId, auth()->user()->id);
            if (!$isAuthorized) return AjaxResponse::render('error', [], 'Unauthorized!', 400);

            $deleted = $this->credentialService->deleteCred($credentialId);

            if ($deleted) {
                return AjaxResponse::render('success', [], 'Deleted successfully!');
            }
            return AjaxResponse::render('error', [], "Couldn't delete!", 400);
        } catch (\Exception $e) {
            return AjaxResponse::render('error', [], $e->getMessage(), 400);
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
            return AjaxResponse::render('error', [], "Something went wrong!", 400);
        }
    }

    public function updateAccessibility(Request $request, $groupId, $credentialId)
    {
        $validatedData = $request->validate([
            'is_private' => ['required', 'numeric', Rule::in([0, 1])]
        ]);

        try {
            $group = $this->groupService->find($groupId);
            if (is_null($group)) {
                return AjaxResponse::render('error', [], 'Invalid data!', 400);
            }

            $credential = $this->credentialService->find($credentialId);
            if (is_null($credential)) {
                return AjaxResponse::render('error', [], 'Invalid data!', 400);
            }

            $isAuthorized = $this->groupService->isUserGroup($groupId, auth()->user()->id);
            if (!$isAuthorized) return AjaxResponse::render('error', [], 'Unauthorized!', 400);

            $updated = $this->credentialService->updateAccessibility($validatedData['is_private'], $credentialId);
            if ($updated) {
                return AjaxResponse::render('success', [], 'Updated successfully!');
            }
            return AjaxResponse::render('error', [], "Couldn't update status", 400);
        } catch (ItemNotFound $e) {
            return AjaxResponse::render('error', [], $e->getMessage(), 400);
        } catch (\Exception $e) {
            return AjaxResponse::render('error', [], "Something went wrong!", 400);
        }
    }
}
