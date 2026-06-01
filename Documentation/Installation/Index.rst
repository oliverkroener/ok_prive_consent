..  include:: /Includes.rst.txt

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

    composer req oliverkroener/ok-prive-consent

See also `Installing extensions <https://docs.typo3.org/m/typo3/tutorial-getting-started/main/en-us/Extensions/Management.html>`_.

..  _installation-extension-manager:

Install via Extension Manager
==============================

Download or upload the extension and activate it via
:guilabel:`Admin Tools` > :guilabel:`Extensions`.

..  _installation-typoscript:

Add the site set
================

In TYPO3 v14 the TypoScript ships as a **site set** (set name
``oliverkroener/ok-prive-consent``). Add it to the ``dependencies`` of each site
that should render the consent banner so the consent script and cookie button are
output on the frontend:

..  rst-class:: bignums-xxl

1.  Open your site configuration

    Edit ``config/sites/<your-site>/config.yaml`` in your project, or open
    :guilabel:`Site Management` > :guilabel:`Sites` in the TYPO3 backend.

2.  Add the set dependency

    Add the set to the ``dependencies`` list:

    ..  code-block:: yaml

        dependencies:
          - oliverkroener/ok-prive-consent

    In the backend, add **[kroener.DIGITAL] Prive Consent** under the
    :guilabel:`Sets` / :guilabel:`Dependencies` field of the site.

3.  Clear caches

    Clear all caches to ensure the TypoScript from the set is loaded.
