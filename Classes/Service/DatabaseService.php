<?php

namespace OliverKroener\OkPriveCookieConsent\Service;

use OliverKroener\Helpers\Service\SiteRootService;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;

class DatabaseService
{
    public function __construct(
        private readonly SiteRootService $siteRootService,
        private readonly ConnectionPool $connectionPool,
    ) {
    }

    /**
     * Retrieves the consent script from the first sys_template record
     */
    public function getConsentScripts(int $pageId): ?array
    {
        $siteRootPid = $this->siteRootService->findNextSiteRoot($pageId);

        if (!$siteRootPid) {
            return null;
        }

        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('sys_template');
        return $queryBuilder
            ->select('tx_ok_prive_cookie_consent_banner_script')
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
    public function saveConsentScript(int $pageId, string $bannerScript): void
    {
        $siteRootPid = $this->siteRootService->findNextSiteRoot($pageId);

        if (!$siteRootPid) {
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
                ->executeStatement();
        }
    }

    /**
     * TypoScript USER function: renders the banner script for frontend output.
     */
    public function renderBannerScript(string $content, array $conf): string
    {
        $request = $GLOBALS['TYPO3_REQUEST'];
        $pageId = (int)$request->getAttribute('routing')->getPageId();

        $scripts = $this->getConsentScripts($pageId);

        return $scripts['tx_ok_prive_cookie_consent_banner_script'] ?? '';
    }
}
