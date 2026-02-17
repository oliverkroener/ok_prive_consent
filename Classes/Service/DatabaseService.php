<?php

declare(strict_types=1);

namespace OliverKroener\OkPriveConsent\Service;

use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\PathUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

class DatabaseService
{
    private ContentObjectRenderer $cObj;

    public function __construct(
        private readonly SiteFinder $siteFinder,
        private readonly ConnectionPool $connectionPool,
    ) {}

    public function setContentObjectRenderer(ContentObjectRenderer $cObj): void
    {
        $this->cObj = $cObj;
    }

    /**
     * TypoScript USER function: renders the banner script for frontend output.
     */
    public function renderBannerScript(string $content, array $conf): string
    {
        $pageId = $this->getPageId();
        if ($pageId === 0) {
            return '';
        }

        try {
            $site = $this->siteFinder->getSiteByPageId($pageId);
        } catch (\TYPO3\CMS\Core\Exception\SiteNotFoundException) {
            return '';
        }

        $siteRootPid = $site->getRootPageId();

        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('sys_template');
        $scripts = $queryBuilder
            ->select('tx_ok_prive_cookie_consent_banner_script', 'tx_ok_prive_cookie_consent_banner_enabled')
            ->from('sys_template')
            ->where(
                $queryBuilder->expr()->eq('pid', $queryBuilder->createNamedParameter($siteRootPid, Connection::PARAM_INT))
            )
            ->executeQuery()
            ->fetchAssociative();

        if ($scripts === false || empty($scripts['tx_ok_prive_cookie_consent_banner_enabled'])) {
            return '';
        }

        $script = trim($scripts['tx_ok_prive_cookie_consent_banner_script'] ?? '');
        if ($script === '') {
            return '';
        }

        $cssPath = PathUtility::getPublicResourceWebPath('EXT:ok_prive_consent/Resources/Public/Css/prive-cookie-button.css');

        return $script
            . '<link rel="stylesheet" href="' . htmlspecialchars($cssPath) . '">'
            . '<a href="#" class="prive-cookie-button" data-cc="c-settings">&nbsp;</a>';
    }

    private function getPageId(): int
    {
        $routing = $this->cObj->getRequest()->getAttribute('routing');
        if ($routing !== null && method_exists($routing, 'getPageId')) {
            return $routing->getPageId();
        }

        return 0;
    }
}
