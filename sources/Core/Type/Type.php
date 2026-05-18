<?php

declare(strict_types=1);

namespace App\Core\Type;

enum Type
{
    case Boolean;
    case Integer;
    case Float;
    case String;
}
