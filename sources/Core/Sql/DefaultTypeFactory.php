<?php

declare(strict_types=1);

namespace App\Core\Sql;

use App\Core\Type\Type;
use Override;

final class DefaultTypeFactory implements TypeFactoryInterface
{
    #[Override]
    public function make(Type $type): string
    {
        $sqlType = match ($type) {
            Type::Integer => 'BIGINT',
            Type::Float => 'FLOAT',
            Type::Boolean => 'BOOLEAN',
            Type::String => 'VARCHAR(1024)',
        };

        return $sqlType;
    }
}
