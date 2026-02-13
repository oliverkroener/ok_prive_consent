<?php

namespace OliverKroener\OkPriveConsent\Controller;

use OliverKroener\OkPriveConsent\Service\DatabaseService;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class ConsentController extends ActionController
{
    protected DatabaseService $databaseService;
    protected SiteFinder $siteFinder;

    public function __construct(
        DatabaseService $databaseService,
        SiteFinder $siteFinder
    ) {
        $this->databaseService = $databaseService;
        $this->siteFinder = $siteFinder;
    }

    /**
     * Displays the form to edit the consent script
     */
    public function indexAction(): void
    {
        $pageId = (int)GeneralUtility::_GP('id');
        $scripts = $this->databaseService->getConsentScripts($pageId);

        if (!empty($scripts)) {
            $this->loadFormDirtyCheckAssets();
            $this->view->assignMultiple([
                'tx_ok_prive_cookie_consent_banner_script' => $scripts['tx_ok_prive_cookie_consent_banner_script'],
                'tx_ok_prive_cookie_consent_banner_enabled' => (bool)$scripts['tx_ok_prive_cookie_consent_banner_enabled'],
            ]);

            try {
                $site = $this->siteFinder->getSiteByPageId($pageId);
                $this->view->assignMultiple([
                    'siteIdentifier' => $site->getIdentifier(),
                    'siteRootPageId' => $site->getRootPageId(),
                ]);
            } catch (\TYPO3\CMS\Core\Exception\SiteNotFoundException $e) {
                // Site info is optional; proceed without it
            }

            return;
        }

        $this->redirect('error');
    }

    /**
     * Shows an error when no site root exists
     */
    public function errorAction(): void
    {
    }

    /**
     * Saves the consent script
     */
    public function saveAction(): void
    {
        $pageId = (int)GeneralUtility::_GP('id');
        $bannerScript = $this->request->getArgument('tx_ok_prive_cookie_consent_banner_script') ?? '';
        $enabled = (bool)($this->request->getArgument('tx_ok_prive_cookie_consent_banner_enabled') ?? false);

        $this->databaseService->saveConsentScript($pageId, $bannerScript, $enabled);

        // Flush frontend page cache so the updated script is served immediately
        $cacheManager = GeneralUtility::makeInstance(CacheManager::class);
        $cacheManager->flushCachesInGroup('pages');

        $this->addFlashMessage(
            LocalizationUtility::translate('flash.message.success', 'ok_prive_consent'),
            '',
            AbstractMessage::OK
        );

        $this->redirect('index');
    }

    private function loadFormDirtyCheckAssets(): void
    {
        $languageService = $this->getLanguageService();
        $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
        $pageRenderer->loadRequireJsModule(
            'TYPO3/CMS/OkPriveConsent/Backend/FormDirtyCheck'
        );
        $pageRenderer->addInlineLanguageLabelArray([
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
    }

    private function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }
}
