<?php

declare(strict_types=1);

namespace App\Command\Elasticsearch;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;

#[AsCommand(
    name: 'app:es:create-index',
    description: 'Create the company employees index',
)]
final class CreateIndexCommand extends ElasticsearchCommand
{
    private const array MAPPING = [
        'settings' => [
            'analysis' => [
                'normalizer' => [
                    'lowercase' => [
                        'type' => 'custom',
                        'filter' => [
                            'lowercase',
                        ],
                    ],
                ],
            ],
        ],
        'mappings' => [
            'properties' => [
                'name' => [
                    'type' => 'text',
                    'fields' => [
                        'keyword' => [
                            'type' => 'keyword',
                            'ignore_above' => 256,
                        ],
                    ],
                ],
                'age' => [
                    'type' => 'integer',
                    'coerce' => false,
                ],
                'grade' => [
                    'type' => 'keyword',
                    'normalizer' => 'lowercase',
                ],
                'salary' => [
                    'type' => 'float',
                    'coerce' => false,
                ],
            ],
        ],
    ];

    public function __invoke(): int
    {
        $this->client->indices()->create([
            'index' => self::INDEX,
            'body' => self::MAPPING,
        ]);

        return Command::SUCCESS;
    }
}
