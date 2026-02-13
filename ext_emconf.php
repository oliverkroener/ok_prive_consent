<?php
$EM_CONF[$_EXTKEY] = [
    'title' => 'Prive Consent',
    'description' => 'Provides a backend module to manage privacy consent scripts for Prive Consent.',
    'category' => 'module',
    'author' => 'Oliver Kroener',
    'author_email' => 'ok@oliver-kroener.de',
    'state' => 'stable',
    'version' => '3.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '11.5.0-11.99.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
