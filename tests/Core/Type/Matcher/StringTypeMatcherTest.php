<?php

declare(strict_types=1);

namespace Test\Core\Type\Matcher;

use App\Core\Type\Matcher\StringTypeMatcher;
use App\Core\Type\Type;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\TestCase;
use Test\Fixture\DataProvider as AppDataProvider;
use ValueError;

final class StringTypeMatcherTest extends TestCase
{
    public function testGetType(): void
    {
        $matcher = new StringTypeMatcher();

        $type = $matcher->getType();

        $this->assertSame(Type::String, $type);
    }

    #[DataProviderExternal(AppDataProvider::class, 'isString')]
    public function testMatchingData(bool $expected, array $dataSamples): void
    {
        $matcher = new StringTypeMatcher();

        $matches = $matcher->match($dataSamples);

        $this->assertSame($expected, $matches);
    }

    public static function provideNonMatchingData(): iterable
    {
        yield [['foo', 'bar', 1]];
        yield [['foo', 'bar', 1.0]];
        yield [['foo', 'bar', true]];
        yield [['foo', 'bar', false]];
    }

    #[DataProvider('provideNonMatchingData')]
    public function testNonMatchingData(array $dataSamples): void
    {
        $matcher = new StringTypeMatcher();

        $this->expectException(ValueError::class);
        $this->expectExceptionMessage('Invalid input data.');

        $matches = $matcher->match($dataSamples);
    }
}
