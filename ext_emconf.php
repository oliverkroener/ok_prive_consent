<?php
$EM_CONF[$_EXTKEY] = [
    'title' => 'Prive Consent',
    'description' => 'Provides a backend module to manage privacy consent scripts for Prive Consent.',
    'category' => 'module',
    'author' => 'Oliver Kroener',
    'author_email' => 'ok@oliver-kroener.de',
    'author_company' => 'https://www.oliver-kroener.de',
    'state' => 'stable',
    'version' => '2.1.0',
    'constraints' => [
        'depends' => [
            'php' => '7.2.0-7.4.99',
            'typo3' => '10.4.0-10.4.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
