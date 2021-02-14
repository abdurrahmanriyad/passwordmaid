<?php

namespace App\Contracts\Repositories;

interface GroupRepository
{
    public function find($id);
    public function findBySlug($slug);
}
