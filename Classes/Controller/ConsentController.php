<?php

namespace OliverKroener\OkPriveCookieConsent\Controller;

use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use OliverKroener\OkPriveCookieConsent\Service\DatabaseService;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;

class ConsentController extends ActionController
{

    /**
     * @var DatabaseService
     */
    private $databaseService;

    public function __construct(DatabaseService $databaseService)
    {
        $this->databaseService = $databaseService;
    }

    /**
     * Displays the form to edit the consent script
     */
    public function indexAction()
    {

        $scripts = $this->databaseService->getConsentScripts();

        if (!empty($scripts)) {
            // Assign data to the view
            $this->view->assign('tx_ok_prive_cookie_consent_banner_script', $scripts['tx_ok_prive_cookie_consent_banner_script']);
            $content = $this->view->render();

            return $this->htmlResponse($content);
        } else {
            return $this->redirect('error');
        }
    }

    /**
     * Shows an error, when no site root exists
     */
    public function errorAction(): ResponseInterface
    {
        // Render the view and return the HTML response
        $content = $this->view->render();

        return $this->htmlResponse($content);
    }

    /**
     * Saves the consent script
     */
    public function saveAction(): ResponseInterface
    {
        $bannerScript = $this->request->getArgument('tx_ok_prive_cookie_consent_banner_script') ?? '';

        $this->databaseService->saveConsentScript($bannerScript);

        // Add a flash message
        $this->addFlashMessage(
            LocalizationUtility::translate('flash.message.success', 'ok_prive_cookie_consent'),
            '',
            ContextualFeedbackSeverity::OK
        );

        // Redirect back to index
        return $this->redirect('index');
    }
}
