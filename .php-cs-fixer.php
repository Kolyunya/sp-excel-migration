<?php

declare(strict_types=1);

use PhpCsFixer\Finder;
use PhpCsFixer\Config;

$finder = Finder::create()
    ->in(__DIR__ . '/sources')
    ->in(__DIR__ . '/tests');

return (new Config())
    ->setRules([
        '@PSR12' => true,
        '@Symfony' => true,
        'global_namespace_import' => false,
        'increment_style' => false,
        'yoda_style' => false,
    ])
    ->setFinder($finder);
