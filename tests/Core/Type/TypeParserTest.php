<?php

declare(strict_types=1);

namespace Test\Core\Type;

use App\Core\Type\Type;
use App\Core\Type\TypeParser;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\TestCase;
use Test\Fixture\DataProvider as DataDataProvider;

final class TypeParserTest extends TestCase
{
    #[DataProviderExternal(DataDataProvider::class, 'isBoolean')]
    public function testBoolean(bool $matches, array $data): void
    {
        $parser = new TypeParser(true);

        foreach ($data as $value) {
            $parser->process($value);
        }
        $type = $parser->getType();

        $this->assertSame(Type::Boolean, $type);
    }

    #[DataProviderExternal(DataDataProvider::class, 'isInteger')]
    public function testInteger(bool $matches, array $data): void
    {
        $parser = new TypeParser(true);

        foreach ($data as $value) {
            $parser->process($value);
        }
        $type = $parser->getType();

        $this->assertSame(Type::Integer, $type);
    }

    #[DataProviderExternal(DataDataProvider::class, 'isFloat')]
    public function testFloat(bool $matches, array $data): void
    {
        $parser = new TypeParser(true);

        foreach ($data as $value) {
            $parser->process($value);
        }
        $type = $parser->getType();

        $this->assertSame(Type::Float, $type);
    }

    #[DataProviderExternal(DataDataProvider::class, 'isString')]
    #[DataProviderExternal(DataDataProvider::class, 'isNotBoolean')]
    #[DataProviderExternal(DataDataProvider::class, 'isNotFloat')]
    public function testString(bool $matches, array $data): void
    {
        $parser = new TypeParser(true);

        foreach ($data as $value) {
            $parser->process($value);
        }
        $type = $parser->getType();

        $this->assertSame(Type::String, $type);
    }
}
