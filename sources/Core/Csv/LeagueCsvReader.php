<?php

declare(strict_types=1);

namespace App\Core\Csv;

use League\Csv\Reader;
use Override;

class LeagueCsvReader implements CsvReaderInterface
{
    /**
     * @var Reader<string[]>
     */
    private Reader $reader;

    private string $fileName;

    public function __construct(string $path, string $delimiter = ',')
    {
        $this->reader = Reader::from($path);
        $this->reader->setDelimiter($delimiter);
        $this->reader->setHeaderOffset(0);

        $this->initializeFileName($path);
    }

    #[Override]
    public function getFileName(): string
    {
        return $this->fileName;
    }

    #[Override]
    public function getHeader(): array
    {
        $header = $this->reader->getHeader();

        return $header;
    }

    #[Override]
    public function getRows(): iterable
    {
        $rows = $this->reader->getRecords();

        return $rows;
    }

    private function initializeFileName(string $path): void
    {
        $basename = basename($path);
        $this->fileName = pathinfo($basename, PATHINFO_FILENAME);
    }
}
