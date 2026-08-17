..  include:: /Includes.rst.txt

:navigation-title: FAQ

..  _faq:

================================
Frequently Asked Questions (FAQ)
================================

..  accordion::
    :name: faq

    ..  accordion-item:: How do I install this extension?
        :name: faq-installation
        :header-level: 2
        :show:

        See chapter :ref:`installation`.

    ..  accordion-item:: Where do I get the Prive consent script?
        :name: faq-prive-script
        :header-level: 2

        You need a `Prive <https://www.prive.eu/>`__ account. After configuring
        your consent banner in the Prive dashboard, copy the JavaScript snippet
        and paste it into the TYPO3 backend module at
        :guilabel:`Web` > :guilabel:`Prive Consent`.

    ..  accordion-item:: Where do I manage the consent script in TYPO3?
        :name: faq-backend-module
        :header-level: 2

        Navigate to :guilabel:`Web` > :guilabel:`Prive Consent` in the TYPO3
        backend. Select a page in the page tree -- the module automatically
        resolves the site root. From there you can edit the script, enable or
        disable the consent banner, and save.

        See chapter :ref:`usage`.

    ..  accordion-item:: Do I need to edit any TypoScript or Fluid templates?
        :name: faq-typoscript
        :header-level: 2

        No. The consent script and cookie button are injected before the
        closing ``</body>`` tag by a PSR-14 event listener that ships with the
        extension. Nothing has to be included, configured or overridden.

        The static TypoScript template **[kroener.DIGITAL] Prive Consent** is
        empty since version 4.2.0 and only kept so existing includes do not
        break.

    ..  accordion-item:: Do I have to add the site set to my site configuration?
        :name: faq-site-set
        :header-level: 2

        No. The set ``oliverkroener/ok-prive-consent`` is empty -- it defines no
        TypoScript, no dependencies and no settings. Adding or removing it makes
        no difference to the frontend output.

        See chapter :ref:`configuration-site-set`.

    ..  accordion-item:: Are site sets available in TYPO3 12?
        :name: faq-site-set-typo3-12
        :header-level: 2

        No. Site sets were introduced in TYPO3 13.1. On TYPO3 12 the
        ``Configuration/Sets/`` directory of this extension is ignored by the
        core. The banner is rendered by the event listener on both TYPO3 12 and
        13, so nothing is missing on TYPO3 12.

    ..  accordion-item:: I upgraded from 4.1.x and my script is gone -- what now?
        :name: faq-upgrade
        :header-level: 2

        The settings moved from the ``sys_template`` record to the site root's
        ``pages`` record. Run the database analyser, then the upgrade wizard
        :guilabel:`EXT:ok_prive_consent: Move consent banner settings from
        sys_template to pages` in :guilabel:`Admin Tools` >
        :guilabel:`Upgrade`.

        See chapter :ref:`installation-upgrade`.

    ..  accordion-item:: Can I use different consent scripts per site?
        :name: faq-multi-site
        :header-level: 2

        Yes. In a TYPO3 multi-site setup, the backend module resolves the
        correct site root page automatically. Each site can have its own
        independent consent script and enable/disable state.

    ..  accordion-item:: How do I disable the consent banner temporarily?
        :name: faq-disable
        :header-level: 2

        In the backend module, uncheck the **Enable Prive script** toggle and
        click **Save**. The saved script is preserved but no longer rendered on
        the frontend. Re-enable it at any time by checking the toggle again.

    ..  accordion-item:: Can I customise the cookie settings button?
        :name: faq-button-style
        :header-level: 2

        Yes. The button is styled via
        ``Resources/Public/Css/prive-cookie-button.css``. You can override these
        styles with custom CSS in your site package.

        See chapter :ref:`configuration-css`.

    ..  accordion-item:: Where can I get help?
        :name: faq-help
        :header-level: 2

        See chapter :ref:`contact`.
