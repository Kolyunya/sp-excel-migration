<?php

declare(strict_types=1);

namespace Test\Core\Type\Matcher;

use App\Core\Type\Matcher\DateTypeMatcher;
use App\Core\Type\Type;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\TestCase;
use Test\Fixture\DataProvider as DataDataProvider;

final class DateTypeMatcherTest extends TestCase
{
    public function testGetType(): void
    {
        $matcher = new DateTypeMatcher();

        $type = $matcher->getType();

        $this->assertSame(Type::Date, $type);
    }

    #[DataProviderExternal(DataDataProvider::class, 'isDate')]
    #[DataProviderExternal(DataDataProvider::class, 'isNotDate')]
    public function testMatch(bool $expected, array $dataSamples): void
    {
        $matcher = new DateTypeMatcher();

        $matches = $matcher->match($dataSamples);

        $this->assertSame($expected, $matches);
    }
}
