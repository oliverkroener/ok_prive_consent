..  include:: /Includes.rst.txt

:navigation-title: Configuration

..  _configuration:

=============
Configuration
=============

The extension requires no configuration. Once it is installed, the consent
script and cookie button are rendered automatically on every frontend page.

..  _configuration-frontend-rendering:

Frontend rendering
==================

Rendering is handled by the PSR-14 event listener
``OliverKroener\OkPriveConsent\EventListener\InjectBannerScript``, registered
through the extension's ``Services.yaml`` via the ``#[AsEventListener]``
attribute. It listens on
``\TYPO3\CMS\Frontend\Event\AfterCacheableContentIsGeneratedEvent`` and splices
the cookie button CSS, cookie button HTML, and consent script in before the
closing ``</body>`` tag.

There is **no** TypoScript, site set, or Fluid template to configure. Injecting
via an event listener keeps rendering independent of site-set load order -- a
theme set re-declaring ``page = PAGE`` can no longer discard the banner -- and
the markup becomes part of the cacheable page content.

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
