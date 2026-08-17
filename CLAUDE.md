# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

TYPO3 CMS extension (`ok_prive_consent`) providing a backend module for managing Prive Cookie Consent banner scripts. Administrators edit JavaScript snippets in the TYPO3 backend; the scripts are stored on the site root page record (`pages` table) and injected into the frontend by a PSR-14 event listener.

- **TYPO3 compatibility:** 14.3 LTS (v14 line)
- **PHP:** >= 8.2 (8.2–8.5)
- **PHP namespace:** `OliverKroener\OkPriveConsent\`
- **Extension key:** `ok_prive_consent`
- **Composer package:** `oliverkroener/ok-prive-consent`
- **Version:** 5.0.1
- **External dependencies:** `typo3/cms-core` (`^14.3`), `typo3/cms-backend` (`^14.3`)

## Build Commands

There are no build, lint, or test commands configured. CSS/JS assets in `Resources/Public/` are maintained directly.

Documentation can be generated via `make docs` (requires Docker).

## Architecture

### Request Flow

```
Backend edit:    TYPO3 Backend → ConsentController (PSR-7) → pages table (site root)
Frontend render: AfterCacheableContentIsGeneratedEvent → InjectBannerScript → DatabaseService → pages (site root)
```

- **`ConsentController`** (`Classes/Controller/Backend/ConsentController.php`) – PSR-7 controller with `#[AsController]` attribute. Uses `ModuleTemplateFactory` for rendering, `UriBuilder` for routing, and `PageRenderer` for JS module loading. Actions: `indexAction` (load form with no-page/no-site/edit states), `saveAction` (persist script + flush page cache).
- **`InjectBannerScript`** (`Classes/EventListener/InjectBannerScript.php`) – PSR-14 listener (`#[AsEventListener]`) on `AfterCacheableContentIsGeneratedEvent`. Injects the banner markup immediately before the last `</body>`; bails out cleanly when there is no `</body>` (e.g. JSON/headless output). Markup is part of cacheable content, so it lands in the page cache.
- **`DatabaseService`** (`Classes/Service/DatabaseService.php`) – `getBannerMarkup(ServerRequestInterface)` builds the frontend banner (CSS link + cookie button + Prive script) for the request's site, returning `''` when the banner is disabled/empty or the page/site cannot be resolved. Resolves the page ID from the `frontend.page.information` request attribute (falling back to `routing`), and builds the CSS asset URI via the System Resource API (`SystemResourceFactory` + `SystemResourcePublisherInterface`).

### Frontend Rendering

The banner is injected by the **PHP event listener** (above), **not** by TypoScript. This was deliberately moved off the former `page.footerData` USER object, which was fragile against site-set load order: a theme set re-declaring `page = PAGE` after this extension's set would discard the `footerData` assignment.

The site set (`Configuration/Sets/OkPriveConsent/`, set name `oliverkroener/ok-prive-consent`) is now **intentionally near-empty** — `setup.typoscript` is comments only, and `config.yaml` declares the set with no dependencies. It exists solely so site configs can keep referencing the set under `dependencies:`. The banner works **even without** the set being referenced, because the listener is registered via the extension's `Services.yaml`.

The banner is only rendered when the `tx_ok_prive_cookie_consent_banner_enabled` flag is set on the site root's `pages` row.

### Dependency Injection

Configured in `Configuration/Services.yaml` (Symfony DI). Autowiring + autoconfiguration only — no per-service overrides. `DatabaseService` is **no longer public** (the v4 `public: true` was removed when the TypoScript USER function was replaced by the event listener).

### Module Registration

- Backend module registered declaratively in `Configuration/Backend/Modules.php` with page tree navigation component and route definitions (`access: user`).
- Module icon registered declaratively in `Configuration/Icons.php`.
- Frontend banner injection registered via the `#[AsEventListener]` attribute on `InjectBannerScript` (picked up through `Services.yaml` autoconfiguration — no manual `Configuration/Services.yaml` event wiring).
- ES6 JavaScript modules mapped in `Configuration/JavaScriptModules.php` under `@oliverkroener/ok-prive-consent/`.

