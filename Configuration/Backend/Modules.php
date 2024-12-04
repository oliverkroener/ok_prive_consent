<?php

use OliverKroener\OkPriveCookieConsent\Controller\ConsentController;

return [
    'web_prive_consent' => [
        'parent' => 'web', // Main module: 'web'
        'access' => 'user,group',
        'path' => '/modules/web/prive-consent',
        'iconIdentifier' => 'module-prive-consent', // Register this icon separately
        'labels' => 'LLL:EXT:ok_prive_cookie_consent/Resources/Private/Language/locallang.xlf',
        'extensionName' => 'OkPriveCookieConsent',
        'controllerActions' => [
            ConsentController::class => [
                'index',
                'error',
                'save',
            ],
        ],
    ],
];