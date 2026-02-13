<?php

use OliverKroener\OkPriveConsent\Controller\ConsentController;

return [
    'web_prive_consent' => [
        'parent' => 'web', // Main module: 'web'
        'access' => 'user,group',
        'path' => '/modules/web/prive-consent',
        'iconIdentifier' => 'module-prive-consent', // Register this icon separately
        'labels' => 'LLL:EXT:ok_prive_consent/Resources/Private/Language/locallang.xlf',
        'extensionName' => 'OkPriveConsent',
        'controllerActions' => [
            ConsentController::class => [
                'index',
                'error',
                'save',
            ],
        ],
    ],
];