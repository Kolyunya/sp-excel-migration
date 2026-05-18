<?php

declare(strict_types=1);

namespace Test\Core\Type\Matcher;

use App\Core\Type\Matcher\IntegerTypeMatcher;
use App\Core\Type\Type;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\TestCase;
use Test\Fixture\DataProvider as DataDataProvider;

final class IntegerTypeMatcherTest extends TestCase
{
    public function testGetType(): void
    {
        $matcher = new IntegerTypeMatcher();

        $type = $matcher->getType();

        $this->assertSame(Type::Integer, $type);
    }

    #[DataProviderExternal(DataDataProvider::class, 'isInteger')]
    #[DataProviderExternal(DataDataProvider::class, 'isNotInteger')]
    public function testMatch(bool $expected, array $dataSamples): void
    {
        $matcher = new IntegerTypeMatcher();

        $matches = $matcher->match($dataSamples);

        $this->assertSame($expected, $matches);
    }
}
