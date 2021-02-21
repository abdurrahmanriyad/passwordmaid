<?php

namespace Tests\Unit\Service;

use App\Project;
use App\Services\UserService;
use App\User;
use Illuminate\Support\Collection;
use Tests\TestCase;

class UserServiceTest extends TestCase
{
    private UserService $userService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userService = resolve(UserService::class);
    }

    /** @test */
    public function can_find_a_user_by_valid_id()
    {
        $createdUser = User::factory()->create();

        $user = $this->userService->find($createdUser->id);

        $this->assertNotNull($user);

        $this->assertInstanceOf(User::class, $user);
    }

    /** @test */
    public function gives_null_if_find_user_by_invalid_id()
    {
        User::factory()->create();

        $user = $this->userService->find(100);

        $this->assertNull($user);
    }

    /** @test */
    public function can_get_all_users()
    {
        User::factory()->count(5)->create();

        $allUsers = $this->userService->allUsers();

        $this->assertCount(5, $allUsers);
    }

    /** @test */
    public function it_returns_all_users_as_collection()
    {
        $allUsers = $this->userService->allUsers();

        $this->assertInstanceOf(Collection::class, $allUsers);

        User::factory()->count(5)->create();

        $allUsers = $this->userService->allUsers();

        $this->assertInstanceOf(Collection::class, $allUsers);
    }

    /** @test */
    public function it_returns_all_as_collection_of_user_model()
    {
        User::factory()->count(5)->create();

        $allUsers = $this->userService->allUsers();

        foreach ($allUsers as $user) {
            $this->assertInstanceOf(User::class, $user);
        }

        $this->assertCount(5, $allUsers);
    }
}
