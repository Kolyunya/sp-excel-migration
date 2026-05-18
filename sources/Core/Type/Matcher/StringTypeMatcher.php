<?php

declare(strict_types=1);

namespace App\Core\Type\Matcher;

use App\Core\Type\Type;
use Override;

class StringTypeMatcher extends AbstractTypeMatcher
{
    #[Override]
    public static function getType(): Type
    {
        return Type::String;
    }

    #[Override]
    protected function matchValue(string $value): bool
    {
        return true;
    }
}
