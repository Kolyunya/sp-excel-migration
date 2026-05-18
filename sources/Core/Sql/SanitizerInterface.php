<?php

declare(strict_types=1);

namespace App\Core\Sql;

interface SanitizerInterface
{
    public function sanitize(string $name): string;
}
