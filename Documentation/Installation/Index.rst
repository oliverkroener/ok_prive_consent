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

..  _installation-setup:

After the installation
======================

No TypoScript needs to be included and no site set needs to be added -- the
consent script and cookie button are injected by a PSR-14 event listener that is
registered through the extension's ``Services.yaml``.

..  rst-class:: bignums-xxl

1.  Update the database

    Go to :guilabel:`Admin Tools` > :guilabel:`Maintenance` >
    :guilabel:`Analyze Database Structure` and apply the suggested changes. This
    adds the two consent fields to the ``pages`` table.

2.  Clear caches

    Clear all caches so the event listener is picked up.

3.  Optional: add the site set (TYPO3 13.1+)

    In :guilabel:`Site Management` > :guilabel:`Sites` you may add the set
    :guilabel:`[kroener.DIGITAL] Prive Consent`. The set is empty and optional --
    see :ref:`Site set <configuration-site-set>`.

..  _installation-upgrade:

Upgrading from version 4.1.1 or older
=====================================

Up to version 4.1.1 the banner settings were stored on the ``sys_template``
record of the site root. Since version 4.2.0 they live on the ``pages`` record
of the site root, so that sites driven purely by site sets -- which have no
``sys_template`` record at all -- keep working.

..  rst-class:: bignums-xxl

1.  Analyze the database structure

    :guilabel:`Admin Tools` > :guilabel:`Maintenance` >
    :guilabel:`Analyze Database Structure`.

2.  Run the upgrade wizard

    In :guilabel:`Admin Tools` > :guilabel:`Upgrade` >
    :guilabel:`Upgrade Wizard`, run
    :guilabel:`EXT:ok_prive_consent: Move consent banner settings from sys_template to pages`.
    It copies the existing script and enable flag to the site root page. Running
    it twice is harmless.

3.  Optional: clean up the TypoScript record

    The static template **[kroener.DIGITAL] Prive Consent** is empty now. It is
    still shipped so existing includes do not break, but it can be removed from
    the :guilabel:`Includes` tab of your template record.
