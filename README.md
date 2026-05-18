# S&P Excel Migration

[![Build](https://github.com/Kolyunya/sp-excel-migration/actions/workflows/build.yaml/badge.svg)](https://github.com/Kolyunya/sp-excel-migration/actions/workflows/build.yaml)

## Table of contents

* [Prerequisites](#prerequisites)
* [`CREATE TABLE` generator](#create-table-generator)
  * [Prebuilt application usage](#prebuilt-application-usage)
  * [Application usage from sources](#application-usage-from-sources)
  * [Quality assurance](#quality-assurance)
  * [Future enhancements](#future-enhancements)
* [Employee search tool](#employee-search-tool)
  * [Employee document example](#employee-document-example)
  * [Employee index mapping](#employee-index-mapping)
  * [Employee index settings](#employee-index-settings)
  * [Application usage](#application-usage)
* [Elasticsearch synchronization](#elasticsearch-synchronization)

## Prerequisites

* Terminal application.
* Docker up and running.

## `CREATE TABLE` generator

A CLI tool for generating `CREATE TABLE` query for a given CSV file.

Features:
* Support for various delimiters with the `--delimiter` option. Default delimiter is a comma.
* Support for optional trimming of the input data with the `--trim` and `--no-trim` option. Enabled by default.
* Support for `boolean`, `integer`, `float`, `string` data types.
* A streaming implementation with `O(1)` memory usage with respect to the number of rows, enabling processing of arbitrarily large files. A CSV file with one million records (over 30 MB) is processed by the production build of the application in 7 seconds on an older laptop.
* Protection against SQL injection attacks.
* Support for CSV file names and column names that include arbitrary special characters.
* Error handling for special edge cases like:
    * Empty file.
    * Empty file name.
    * Empty column names.
    * Duplicate column names.
* Full dataset scanning for maximum accuracy when choosing the appropriate data type for a column.
* Extensive test suite that covers a large part of the functionality. The test suite includes unit, acceptance, and load tests (hundreds of thousands and millions of records per file).
* Full type coverage that passes PHPStan analysis with the strictest configuration.
* Standard code style enforcement via the `PHP CS Fixer` utility.
* CI/CD pipelines with quality assurance, application building and publishing to the Docker registry.
* Bleeding-edge versions of the PHP interpreter and third-party libraries.


### Prebuilt application usage
The application is distributed as a Docker image. No setup or configuration is required.

Navigate to the directory containing CSV files:
```bash
cd /home/user/csv
```

Adjust the `--file` option with the file name to process. Adjust the `--delimiter` option with the actual delimiter used in the file. Run the resulting command (will take some time for the image to be downloaded):
```bash
docker run \
    --volume ${PWD}:/tmp/csv \
    --pull="always" \
    --quiet \
    --rm \
    ghcr.io/kolyunya/sp-excel-migration:master \
    --file="/tmp/csv/employees.csv" \
    --delimiter=","
```

### Application usage from sources

Clone the repository:
```bash
git clone git@github.com:Kolyunya/sp-excel-migration.git

cd sp-excel-migration
```

Install dependencies:
```bash
docker compose run --rm php composer install
```

Inspect application options:
```bash
docker compose run --rm php php app.php app:create-table --help
```

Put some CSV file in the root directory of the project. Adjust the `--file` option with the file name to process. Adjust the `--delimiter` option with the actual delimiter used in the file. Run the resulting command:
```bash
docker compose run --quiet --rm php php app.php app:create-table --file="employees.csv" --delimiter=","
```

### Quality assurance

Run the test suite:
```bash
docker compose run --rm php composer test
```

Run the static analysis:
```bash
docker compose run --rm php composer analyze
```

Run the code style checker:
```bash
docker compose run --rm php composer validate-style
```

Or run all QA procedures at once with:
```bash
docker compose run --rm php composer qa
```

### Future enhancements:
* Enhance `CreateTableFactory` to optionally add `ID INT AUTO_INCREMENT PRIMARY KEY` to the resulting table.
* Automatically detect the need for larger string data types, e.g. `TEXT`.
* Automatically detect the need for the `DOUBLE PRECISION` data type.
* Implement support for various SQL engines and dialects.
* Implement an `--allow-duplicate-column-names` option. Incrementing suffixes should be added to the column names when the column names are duplicated.
* Implement an `--allow-missing-column-names` option. Incrementing column names should be generated when column names are missing.
* Implement a custom exception system. Catch inner library exceptions and wrap them into custom ones defined specifically for this application.

## Employee search tool

A tool for searching employees by multiple criteria.

### Employee document example

```json
{
    "name": "John Doe",
    "age": 67,
    "grade": "L10",
    "salary": 91611.78
}
```

### Employee index mapping
```json
{
    "id": {
        "type": "keyword"
    },
    "name": {
        "type": "text",
        "fields": {
            "keyword": {
                "type": "keyword",
                "ignore_above": 256
            }
        }
    },
    "age": {
        "type": "integer",
        "coerce": false
    },
    "grade": {
        "type": "keyword",
        "normalizer": "lowercase"
    },
    "salary": {
        "type": "float",
        "coerce": false
    }
}
```

### Employee index settings
A lowercase normalizer is configured in order to support case-insensitive keyword search for the `grade` property.
```json
{
    "analysis": {
        "normalizer": {
            "lowercase": {
                "type": "custom",
                "filter": [
                    "lowercase"
                ]
            }
        }
    }
}
```


### Application usage

Start required services. Might take several minutes. The `--wait` option is crucial.
```bash
docker compose up --detach --wait
```

Create the employees index:
```bash
docker compose run --rm php php app.php app:es:create-index
```

Populate employee. Bulk indexing API is used to optimize for large data-sets.
```bash
docker compose run --rm php php app.php app:es:populate-employees --count="10000"
```

Search among employees:
```bash
docker compose run --rm php php app.php app:es:search-employees --name="John"
```

Employee search supports the following criteria:
* One and more IDs.
* Fuzzy name (case-insensitive).
* One and more grades (case-insensitive).
* Age range.
* Salary range.

Pagination supports `--page` and `--page-size` options. Results are sorted in the ascending order of the `name.keyword`.

All available options:
```bash
docker compose run --rm php php app.php app:es:search-employees \
    --ids="f4efd723-9142-3356-8329-7f7fa190fbab" \
    --name="John" \
    --age-gte="18" \
    --age-lte="75" \
    --grades="L5" \
    --salary-gte="50000" \
    --salary-lte="100000" \
    --page="1" \
    --page-size="5"
```

Perform a dry-run: only print the resulting query without performing an actual search:
```bash
docker compose run --rm php php app.php app:es:search-employees --name="John" --dry-run
```

Delete the employees index:
```bash
docker compose run --rm php php app.php app:es:delete-index
```

## Elasticsearch synchronization

Keeping the primary relational database in sync with Elasticsearch is one of the crucial problems to solve for the search, analytics, and data representation features to function properly.

This is a classic problem in distributed systems: primary relational database and the Elasticsearch are two independent systems that our application is talking to over the wire. And network requests are unreliable by their nature. They may and will occasionally fail.

A naive approach to the problem would be to open a transaction in the primary database, index the document into Elasticsearch, modify the employee's salary, and commit the transaction.

The problem with this approach is that the ES indexing operation may be quite slow. And also it may fail and may require a few retries, while still keeping the SQL transaction open. This is extremely inefficient and may compromise the performance and the responsiveness of the whole application.

One of the classic industry-standard approaches to this problem is called the "Transactional Outbox" pattern. With this pattern we modify the user state and insert the "Index user with ID = X" task in the "Outbox" table in a very quick and lightweight SQL transaction.

At the same time we have separate background workers that regularly check the outbox table for new tasks, process them and mark them as complete.

This way we achieve the "at least one" processing guarantee for the tasks in the outbox. And that is perfectly fine for us if the user might get indexed multiple times, because this operation is fully idempotent. This way we achieve an eventual consistency for the data inside the SQL and ES databases.
