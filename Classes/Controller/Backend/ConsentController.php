<?php

declare(strict_types=1);

namespace OliverKroener\OkPriveConsent\Controller\Backend;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Attribute\AsController;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Backend\Template\Components\ButtonBar;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Http\RedirectResponse;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Imaging\IconSize;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Messaging\FlashMessageService;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Type\Bitmask\Permission;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use TYPO3\CMS\Core\Utility\GeneralUtility;

#[AsController]
class ConsentController
{
    public function __construct(
        private readonly ModuleTemplateFactory $moduleTemplateFactory,
        private readonly SiteFinder $siteFinder,
        private readonly ConnectionPool $connectionPool,
        private readonly UriBuilder $uriBuilder,
        private readonly PageRenderer $pageRenderer,
        private readonly IconFactory $iconFactory,
    ) {}

    public function indexAction(ServerRequestInterface $request): ResponseInterface
    {
        $id = (int)($request->getQueryParams()['id'] ?? 0);
        $view = $this->moduleTemplateFactory->create($request);

        $languageService = $this->getLanguageService();
        $moduleTitle = $languageService->sL(
            'LLL:EXT:ok_prive_consent/Resources/Private/Language/locallang.xlf:module.title'
        );

        $pageInfo = BackendUtility::readPageAccess(
            $id,
            $this->getBackendUser()->getPagePermsClause(Permission::PAGE_SHOW)
        ) ?: [];

        $view->setTitle($moduleTitle, $pageInfo['title'] ?? '');

        if ($pageInfo !== []) {
            $view->getDocHeaderComponent()->setMetaInformation($pageInfo);
        }

        if ($id === 0) {
            $view->assign('noPageSelected', true);
            return $view->renderResponse('Backend/Consent/Index');
        }

        try {
            $site = $this->siteFinder->getSiteByPageId($id);
            $siteRootPid = $site->getRootPageId();
        } catch (\TYPO3\CMS\Core\Exception\SiteNotFoundException) {
            $view->assign('noSiteFound', true);
            return $view->renderResponse('Backend/Consent/Index');
        }

        $scripts = $this->getConsentScripts($siteRootPid);

        if ($scripts === false) {
            $view->assign('noSiteFound', true);
            return $view->renderResponse('Backend/Consent/Index');
        }

        $saveUrl = (string)$this->uriBuilder->buildUriFromRoute(
            'web_okpriveconsent.save',
            ['id' => $id]
        );

        // Add save button to DocHeader
        $buttonBar = $view->getDocHeaderComponent()->getButtonBar();
        $saveButton = $buttonBar->makeInputButton()
            ->setName('_savedok')
            ->setValue('1')
            ->setForm('PriveConsentForm')
            ->setIcon($this->iconFactory->getIcon('actions-document-save', IconSize::SMALL))
            ->setTitle($languageService->sL('LLL:EXT:core/Resources/Private/Language/locallang_common.xlf:save'))
            ->setShowLabelText(true);
        $buttonBar->addButton($saveButton, ButtonBar::BUTTON_POSITION_LEFT, 2);

        // Unsaved-changes detection
        $this->pageRenderer->loadJavaScriptModule(
            '@oliverkroener/ok-prive-consent/backend/form-dirty-check.js'
        );
        $this->pageRenderer->addInlineLanguageLabelArray([
            'label.confirm.close_without_save.title' => $languageService->sL(
                'LLL:EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf:label.confirm.close_without_save.title'
            ),
            'label.confirm.close_without_save.content' => $languageService->sL(
                'LLL:EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf:label.confirm.close_without_save.content'
            ),
            'buttons.confirm.close_without_save.yes' => $languageService->sL(
                'LLL:EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf:buttons.confirm.close_without_save.yes'
            ),
            'buttons.confirm.close_without_save.no' => $languageService->sL(
                'LLL:EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf:buttons.confirm.close_without_save.no'
            ),
            'buttons.confirm.save_and_close' => $languageService->sL(
                'LLL:EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf:buttons.confirm.save_and_close'
            ),
        ]);

        $view->assignMultiple([
            'tx_ok_prive_cookie_consent_banner_script' => $scripts['tx_ok_prive_cookie_consent_banner_script'] ?? '',
            'tx_ok_prive_cookie_consent_banner_enabled' => (bool)($scripts['tx_ok_prive_cookie_consent_banner_enabled'] ?? false),
            'siteIdentifier' => $site->getIdentifier(),
            'siteRootPageId' => $siteRootPid,
            'saveUrl' => $saveUrl,
        ]);

        return $view->renderResponse('Backend/Consent/Index');
    }

