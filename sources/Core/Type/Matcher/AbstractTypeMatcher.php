<?php

declare(strict_types=1);

namespace App\Core\Type\Matcher;

use ValueError;

abstract class AbstractTypeMatcher implements TypeMatcherInterface
{
    final public function match(array $values, bool $trim = true): bool
    {
        if (empty($values)) {
            throw new ValueError('Values array must not be empty.');
        }

        foreach ($values as $value) {
            if (!is_string($value)) {
                throw new ValueError('Invalid input data.');
            }

            if ($trim) {
                $value = trim($value);
            }

            if (empty($value)) {
                continue;
            }

            $matches = $this->matchValue($value);
            if (!$matches) {
                return false;
            }
        }

        return true;
    }

    abstract protected function matchValue(string $value): bool;
}
