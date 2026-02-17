..  include:: /Includes.rst.txt

..  _introduction:

============
Introduction
============

What does it do?
================

The **Prive Consent** extension provides a TYPO3 backend module for managing
`Prive Cookie Consent <https://www.prive.eu/>`__ banner scripts. Administrators
can edit, enable, and deploy consent scripts directly from the TYPO3 backend
without needing file system access or a deployment pipeline.

The consent script and a floating cookie settings button are rendered
automatically on all frontend pages via TypoScript.

Features
========

-  **Backend module** under *Web > Prive Consent* for editing consent scripts
-  **Enable/disable toggle** to activate or deactivate the banner without
   removing the script
-  **Multi-site support** — automatically resolves the correct site root per
   TYPO3 site configuration
-  **Unsaved changes protection** — warns before navigating away with unsaved
   modifications, with a "save and close" option
-  **Automatic frontend rendering** — script and cookie settings button injected
   via TypoScript ``page.footerData``
-  **Cache flush on save** — frontend page cache is cleared automatically after
   saving

Requirements
============

-  TYPO3 10.4 LTS
-  PHP >= 7.2
-  A `Prive <https://www.prive.eu/>`__ account to obtain the consent JavaScript
   snippet
