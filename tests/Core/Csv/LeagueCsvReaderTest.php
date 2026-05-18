<?php

declare(strict_types=1);

namespace Test\Core\Csv;

use App\Core\Csv\LeagueCsvReader;
use League\Csv\SyntaxError;
use League\Csv\UnavailableStream;
use PHPUnit\Framework\Attributes\TestWith;
use Test\Utility\TestCase;

final class LeagueCsvReaderTest extends TestCase
{
    public function testMissingFile(): void
    {
        $file = $this->fixture('missing.csv');

        $this->expectException(UnavailableStream::class);
        $this->expectExceptionMessage('No such file or directory.');

        new LeagueCsvReader($file);
    }

    public function testEmptyFile(): void
    {
        $file = $this->fixture('invalid/empty-file.csv');
        $reader = new LeagueCsvReader($file);

        $this->expectException(SyntaxError::class);
        $this->expectExceptionMessage('The header record does not exist or is empty');

        $reader->getHeader();
    }

    #[TestWith(['delimiter/comma.csv', ',', 'comma'])]
    #[TestWith(['delimiter/semi.csv', ';', 'semi'])]
    #[TestWith(['delimiter/tab.csv', "\t", 'tab'])]
    public function testGetFileName(string $file, string $delimiter, string $name): void
    {
        $path = $this->fixture($file);
        $reader = new LeagueCsvReader($path, $delimiter);

        $actualName = $reader->getFileName();

        $this->assertSame($name, $actualName);
    }

    #[TestWith(['delimiter/comma.csv', ','])]
    #[TestWith(['delimiter/semi.csv', ';'])]
    #[TestWith(['delimiter/tab.csv', "\t"])]
    public function testGetHeader(string $file, string $delimiter): void
    {
        $path = $this->fixture($file);
        $reader = new LeagueCsvReader($path, $delimiter);

        $header = $reader->getHeader();

        $this->assertSame(['Name', 'Age', 'Grade', 'Salary', 'On Vacation'], $header);
    }

    #[TestWith(['delimiter/comma.csv', ','])]
    #[TestWith(['delimiter/semi.csv', ';'])]
    #[TestWith(['delimiter/tab.csv', "\t"])]
    public function testGetRows(string $file, string $delimiter): void
    {
        $path = $this->fixture($file);
        $reader = new LeagueCsvReader($path, $delimiter);

        $rows = iterator_to_array($reader->getRows(), false);

        $this->assertCount(3, $rows);
        $this->assertSame('Alice Smith', $rows[0]['Name']);
        $this->assertSame('Bob Johnson', $rows[1]['Name']);
        $this->assertSame('Charlie Lee', $rows[2]['Name']);
    }
}
