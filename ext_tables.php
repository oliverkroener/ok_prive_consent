<?php

defined('TYPO3_MODE') || die('Access denied!');

$_EXTKEY = 'ok_prive_cookie_consent';

// Register backend module
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
    'OkPriveCookieConsent',
    'web',
    'priveconsent',
    '',
    [
        'OliverKroener\\OkPriveCookieConsent\\Controller\\ConsentController' => 'index, error, save',
    ],
    [
        'access' => 'user,group',
        'icon'   => 'EXT:' . $_EXTKEY . '/Resources/Public/Icons/module-icon.svg',
        'labels' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang.xlf',
    ]
);