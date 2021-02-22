<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Collection;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, RefreshDatabase;

    protected function isCollectionOf(string $class, Collection $items)
    {
        foreach ($items as $item) {
            $this->assertInstanceOf($class, $item);
        }
    }
}
