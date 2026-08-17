# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

TYPO3 CMS extension (`ok_prive_consent`) providing a backend module for managing Prive Cookie Consent banner scripts. Administrators edit JavaScript snippets in the TYPO3 backend; the scripts are stored on the site root `pages` record and injected into the frontend by a PSR-14 event listener.

- **TYPO3 compatibility:** 12.4 LTS, 13.4 LTS
- **PHP:** >= 8.1
- **PHP namespace:** `OliverKroener\OkPriveConsent\`
- **Extension key:** `ok_prive_consent`
- **Composer package:** `oliverkroener/ok-prive-consent`
- **Version:** 4.2.0
- **External dependencies:** `typo3/cms-core`, `typo3/cms-backend`, `typo3/cms-frontend`, `typo3/cms-install`

## Build Commands

There are no build, lint, or test commands configured. CSS/JS assets in `Resources/Public/` are maintained directly.

Documentation can be generated via `make docs` (requires Docker).

## Architecture

### Request Flow

```
TYPO3 Backend → ConsentController (PSR-7) → pages (site root)
                      ↑
                 ModuleTemplateFactory, SiteFinder, ConnectionPool (TYPO3 core)

Frontend → AfterCacheableContentIsGeneratedEvent → InjectBannerScript
                                                      → DatabaseService → pages (site root)
```

- **`ConsentController`** (`Classes/Controller/Backend/ConsentController.php`) – PSR-7 controller with `#[AsController]` attribute. Uses `ModuleTemplateFactory` for rendering, `UriBuilder` for routing, and `PageRenderer` for JS module loading. Actions: `indexAction` (load form with no-page/no-site/edit states), `saveAction` (persist script + flush page cache). Reads/writes the two fields on the site root `pages` record by `uid`.
- **`DatabaseService`** (`Classes/Service/DatabaseService.php`) – `getBannerMarkup(ServerRequestInterface)` builds the frontend markup (CSS link + cookie button + script) for the site the request belongs to. Resolves the page ID from `frontend.page.information` (13.3+) with a fallback to the `routing` attribute (12 + 13).
- **`InjectBannerScript`** (`Classes/EventListener/InjectBannerScript.php`) – PSR-14 listener on `AfterCacheableContentIsGeneratedEvent`; splices the markup in before the last `</body>` via `$event->getController()->content`. Registered with the `event.listener` tag in `Services.yaml` (the `#[AsEventListener]` attribute is TYPO3 13+ only).
- **`MigrateConsentStorageToPagesUpgradeWizard`** (`Classes/Updates/`) – Install Tool wizard `okPriveConsentMigrateStorageToPages`, copies pre-4.2.0 values from `sys_template` to the site root page. Uses `TYPO3\CMS\Install\Attribute\UpgradeWizard` + `TYPO3\CMS\Install\Updates\UpgradeWizardInterface` (both exist in 12.4 and 13.4).

### Frontend Rendering

Rendering happens entirely in PHP via `InjectBannerScript`. There is no TypoScript involved: `Configuration/TypoScript/setup.typoscript` is comment-only and kept solely so existing static-template includes do not dangle. The markup order is CSS link → cookie button → script, injected before the last `</body>` while the content is still cacheable (so it lands in the page cache).

The banner script is only rendered when the `tx_ok_prive_cookie_consent_banner_enabled` flag is set.

### Site Sets

`Configuration/Sets/OkPriveConsent/` ships an intentionally empty set (`oliverkroener/ok-prive-consent`). **Site sets require TYPO3 13.1+**; on TYPO3 12 the directory is ignored by the core. The set defines no TypoScript, dependencies or settings — it exists only so site configs can reference it. Rendering never depends on it.

### Dependency Injection

Configured in `Configuration/Services.yaml` (Symfony DI). Autowiring is enabled. `InjectBannerScript` is registered with the `event.listener` tag (identifier `ok-prive-consent/inject-banner-script`); the upgrade wizard is tagged automatically through the `#[UpgradeWizard]` attribute. No service needs to be public.

### Module Registration

- Backend module registered declaratively in `Configuration/Backend/Modules.php` with page tree navigation component and route definitions.
- Module icon registered declaratively in `Configuration/Icons.php`.
- ES6 JavaScript modules mapped in `Configuration/JavaScriptModules.php` under `@oliverkroener/ok-prive-consent/`.

### Templates

Fluid template at `Resources/Private/Templates/Backend/Consent/Index.html`. Uses `<f:layout name="Module" />` provided by `ModuleTemplateFactory`. Template handles three states: no page selected, no site found, and edit form. Localizations in XLIFF format (`locallang.xlf` for English, `de.locallang.xlf` for German).

### Database Fields

Both custom fields live on the `pages` table, on the **site root record** (looked up by `uid`), declared in `ext_tables.sql`:

- `tx_ok_prive_cookie_consent_banner_script` — the JavaScript snippet
- `tx_ok_prive_cookie_consent_banner_enabled` — boolean toggle

They have no TCA — the module reads and writes them directly through `ConnectionPool`. Queries reduce restrictions to `DeletedRestriction` so a hidden site root stays editable and renderable.

The same two columns are still declared on `sys_template` (the pre-4.2.0 storage) so the upgrade wizard can read the old values. `Configuration/TCA/Overrides/sys_template.php` keeps registering the now-empty static template.

Note: field names retain the original `ok_prive_cookie_consent` prefix for backward compatibility with existing data.

### Frontend Assets

Backend JS/CSS assets live in `Resources/Public/`. Brand colors: primary `#f05722`, secondary `#0fa8dd`.

## TYPO3 12+13 API Patterns

- **`#[AsController]`** — attribute marking PSR-7 backend controllers
- **`ModuleTemplateFactory`** — creates module views (`$view = $this->moduleTemplateFactory->create($request)`)
- **`$view->renderResponse('Backend/Consent/Index')`** — renders Fluid template and returns PSR-7 response
- **`UriBuilder::buildUriFromRoute()`** — generates backend module route URIs
- **`ContextualFeedbackSeverity::OK`** — flash message severity enum (replaces `AbstractMessage::OK`)
- **`FlashMessage` + `FlashMessageService`** — explicit flash message creation and enqueuing
- **`$request->getAttribute('routing')->getPageId()`** — get page ID in frontend context (works in 12 + 13; `frontend.page.information` is 13.3+ only)
- **`AfterCacheableContentIsGeneratedEvent`** — available since 12.0, but `getContent()`/`setContent()` are v14 API: on 12/13 use `$event->getController()->content`
- **`event.listener` service tag** — cross-version listener registration; `#[AsEventListener]` needs 13+
- **`TYPO3\CMS\Install\Attribute\UpgradeWizard`** — present in 12.4 and 13.4 (the `Core\Attribute` variant is v14)
- **`$GLOBALS['LANG']`** — LanguageService for backend label resolution
- **`Connection::PARAM_INT`** — TYPO3 connection constants

## JavaScript

Backend JS uses ES6 module format. The dirty-check module is at `Resources/Public/JavaScript/backend/form-dirty-check.js` and is loaded via `loadJavaScriptModule('@oliverkroener/ok-prive-consent/backend/form-dirty-check.js')`.

`form-dirty-check.js` integrates with TYPO3's `ConsumerScope` to intercept page tree clicks and module navigation, showing an unsaved-changes confirmation modal (with "save and close" support via fetch-based AJAX form submission).

## Git Commit Convention

Prefix commits with a tag: `[TASK]` for features/refactoring, `[BUGFIX]` for fixes, `[VERSION]` for version bumps.
