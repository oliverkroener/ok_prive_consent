<?php
$EM_CONF[$_EXTKEY] = [
    'title' => 'Prive Consent',
    'description' => 'Provides a backend module to manage privacy consent scripts for Prive Consent.',
    'category' => 'module',
    'author' => 'Oliver Kroener',
    'author_email' => 'ok@oliver-kroener.de',
    'author_company' => 'https://www.oliver-kroener.de',
    'state' => 'stable',
    'version' => '3.1.0',
    'constraints' => [
        'depends' => [
            'typo3' => '12.4.0-13.99.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
