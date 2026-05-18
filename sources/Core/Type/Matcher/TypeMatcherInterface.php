<?php

declare(strict_types=1);

namespace App\Core\Type\Matcher;

use App\Core\Type\Type;

interface TypeMatcherInterface
{
    public static function getType(): Type;

    /**
     * @param mixed[] $values
     */
    public function match(array $values, bool $trim = true): bool;
}
