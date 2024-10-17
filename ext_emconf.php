<?php
$EM_CONF[$_EXTKEY] = [
    'title' => 'Prive Cookie Consent',
    'description' => 'Provides a backend module to manage privacy cookie consent scripts for Prive Cookie consent.',
    'category' => 'module',
    'author' => 'Oliver Kroener',
    'author_email' => 'ok@oliver-kroener.de',
    'state' => 'beta',
    'version' => '2.0.',
    'constraints' => [
        'depends' => [
            'typo3' => '11.5.0-11.5.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];