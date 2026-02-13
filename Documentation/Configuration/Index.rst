:navigation-title: Configuration

..  _configuration:

=============
Configuration
=============

The extension requires minimal configuration. After including the static
TypoScript template (see :ref:`Installation <installation-typoscript>`), the
consent script and cookie button are rendered automatically.

..  _configuration-typoscript:

TypoScript setup
================

The static TypoScript template **[kroener.DIGITAL] Prive Consent** configures:

- ``lib.priveScript`` — a ``USER`` object that calls
  ``DatabaseService->renderBannerScript()`` to output the consent script
- ``page.footerData`` — inserts the cookie button HTML and consent script
  before ``</body>``
- ``page.includeCSS`` — loads the cookie button stylesheet

The ``footerData`` keys use ``crc32('ok_prive_cookie_consent')`` to avoid
collisions with other extensions.

..  _configuration-css:

Cookie button styling
=====================

The floating cookie settings button is styled via:

``Resources/Public/Css/prive-cookie-button.css``

To override the default appearance, add custom CSS in your site package that
targets the cookie button elements. The brand colours used by default are:

- Primary: ``#f05722``
- Secondary: ``#0fa8dd``

..  _configuration-prive-account:

Prive account
=============

The JavaScript snippet that powers the consent banner is provided by
`Prive <https://www.prive.eu/>`__. You need a Prive account to obtain the
script. Paste the script into the backend module (see :ref:`Usage <usage>`).

Refer to the Prive documentation for configuring which cookies are managed,
consent categories, banner appearance, and language settings.
