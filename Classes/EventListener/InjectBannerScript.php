<?php

declare(strict_types=1);

namespace OliverKroener\OkPriveConsent\EventListener;

use OliverKroener\OkPriveConsent\Service\DatabaseService;
use TYPO3\CMS\Core\Attribute\AsEventListener;
use TYPO3\CMS\Frontend\Event\AfterCacheableContentIsGeneratedEvent;

/**
 * Injects the Prive cookie-consent banner (CSS link + button + script) right
 * before the closing </body> tag of every rendered frontend page.
 *
 * Replaces the former TypoScript `page.footerData` USER object, which was
 * fragile against site-set load order (a theme re-declaring `page = PAGE`
 * after this extension's set would discard the footerData assignment). The
 * markup is part of the cacheable content, so it is stored in the page cache.
 */
#[AsEventListener('ok-prive-consent/inject-banner-script')]
final class InjectBannerScript
{
    public function __construct(
        private readonly DatabaseService $databaseService,
    ) {}

    public function __invoke(AfterCacheableContentIsGeneratedEvent $event): void
    {
        $markup = $this->databaseService->getBannerMarkup($event->getRequest());
        if ($markup === '') {
            return;
        }

        $content = $event->getContent();
        $bodyClose = strripos($content, '</body>');
        if ($bodyClose === false) {
            // No </body> (e.g. JSON/headless output) — do not inject.
            return;
        }

        $event->setContent(
            substr($content, 0, $bodyClose) . $markup . substr($content, $bodyClose)
        );
    }
}
