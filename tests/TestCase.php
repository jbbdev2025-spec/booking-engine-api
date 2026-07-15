<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\Concerns\InteractsWithDatabase;

abstract class TestCase extends BaseTestCase
{
    use InteractsWithDatabase;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed la base de données de test avec Gladstone
        $this->seed(\Database\Seeders\Test\BeautySalonTestSeeder::class);
    }
}
