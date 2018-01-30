<?php

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$finder = Finder::create()
    ->name('db-backup')
    ->name('.php_cs')
    ->exclude('bin')
    ->in(__DIR__);

return Config::create()
    ->setFinder($finder)
    ->setUsingCache(true)
    ->setRules([
        '@Symfony' => true,
        'ordered_imports' => true,
        'array_syntax' => [
            'syntax' => 'short',
        ],
        'phpdoc_summary' => false,
    ]);
