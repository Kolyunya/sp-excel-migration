<?php

declare(strict_types=1);

namespace Test\Fixture;

class DataProvider
{
    public static function isDate(): iterable
    {
        yield [true, ['2026 01 15', '2026 02 20', '2026 05 01']];
        yield [true, ['2026/01/15', '2026/02/20', '2026/05/01']];
        yield [true, ['2026-01-15', '2026-02-20', '2026-05-01']];
        yield [true, ['2026-01-15', '2026/02/20', '2026 05 01']];
        yield [true, [' 2026-01-15 ', '2026-02-20', '2026-05-01']];
        yield [true, ['2026-01-15', '', '2026-05-01']];
    }

    public static function isNotDate(): iterable
    {
        yield [false, ['2026-01-15', 'True', '2026-05-01']];
        yield [false, [' 2026-01-15 ', '1', '2026-05-01']];
        yield [false, [' 2026-01-15 ', '1.5', '2026-05-01']];
    }

    public static function isBoolean(): iterable
    {
        yield [true, ['True', 'False', 'True']];
        yield [true, [' yes ', ' false ', ' yes ']];
        yield [true, ['Yes', '', 'No']];
    }

    public static function isNotBoolean(): iterable
    {
        yield [false, ['True', 'False', 'foo']];
        yield [false, ['True', 'False', '1']];
        yield [false, ['True', 'False', '1.0']];
    }

    public static function isFloat(): iterable
    {
        yield 'all-float' => [true, ['1.0', '2.5', '3.0']];
        yield 'with-int' => [true, ['1.0', '2.5', '3']];
        yield 'with-spaces' => [true, [' 1.0 ', ' 2.5 ', ' 3.0 ']];
        yield 'with-empty' => [true, ['1.0', '', '3.0']];
    }

    public static function isNotFloat(): iterable
    {
        yield 'with-str' => [false, ['1.0', '2.5', 'foo']];
        yield 'with-bool' => [false, [' 1.0 ', ' 2.5 ', ' True ']];
    }

    public static function isInteger(): iterable
    {
        yield 'all-int' => [true, ['1', '0', '1']];
        yield 'with-spaces' => [true, [' 1 ', ' 0 ', ' 1 ']];
        yield 'with-empty' => [true, [' 1 ', '', '0']];
    }

    public static function isNotInteger(): iterable
    {
        yield 'with-str' => [false, ['1', '0', 'foo']];
        yield 'with-bool' => [false, ['1', '0', 'True']];
        yield 'with-float' => [false, ['1', '0', '1.0']];
    }

    public static function isString(): iterable
    {
        yield [true, ['foo', 'bar', 'baz']];
    }
}
