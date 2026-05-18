<?php

declare(strict_types=1);

namespace Test\Core\Type\Matcher;

use App\Core\Type\Matcher\FloatTypeMatcher;
use App\Core\Type\Type;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\TestCase;
use Test\Fixture\DataProvider;

final class FloatTypeMatcherTest extends TestCase
{
    public function testGetType(): void
    {
        $matcher = new FloatTypeMatcher();

        $type = $matcher->getType();

        $this->assertSame(Type::Float, $type);
    }

    #[DataProviderExternal(DataProvider::class, 'isFloat')]
    #[DataProviderExternal(DataProvider::class, 'isNotFloat')]
    public function testMatch(bool $expected, array $dataSamples): void
    {
        $matcher = new FloatTypeMatcher();

        $matches = $matcher->match($dataSamples);

        $this->assertSame($expected, $matches);
    }
}
