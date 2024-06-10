<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->exclude([
        "cache",
        "logs",
        "migrations",
        "vendor"
    ]);

$config = new PhpCsFixer\Config();

$rules = [
    '@PSR1' => true,
    '@PSR2' => true,
    '@PSR12' => true,
    'array_syntax' => [
        'syntax' => 'short'
    ],
    'binary_operator_spaces' => [
        'default' => 'single_space'
    ],
    'blank_line_before_statement' => [
        'statements' => [
            'declare',
            'do',
            'for',
            'foreach',
            'goto',
            'if',
            'phpdoc',
            'switch',
            'try',
            'while'
        ]
    ],
    'braces' => [
        'position_after_functions_and_oop_constructs' => 'same'
    ],
    'class_definition' => true,
    'class_attributes_separation' => true,
    'no_extra_blank_lines' => [
        'tokens' => [
            'attribute',
            'break',
            'case',
            'continue',
            'curly_brace_block',
            'default',
            'extra',
            'parenthesis_brace_block',
            'return',
            'square_brace_block',
            'switch',
            'throw',
            'use',
            'use_trait'
        ]
    ],
    'no_spaces_around_offset' => [
        'positions' => [
            'inside',
            'outside'
        ]
    ],
    'ordered_class_elements' => [
        'order' => [
            'use_trait',
            'constant_public',
            'constant_protected',
            'constant_private',
            'property_public',
            'property_protected',
            'property_private',
            'construct',
            'destruct',
            'magic',
            'phpunit',
            'method_public',
            'method_protected',
            'method_private'
        ]
    ],
    'ordered_imports' => true,
    'phpdoc_order' => true,
    'trim_array_spaces' => true,
    'whitespace_after_comma_in_array' => [
        'ensure_single_space' => true
    ]
];

return $config->setRules($rules)->setFinder($finder);
