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
through the extension's ``Services.yaml`` with the ``event.listener`` service
tag. It listens on
``\TYPO3\CMS\Frontend\Event\AfterCacheableContentIsGeneratedEvent`` and
splices the cookie button CSS, cookie button HTML, and consent script in before
the closing ``</body>`` tag. The markup is added while the content is still
cacheable, so it becomes part of the page cache.

There is **no** TypoScript, site set, or Fluid template to configure. Injecting
via an event listener keeps rendering independent of site-set load order -- a
theme set re-declaring ``page = PAGE`` can no longer discard the banner.

..  _configuration-typoscript:

Static TypoScript template
==========================

The static template **[kroener.DIGITAL] Prive Consent** still exists, but it is
empty since version 4.2.0. Up to version 4.1.1 it defined ``lib.priveScript``
and a ``page.footerData`` entry; both were removed when rendering moved into
PHP.

It is only kept so that TypoScript records which already include it do not end
up with a dangling reference. Including it is optional and has no effect, and it
can safely be removed from your template record.

..  _configuration-site-set:

Site set
========

..  note::
    Site sets require **TYPO3 13.1 or newer**. On TYPO3 12 the
    ``Configuration/Sets/`` directory is ignored by the core -- the banner works
    there exactly the same way, through the event listener.

The extension ships a site set in ``Configuration/Sets/OkPriveConsent/``:

..  code-block:: yaml
    :caption: EXT:ok_prive_consent/Configuration/Sets/OkPriveConsent/config.yaml

    name: oliverkroener/ok-prive-consent
    label: '[kroener.DIGITAL] Prive Consent'
    dependencies: []

On TYPO3 13 it is listed in the backend under :guilabel:`Site Management` >
:guilabel:`Sites` as :guilabel:`[kroener.DIGITAL] Prive Consent`.

..  important::
    The set is intentionally empty and **not required** for the banner to work:

    -  ``dependencies`` is an empty list -- it pulls in no other set.
    -  ``setup.typoscript`` contains comments only -- it defines no TypoScript.
    -  There is no ``settings.definitions.yaml`` -- the set exposes no settings.

    Adding it to a site configuration, or leaving it out, makes no difference to
    the frontend output.

..  code-block:: yaml
    :caption: config/sites/<identifier>/config.yaml -- the set reference is optional

    rootPageId: 1
    base: 'https://example.org/'
    dependencies:
      - oliverkroener/ok-prive-consent

See :ref:`site sets <t3coreapi:site-sets>` in the TYPO3 Core API reference for
general information on site sets.

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
