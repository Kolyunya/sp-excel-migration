<?php

declare(strict_types=1);

namespace Test\Core\Sql;

use App\Core\Sql\CreateTableFactory;
use App\Core\Sql\DefaultSanitizer;
use App\Core\Sql\DefaultTypeFactory;
use App\Core\Type\Type;
use Test\Utility\TestCase;

final class CreateTableFactoryTest extends TestCase
{
    public function testMake(): void
    {
        $factory = new CreateTableFactory(
            new DefaultTypeFactory(),
            new DefaultSanitizer(),
        );

        $sql = $factory->make('students', [
            'name' => Type::String,
            'age' => Type::Integer,
            'gpa' => Type::Float,
            'graduated' => Type::Boolean,
        ]);

        $expectedSql = 'CREATE TABLE `students` (`name` VARCHAR(1024), `age` BIGINT, `gpa` FLOAT, `graduated` BOOLEAN);';
        $this->assertSame($expectedSql, $sql);
    }
}
