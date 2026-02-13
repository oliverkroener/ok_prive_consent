# Prive Consent for TYPO3

A TYPO3 backend module for managing [Prive Consent](https://www.prive.eu/) banner scripts. Edit, enable, and deploy consent scripts directly from the TYPO3 backend — no file access or deployment pipeline required.

## Features

- **Backend module** — manage consent scripts through a dedicated TYPO3 module under *Web > Prive Consent*
- **Enable/disable toggle** — activate or deactivate the consent banner without removing the script
- **Multi-site support** — automatically resolves the correct site root for TYPO3 multi-site setups
- **Unsaved changes protection** — warns before navigating away with unsaved edits
- **Automatic frontend rendering** — scripts are injected into the page footer via TypoScript
- **Cookie settings button** — a floating button allowing visitors to re-open the consent dialog
- **Localized** — English and German translations included

## Requirements

| Component | Version                      |
|-----------|------------------------------|
| TYPO3     | 12.4 &ndash; 14.x           |
| PHP       | as required by your TYPO3 version |

No external PHP dependencies beyond `typo3/cms-core`.

## Installation

### Composer (recommended)

```bash
composer require oliverkroener/ok-prive-consent
```

### TYPO3 Extension Manager

Download or upload the extension and activate it via *Admin Tools > Extensions*.

After installation, include the static TypoScript template:

1. Go to *Web > Template* and select your site root page
2. Open *Info/Modify* and click *Edit the whole template record*
3. Under the *Includes* tab, add **[kroener.DIGITAL] Prive Consent** to the list of static templates

## Usage

1. Navigate to **Web > Prive Consent** in the TYPO3 backend
2. The module displays the detected site identifier and root page
3. Toggle **Enable Prive script** to activate or deactivate the consent banner
4. Paste the JavaScript snippet provided by Prive into the script field
5. Click **Save**

The script is rendered automatically in the page footer on every frontend page where the static TypoScript template is included.

## How It Works

The extension stores the consent script in a custom field on the `sys_template` record of the site root page. On frontend rendering, a TypoScript `USER` object calls `DatabaseService->renderBannerScript()` to output the script before `</body>`, alongside a small cookie-settings button that lets visitors re-open the consent dialog.

```
TYPO3 Backend               Frontend
     │                          │
     ▼                          │
ConsentController               │
     │                          │
     ▼                          │
DatabaseService ──── sys_template ────► TypoScript USER object
  (save/load)        (storage)           (renderBannerScript)
                                              │
                                              ▼
                                         page.footerData
```

## Configuration

No additional TypoScript or extension configuration is needed beyond including the static template. The cookie button is styled via `Resources/Public/Css/prive-cookie-button.css` and can be overridden with custom CSS.

## License

[GPL-2.0-or-later](LICENSE)

## Author

Oliver Kroener — [oliver-kroener.de](https://www.oliver-kroener.de) — [ok@oliver-kroener.de](mailto:ok@oliver-kroener.de)
