<?php

declare(strict_types=1);

namespace App\Command\Elasticsearch;

use Elastic\Elasticsearch\Response\Elasticsearch;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:es:search-employees',
    description: 'Search employees',
)]
final class SearchEmployeeCommand extends ElasticsearchCommand
{
    /**
     * @var mixed[]
     */
    private array $must = [];

    /**
     * @var mixed[]
     */
    private array $filter = [];

    /**
     * @param string[] $ids
     * @param string[] $grades
     */
    public function __invoke(
        OutputInterface $output,

        #[Option]
        array $ids = [],

        #[Option]
        ?string $name = null,

        #[Option]
        ?int $ageGte = null,

        #[Option]
        ?int $ageLte = null,

        #[Option]
        array $grades = [],

        #[Option]
        ?float $salaryGte = null,

        #[Option]
        ?float $salaryLte = null,

        #[Option]
        int $page = 1,

        #[Option]
        int $pageSize = 5,

        #[Option]
        bool $dryRun = false,
    ): int {
        $this->filterIds($ids);
        $this->matchName($name);
        $this->filterAge($ageGte, $ageLte);
        $this->filterGrades($grades);
        $this->filterSalary($salaryGte, $salaryLte);

        $page = max(1, $page);
        $pageSize = max(1, $pageSize);
        $from = ($page - 1) * $pageSize;

        $search = [
            'query' => $this->makeQuery(),
            'sort' => [
                [
                    'name.keyword' => [
                        'order' => 'asc',
                    ],
                ],
            ],
            'from' => $from,
            'size' => $pageSize,
            'track_total_hits' => true,
        ];

        if ($dryRun) {
            $json = json_encode($search, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR);
            $output->writeln($json);
            $output->writeln('');
        } else {
            $response = $this->client->search([
                'index' => self::INDEX,
                'body' => $search,
            ]);
            assert($response instanceof Elasticsearch);

            $this->printDocuments($output, $response);
            $this->printStats($output, $response, $page, $pageSize);
        }

        return Command::SUCCESS;
    }

    /**
     * @param string[] $ids
     */
    private function filterIds(array $ids): void
    {
        if (empty($ids)) {
            return;
        }

        $this->filter[] = [
            'ids' => [
                'values' => $ids,
            ],
        ];
    }

    private function matchName(?string $name): void
    {
        if (empty($name)) {
            return;
        }

        $this->must[] = [
            'match' => [
                'name' => [
                    'query' => $name,
                    'fuzziness' => 'AUTO',
                ],
            ],
        ];
    }

    private function filterAge(?int $gte, ?int $lte): void
    {
        $range = [];

        if ($gte !== null) {
            $range['gte'] = $gte;
        }

        if ($lte !== null) {
            $range['lte'] = $lte;
        }

        if ($range) {
            $this->filter[] = [
                'range' => [
                    'age' => $range,
                ],
            ];
        }
    }

    /**
     * @param string[] $grades
     */
    private function filterGrades(array $grades): void
    {
        if (empty($grades)) {
            return;
        }

        $this->filter[] = [
            'terms' => [
                'grade' => $grades,
            ],
        ];
    }

    private function filterSalary(?float $gte, ?float $lte): void
    {
        $range = [];

        if ($gte !== null) {
            $range['gte'] = $gte;
        }

        if ($lte !== null) {
            $range['lte'] = $lte;
        }

        if ($range) {
            $this->filter[] = [
                'range' => [
                    'salary' => $range,
                ],
            ];
        }
    }

    /**
     * @return mixed[]
     */
    private function makeQuery(): array
    {
        $query = null;

        if (!$this->must && !$this->filter) {
            $query = [
                'match_all' => (object) [],
            ];
        } else {
            $query = [
                'bool' => array_filter([
                    'must' => $this->must,
                    'filter' => $this->filter,
                ]),
            ];
        }

        return $query;
    }

    private function getTotalDocuments(): int
    {
        $countResponse = $this->client->count([
            'index' => self::INDEX,
        ]);

        $totalDocuments = $countResponse['count'];
        assert(is_int($totalDocuments));

        return $totalDocuments;
    }

    private function printDocuments(OutputInterface $output, Elasticsearch $response): void
    {
        $documents = [];
        foreach ($response['hits']['hits'] as $hit) {
            $document['_id'] = $hit['_id'];
            $document = array_merge($document, $hit['_source']);
            $documents[] = $document;
        }

        $json = json_encode($documents, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR);
        $output->writeln($json);
    }

    private function printStats(
        OutputInterface $output,
        Elasticsearch $response,
        int $page,
        int $pageSize,
    ): void {
        $documentsTotal = $this->getTotalDocuments();
        $documentsMatched = $response['hits']['total']['value'];
        assert(is_int($documentsMatched));
        $documentsShown = count($response['hits']['hits']);
        $pagesTotal = (int) ceil($documentsMatched / $pageSize);

        $output->writeln('');
        $output->writeln(sprintf('Documents total: %d.', $documentsTotal));
        $output->writeln(sprintf('Documents matched: %d.', $documentsMatched));
        $output->writeln(sprintf('Documents shown: %d.', $documentsShown));
        $output->writeln(sprintf('Page: %d of %d.', $page, $pagesTotal));
        $output->writeln('');
    }
}
