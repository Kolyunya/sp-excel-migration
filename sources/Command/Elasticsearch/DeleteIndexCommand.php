<?php

declare(strict_types=1);

namespace App\Command\Elasticsearch;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;

#[AsCommand(
    name: 'app:es:delete-index',
    description: 'Delete the company employees index',
)]
final class DeleteIndexCommand extends ElasticsearchCommand
{
    public function __invoke(): int
    {
        $this->client->indices()->delete([
            'index' => self::INDEX,
        ]);

        return Command::SUCCESS;
    }
}
