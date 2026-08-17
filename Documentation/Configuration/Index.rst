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

..  _configuration-site-set:

Site set
========

The extension ships a site set in ``Configuration/Sets/OkPriveConsent/``:

..  code-block:: yaml
    :caption: EXT:ok_prive_consent/Configuration/Sets/OkPriveConsent/config.yaml

    name: oliverkroener/ok-prive-consent
    label: '[kroener.DIGITAL] Prive Consent'
    dependencies: []

In the backend it is listed under :guilabel:`Site Management` >
:guilabel:`Sites` as :guilabel:`[kroener.DIGITAL] Prive Consent`.

..  important::
    The set is intentionally empty and **not required** for the banner to work:

    -  ``dependencies`` is an empty list -- it pulls in no other set.
    -  ``setup.typoscript`` contains comments only -- it defines no TypoScript.
    -  There is no ``settings.definitions.yaml`` -- the set exposes no settings.

    It is shipped solely so that site configurations written for earlier
    versions, which list ``oliverkroener/ok-prive-consent`` under
    ``dependencies``, keep resolving instead of referencing a set that no
    longer exists.

..  _configuration-site-set-keep-or-remove:

Keeping or removing the dependency
----------------------------------

Both are valid — pick either:

Keep it
    The set is a no-op, so an existing dependency entry is harmless.

Remove it
    Delete the ``oliverkroener/ok-prive-consent`` line from ``dependencies`` in
    your site's ``config.yaml``. The banner keeps rendering, because
    ``InjectBannerScript`` is registered through the extension's
    ``Services.yaml`` — not through the set.

..  code-block:: yaml
    :caption: config/sites/<identifier>/config.yaml — the set reference is optional

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
