<?php

use OliverKroener\OkPriveConsent\Controller\Backend\ConsentController;

return [
    'web_okpriveconsent' => [
        'parent' => 'web',
        'position' => ['after' => 'web_info'],
        'access' => 'user,group',
        'path' => '/module/web/prive-consent',
        'iconIdentifier' => 'module-prive-consent',
        'labels' => 'LLL:EXT:ok_prive_consent/Resources/Private/Language/locallang.xlf',
        'navigationComponent' => '@typo3/backend/page-tree/page-tree-element',
        'routes' => [
            '_default' => [
                'target' => ConsentController::class . '::indexAction',
            ],
            'save' => [
                'target' => ConsentController::class . '::saveAction',
                'methods' => ['POST'],
            ],
        ],
    ],
];