    public function saveAction(ServerRequestInterface $request): ResponseInterface
    {
        $parsedBody = $request->getParsedBody();
        $id = (int)($request->getQueryParams()['id'] ?? $parsedBody['id'] ?? 0);

        $bannerScript = (string)($parsedBody['tx_ok_prive_cookie_consent_banner_script'] ?? '');
        $enabled = (bool)($parsedBody['tx_ok_prive_cookie_consent_banner_enabled'] ?? false);

        if ($id > 0) {
            try {
                $site = $this->siteFinder->getSiteByPageId($id);
                $siteRootPid = $site->getRootPageId();
                $this->saveConsentScript($siteRootPid, $bannerScript, $enabled);

                // Flush frontend page cache so the updated script is served immediately
                GeneralUtility::makeInstance(CacheManager::class)->flushCachesInGroup('pages');

                $this->addFlashMessage(
                    'flash.message.success',
                    ContextualFeedbackSeverity::OK
                );
            } catch (\TYPO3\CMS\Core\Exception\SiteNotFoundException) {
                // no site found — redirect back without saving
            }
        }

        return new RedirectResponse(
            (string)$this->uriBuilder->buildUriFromRoute('web_okpriveconsent', ['id' => $id])
        );
    }

    /**
     * @return array<string, mixed>|false
     */
    protected function getConsentScripts(int $siteRootPid): array|false
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('sys_template');

        return $queryBuilder
            ->select('tx_ok_prive_cookie_consent_banner_script', 'tx_ok_prive_cookie_consent_banner_enabled')
            ->from('sys_template')
            ->where(
                $queryBuilder->expr()->eq('pid', $queryBuilder->createNamedParameter($siteRootPid, Connection::PARAM_INT))
            )
            ->executeQuery()
            ->fetchAssociative();
    }

    protected function saveConsentScript(int $siteRootPid, string $bannerScript, bool $enabled): void
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('sys_template');

        $record = $queryBuilder
            ->select('uid')
            ->from('sys_template')
            ->where(
                $queryBuilder->expr()->eq('pid', $queryBuilder->createNamedParameter($siteRootPid, Connection::PARAM_INT))
            )
            ->executeQuery()
            ->fetchFirstColumn();

        if (!empty($record[0])) {
            $connection = $this->connectionPool->getConnectionForTable('sys_template');
            $connection->update(
                'sys_template',
                [
                    'tx_ok_prive_cookie_consent_banner_script' => $bannerScript,
                    'tx_ok_prive_cookie_consent_banner_enabled' => (int)$enabled,
                ],
                ['uid' => (int)$record[0]]
            );
        }
    }

    private function addFlashMessage(string $bodyKey, ContextualFeedbackSeverity $severity): void
    {
        $languageService = $this->getLanguageService();
        $flashMessage = GeneralUtility::makeInstance(
            FlashMessage::class,
            $languageService->sL('LLL:EXT:ok_prive_consent/Resources/Private/Language/locallang.xlf:' . $bodyKey),
            '',
            $severity,
            true
        );
        GeneralUtility::makeInstance(FlashMessageService::class)
            ->getMessageQueueByIdentifier()
            ->enqueue($flashMessage);
    }

    private function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }

    private function getBackendUser(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }
}
