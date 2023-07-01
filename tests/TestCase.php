<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function getJSONFixture($fileName): array
    {
        return json_decode($this->getFixture($fileName), true);
    }

    protected function getFixture($fileName): bool|string
    {
        return file_get_contents($this->getFixturePath($fileName));
    }

    public function getFixturePath($fileName): string
    {
        return __DIR__ . '/Fixtures/' . $fileName;
    }
}
