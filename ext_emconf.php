<?php
$EM_CONF[$_EXTKEY] = [
    'title' => 'Prive Consent',
    'description' => 'Provides a backend module to manage privacy consent scripts for Prive Consent.',
    'category' => 'module',
    'author' => 'Oliver Kroener',
    'author_email' => 'ok@oliver-kroener.de',
    'author_company' => 'https://www.oliver-kroener.de',
    'state' => 'stable',
    'version' => '5.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '14.3.0-14.99.99',
            'php' => '8.2.0-8.5.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
