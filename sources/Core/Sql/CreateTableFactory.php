<?php

declare(strict_types=1);

namespace App\Core\Sql;

use App\Core\Type\Type;

final readonly class CreateTableFactory
{
    public function __construct(
        private TypeFactoryInterface $typeFactory,
        private SanitizerInterface $sanitizer,
    ) {
    }

    /**
     * @param array<string, Type> $columns
     */
    public function make(string $table, array $columns): string
    {
        $template = 'CREATE TABLE `%s` (%s);';
        $sanitizedTable = $this->sanitizer->sanitize($table);
        $columns = $this->makeColumns($columns);
        $sql = sprintf($template, $sanitizedTable, $columns);

        return $sql;
    }

    /**
     * @param array<string, Type> $columns
     */
    private function makeColumns(array $columns): string
    {
        $parts = [];

        foreach ($columns as $name => $type) {
            $part = $this->makeColumn($type, $name);
            $parts[] = $part;
        }

        $sql = implode(', ', $parts);

        return $sql;
    }

    private function makeColumn(Type $type, string $name): string
    {
        $sqlType = $this->typeFactory->make($type);
        $sanitizedName = $this->sanitizer->sanitize($name);

        $template = '`%s` %s';
        $sql = sprintf($template, $sanitizedName, $sqlType);

        return $sql;
    }
}
