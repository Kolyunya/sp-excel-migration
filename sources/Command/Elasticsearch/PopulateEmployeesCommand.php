<?php

declare(strict_types=1);

namespace App\Command\Elasticsearch;

use Faker\Factory;
use Faker\Generator;
use InvalidArgumentException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;

#[AsCommand(
    name: 'app:es:populate-employees',
    description: 'Populate employees',
)]
final class PopulateEmployeesCommand extends ElasticsearchCommand
{
    private Generator $faker;

    public function __construct(?string $name = null, ?callable $code = null)
    {
        parent::__construct($name, $code);

        $this->faker = Factory::create();
    }

    public function __invoke(
        #[Option(description: 'Number of employees to create', shortcut: 'c')]
        int $count = 10_000,
    ): int {
        if ($count < 1) {
            throw new InvalidArgumentException('Employee count must be greater than zero.');
        }

        $parameters = [];
        for ($i = 0; $i < $count; $i++) {
            $parameters['body'][] = [
                'index' => [
                    '_index' => self::INDEX,
                    '_id' => $this->makeEmployeeId(),
                ],
            ];

            $employee = $this->makeEmployee();
            $parameters['body'][] = $employee;
        }

        $this->client->bulk($parameters);

        return Command::SUCCESS;
    }

    private function makeEmployeeId(): string
    {
        $id = $this->faker->uuid();

        return $id;
    }

    /**
     * @return mixed[]
     */
    private function makeEmployee(): array
    {
        $employee = [
            'name' => $this->faker->name(),
            'age' => $this->faker->numberBetween(18, 75),
            'grade' => sprintf('L%d', $this->faker->numberBetween(1, 10)),
            'salary' => $this->faker->randomFloat(2, 50 * 1000, 100 * 1000),
        ];

        return $employee;
    }
}
