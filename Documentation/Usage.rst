:navigation-title: Usage

..  _usage:

=====
Usage
=====

This extension provides a dedicated TYPO3 backend module for managing the
`Prive Cookie Consent <https://www.prive.eu/>`__ banner script. Administrators
can edit, enable, and deploy the consent script without needing file system
access or a deployment pipeline.

..  _usage-backend-module:

Using the backend module
========================

..  important::
    The backend module requires admin access. It is only available to TYPO3
    administrators.

..  rst-class:: bignums-xxl

1.  Open the module

    In the TYPO3 backend, navigate to :guilabel:`Web` > :guilabel:`Prive Consent`.

2.  Select a page in the page tree

    Click on any page that belongs to the site you want to configure. The module
    automatically resolves the site root page from the TYPO3 Site Configuration.

    The module displays the detected **site identifier** and **root page ID**.

3.  Toggle the consent banner

    Use the **Enable Prive script** toggle to activate or deactivate the
    consent banner. When disabled, no script is rendered on the frontend, but
    the saved script is preserved.

4.  Edit the script

    Paste the JavaScript snippet provided by Prive into the script editor field.
    The editor uses a monospace textarea for editing JavaScript code.

5.  Save

    Click the **Save** button to persist the script. A success message confirms
    the script has been saved.

..  _usage-unsaved-changes:

Unsaved changes protection
==========================

The module detects unsaved changes and warns before navigating away. If you
modify the script or toggle the enable switch without saving, a confirmation
dialog appears when you attempt to leave the page.

The dialog offers three options:

- **No, I will continue editing** — stay on the page and keep editing
- **Yes, discard my changes** — leave the page without saving
- **Save and close** — save the current changes via AJAX and then navigate away

..  _usage-frontend-rendering:

Frontend rendering
==================

Once a script is saved and enabled, it is automatically rendered on every
frontend page where the static TypoScript template is included:

- The **consent script** is injected into ``page.footerData`` (before ``</body>``)
- A **cookie settings button** is also rendered, allowing visitors to re-open
  the consent dialog at any time

No additional TypoScript or Fluid template changes are required.

..  _usage-how-it-works:

How it works
============

The extension stores the consent script and an enable flag in custom fields
(``tx_ok_prive_cookie_consent_banner_script`` and
``tx_ok_prive_cookie_consent_banner_enabled``) on the ``sys_template`` record
of the site root page.

On frontend rendering, a TypoScript ``USER`` object calls
``DatabaseService->renderBannerScript()`` to output the script and the cookie
settings button in the page footer. The script is only rendered when the
enable flag is set.

..  code-block:: text

    TYPO3 Backend               Frontend
         |                          |
         v                          |
    ConsentController               |
         |                          |
         v                          |
    DatabaseService ---- sys_template -----> TypoScript USER object
      (save/load)        (storage)           (renderBannerScript)
                                                  |
                                                  v
                                             page.footerData

..  _usage-multi-site:

Multi-site support
==================

In a TYPO3 multi-site setup, the extension uses ``SiteFinder`` to resolve the
correct site root page for the currently selected page in the page tree. Each
site can have its own independent consent script and enable/disable state.
