<?php

declare(strict_types=1);

namespace App\Core\Type;

use App\Core\Type\Matcher\BooleanTypeMatcher;
use App\Core\Type\Matcher\DateTypeMatcher;
use App\Core\Type\Matcher\FloatTypeMatcher;
use App\Core\Type\Matcher\IntegerTypeMatcher;
use App\Core\Type\Matcher\StringTypeMatcher;
use App\Core\Type\Matcher\TypeMatcherInterface;

final class TypeParser
{
    /**
     * @var TypeMatcherInterface[]
     */
    private array $matchers;

    public function __construct(
        private readonly bool $trim,
    ) {
        $this->matchers = [
            new DateTypeMatcher(),
            new IntegerTypeMatcher(),
            new FloatTypeMatcher(),
            new BooleanTypeMatcher(),
        ];
    }

    public function process(string $value): void
    {
        if (empty($this->matchers)) {
            return;
        }

        foreach ($this->matchers as $index => $matcher) {
            $matches = $matcher->match([$value], $this->trim);
            if (!$matches) {
                unset($this->matchers[$index]);
            }
        }
    }

    public function getType(): Type
    {
        $type = null;

        if (empty($this->matchers)) {
            $type = StringTypeMatcher::getType();
        } else {
            $matcher = reset($this->matchers);
            $type = $matcher::getType();
        }

        return $type;
    }
}
