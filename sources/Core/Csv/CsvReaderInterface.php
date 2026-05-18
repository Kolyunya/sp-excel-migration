<?php

declare(strict_types=1);

namespace App\Core\Csv;

interface CsvReaderInterface
{
    public function getFileName(): string;

    /**
     * @return string[]
     */
    public function getHeader(): array;

    /**
     * @return iterable<int|string, string[]>
     */
    public function getRows(): iterable;
}
