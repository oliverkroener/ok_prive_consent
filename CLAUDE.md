# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

TYPO3 CMS extension (`ok_prive_consent`) providing a backend module for managing Prive Cookie Consent banner scripts. Administrators edit JavaScript snippets in the TYPO3 backend; the scripts are stored in the `sys_template` table and rendered on the frontend via TypoScript.

- **TYPO3 compatibility:** 10.4 LTS
- **PHP:** >= 7.4
- **PHP namespace:** `OliverKroener\OkPriveConsent\`
- **Extension key:** `ok_prive_consent`
- **Composer package:** `oliverkroener/ok-prive-consent`
- **Version:** 2.0.0
- **External dependencies:** none (only `typo3/cms-core`)

## Build Commands

There are no build, lint, or test commands configured. CSS/JS assets in `Resources/Public/` are maintained directly.

Documentation can be generated via `make docs` (requires Docker).

## Architecture

### Request Flow

```
TYPO3 Backend → ConsentController → DatabaseService → sys_template table
                                         ↑
                                    SiteFinder (TYPO3 core)
```

- **`ConsentController`** – Extbase controller with `indexAction` (load form), `saveAction` (persist script), `errorAction` (no site root found). Actions return `void` (TYPO3 10 pattern). Uses `$this->view->assignMultiple()` for rendering.
- **`DatabaseService`** – Queries/updates the custom `tx_ok_prive_cookie_consent_banner_script` and `tx_ok_prive_cookie_consent_banner_enabled` fields on `sys_template`. Also exposes `renderBannerScript()` as a TypoScript USER function for frontend output. Uses `SiteFinder` to resolve the site root page.

### Frontend Rendering

TypoScript in `Configuration/TypoScript/setup.typoscript` defines `lib.priveScript` (USER object calling `DatabaseService->renderBannerScript`) and a `page.footerData` USER object (keyed by `crc32('ok_prive_cookie_consent')` to avoid collisions) that calls the same method. CSS is included via `page.includeCSS`.

The `renderBannerScript()` method controls all frontend output: when the `tx_ok_prive_cookie_consent_banner_enabled` flag is set, it renders both the cookie settings button HTML and the banner script. When disabled, nothing is rendered (no button, no script).

### Dependency Injection

Configured in `Configuration/Services.yaml` (Symfony DI). Autowiring is enabled; `DatabaseService` is marked public (needed for TypoScript USER calls).

### Module Registration

- Backend module registered in `ext_tables.php` via `ExtensionUtility::registerModule()` with page tree navigation component.
- Module icon registered in `ext_localconf.php` via `IconRegistry::registerIcon()`.

### Templates

Fluid templates in `Resources/Private/Templates/Consent/` (`Index.html`, `Error.html`). Templates use `<f:layout name="Module" />` with the layout at `Resources/Private/Layouts/Module.html` (renders via `<be:moduleLayout>`). Localizations in XLIFF format (`locallang.xlf` for English, `de.locallang.xlf` for German).

### Database Fields

Both custom fields live on the `sys_template` table (added via TCA override in `Configuration/TCA/Overrides/sys_template.php`):

- `tx_ok_prive_cookie_consent_banner_script` — the JavaScript snippet
- `tx_ok_prive_cookie_consent_banner_enabled` — boolean toggle

Note: field names retain the original `ok_prive_cookie_consent` prefix for backward compatibility with existing data.

### Frontend Assets

Backend JS/CSS assets live in `Resources/Public/`. Brand colors: primary `#f05722`, secondary `#0fa8dd`.

## TYPO3 10.4 API Patterns

- **Guard constant:** `defined('TYPO3_MODE') || die();` in `ext_localconf.php`, `ext_tables.php`, and TCA overrides (TYPO3 11+ uses `defined('TYPO3')`)
- **`ext_localconf.php` / `ext_tables.php`:** Never use `declare(strict_types=1)` (files are concatenated into cache). Use FQCNs, not `use` statements.
- **`$this->view`** — use Extbase's built-in view (`$this->view->assignMultiple()`); actions return `void` (not `ResponseInterface`)
- **`$GLOBALS['TSFE']->id`** — use TSFE to get the current page ID in frontend context
- **`$GLOBALS['LANG']`** — LanguageService for backend label resolution
- **`Connection::PARAM_INT`** — TYPO3 connection constants (not `\PDO::PARAM_INT`)
- **Doctrine DBAL:** `execute()`, `fetch()`, `fetchAll()`, `fetchColumn()` (v11 uses `executeQuery()`, `fetchAssociative()`, etc.)
- **`loadRequireJsModule()`** — RequireJS/AMD module loading
- **`ExtensionUtility::registerModule()`** — backend module registration in `ext_tables.php`
- **`IconRegistry::registerIcon()`** — icon registration in `ext_localconf.php`
- **`AbstractMessage::OK`** — flash message severity constants (not `ContextualFeedbackSeverity`)
- **`footerData` keys** — use high numeric keys derived from `crc32()` of extension key to avoid collisions

## JavaScript

Backend JS uses AMD/RequireJS format (`define([...], function (...) { ... })`). The dirty-check module is at `Resources/Public/JavaScript/Backend/FormDirtyCheck.js` and is loaded via `loadRequireJsModule('TYPO3/CMS/OkPriveConsent/Backend/FormDirtyCheck')`.

`FormDirtyCheck.js` integrates with TYPO3's `ConsumerScope` to intercept page tree clicks and module navigation, showing an unsaved-changes confirmation modal (with "save and close" support via AJAX form submission).

## Git Commit Convention

Prefix commits with a tag: `[TASK]` for features/refactoring, `[BUGFIX]` for fixes, `[VERSION]` for version bumps.
