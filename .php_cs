<?php
/*
 * This file is part of the DB Backup utility.
 *
 * (c) Tom Adam <tomadam@instantiate.co.uk>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

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
