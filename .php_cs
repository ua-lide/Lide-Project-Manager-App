<?php

$finder = PhpCsFixer\Finder::create()
    ->notPath('vendor')
    ->notPath('build')
    ->in(__DIR__)
    ->name('*.php')
    ->notName('*.blade.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

return PhpCsFixer\Config::create()
    ->setRules([
        '@PSR2' => true,

        // Adding some more rules 'cause the PSR-2 specification allows too much liberty
        'linebreak_after_opening_tag' => true,
        'single_blank_line_before_namespace' => true, // Symfony
    ])
    ->setFinder($finder);
