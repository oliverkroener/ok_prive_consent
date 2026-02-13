<?php
$EM_CONF[$_EXTKEY] = [
    'title' => 'Prive Consent',
    'description' => 'Provides a backend module to manage privacy consent scripts for Prive Consent.',
    'category' => 'module',
    'author' => 'Oliver Kroener',
    'author_email' => 'ok@oliver-kroener.de',
    'state' => 'stable',
    'version' => '2.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '10.4.0-10.4.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
