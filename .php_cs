<?php

$finder = Symfony\CS\Finder\Symfony23Finder::create()
    ->in(__DIR__.'/src');

return Symfony\CS\Config\Config::create()
    ->setUsingCache(true)
    ->fixers([
        '-phpdoc_short_description',
        'ordered_use',
        'short_array_syntax',
    ])
    ->finder($finder);
