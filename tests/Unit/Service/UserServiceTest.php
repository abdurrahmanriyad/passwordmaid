<?php

namespace Tests\Unit\Service;

use App\Project;
use App\Services\UserService;
use App\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
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

    /** @test */
    public function can_search_users_by_name()
    {
        $count = 1;
        User::factory()->count(5)->create([
            'name' => 'Jhon ' . Str::random(3) . " Doe",
        ]);

        User::factory()->count(10)->create();

        $this->assertCount(15, User::all());

        $searchedUsers = $this->userService->searchUsers('Jhon');

        $this->assertCount(5, $searchedUsers);

        $searchedUsers = $this->userService->searchUsers('Doe');

        $this->assertCount(5, $searchedUsers);
    }

    /** @test */
    public function can_search_users_by_name_with_case_sensitivity()
    {
        $count = 1;
        User::factory()->count(5)->create([
            'name' => 'Jhon ' . Str::random(3) . " Doe",
        ]);

        User::factory()->count(10)->create();

        $this->assertCount(15, User::all());

        $searchedUsers = $this->userService->searchUsers('jhon');

        $this->assertCount(5, $searchedUsers);

        $searchedUsers = $this->userService->searchUsers('doe');

        $this->assertCount(5, $searchedUsers);
    }

    /** @test */
    public function can_search_users_by_email()
    {
        $id = 0;
        User::factory()->count(5)->create()->each(function ($user) use (&$id) {
            $user->update(['email' => "user_" . $id++ . "@unique.com"]);
        });

        User::factory()->count(10)->create();

        $this->assertCount(15, User::all());

        $searchedUsers = $this->userService->searchUsers('@unique.com');

        $this->assertCount(5, $searchedUsers);

        $searchedUsers = $this->userService->searchUsers('@unique.com');

        $this->assertCount(5, $searchedUsers);
    }

    /** @test */
    public function can_search_users_by_email_without_case_sensitivity()
    {
        $id = 0;
        User::factory()->count(5)->create()->each(function ($user) use (&$id) {
            $user->update(['email' => "user_" . $id++ . "@unique.com"]);
        });

        User::factory()->count(10)->create();

        $this->assertCount(15, User::all());

        $searchedUsers = $this->userService->searchUsers('@Unique.com');

        $this->assertCount(5, $searchedUsers);

        $searchedUsers = $this->userService->searchUsers('@Unique.com');

        $this->assertCount(5, $searchedUsers);
    }
}
