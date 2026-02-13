<?php

namespace OliverKroener\OkPriveCookieConsent\Controller;

use OliverKroener\OkPriveCookieConsent\Service\DatabaseService;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Backend\Attribute\AsController;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

#[AsController]
class ConsentController extends ActionController
{
    public function __construct(
        private readonly DatabaseService $databaseService,
        private readonly ModuleTemplateFactory $moduleTemplateFactory,
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
            $moduleTemplate = $this->moduleTemplateFactory->create($this->request);
            $moduleTemplate->assign(
                'tx_ok_prive_cookie_consent_banner_script',
                $scripts['tx_ok_prive_cookie_consent_banner_script']
            );
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

        $this->databaseService->saveConsentScript($pageId, $bannerScript);

        $this->addFlashMessage(
            LocalizationUtility::translate('flash.message.success', 'ok_prive_cookie_consent'),
            '',
            ContextualFeedbackSeverity::OK
        );

        return $this->redirect('index');
    }
}
