:navigation-title: Installation

..  _installation:

============
Installation
============

..  _installation-composer:

Install with Composer
=====================

..  note::
    This is the recommended way to install this extension.

Install the extension via Composer:

..  code-block:: bash

    composer req oliverkroener/ok-prive-cookie-consent

See also `Installing extensions, TYPO3 Getting started <https://docs.typo3.org/permalink/t3start:installing-extensions>`_.

..  _installation-extension-manager:

Install via Extension Manager
==============================

Download or upload the extension and activate it via
:guilabel:`Admin Tools` > :guilabel:`Extensions`.

..  _installation-typoscript:

Include the static TypoScript
=============================

After installation, include the static TypoScript template so the consent
script and cookie button are rendered on the frontend:

..  rst-class:: bignums-xxl

1.  Open the Template module

    In the TYPO3 backend, go to the :guilabel:`Template` module.

2.  Select the root page

    Select the root page of your site.

3.  Edit the template record

    Choose :guilabel:`Info/Modify` and click :guilabel:`Edit the whole template record`.

4.  Include the static template

    Switch to the :guilabel:`Includes` tab and add
    **[kroener.DIGITAL] Prive Consent** from the list of available static templates.

5.  Clear caches

    Clear all caches to ensure the TypoScript is loaded.
