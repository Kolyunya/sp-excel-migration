<?php

declare(strict_types=1);

namespace App\Command\Elasticsearch;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use Symfony\Component\Console\Command\Command;

abstract class ElasticsearchCommand extends Command
{
    protected const string INDEX = 'employees';

    protected Client $client;

    public function __construct(?string $name = null, ?callable $code = null)
    {
        parent::__construct($name, $code);

        $this->client = ClientBuilder::create()
            ->setHosts([
                'http://elasticsearch:9200',
            ])
            ->build()
        ;
    }
}