### Templates

Fluid template at `Resources/Private/Templates/Backend/Consent/Index.html`. Uses `<f:layout name="Module" />` provided by `ModuleTemplateFactory`. Template handles three states: no page selected, no site found, and edit form. Localizations in XLIFF format (`locallang.xlf` for English, `de.locallang.xlf` for German).

### Database Fields

Both custom fields live on the `pages` table and are read/written on the **site root page** (declared in `ext_tables.sql`; there is **no** TCA definition — the fields are read/written exclusively via QueryBuilder from the backend module):

- `tx_ok_prive_cookie_consent_banner_script` — the JavaScript snippet
- `tx_ok_prive_cookie_consent_banner_enabled` — boolean toggle

Note: field names retain the original `ok_prive_cookie_consent` prefix for backward compatibility with existing data.

Up to v5 the fields lived on `sys_template`. Sites driven by **site sets** have no `sys_template` record at all, so the module always fell back to its "no site found" error. `MigrateConsentStorageToPagesUpgradeWizard` (`Classes/Upgrades/`, registered via the v14 `#[UpgradeWizard]` attribute from EXT:core — no EXT:install dependency) copies old `sys_template` values onto the matching site root page.

### Frontend Assets

Backend JS/CSS assets live in `Resources/Public/`. Brand colors: primary `#f05722`, secondary `#0fa8dd`.

## TYPO3 v14 API Patterns

- **`#[AsController]`** — attribute marking PSR-7 backend controllers
- **`#[AsEventListener]` + `AfterCacheableContentIsGeneratedEvent`** — PSR-14 way to mutate final frontend HTML while staying inside the page cache (used to inject the banner before `</body>`)
- **`ModuleTemplateFactory`** — creates module views (`$view = $this->moduleTemplateFactory->create($request)`)
- **`$view->renderResponse('Backend/Consent/Index')`** — renders Fluid template and returns PSR-7 response
- **`UriBuilder::buildUriFromRoute()`** — generates backend module route URIs
- **`ComponentFactory::createInputButton()`** — DocHeader button creation (v14; replaces deprecated `ButtonBar::makeInputButton()`). Still added via `$buttonBar->addButton()`.
- **`IconSize::SMALL`** — icon size enum passed to `IconFactory::getIcon()` (v14; `Icon::SIZE_*` constants removed)
- **`DocHeaderComponent::setPageBreadcrumb($pageInfo)`** — v14 replacement for deprecated `setMetaInformation()`
- **`ContextualFeedbackSeverity::OK`** — flash message severity enum
- **`FlashMessage` + `FlashMessageService`** — explicit flash message creation and enqueuing
- **`$request->getAttribute('frontend.page.information')->getId()`** — get page ID in frontend context (replaces `$GLOBALS['TSFE']->id`)
- **System Resource API** — `SystemResourceFactory::createPublicResource()` + `SystemResourcePublisherInterface::generateUri()` for `EXT:` asset URIs (replaces deprecated `PathUtility::getPublicResourceWebPath()`)
- **Site set** — static TypoScript ships under `Configuration/Sets/OkPriveConsent/` (v14; `ExtensionManagementUtility::addStaticFile()` removed)
- **`$GLOBALS['LANG']`** — LanguageService for backend label resolution
- **`Connection::PARAM_INT`** — TYPO3 connection constants

## JavaScript

Backend JS uses ES6 module format. The dirty-check module is at `Resources/Public/JavaScript/backend/form-dirty-check.js` and is loaded via `loadJavaScriptModule('@oliverkroener/ok-prive-consent/backend/form-dirty-check.js')`.

`form-dirty-check.js` integrates with TYPO3's `ConsumerScope` to intercept page tree clicks and module navigation, showing an unsaved-changes confirmation modal (with "save and close" support via fetch-based AJAX form submission).

## Git Commit Convention

Prefix commits with a tag: `[TASK]` for features/refactoring, `[BUGFIX]` for fixes, `[VERSION]` for version bumps.
