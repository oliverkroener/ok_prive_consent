# Prive Consent (`ok_prive_consent`)

[![TYPO3 12](https://img.shields.io/badge/TYPO3-12-orange?logo=typo3)](https://get.typo3.org/version/12)
[![TYPO3 13](https://img.shields.io/badge/TYPO3-13-orange?logo=typo3)](https://get.typo3.org/version/13)
[![PHP 8.1+](https://img.shields.io/badge/PHP-8.1%2B-777BB4?logo=php&logoColor=white)](https://www.php.net/)
[![License: GPL v2+](https://img.shields.io/badge/License-GPL%20v2%2B-blue)](https://www.gnu.org/licenses/old-licenses/gpl-2.0.html)
[![Version](https://img.shields.io/badge/version-4.1.1-green)](https://github.com/oliverkroener/ok-prive-consent)

TYPO3 backend module for managing [Prive Cookie Consent](https://www.prive.eu/) banner scripts.

## Features

- **Backend module** under *Web > Prive Consent* for editing consent scripts
- **Enable/disable toggle** to activate or deactivate the banner without removing the script
- **Multi-site support** -- automatically resolves the correct site root per TYPO3 site configuration
- **Unsaved changes protection** -- warns before navigating away with unsaved modifications (with "save and close" support)
- **Automatic frontend rendering** -- script and cookie settings button injected via TypoScript `page.footerData`
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

### Include static TypoScript

1. Open the **Template** module in the TYPO3 backend
2. Select the root page of your site
3. Edit the template record (*Info/Modify > Edit the whole template record*)
4. Under the **Includes** tab, add **[kroener.DIGITAL] Prive Consent**
5. Clear all caches

## Usage

1. Navigate to **Web > Prive Consent** in the TYPO3 backend
2. Select a page in the page tree (the module resolves the site root automatically)
3. Toggle **Enable Prive script** to activate/deactivate the banner
4. Paste the JavaScript snippet from your [Prive](https://www.prive.eu/) dashboard
5. Click **Save**

The consent script and a cookie settings button are rendered automatically in the page footer on all frontend pages.

## Configuration

The extension works out of the box after including the static TypoScript template. To customise the cookie settings button, override the styles from `Resources/Public/Css/prive-cookie-button.css` in your site package.

Brand colours: primary `#f05722`, secondary `#0fa8dd`.

## Architecture

```
TYPO3 Backend --> ConsentController --> sys_template table
                       |
              ModuleTemplateFactory,
              SiteFinder, ConnectionPool
                    (TYPO3 core)

Frontend --> TypoScript USER object --> DatabaseService --> sys_template table
                                              |
                                         SiteFinder (TYPO3 core)
```

| Component | Path | Description |
|-----------|------|-------------|
| `ConsentController` | `Classes/Controller/Backend/` | PSR-7 controller (`#[AsController]`) with `indexAction` and `saveAction` |
| `DatabaseService` | `Classes/Service/` | Renders banner script for frontend output as a TypoScript USER function |
| Module registration | `Configuration/Backend/Modules.php` | Declarative backend module under Web menu with page tree navigation |
| Icon registration | `Configuration/Icons.php` | SVG module icon via `SvgIconProvider` |
| JavaScript modules | `Configuration/JavaScriptModules.php` | ES6 module mapping for `@oliverkroener/ok-prive-consent/` |
| Dependency injection | `Configuration/Services.yaml` | Autowiring enabled; `DatabaseService` marked public for TypoScript USER |
| TCA override | `Configuration/TCA/Overrides/sys_template.php` | Registers static TypoScript template |
| TypoScript | `Configuration/TypoScript/setup.typoscript` | Defines `lib.priveScript` USER object and `page.footerData` |
| Fluid templates | `Resources/Private/Templates/Backend/Consent/` | `Index.html` -- form with three states (no page, no site, edit) |
| FormDirtyCheck | `Resources/Public/JavaScript/backend/` | ES6 module for unsaved changes detection with ConsumerScope integration |
| Localisation | `Resources/Private/Language/` | English (`locallang.xlf`) and German (`de.locallang.xlf`) |

### Database fields (on `sys_template`)

| Field | Type | Description |
|-------|------|-------------|
| `tx_ok_prive_cookie_consent_banner_script` | text | The JavaScript consent snippet |
| `tx_ok_prive_cookie_consent_banner_enabled` | boolean | Enable/disable toggle |

### Frontend rendering order

The `page.footerData` output follows this order to ensure correct DOM timing:

1. **CSS** -- cookie button stylesheet loaded via `<link>` tag
2. **Cookie button** -- `<a>` element with `data-cc="c-settings"` attribute
3. **Prive script** -- the consent JavaScript snippet (so it can bind to the button already in DOM)

## Documentation

Full documentation is available in the `Documentation/` directory. Generate rendered docs locally with:

```bash
make docs
```

This uses the official [TYPO3 Documentation rendering container](https://github.com/TYPO3-Documentation/render-guides).

## License

GPL-2.0-or-later

## Author

**Oliver Kroener** -- [oliver-kroener.de](https://www.oliver-kroener.de) -- [ok@oliver-kroener.de](mailto:ok@oliver-kroener.de)
