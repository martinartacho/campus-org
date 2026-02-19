<?php

return (new PhpCsFixer\Config())
    ->setRules([
        '@PSR12' => true,
        'no_extra_blank_lines' => ['tokens' => ['extra']],
        'no_trailing_whitespace' => true,
        'trim_array_spaces' => true,
        'no_unused_imports' => true, // quita use innecesarios
    ])
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->in([
                __DIR__ . '/app/Http/Controllers',
                __DIR__ . '/resources/views/notifications',
            ])
    );
