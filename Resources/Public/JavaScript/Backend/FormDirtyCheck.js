define(['TYPO3/CMS/Backend/Modal', 'TYPO3/CMS/Backend/Severity', 'jquery'], function (Modal, Severity, $) {
    'use strict';

    function FormDirtyCheck() {
        this.form = null;
        this.isDirty = false;
        this.initialData = '';
    }

    FormDirtyCheck.prototype.initialize = function () {
        this.form = document.getElementById('priveConsentForm');
        if (!this.form) {
            return;
        }

        this.initialData = this.serializeForm();
        var self = this;

        // Track input changes
        this.form.addEventListener('input', function () { self.checkDirty(); });
        this.form.addEventListener('change', function () { self.checkDirty(); });

        // Register with TYPO3's ConsumerScope for page tree / module navigation
        try {
            top.TYPO3.Backend.consumerScope.attach(this);
        } catch (e) {
            // ConsumerScope not available (e.g. cross-origin)
        }

        // Browser close/refresh fallback
        window.addEventListener('beforeunload', function (e) {
            if (self.isDirty) {
                e.preventDefault();
                e.returnValue = '';
            }
        });

        // Detach consumer when iframe is unloaded
        window.addEventListener('pagehide', function () {
            try {
                top.TYPO3.Backend.consumerScope.detach(self);
            } catch (e) {
                // ignore
            }
        }, {once: true});

        // Reset dirty state on form submit
        this.form.addEventListener('submit', function () {
            self.isDirty = false;
        });
    };

    FormDirtyCheck.prototype.serializeForm = function () {
        return new URLSearchParams(new FormData(this.form)).toString();
    };

    FormDirtyCheck.prototype.checkDirty = function () {
        this.isDirty = this.serializeForm() !== this.initialData;
    };

    /**
     * ConsumerScope consumer interface.
     * Called by TYPO3 before navigating away (page tree click, module switch, refresh).
     * Returns a jQuery Deferred that resolves to allow navigation or stays pending to block it.
     */
    FormDirtyCheck.prototype.consume = function (interactionRequest) {
        var deferred = $.Deferred();

        if (!this.isDirty) {
            deferred.resolve();
            return deferred;
        }

        this.showConfirmModal().then(function () { deferred.resolve(); });

        return deferred;
    };

    /**
     * Shows the standard TYPO3 "unsaved changes" modal with three buttons.
     * Returns a jQuery Deferred that resolves when navigation should proceed.
     */
    FormDirtyCheck.prototype.showConfirmModal = function () {
        var self = this;
        var deferred = $.Deferred();

        var lang = TYPO3 && TYPO3.lang ? TYPO3.lang : {};
        var buttons = [
            {
                text: lang['buttons.confirm.close_without_save.no'] || 'No, I will continue editing',
                btnClass: 'btn-default',
                name: 'no',
                active: true
            },
            {
                text: lang['buttons.confirm.close_without_save.yes'] || 'Yes, discard my changes',
                btnClass: 'btn-default',
                name: 'yes'
            },
            {
                text: lang['buttons.confirm.save_and_close'] || 'Save and close',
                btnClass: 'btn-primary',
                name: 'save'
            }
        ];

        var $modal = Modal.confirm(
            lang['label.confirm.close_without_save.title'] || 'Do you want to close without saving?',
            lang['label.confirm.close_without_save.content'] || 'You currently have unsaved changes. Are you sure you want to discard these changes?',
            Severity.warning,
            buttons
        );

        $modal.on('button.clicked', function (e) {
            var name = e.target.getAttribute('name');
            Modal.dismiss();

            if (name === 'yes') {
                self.isDirty = false;
                deferred.resolve();
            } else if (name === 'save') {
                self.saveForm()
                    .done(function () {
                        self.isDirty = false;
                        deferred.resolve();
                    });
                // Save failed: deferred stays pending, navigation blocked
            }
            // 'no' or backdrop click: deferred stays pending, navigation blocked
        });

        return deferred;
    };

    /**
     * Submits the form via AJAX so the configuration is saved
     * without a full page reload.
     */
    FormDirtyCheck.prototype.saveForm = function () {
        return $.ajax({
            url: this.form.action,
            method: 'POST',
            data: $(this.form).serialize(),
            dataType: 'html'
        });
    };

    var checker = new FormDirtyCheck();
    checker.initialize();
});
