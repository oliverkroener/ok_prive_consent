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

No further setup required
=========================

There is nothing more to configure. The consent banner is injected on the
frontend by a PSR-14 event listener that is registered automatically when the
extension is installed -- no TypoScript, site set, or Fluid template change is
needed.

..  note::
    A site set named ``oliverkroener/ok-prive-consent`` is still shipped so that
    existing site configurations referencing it under ``dependencies`` keep
    working. It is now a no-op and entirely optional -- you may leave it or
    remove the dependency.

After installing the extension, clear all caches and open
:guilabel:`Web` > :guilabel:`Prive Consent` to add your script (see
:ref:`Usage <usage>`).
