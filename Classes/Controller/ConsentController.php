<?php

namespace OliverKroener\OkPriveCookieConsent\Controller;

use OliverKroener\OkPriveCookieConsent\Service\DatabaseService;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Backend\Attribute\AsController;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

#[AsController]
class ConsentController extends ActionController
{
    public function __construct(
        private readonly DatabaseService $databaseService,
        private readonly ModuleTemplateFactory $moduleTemplateFactory,
        private readonly SiteFinder $siteFinder,
    ) {
    }

    /**
     * Displays the form to edit the consent script
     */
    public function indexAction(): ResponseInterface
    {
        $pageId = (int)($this->request->getParsedBody()['id'] ?? $this->request->getQueryParams()['id'] ?? 0);
        $scripts = $this->databaseService->getConsentScripts($pageId);

        if (!empty($scripts)) {
            $this->loadFormDirtyCheckAssets();
            $moduleTemplate = $this->moduleTemplateFactory->create($this->request);
            $moduleTemplate->assignMultiple([
                'tx_ok_prive_cookie_consent_banner_script' => $scripts['tx_ok_prive_cookie_consent_banner_script'],
                'tx_ok_prive_cookie_consent_banner_enabled' => (bool)$scripts['tx_ok_prive_cookie_consent_banner_enabled'],
            ]);

            try {
                $site = $this->siteFinder->getSiteByPageId($pageId);
                $moduleTemplate->assignMultiple([
                    'siteIdentifier' => $site->getIdentifier(),
                    'siteRootPageId' => $site->getRootPageId(),
                ]);
            } catch (\TYPO3\CMS\Core\Exception\SiteNotFoundException) {
                // Site info is optional; proceed without it
            }

            return $moduleTemplate->renderResponse('Consent/Index');
        }

        return $this->redirect('error');
    }

    /**
     * Shows an error when no site root exists
     */
    public function errorAction(): ResponseInterface
    {
        $moduleTemplate = $this->moduleTemplateFactory->create($this->request);
        return $moduleTemplate->renderResponse('Consent/Error');
    }

    /**
     * Saves the consent script
     */
    public function saveAction(): ResponseInterface
    {
        $pageId = (int)($this->request->getParsedBody()['id'] ?? $this->request->getQueryParams()['id'] ?? 0);
        $bannerScript = $this->request->getArgument('tx_ok_prive_cookie_consent_banner_script') ?? '';
        $enabled = (bool)($this->request->getArgument('tx_ok_prive_cookie_consent_banner_enabled') ?? false);

        $this->databaseService->saveConsentScript($pageId, $bannerScript, $enabled);

        $this->addFlashMessage(
            LocalizationUtility::translate('flash.message.success', 'ok_prive_cookie_consent'),
            '',
            ContextualFeedbackSeverity::OK
        );

        return $this->redirect('index');
    }

    private function loadFormDirtyCheckAssets(): void
    {
        $languageService = $this->getLanguageService();
        $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
        $pageRenderer->loadJavaScriptModule(
            '@oliverkroener/ok-prive-cookie-consent/backend/form-dirty-check.js'
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
