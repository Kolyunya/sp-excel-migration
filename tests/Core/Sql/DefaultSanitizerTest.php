<?php

declare(strict_types=1);

namespace Test\Core\Sql;

use App\Core\Sql\DefaultSanitizer;
use PHPUnit\Framework\Attributes\TestWith;
use Test\Utility\TestCase;
use ValueError;

final class DefaultSanitizerTest extends TestCase
{
    #[TestWith(['abc', 'abc'])]
    #[TestWith([' abc ', 'abc'])]
    #[TestWith([' a b c ', 'a_b_c'])]
    #[TestWith([' a-b-c ', 'a_b_c'])]
    #[TestWith(['foo-----bar', 'foo_bar'])]
    #[TestWith([';DROP DATABASE `users`;', '_DROP_DATABASE_users_'])]
    public function testSanitize(string $name, string $sanitizedName): void
    {
        $sanitizer = new DefaultSanitizer();

        $actualSanitizedName = $sanitizer->sanitize($name);

        $this->assertSame($sanitizedName, $actualSanitizedName);
    }

    public function testSanitizedNameIsEmpty(): void
    {
        $sanitizer = new DefaultSanitizer();

        $this->expectException(ValueError::class);
        $this->expectExceptionMessage('The sanitized name is empty.');

        $actualSanitizedName = $sanitizer->sanitize('   ');
    }
}
