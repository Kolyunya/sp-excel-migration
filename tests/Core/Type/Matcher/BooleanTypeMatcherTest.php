<?php

declare(strict_types=1);

namespace Test\Core\Type\Matcher;

use App\Core\Type\Matcher\BooleanTypeMatcher;
use App\Core\Type\Type;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\TestCase;
use Test\Fixture\DataProvider as DataDataProvider;

final class BooleanTypeMatcherTest extends TestCase
{
    public function testGetType(): void
    {
        $matcher = new BooleanTypeMatcher();

        $type = $matcher->getType();

        $this->assertSame(Type::Boolean, $type);
    }

    #[DataProviderExternal(DataDataProvider::class, 'isBoolean')]
    #[DataProviderExternal(DataDataProvider::class, 'isNotBoolean')]
    public function testMatch(bool $expected, array $dataSamples): void
    {
        $matcher = new BooleanTypeMatcher();

        $matches = $matcher->match($dataSamples);

        $this->assertSame($expected, $matches);
    }
}
