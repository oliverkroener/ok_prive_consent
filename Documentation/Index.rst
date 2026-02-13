:navigation-title:
    Prive Consent

..  _start:

=============
Prive Consent
=============

:Extension key:
    ok_prive_consent

:Package name:
    oliverkroener/ok-prive-consent

:Version:
    |release|

:Language:
    en

:Author:
    Oliver Kroener <https://www.oliver-kroener.de> & Contributors

:License:
   This document is published under the
   `Open Publication License <https://www.opencontent.org/openpub/>`__.

:Rendered:
    |today|

..  toctree::
    :titlesonly:
    :hidden:
    :maxdepth: 2

    Installation
    Usage
    Configuration/Index
    Faq
    GetHelp
    Sitemap

..  toctree::
    :hidden:

    Sitemap

..  note::
    * **Purpose**: Provides a TYPO3 backend module for managing `Prive Cookie Consent <https://www.prive.eu/>`__ banner scripts
    * **Backend module**: Edit, enable, and deploy consent scripts via a dedicated module under *Web > Prive Consent*
    * **Enable/disable toggle**: Activate or deactivate the consent banner without removing the script
    * **Multi-site support**: Automatically resolves the correct site root for TYPO3 multi-site setups
    * **Unsaved changes protection**: Warns before navigating away with unsaved modifications
    * **Automatic rendering**: Scripts are injected into the page footer via TypoScript

..  card-grid::
    :columns: 1
    :columns-md: 2
    :gap: 4
    :class: pb-4
    :card-height: 100

    ..  card:: :ref:`Installation <installation>`

        How to install this extension via Composer and include the
        static TypoScript template.

    ..  card:: :ref:`Usage <usage>`

        How to manage Prive consent scripts using the backend module.

    ..  card:: :ref:`Configuration <configuration>`

        Customise the cookie button appearance and override CSS.

    ..  card:: :ref:`Frequently Asked Questions (FAQ) <faq>`

        Common questions about setup and usage.

    ..  card:: :ref:`How to get help <help>`

        Where to get help and how to report issues.
