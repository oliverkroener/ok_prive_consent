# Prive Consent (`ok_prive_consent`)

[![TYPO3 12](https://img.shields.io/badge/TYPO3-12-orange?logo=typo3)](https://get.typo3.org/version/12)
[![TYPO3 13](https://img.shields.io/badge/TYPO3-13-orange?logo=typo3)](https://get.typo3.org/version/13)
[![PHP 8.1+](https://img.shields.io/badge/PHP-8.1%2B-777BB4?logo=php&logoColor=white)](https://www.php.net/)
[![License: GPL v2+](https://img.shields.io/badge/License-GPL%20v2%2B-blue)](https://www.gnu.org/licenses/old-licenses/gpl-2.0.html)
[![Version](https://img.shields.io/badge/version-4.2.0-green)](https://github.com/oliverkroener/ok_prive_consent)

TYPO3 backend module for managing [Prive Cookie Consent](https://www.prive.eu/) banner scripts.

## Features

- **Backend module** under *Web > Prive Consent* for editing consent scripts
- **Enable/disable toggle** to activate or deactivate the banner without removing the script
- **Multi-site support** -- automatically resolves the correct site root per TYPO3 site configuration
- **Unsaved changes protection** -- warns before navigating away with unsaved modifications (with "save and close" support)
- **Automatic frontend rendering** -- script and cookie settings button injected before `</body>` by a PSR-14 event listener, no TypoScript required
- **Site set** (TYPO3 13.1+) -- selectable under *Site Management > Sites*; optional, the banner works without it
- **Cache flush on save** -- frontend page cache is cleared automatically after saving
- **Cookie settings button** -- fixed-position floating button with SVG cookie icon for visitors to reopen consent dialog

## Requirements

- TYPO3 12.4 LTS or 13.4 LTS
- PHP >= 8.1

## Installation

### Composer (recommended)

```bash
composer req oliverkroener/ok-prive-consent
```

### Extension Manager

Download or upload the extension and activate it via *Admin Tools > Extensions*.

### Set up

Nothing to include. Rendering is handled by the PSR-14 event listener
`InjectBannerScript`, which is registered through the extension's `Services.yaml`.
Just clear all caches after installation.

Optionally, on TYPO3 13.1+, add the site set **[kroener.DIGITAL] Prive Consent**
(`oliverkroener/ok-prive-consent`) to your site configuration. The set is a no-op and
purely cosmetic -- see [Site set](#site-set).

### Upgrading from 4.1.x

The banner settings moved from the `sys_template` record to the site root's `pages`
record. After updating:

1. Run the database analyser (*Admin Tools > Maintenance > Analyze Database Structure*)
2. Run the upgrade wizard *"EXT:ok_prive_consent: Move consent banner settings from
   sys_template to pages"* (*Admin Tools > Upgrade > Upgrade Wizard*)

The static TypoScript template **[kroener.DIGITAL] Prive Consent** is now empty. It is
still shipped so existing includes do not break, but it no longer does anything and can
be removed from your template record.

## Usage

1. Navigate to **Web > Prive Consent** in the TYPO3 backend
2. Select a page in the page tree (the module resolves the site root automatically)
3. Toggle **Enable Prive script** to activate/deactivate the banner
4. Paste the JavaScript snippet from your [Prive](https://www.prive.eu/) dashboard
5. Click **Save**

The consent script and a cookie settings button are rendered automatically in the page footer on all frontend pages.

## Configuration

The extension works out of the box -- no TypoScript, site set or Fluid template needs to
be configured. To customise the cookie settings button, override the styles from
`Resources/Public/Css/prive-cookie-button.css` in your site package.

Brand colours: primary `#f05722`, secondary `#0fa8dd`.

## Architecture

```
TYPO3 Backend --> ConsentController --> pages (site root)
                       |
              ModuleTemplateFactory,
              SiteFinder, ConnectionPool
                    (TYPO3 core)

Frontend --> AfterCacheableContentIsGeneratedEvent
                 --> InjectBannerScript --> DatabaseService --> pages (site root)
                                                    |
                                             SiteFinder (TYPO3 core)
```

| Component | Path | Description |
|-----------|------|-------------|
| `ConsentController` | `Classes/Controller/Backend/` | PSR-7 controller (`#[AsController]`) with `indexAction` and `saveAction` |
| `InjectBannerScript` | `Classes/EventListener/` | PSR-14 listener on `AfterCacheableContentIsGeneratedEvent`, splices the markup in before `</body>` |
| `DatabaseService` | `Classes/Service/` | Builds the banner markup for a request (`getBannerMarkup()`) |
| `MigrateConsentStorageToPagesUpgradeWizard` | `Classes/Updates/` | Copies settings from `sys_template` to the site root page |
| Module registration | `Configuration/Backend/Modules.php` | Declarative backend module under Web menu with page tree navigation |
| Icon registration | `Configuration/Icons.php` | SVG module icon via `SvgIconProvider` |
| JavaScript modules | `Configuration/JavaScriptModules.php` | ES6 module mapping for `@oliverkroener/ok-prive-consent/` |
| Dependency injection | `Configuration/Services.yaml` | Autowiring enabled; registers the event listener via the `event.listener` tag |
| Site set | `Configuration/Sets/OkPriveConsent/` | Empty placeholder set (TYPO3 13.1+); ignored on TYPO3 12 |
| TCA override | `Configuration/TCA/Overrides/sys_template.php` | Registers the (now empty) static TypoScript template |
| TypoScript | `Configuration/TypoScript/setup.typoscript` | Comments only -- kept so existing includes do not break |
| Fluid templates | `Resources/Private/Templates/Backend/Consent/` | `Index.html` -- form with three states (no page, no site, edit) |
| FormDirtyCheck | `Resources/Public/JavaScript/backend/` | ES6 module for unsaved changes detection with ConsumerScope integration |
| Localisation | `Resources/Private/Language/` | English (`locallang.xlf`) and German (`de.locallang.xlf`) |

### Site set

`Configuration/Sets/OkPriveConsent/` ships a site set named
`oliverkroener/ok-prive-consent`. Site sets require **TYPO3 13.1 or newer**; on TYPO3 12
the directory is ignored by the core and has no effect.

The set is intentionally empty -- no dependencies, no TypoScript, no settings -- because
the banner is injected in PHP. Injecting via an event listener keeps rendering
independent of site-set load order: a theme set re-declaring `page = PAGE` can no longer
discard the banner. Adding the set to a site is optional and changes nothing.

### Database fields (on `pages`, site root record)

| Field | Type | Description |
|-------|------|-------------|
| `tx_ok_prive_cookie_consent_banner_script` | text | The JavaScript consent snippet |
| `tx_ok_prive_cookie_consent_banner_enabled` | boolean | Enable/disable toggle |

### Frontend rendering order

The injected markup follows this order to ensure correct DOM timing:

1. **CSS** -- cookie button stylesheet loaded via `<link>` tag
2. **Cookie button** -- `<a>` element with `data-cc="c-settings"` attribute
3. **Prive script** -- the consent JavaScript snippet (so it can bind to the button already in DOM)

The markup is spliced in immediately before the closing `</body>` tag while the content is
still cacheable, so it becomes part of the page cache.

## Documentation

Full documentation is available in the `Documentation/` directory. Generate rendered docs locally with:

```bash
make docs
```

This uses the official [TYPO3 Documentation rendering container](https://github.com/TYPO3-Documentation/render-guides).

## License

GPL-2.0-or-later

## Author — Oliver Kroener

### Automated. Scaled. Done.

Web3 · Cloud · Automation

Technology is only valuable when it solves a real problem. For over 30 years I've been translating between business and tech — so your investment in digitalisation doesn't stall at proof-of-concept but delivers measurable results.

- Website: [oliver-kroener.de](https://www.oliver-kroener.de)
- Web3: [web3.oliver-kroener.de](https://web3.oliver-kroener.de/)
- Email: [ok@oliver-kroener.de](mailto:ok@oliver-kroener.de)
- Web3 Email: [oliverkroener@ethermail.io](mailto:oliverkroener@ethermail.io)
