<?php

declare(strict_types=1);

namespace App\Core\Sql;

use RuntimeException;
use ValueError;

class DefaultSanitizer implements SanitizerInterface
{
    public function sanitize(string $name): string
    {
        $name = trim($name);

        $name = preg_replace('/[^0-9a-zA-Z]/ui', '_', $name);
        if ($name === null) {
            throw new RuntimeException('Unable to sanitize the name');
        }

        do {
            $name = str_replace('__', '_', $name, $count);
        } while ($count > 0);

        if (empty($name)) {
            throw new ValueError('The sanitized name is empty.');
        }

        return $name;
    }
}
