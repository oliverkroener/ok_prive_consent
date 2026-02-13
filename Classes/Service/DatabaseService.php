<?php

namespace OliverKroener\OkPriveConsent\Service;

use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Exception\SiteNotFoundException;
use TYPO3\CMS\Core\Site\SiteFinder;

class DatabaseService
{
    protected SiteFinder $siteFinder;
    protected ConnectionPool $connectionPool;

    public function __construct(
        SiteFinder $siteFinder,
        ConnectionPool $connectionPool
    ) {
        $this->siteFinder = $siteFinder;
        $this->connectionPool = $connectionPool;
    }

    /**
     * Retrieves the consent script from the first sys_template record
     */
    public function getConsentScripts(int $pageId): ?array
    {
        try {
            $siteRootPid = $this->siteFinder->getSiteByPageId($pageId)->getRootPageId();
        } catch (SiteNotFoundException $e) {
            return null;
        }

        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('sys_template');
        return $queryBuilder
            ->select('tx_ok_prive_cookie_consent_banner_script', 'tx_ok_prive_cookie_consent_banner_enabled')
            ->from('sys_template')
            ->where(
                $queryBuilder->expr()->eq('pid', $queryBuilder->createNamedParameter($siteRootPid, Connection::PARAM_INT))
            )
            ->executeQuery()
            ->fetchAssociative() ?: null;
    }

    /**
     * Saves the consent script to the first sys_template record
     */
    public function saveConsentScript(int $pageId, string $bannerScript, bool $enabled = false): void
    {
        try {
            $siteRootPid = $this->siteFinder->getSiteByPageId($pageId)->getRootPageId();
        } catch (SiteNotFoundException $e) {
            return;
        }

        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('sys_template');
        $record = $queryBuilder
            ->select('uid')
            ->from('sys_template')
            ->where(
                $queryBuilder->expr()->eq('pid', $queryBuilder->createNamedParameter($siteRootPid, Connection::PARAM_INT))
            )
            ->executeQuery()
            ->fetchFirstColumn();

        if (!empty($record)) {
            $updateQueryBuilder = $this->connectionPool->getQueryBuilderForTable('sys_template');
            $updateQueryBuilder
                ->update('sys_template')
                ->where(
                    $updateQueryBuilder->expr()->eq('uid', $updateQueryBuilder->createNamedParameter((int)$record[0], Connection::PARAM_INT))
                )
                ->set('tx_ok_prive_cookie_consent_banner_script', $bannerScript)
                ->set('tx_ok_prive_cookie_consent_banner_enabled', (int)$enabled, true, Connection::PARAM_INT)
                ->executeStatement();
        }
    }

    /**
     * TypoScript USER function: renders the banner script for frontend output.
     */
    public function renderBannerScript(string $content, array $conf): string
    {
        $pageId = (int)$GLOBALS['TSFE']->id;

        $scripts = $this->getConsentScripts($pageId);

        if (empty($scripts['tx_ok_prive_cookie_consent_banner_enabled'])) {
            return '';
        }

        $script = trim($scripts['tx_ok_prive_cookie_consent_banner_script'] ?? '');

        return $script !== '' ? $scripts['tx_ok_prive_cookie_consent_banner_script'] : '';
    }
}
