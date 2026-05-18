<?php

declare(strict_types=1);

namespace App\Core\Sql;

use App\Core\Type\Type;

interface TypeFactoryInterface
{
    public function make(Type $type): string;
}
