<?php

declare(strict_types=1);

namespace App\Command;

use App\Core\Csv\LeagueCsvReader;
use App\Core\Sql\CreateTableFactory;
use App\Core\Sql\DefaultSanitizer;
use App\Core\Sql\DefaultTypeFactory;
use App\Core\Type\TypeParser;
use InvalidArgumentException;
use LogicException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use ValueError;

#[AsCommand(
    name: 'app:create-table',
    description: 'Generate a "CREATE TABLE" query for a given CSV file',
)]
class CreateTableCommand extends Command
{
    public function __invoke(
        OutputInterface $output,

        #[Option(description: 'Path to the CSV file to process', shortcut: 'f')]
        ?string $file = null,

        #[Option(description: 'CSV delimiter used in the file', shortcut: 'd')]
        string $delimiter = ',',

        #[Option(description: 'Whether to trim input data or not', shortcut: 't')]
        bool $trim = true,
    ): int {
        if (!$file || !is_file($file)) {
            throw new InvalidArgumentException('Invalid or missing file option.');
        }

        if (empty($delimiter)) {
            throw new InvalidArgumentException('Invalid or missing delimiter option.');
        }

        $csvReader = new LeagueCsvReader($file, $delimiter);
        $fileName = $csvReader->getFileName();
        $header = $csvReader->getHeader();

        $parsers = [];
        foreach ($header as $column) {
            $parser = new TypeParser($trim);
            $parsers[$column] = $parser;
        }

        $rows = $csvReader->getRows();
        $rowCount = 0;
        foreach ($rows as $row) {
            $rowCount++;
            foreach ($row as $column => $value) {
                $parser = $parsers[$column] ?? null;
                if (!$parser) {
                    throw new LogicException('Could not retrieve type parser.');
                }

                $parser->process($value);
            }
        }

        if ($rowCount === 0) {
            throw new ValueError('The file is empty.');
        }

        $columns = [];
        foreach ($header as $column) {
            $parser = $parsers[$column] ?? null;
            if (!$parser) {
                throw new LogicException('Could not retrieve type parser.');
            }

            $type = $parser->getType();
            $columns[$column] = $type;
        }

        $createTableFactory = new CreateTableFactory(
            new DefaultTypeFactory(),
            new DefaultSanitizer(),
        );
        $createTableSql = $createTableFactory->make($fileName, $columns);

        $output->writeln($createTableSql);

        return Command::SUCCESS;
    }
}
