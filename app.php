#!/usr/bin/env php
<?php

require __DIR__.'/vendor/autoload.php';

use App\Command\CreateTableCommand;
use App\Command\Elasticsearch\CreateIndexCommand;
use App\Command\Elasticsearch\DeleteIndexCommand;
use App\Command\Elasticsearch\PopulateEmployeesCommand;
use App\Command\Elasticsearch\SearchEmployeeCommand;
use Symfony\Component\Console\Application;

$application = new Application();
$application->addCommand(new CreateTableCommand());
$application->addCommand(new CreateIndexCommand());
$application->addCommand(new PopulateEmployeesCommand());
$application->addCommand(new SearchEmployeeCommand());
$application->addCommand(new DeleteIndexCommand());
$application->run();
