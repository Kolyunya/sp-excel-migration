<?php

declare(strict_types=1);

namespace Test\Utility;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;

class TestCase extends PHPUnitTestCase
{
    protected function fixture(string $file): string
    {
        $components = [
            dirname(__DIR__, 1),
            'Fixture',
            'Csv',
            $file,
        ];

        $path = implode(DIRECTORY_SEPARATOR, $components);

        return $path;
    }
}
