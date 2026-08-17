..  include:: /Includes.rst.txt

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
    Access to the backend module is controlled per backend user and group
    (``access: user``). Administrators always have access; grant the
    *Web > Prive* module to other users or groups as needed.

..  rst-class:: bignums-xxl

1.  Open the module

    In the TYPO3 backend, navigate to :guilabel:`Web` > :guilabel:`Prive`. The
    module opens with the header :guilabel:`Prive Settings`.

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
frontend page of the site:

- The **consent script** is injected before ``</body>`` by a PSR-14 event listener
- A **cookie settings button** is also rendered, allowing visitors to re-open
  the consent dialog at any time

No site set, TypoScript, or Fluid template changes are required.

..  _usage-how-it-works:

How it works
============

The extension stores the consent script and an enable flag in custom fields
(``tx_ok_prive_cookie_consent_banner_script`` and
``tx_ok_prive_cookie_consent_banner_enabled``) on the ``pages`` record
of the site root page.

On frontend rendering, the PSR-14 event listener
``InjectBannerScript`` (listening on ``AfterCacheableContentIsGeneratedEvent``)
calls ``DatabaseService->getBannerMarkup()`` and splices the resulting script and
cookie settings button in before ``</body>``. The markup is only produced when the
enable flag is set, and it becomes part of the cacheable page content.

..  code-block:: text

    TYPO3 Backend               Frontend
         |                          |
         v                          |
    ConsentController               |
         |                          |
         v                          v
    DatabaseService ---- pages ----------> InjectBannerScript (event listener)
      (save/load)        (storage)           (getBannerMarkup)
                                                  |
                                                  v
                                          inject before </body>

..  note::

    Up to version 5.0 the fields were stored on the ``sys_template`` record of the
    site root page. Sites configured with :ref:`site sets <t3coreapi:site-sets>`
    have no ``sys_template`` record, which made the module report
    *"Please select a root site or a page that contains a site root and template!"*.
    After updating, run the database analyser and then the upgrade wizard
    *"EXT:ok_prive_consent: Move consent banner settings from sys_template to pages"*
    to carry existing values over:

    ..  code-block:: bash

        vendor/bin/typo3 upgrade:run okPriveConsentMigrateStorageToPages

..  _usage-multi-site:

Multi-site support
==================

In a TYPO3 multi-site setup, the extension uses ``SiteFinder`` to resolve the
correct site root page for the currently selected page in the page tree. Each
site can have its own independent consent script and enable/disable state.
