<?php

declare(strict_types=1);

namespace Test\Acceptance;

use App\Command\CreateTableCommand;
use InvalidArgumentException;
use League\Csv\SyntaxError;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\TestWith;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Test\Utility\TestCase;
use ValueError;

final class CreateTableTest extends TestCase
{
    public function testMissingFileOption(): void
    {
        $application = new Application();
        $application->addCommand(new CreateTableCommand());

        $command = $application->find('app:create-table');
        $tester = new CommandTester($command);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid or missing file option.');

        $tester->execute([]);
    }

    public function testInvalidFilePath(): void
    {
        $application = new Application();
        $application->addCommand(new CreateTableCommand());

        $command = $application->find('app:create-table');
        $tester = new CommandTester($command);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid or missing file option.');

        $tester->execute([
            '--file' => 'invalid-file.csv',
        ]);
    }

    public function testDuplicateColumnNames(): void
    {
        $application = new Application();
        $application->addCommand(new CreateTableCommand());

        $command = $application->find('app:create-table');
        $tester = new CommandTester($command);

        $this->expectException(SyntaxError::class);
        $this->expectExceptionMessage('The header record contains duplicate column names.');

        $tester->execute([
            '--file' => $this->fixture('invalid/duplicate-column-names.csv'),
        ]);
    }

    public function testMissingColumnNames(): void
    {
        $application = new Application();
        $application->addCommand(new CreateTableCommand());

        $command = $application->find('app:create-table');
        $tester = new CommandTester($command);

        $this->expectException(ValueError::class);
        $this->expectExceptionMessage('The sanitized name is empty.');

        $tester->execute([
            '--file' => $this->fixture('invalid/missing-column-names.csv'),
        ]);
    }

    public function testHeaderOnlyFile(): void
    {
        $application = new Application();
        $application->addCommand(new CreateTableCommand());

        $command = $application->find('app:create-table');
        $tester = new CommandTester($command);

        $this->expectException(ValueError::class);
        $this->expectExceptionMessage('The file is empty.');

        $tester->execute([
            '--file' => $this->fixture('invalid/only-header.csv'),
        ]);
    }

    public function testEmptyCells(): void
    {
        $application = new Application();
        $application->addCommand(new CreateTableCommand());

        $command = $application->find('app:create-table');
        $tester = new CommandTester($command);

        $tester->execute([
            '--file' => $this->fixture('empty/cells.csv'),
        ]);

        $this->assertSame(Command::SUCCESS, $tester->getStatusCode());
        $this->assertSame($this->expectedOutput('cells'), $tester->getDisplay());
    }

    #[TestWith(['delimiter/comma.csv', ',', 'comma'])]
    #[TestWith(['delimiter/semi.csv', ';', 'semi'])]
    #[TestWith(['delimiter/tab.csv', "\t", 'tab'])]
    public function testDelimiters(string $file, string $delimiter, string $table): void
    {
        $application = new Application();
        $application->addCommand(new CreateTableCommand());

        $command = $application->find('app:create-table');
        $tester = new CommandTester($command);

        $tester->execute([
            '--file' => $this->fixture($file),
            '--delimiter' => $delimiter,
        ]);

        $this->assertSame(Command::SUCCESS, $tester->getStatusCode());
        $this->assertSame($this->expectedOutput($table), $tester->getDisplay());
    }

    public function testTrimmedFile(): void
    {
        $application = new Application();
        $application->addCommand(new CreateTableCommand());

        $command = $application->find('app:create-table');
        $tester = new CommandTester($command);

        $tester->execute([
            '--file' => $this->fixture('trim/trimmed.csv'),
            '--trim' => false,
        ]);

        $this->assertSame(Command::SUCCESS, $tester->getStatusCode());
        $this->assertSame($this->expectedOutput('trimmed'), $tester->getDisplay());
    }

    public function testUntrimmedFile(): void
    {
        $application = new Application();
        $application->addCommand(new CreateTableCommand());

        $command = $application->find('app:create-table');
        $tester = new CommandTester($command);

        $tester->execute([
            '--file' => $this->fixture('trim/untrimmed.csv'),
            '--trim' => true,
        ]);

        $this->assertSame(Command::SUCCESS, $tester->getStatusCode());
        $this->assertSame($this->expectedOutput('untrimmed'), $tester->getDisplay());
    }

    #[Group('slow')]
    #[TestWith(['large/ten-megabytes.csv', 'ten_megabytes', 10])]
    #[TestWith(['large/million-records.csv', 'million_records', 30])]
    public function testLargeFile(string $file, string $table, int $threshold): void
    {
        $application = new Application();
        $application->addCommand(new CreateTableCommand());

        $command = $application->find('app:create-table');
        $tester = new CommandTester($command);

        $timeStart = microtime(true);
        $tester->execute([
            '--file' => $this->fixture($file),
        ]);
        $timeEnd = microtime(true);
        $elapsed = $timeEnd - $timeStart;

        $this->assertSame(Command::SUCCESS, $tester->getStatusCode());
        $this->assertSame($this->expectedOutput($table), $tester->getDisplay());
        $this->assertLessThan($threshold, $elapsed);
    }

    private function expectedOutput(string $table): string
    {
        $template = "CREATE TABLE `%s` (`Name` VARCHAR(1024), `Age` BIGINT, `Grade` VARCHAR(1024), `Salary` FLOAT, `On_Vacation` BOOLEAN);\n";

        $output = sprintf($template, $table);

        return $output;
    }
}
