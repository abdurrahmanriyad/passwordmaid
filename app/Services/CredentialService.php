<?php

namespace App\Services;

use App\Exceptions\ItemNotFound;
use App\Repositories\CredentialRepository;
use App\Repositories\CredentialTypeRepository;
use App\Repositories\CustomFieldRepository;
use App\Repositories\ProjectRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class CredentialService extends Service
{
    private $credRepository = null;
    private $customFieldRepository = null;
    private $groupService = null;

    public function __construct(
        CredentialRepository $credRepository,
        CustomFieldRepository $customFieldRepository,
        GroupService $groupService
    )
    {
        $this->credRepository = $credRepository;
        $this->customFieldRepository = $customFieldRepository;
        $this->groupService = $groupService;
    }

    public function find($id)
    {
        return $this->credRepository->find($id);
    }

    public function getAllCredentials()
    {
        return $this->credRepository->getAll();
    }

    public function getGroupCredentials($groupId)
    {
        return $this->resolveEncryption($this->credRepository->getGroupCredentials($groupId));
    }

    public function getSharedGroupCredentials($groupId)
    {
        $group = $this->groupService->find($groupId);
        if ($group->project->active == '0') throw new ItemNotFound('Project not found!');
        return $this->resolveEncryption($this->credRepository->getSharedGroupCredentials($groupId));
    }

    public function resolveEncryption(Collection $credentials)
    {
        foreach ($credentials as $credential) {
            if (isset($credential->customFields)) {
                foreach ($credential->customFields as $customField) {
                    if ($customField->is_encrypted) {
                        $customField->value = Crypt::decryptString($customField->value);
                    }
                }
            }
        }

        return $credentials;
    }

    public function createCredential(array $data)
    {
        if (isset($data['is_private'])) $credData['is_private'] = (string)$data['is_private'];
        $credData['title'] = $data['title'];
        $credData['group_id'] = $data['group_id'];

        $credential = $this->credRepository->create($credData);
        if ($credential) {
            foreach ($data['keys'] as $index => $key) {
                $isEncrypted = 0;
                $value = $data['values'][$index];
                if ($data['is_encrypted'][$index] && ($data['is_encrypted'][$index] == 'on')) {
                    $isEncrypted = 1;
                    $value = Crypt::encryptString($value);
                }
                $this->customFieldRepository->create([
                    'name' => $key,
                    'value' => $value,
                    'credential_id' => $credential->id,
                    'is_encrypted' => $isEncrypted
                ]);
            }
        }
    }

    public function updateCredential(array $data, $credentialId)
    {
        if (isset($data['is_private'])) $credData['is_private'] = $data['is_private'];
        $credData['title'] = $data['title'];
        $credData['group_id'] = $data['group_id'];

        $credential = $this->credRepository->update($credData, $credentialId);
        if ($credential) {
            // delete existing credentials custom fields
            $this->customFieldRepository->deleteCredCustomFields($credentialId);

            foreach ($data['keys'] as $index => $key) {
                $isEncrypted = 0;
                $value = $data['values'][$index];
                if ($data['is_encrypted'][$index] && ($data['is_encrypted'][$index] == 'on')) {
                    $isEncrypted = 1;
                    $value = Crypt::encryptString($value);
                }
                $this->customFieldRepository->create([
                    'name' => $key,
                    'value' => $value,
                    'credential_id' => $credentialId,
                    'is_encrypted' => $isEncrypted
                ]);
            }
        }
    }

    public function deleteCred($id)
    {
        return $this->credRepository->delete($id);
    }

    public function updateAccessibility($isPrivate, $id)
    {
        return $this->credRepository->updatedAccessibility($isPrivate, $id);
    }

    public function isInvalidCustomFields(array $keys, array $values, array $isEncrypted)
    {
        $keys = trimArray($keys);
        $values = trimArray($values);

        if (count($keys) != count($values)) {
            return true;
        } else if (count($keys) != count($isEncrypted)) {
            return true;
        }

        return false;
    }
}