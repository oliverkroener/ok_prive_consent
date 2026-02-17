# Prive Consent (`ok_prive_consent`)

[![TYPO3 10](https://img.shields.io/badge/TYPO3-10-orange?logo=typo3)](https://get.typo3.org/version/10)
[![PHP 7.2+](https://img.shields.io/badge/PHP-7.2%2B-777BB4?logo=php&logoColor=white)](https://www.php.net/)
[![License: GPL v2+](https://img.shields.io/badge/License-GPL%20v2%2B-blue)](https://www.gnu.org/licenses/old-licenses/gpl-2.0.html)
[![Version](https://img.shields.io/badge/version-2.1.0-green)](https://github.com/oliverkroener/ok-prive-consent)

TYPO3 backend module for managing [Prive Cookie Consent](https://www.prive.eu/) banner scripts.

## Features

- **Backend module** under *Web > Prive Consent* for editing consent scripts
- **Enable/disable toggle** to activate or deactivate the banner without removing the script
- **Multi-site support** — automatically resolves the correct site root per TYPO3 site configuration
- **Unsaved changes protection** — warns before navigating away with unsaved modifications (with "save and close" support)
- **Automatic frontend rendering** — script and cookie settings button injected via TypoScript `page.footerData`
- **Cache flush on save** — frontend page cache is cleared automatically after saving

## Requirements

- TYPO3 10.4 LTS
- PHP >= 7.2

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
TYPO3 Backend → ConsentController → DatabaseService → sys_template table
                                         ↑
                                    SiteFinder (TYPO3 core)
```

| Component | Path | Description |
|-----------|------|-------------|
| `ConsentController` | `Classes/Controller/` | Extbase controller with `index`, `save`, `error` actions |
| `DatabaseService` | `Classes/Service/` | Reads/writes consent fields on `sys_template`; renders banner script via TypoScript USER |
| TCA override | `Configuration/TCA/Overrides/sys_template.php` | Registers static TypoScript template |
| TypoScript | `Configuration/TypoScript/setup.typoscript` | Defines `lib.priveScript` USER object and `page.footerData` |
| Fluid templates | `Resources/Private/Templates/Consent/` | `Index.html` (form), `Error.html` (no site root) |
| FormDirtyCheck | `Resources/Public/JavaScript/Backend/` | AMD/RequireJS module for unsaved changes detection |

### Database fields (on `sys_template`)

| Field | Type | Description |
|-------|------|-------------|
| `tx_ok_prive_cookie_consent_banner_script` | text | The JavaScript consent snippet |
| `tx_ok_prive_cookie_consent_banner_enabled` | boolean | Enable/disable toggle |

## Documentation

Full documentation is available in the `Documentation/` directory. Generate rendered docs locally with:

```bash
make docs
```

This uses the official [TYPO3 Documentation rendering container](https://github.com/TYPO3-Documentation/render-guides).

## License

GPL-2.0-or-later

## Author

**Oliver Kroener** — [oliver-kroener.de](https://www.oliver-kroener.de) — [ok@oliver-kroener.de](mailto:ok@oliver-kroener.de)
