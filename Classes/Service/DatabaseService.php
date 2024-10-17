<?php

namespace OliverKroener\OkPriveCookieConsent\Service;

use OliverKroener\Helpers\Service\SiteRootService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;


class DatabaseService
{
    /**
     * @var SiteRootService
     */
    private $siteRootService;

    public function __construct(SiteRootService $siteRootService)
    {
        $this->siteRootService = $siteRootService;
    }

    /**
     * Retrieves the consent script from the first sys_template record
     *
     * @param bool $frontendMode
     * @return ?array
     */
    public function getConsentScripts($frontendMode = false): ?array
    {
        /** @var \TYPO3\CMS\Core\Database\ConnectionPool $connectionPool */
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        $queryBuilder = $connectionPool->getQueryBuilderForTable('sys_template');

        if ($frontendMode) {
            $currentPageId = (int) $GLOBALS['TSFE']->id;
        } else {
            $currentPageId = (int) GeneralUtility::_GP('id');
        }

        $siteRootPid = $this->siteRootService->findNextSiteRoot($currentPageId);

        // return null if no site root is found
        if (!$siteRootPid)
            return null;

        $scripts = $queryBuilder
            ->select('tx_ok_prive_cookie_consent_banner_script')
            ->from('sys_template')
            ->where(
                $queryBuilder->expr()->eq('pid', $queryBuilder->createNamedParameter($siteRootPid, \PDO::PARAM_INT))
            )
            ->execute()
            ->fetchAssociative();

        return $scripts;
    }

    /**
     * Saves the consent script to the first sys_template record
     *
     * @param string $script
     * @return void
     */
    public function saveConsentScript(string $bannerScript): void
    {
        /** @var \TYPO3\CMS\Core\Database\ConnectionPool $connectionPool */
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        $queryBuilder = $connectionPool->getQueryBuilderForTable('sys_template');

        $currentPageId = (int) GeneralUtility::_GP('id');
        $siteRootPid = $this->siteRootService->findNextSiteRoot($currentPageId);

        // Fetch the first sys_template record with pid=0
        $record = $queryBuilder
            ->select('uid')
            ->from('sys_template')
            ->where(
                $queryBuilder->expr()->eq('pid', $queryBuilder->createNamedParameter($siteRootPid, \PDO::PARAM_INT))
            )
            ->execute()
            ->fetchFirstColumn();

        if ($record[0]) {
            // Update existing record
            $queryBuilder
                ->update('sys_template')
                ->where(
                    $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter((int) $record[0], \PDO::PARAM_INT))
                )
                ->set('tx_ok_prive_cookie_consent_banner_script', $bannerScript)
                ->execute();
        }
    }

    /**
     * Retrieves the specified script from the active sys_template.
     *
     * @param string $content The current content (unused)
     * @param array $conf Configuration array, expecting 'type' => 'head' or 'body'
     * @return string The script content or an empty string if not set.
     */
    public function renderBannerScript($content, $conf): string
    {
        // Get scripts
        $script = $this->getConsentScripts(true);

        $script = $script['tx_ok_prive_cookie_consent_banner_script'] ?? '';

        // Check if 'type="text/javascript"' is already present
        if (strpos($script, 'type="text/javascript"') === false) {
            // Add 'type="text/javascript"' if it's missing
            $script = str_replace('<script', '<script type="text/javascript"', $script);
        }

        return $script;
    }
}
