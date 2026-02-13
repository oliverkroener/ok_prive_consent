# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

TYPO3 CMS extension (`ok_prive_cookie_consent`) providing a backend module for managing Prive Cookie Consent banner scripts. Administrators edit JavaScript snippets via a Monaco Editor in the TYPO3 backend; the scripts are stored in the `sys_template` table and rendered on the frontend via TypoScript.

- **TYPO3 compatibility:** 12.4 – 14.x (14 via `@dev`)
- **PHP namespace:** `OliverKroener\OkPriveCookieConsent\`
- **Extension key:** `ok_prive_cookie_consent`
- **External dependencies:** none (only `typo3/cms-core`)

## Build Commands

There are no build, lint, or test commands configured. The `design/` directory (formerly used for Vite-based frontend builds) has been removed; CSS/JS assets in `Resources/Public/` are maintained directly.

## Architecture

### Request Flow

```
TYPO3 Backend → ConsentController → DatabaseService → sys_template table
                                         ↑
                                    SiteFinder (TYPO3 core)
```

- **`ConsentController`** – Extbase controller (`#[AsController]`) with `indexAction` (load form), `saveAction` (persist script), `errorAction` (no site root found). Uses `ModuleTemplateFactory` and `SiteFinder` for rendering.
- **`DatabaseService`** – Queries/updates the custom `tx_ok_prive_cookie_consent_banner_script` field on `sys_template`. Also exposes `renderBannerScript()` as a TypoScript USER function for frontend output. Uses `SiteFinder` to resolve the site root page.

### Frontend Rendering

TypoScript in `Configuration/TypoScript/setup.typoscript` defines `lib.priveScript` (USER object calling `DatabaseService->renderBannerScript`), inserts the cookie button HTML and banner script via `page.footerData` (keyed by `crc32('ok_prive_cookie_consent')` to avoid collisions), and includes CSS via `page.includeCSS`.

### Dependency Injection

Configured in `Configuration/Services.yaml` (Symfony DI). Autowiring is enabled; `DatabaseService` is marked public (needed for TypoScript USER calls).

### Templates

Fluid templates in `Resources/Private/Templates/Consent/`. Templates are rendered via `ModuleTemplate::renderResponse()` (no custom Fluid layout). CSS/JS assets are loaded via `<f:asset.css>` and `<f:asset.script>` ViewHelpers. Localizations in XLIFF format (`locallang.xlf` for English, `de.locallang.xlf` for German).

### Frontend Design

Backend JS/CSS assets live in `Resources/Public/`. Monaco Editor is loaded as a TYPO3 backend JavaScript module. Brand colors: primary `#f05722`, secondary `#0fa8dd`.

## TYPO3 Cross-Version Notes

Code targets TYPO3 12, 13, and 14 simultaneously. Key API choices:

- **No `$this->view`** — use `ModuleTemplateFactory` + `$moduleTemplate->renderResponse()` (12+)
- **No `GeneralUtility::_GP()`** — read from PSR-7 request (`getParsedBody`/`getQueryParams`) (removed in 13)
- **No `$GLOBALS['TSFE']`** — use `$GLOBALS['TYPO3_REQUEST']->getAttribute('routing')` (removed in 14)
- **No `\PDO::PARAM_INT`** — use `Connection::PARAM_INT` (removed in 13)
- **No `QueryBuilder->execute()`** — use `executeQuery()` / `executeStatement()` (removed in 13)
- **`#[AsController]`** attribute on backend controllers (available from 12.1, expected in 14)
- **`footerData` keys** — use high numeric keys derived from `crc32()` of extension key to avoid collisions

## Git Commit Convention

Prefix commits with a tag: `[TASK]` for features/refactoring, `[BUGFIX]` for fixes, `[VERSION]` for version bumps.
