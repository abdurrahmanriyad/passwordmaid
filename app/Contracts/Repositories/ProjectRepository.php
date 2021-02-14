<?php

namespace App\Contracts\Repositories;

interface ProjectRepository
{
    public function find($id);
    public function findBySlug($slug);
}
