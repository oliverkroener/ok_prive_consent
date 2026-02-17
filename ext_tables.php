<?php

defined('TYPO3_MODE') || die('Access denied!');

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
    'OkPriveConsent',
    'web',
    'consent',
    '',
    [
        \OliverKroener\OkPriveConsent\Controller\ConsentController::class => 'index,error,save',
    ],
    [
        'access' => 'user,group',
        'iconIdentifier' => 'module-prive-consent',
        'labels' => 'LLL:EXT:ok_prive_consent/Resources/Private/Language/locallang.xlf',
        'navigationComponentId' => 'TYPO3/CMS/Backend/PageTree/PageTreeElement',
    ]
);
