<?php

namespace Tests\Unit\Service;


use App\Project;
use App\Services\ProjectService;
use Illuminate\Support\Collection;
use Tests\TestCase;

class ProjectServiceTest extends TestCase
{
    /** @var ProjectService projectService */
    protected $projectService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->projectService = resolve(ProjectService::class);
    }

    /** @test */
    public function can_get_all_projects()
    {
        Project::factory()->count(7)->create();

        $projects = $this->projectService->getAllProjects();

        $this->assertCount(7, $projects);
    }

    /** @test */
    public function all_projects_contains_collection()
    {
        Project::factory()->count(7)->create();

        $projects = $this->projectService->getAllProjects();

        $this->assertInstanceOf(Collection::class, $projects);
    }

    /** @test */
    public function all_projects_contains_collection_of_project_model()
    {
        Project::factory()->count(7)->create();

        $projects = $this->projectService->getAllProjects();

        $this->isCollectionOf(Project::class, $projects);

        $this->assertCount(7, $projects);
    }
}
