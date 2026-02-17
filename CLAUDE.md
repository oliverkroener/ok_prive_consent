# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

TYPO3 CMS extension (`ok_prive_consent`) providing a backend module for managing Prive Cookie Consent banner scripts. Administrators edit JavaScript snippets in the TYPO3 backend; the scripts are stored in the `sys_template` table and rendered on the frontend via TypoScript.

- **TYPO3 compatibility:** 12.4 LTS, 13.4 LTS
- **PHP:** >= 8.1
- **PHP namespace:** `OliverKroener\OkPriveConsent\`
- **Extension key:** `ok_prive_consent`
- **Composer package:** `oliverkroener/ok-prive-consent`
- **Version:** 3.1.0
- **External dependencies:** `typo3/cms-core`, `typo3/cms-backend`

## Build Commands

There are no build, lint, or test commands configured. CSS/JS assets in `Resources/Public/` are maintained directly.

Documentation can be generated via `make docs` (requires Docker).

## Architecture

### Request Flow

```
TYPO3 Backend → ConsentController (PSR-7) → sys_template table
                      ↑
                 ModuleTemplateFactory, SiteFinder, ConnectionPool (TYPO3 core)
```

- **`ConsentController`** (`Classes/Controller/Backend/ConsentController.php`) – PSR-7 controller with `#[AsController]` attribute. Uses `ModuleTemplateFactory` for rendering, `UriBuilder` for routing, and `PageRenderer` for JS module loading. Actions: `indexAction` (load form with no-page/no-site/edit states), `saveAction` (persist script + flush page cache).
- **`DatabaseService`** (`Classes/Service/DatabaseService.php`) – Renders the banner script for frontend output as a TypoScript USER function. Uses `ContentObjectRenderer` to resolve the current page ID via routing attribute.

### Frontend Rendering

TypoScript in `Configuration/TypoScript/setup.typoscript` defines `lib.priveScript` (USER object calling `DatabaseService->renderBannerScript`), inserts the cookie button HTML and banner script via `page.footerData` (keyed by `crc32('ok_prive_cookie_consent')` to avoid collisions).

The banner script is only rendered when the `tx_ok_prive_cookie_consent_banner_enabled` flag is set.

### Dependency Injection

Configured in `Configuration/Services.yaml` (Symfony DI). Autowiring is enabled; `DatabaseService` is marked public (needed for TypoScript USER calls).

### Module Registration

- Backend module registered declaratively in `Configuration/Backend/Modules.php` with page tree navigation component and route definitions.
- Module icon registered declaratively in `Configuration/Icons.php`.
- ES6 JavaScript modules mapped in `Configuration/JavaScriptModules.php` under `@oliverkroener/ok-prive-consent/`.

### Templates

Fluid template at `Resources/Private/Templates/Backend/Consent/Index.html`. Uses `<f:layout name="Module" />` provided by `ModuleTemplateFactory`. Template handles three states: no page selected, no site found, and edit form. Localizations in XLIFF format (`locallang.xlf` for English, `de.locallang.xlf` for German).

### Database Fields

Both custom fields live on the `sys_template` table (added via TCA override in `Configuration/TCA/Overrides/sys_template.php`):

- `tx_ok_prive_cookie_consent_banner_script` — the JavaScript snippet
- `tx_ok_prive_cookie_consent_banner_enabled` — boolean toggle

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
- **`ContentObjectRenderer::getRequest()->getAttribute('routing')->getPageId()`** — get page ID in frontend context (replaces `$GLOBALS['TSFE']->id`)
- **`$GLOBALS['LANG']`** — LanguageService for backend label resolution
- **`Connection::PARAM_INT`** — TYPO3 connection constants
- **`footerData` keys** — use high numeric keys derived from `crc32()` of extension key to avoid collisions

## JavaScript

Backend JS uses ES6 module format. The dirty-check module is at `Resources/Public/JavaScript/backend/form-dirty-check.js` and is loaded via `loadJavaScriptModule('@oliverkroener/ok-prive-consent/backend/form-dirty-check.js')`.

`form-dirty-check.js` integrates with TYPO3's `ConsumerScope` to intercept page tree clicks and module navigation, showing an unsaved-changes confirmation modal (with "save and close" support via fetch-based AJAX form submission).

## Git Commit Convention

Prefix commits with a tag: `[TASK]` for features/refactoring, `[BUGFIX]` for fixes, `[VERSION]` for version bumps.
