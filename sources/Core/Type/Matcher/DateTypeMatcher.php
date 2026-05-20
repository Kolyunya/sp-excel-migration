<?php

declare(strict_types=1);

namespace App\Core\Type\Matcher;

use App\Core\Type\Type;
use Override;

class DateTypeMatcher extends AbstractTypeMatcher
{
    private const array FORMATS = [
        'Y-m-d',
        'Y/m/d',
        'Y m d',
    ];

    #[Override]
    public static function getType(): Type
    {
        return Type::Date;
    }

    #[Override]
    protected function matchValue(string $value): bool
    {
        foreach (self::FORMATS as $format) {
            $result = date_create_from_format($format, $value);
            if ($result !== false) {
                return true;
            }
        }

        return false;
    }
}
