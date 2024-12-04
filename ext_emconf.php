<?php
$EM_CONF[$_EXTKEY] = [
    'title' => 'Prive Cookie Consent',
    'description' => 'Provides a backend module to manage privacy cookie consent scripts for Prive Cookie consent.',
    'category' => 'module',
    'author' => 'Oliver Kroener',
    'author_email' => 'ok@oliver-kroener.de',
    'state' => 'beta',
    'version' => '3.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '12.4.0-13.4.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];