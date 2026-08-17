<?php

declare(strict_types=1);

namespace OliverKroener\OkPriveConsent\Service;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\PathUtility;

class DatabaseService
{
    public function __construct(
        private readonly SiteFinder $siteFinder,
        private readonly ConnectionPool $connectionPool,
    ) {}

    /**
     * Builds the frontend banner markup (CSS link + cookie button + Prive script)
     * for the site the given request belongs to. Returns '' when the banner is
     * disabled, empty, or the page/site cannot be resolved.
     */
    public function getBannerMarkup(ServerRequestInterface $request): string
    {
        $pageId = $this->getPageId($request);
        if ($pageId === 0) {
            return '';
        }

        try {
            $site = $this->siteFinder->getSiteByPageId($pageId);
        } catch (\TYPO3\CMS\Core\Exception\SiteNotFoundException) {
            return '';
        }

        $siteRootPid = $site->getRootPageId();

        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('pages');
        // The banner config must be readable even if the site root itself is hidden
        // or time-restricted — visibility of the requested page is handled by the core.
        $queryBuilder->getRestrictions()->removeAll()->add(new DeletedRestriction());
        $scripts = $queryBuilder
            ->select('tx_ok_prive_cookie_consent_banner_script', 'tx_ok_prive_cookie_consent_banner_enabled')
            ->from('pages')
            ->where(
                $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($siteRootPid, Connection::PARAM_INT))
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

        // Order: CSS → cookie button → Prive script
        // The button must be in the DOM before Prive executes so it can bind its click handler.
        return '<link rel="stylesheet" href="' . htmlspecialchars($cssPath) . '">'
            . '<a href="#" class="prive-cookie-button" data-cc="c-settings">&nbsp;</a>'
            . $script;
    }

    private function getPageId(ServerRequestInterface $request): int
    {
        // TYPO3 13.3+ page information attribute.
        $pageInformation = $request->getAttribute('frontend.page.information');
        if ($pageInformation !== null && method_exists($pageInformation, 'getId')) {
            return (int)$pageInformation->getId();
        }

        // PageArguments — available in TYPO3 12 and 13.
        $routing = $request->getAttribute('routing');
        if ($routing !== null && method_exists($routing, 'getPageId')) {
            return (int)$routing->getPageId();
        }

        return 0;
    }
}
