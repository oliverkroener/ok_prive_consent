# Prive Consent — TYPO3 Extension

TYPO3 backend module for managing [Prive Cookie Consent](https://www.prive.eu/) banner scripts.

|                  |                                      |
|------------------|--------------------------------------|
| Extension key    | `ok_prive_consent`                   |
| Composer package | `oliverkroener/ok-prive-consent`     |
| TYPO3            | 10.4 LTS                             |
| PHP              | >= 7.4                               |
| Version          | 2.0.0                                |
| Author           | [Oliver Kroener](https://www.oliver-kroener.de) |
| License          | GPL-2.0-or-later                     |

## Features

- **Backend module** under *Web > Prive Consent* for editing consent scripts
- **Enable/disable toggle** to activate or deactivate the banner without removing the script
- **Multi-site support** — automatically resolves the correct site root per TYPO3 site configuration
- **Unsaved changes protection** — warns before navigating away with unsaved modifications
- **Automatic frontend rendering** — script and cookie settings button injected via TypoScript `page.footerData`

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

## Documentation

Full documentation is available in the `Documentation/` directory. Generate rendered docs locally with:

```bash
make docs
```

This uses the official [TYPO3 Documentation rendering container](https://github.com/TYPO3-Documentation/render-guides).

## Support

- TYPO3 Slack: https://typo3.org/community/meet/chat-slack
- TYPO3 Forum: https://talk.typo3.org/c/typo3-questions/19
- Author contact: https://www.oliver-kroener.de
- Issues: https://github.com/oliverkroener/ok-prive-consent/issues
