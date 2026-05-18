<?php

declare(strict_types=1);

namespace App\Core\Type\Matcher;

use App\Core\Type\Type;
use Override;

class FloatTypeMatcher extends AbstractTypeMatcher
{
    #[Override]
    public static function getType(): Type
    {
        return Type::Float;
    }

    #[Override]
    protected function matchValue(string $value): bool
    {
        $matches = filter_var($value, FILTER_VALIDATE_FLOAT) !== false;

        return $matches;
    }
}
