# Prive Consent (`ok_prive_consent`)

[![TYPO3 14](https://img.shields.io/badge/TYPO3-14-orange?logo=typo3)](https://get.typo3.org/version/14)
[![PHP 8.2+](https://img.shields.io/badge/PHP-8.2%2B-777BB4?logo=php&logoColor=white)](https://www.php.net/)
[![License: GPL v2+](https://img.shields.io/badge/License-GPL%20v2%2B-blue)](https://www.gnu.org/licenses/old-licenses/gpl-2.0.html)
[![Version](https://img.shields.io/badge/version-5.0.0-green)](https://github.com/oliverkroener/ok-prive-consent)

TYPO3 backend module for managing [Prive Cookie Consent](https://www.prive.eu/) banner scripts.

## Features

- **Backend module** under *Web > Prive Consent* for editing consent scripts
- **Enable/disable toggle** to activate or deactivate the banner without removing the script
- **Multi-site support** -- automatically resolves the correct site root per TYPO3 site configuration
- **Unsaved changes protection** -- warns before navigating away with unsaved modifications (with "save and close" support)
- **Automatic frontend rendering** -- script and cookie settings button injected before `</body>` by a PSR-14 event listener (no TypoScript or template changes required)
- **Cache flush on save** -- frontend page cache is cleared automatically after saving
- **Cookie settings button** -- fixed-position floating button with SVG cookie icon for visitors to reopen consent dialog

## Requirements

- TYPO3 14.3 LTS
- PHP >= 8.2 (8.2–8.5)

## Installation

### Composer (recommended)

```bash
composer require oliverkroener/ok-prive-consent
```

### Extension Manager

Download or upload the extension and activate it via *Admin Tools > Extensions*.

### No site set required

The consent banner renders automatically on every frontend page once the extension is
installed -- it is injected by a PSR-14 event listener registered through the extension's
`Services.yaml`. **No site set, TypoScript, or template change is needed.**

A site set named `oliverkroener/ok-prive-consent` is still shipped (so existing site
configurations that reference it under `dependencies` keep working), but it is now a
no-op and entirely optional.

## Usage

1. Navigate to **Web > Prive Consent** in the TYPO3 backend
2. Select a page in the page tree (the module resolves the site root automatically)
3. Toggle **Enable Prive script** to activate/deactivate the banner
4. Paste the JavaScript snippet from your [Prive](https://www.prive.eu/) dashboard
5. Click **Save**

The consent script and a cookie settings button are rendered automatically in the page footer on all frontend pages.

## Configuration

The extension works out of the box once installed -- the banner is injected by the event
listener with no further configuration. To customise the cookie settings button, override
the styles from `Resources/Public/Css/prive-cookie-button.css` in your site package.

Brand colours: primary `#f05722`, secondary `#0fa8dd`.

## Architecture

```
TYPO3 Backend --> ConsentController --> sys_template table
                       |
              ModuleTemplateFactory,
              SiteFinder, ConnectionPool
                    (TYPO3 core)

Frontend --> AfterCacheableContentIsGeneratedEvent
                  |
             InjectBannerScript (listener) --> DatabaseService --> sys_template table
                  |                                  |
          inject before </body>              SiteFinder (TYPO3 core)
```

| Component | Path | Description |
|-----------|------|-------------|
| `ConsentController` | `Classes/Controller/Backend/` | PSR-7 controller (`#[AsController]`) with `indexAction` and `saveAction` |
| `InjectBannerScript` | `Classes/EventListener/` | PSR-14 listener on `AfterCacheableContentIsGeneratedEvent`; splices the banner markup before `</body>` |
| `DatabaseService` | `Classes/Service/` | Resolves the site and builds the banner markup via `getBannerMarkup(ServerRequestInterface)` |
| Module registration | `Configuration/Backend/Modules.php` | Declarative backend module under Web menu with page tree navigation |
| Icon registration | `Configuration/Icons.php` | SVG module icon via `SvgIconProvider` |
| JavaScript modules | `Configuration/JavaScriptModules.php` | ES6 module mapping for `@oliverkroener/ok-prive-consent/` |
| Dependency injection | `Configuration/Services.yaml` | Autowiring + autoconfigure (the event listener is auto-registered via `#[AsEventListener]`) |
| Site set | `Configuration/Sets/OkPriveConsent/` | `config.yaml` + no-op `setup.typoscript`; kept only so existing site-config references stay valid |
| Fluid templates | `Resources/Private/Templates/Backend/Consent/` | `Index.html` -- form with three states (no page, no site, edit) |
| FormDirtyCheck | `Resources/Public/JavaScript/backend/` | ES6 module for unsaved changes detection with ConsumerScope integration |
| Localisation | `Resources/Private/Language/` | English (`locallang.xlf`) and German (`de.locallang.xlf`) |

### Database fields (on `sys_template`)

| Field | Type | Description |
|-------|------|-------------|
| `tx_ok_prive_cookie_consent_banner_script` | text | The JavaScript consent snippet |
| `tx_ok_prive_cookie_consent_banner_enabled` | boolean | Enable/disable toggle |

The fields are declared in `ext_tables.sql` and read/written exclusively via QueryBuilder
from the backend module (there is **no** TCA definition). Field names retain the original
`ok_prive_cookie_consent` prefix for backward compatibility with existing data.

### Frontend rendering order

The listener injects the markup before `</body>` in this order to ensure correct DOM timing:

1. **CSS** -- cookie button stylesheet loaded via `<link>` tag
2. **Cookie button** -- `<a>` element with `data-cc="c-settings"` attribute
3. **Prive script** -- the consent JavaScript snippet (so it can bind to the button already in DOM)

Injecting via a PSR-14 event listener (rather than a TypoScript `page.footerData` USER object)
makes rendering independent of site-set load order: a theme set that re-declares `page = PAGE`
after this extension can no longer discard the banner. The markup is part of the cacheable page
content, so it is stored in the page cache.

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
