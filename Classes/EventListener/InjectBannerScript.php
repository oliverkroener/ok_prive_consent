<?php

declare(strict_types=1);

namespace OliverKroener\OkPriveConsent\EventListener;

use OliverKroener\OkPriveConsent\Service\DatabaseService;
use TYPO3\CMS\Frontend\Event\AfterCacheableContentIsGeneratedEvent;

/**
 * Injects the Prive cookie-consent banner (CSS link + button + script) right
 * before the closing </body> tag of every rendered frontend page.
 *
 * Replaces the former TypoScript `page.footerData` USER object, which was
 * fragile against site-set load order (a theme re-declaring `page = PAGE`
 * after this extension's set would discard the footerData assignment). The
 * markup is part of the cacheable content, so it is stored in the page cache.
 *
 * Registered via Configuration/Services.yaml — the #[AsEventListener] attribute
 * used on the TYPO3 14 branch does not exist in TYPO3 12.
 */
final class InjectBannerScript
{
    public function __construct(
        private readonly DatabaseService $databaseService,
    ) {}

    public function __invoke(AfterCacheableContentIsGeneratedEvent $event): void
    {
        // TYPO3 12/13 have no getContent()/setContent() on the event — the rendered
        // content lives on the controller and is written to the page cache from there.
        $controller = $event->getController();
        $content = (string)$controller->content;

        $bodyClose = strripos($content, '</body>');
        if ($bodyClose === false) {
            // No </body> (e.g. JSON/headless output) — do not inject.
            return;
        }

        $markup = $this->databaseService->getBannerMarkup($event->getRequest());
        if ($markup === '') {
            return;
        }

        $controller->content = substr($content, 0, $bodyClose) . $markup . substr($content, $bodyClose);
    }
}
