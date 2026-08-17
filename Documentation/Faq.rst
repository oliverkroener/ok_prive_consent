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
        :guilabel:`Web` > :guilabel:`Prive`.

    ..  accordion-item:: Where do I manage the consent script in TYPO3?
        :name: faq-backend-module
        :header-level: 2

        Navigate to :guilabel:`Web` > :guilabel:`Prive` in the TYPO3
        backend. Select a page in the page tree -- the module automatically
        resolves the site root. From there you can edit the script, enable or
        disable the consent banner, and save.

        See chapter :ref:`usage`.

    ..  accordion-item:: Do I need to edit any TypoScript or Fluid templates?
        :name: faq-typoscript
        :header-level: 2

        No. The consent script and cookie button are injected automatically on
        all frontend pages by a PSR-14 event listener as soon as the extension
        is installed. No site set, TypoScript, or template changes are needed.

    ..  accordion-item:: Do I have to add the site set to my site configuration?
        :name: faq-site-set
        :header-level: 2

        No. The extension ships a site set called
        ``oliverkroener/ok-prive-consent`` (shown in the backend as
        *[kroener.DIGITAL] Prive Consent*), but it is empty and optional. The
        banner is injected by a PSR-14 event listener that is registered
        through the extension's ``Services.yaml``, so it works whether or not
        the set is referenced.

        If your site's ``config.yaml`` already lists the set under
        ``dependencies``, you may keep it or remove it.

        See chapter :ref:`configuration-site-set`.

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
