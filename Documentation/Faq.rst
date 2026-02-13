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
        backend. Select a page in the page tree — the module automatically
        resolves the site root. From there you can edit the script, enable or
        disable the consent banner, and save.

        See chapter :ref:`usage`.

    ..  accordion-item:: Do I need to edit any TypoScript or Fluid templates?
        :name: faq-typoscript
        :header-level: 2

        No. After including the static TypoScript template
        **[kroener.DIGITAL] Prive Consent**, the consent script and cookie
        button are rendered automatically on all frontend pages. No additional
        TypoScript or template changes are needed.

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

        See chapter :ref:`help`.
