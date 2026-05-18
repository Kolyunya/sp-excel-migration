<?php

declare(strict_types=1);

namespace App\Core\Type\Matcher;

use App\Core\Type\Type;
use Override;

class BooleanTypeMatcher extends AbstractTypeMatcher
{
    private const array OPTIONS = [
        // Truthy
        'true',
        'yes',

        // Falsy
        'false',
        'no',
    ];

    #[Override]
    public static function getType(): Type
    {
        return Type::Boolean;
    }

    #[Override]
    protected function matchValue(string $value): bool
    {
        $value = mb_strtolower($value);
        $matches = in_array($value, self::OPTIONS);

        return $matches;
    }
}
