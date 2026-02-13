import '../scss/backend.scss'
import * as monaco from 'monaco-editor'

document.addEventListener('DOMContentLoaded', () => {

    function initMonaco(monacoId, scriptId) {
        const editorContainer = document.getElementById(monacoId);
        const textarea = document.getElementById(scriptId);

        if (editorContainer && textarea) {
            const editor = monaco.editor.create(editorContainer, {
                value: textarea.value || '',
                language: 'javascript',
                theme: 'vs-dark',
                wordWrap: 'on',
                wrappingStrategy: 'advanced',
                wordWrapColumn: 80,
                wordWrapMinified: true,
                automaticLayout: true,
                scrollbar: {
                    horizontal: 'hidden',
                    vertical: 'auto',
                    alwaysConsumeMouseWheel: false,
                    verticalHasArrows: true,
                    horizontalHasArrows: false,
                },
            });

            const form = textarea.closest('form');

            if (form) {
                form.addEventListener('submit', () => {
                    textarea.value = editor.getValue();
                });
            }
        }
    }

    initMonaco('monaco-editor', 'tx_ok_prive_cookie_consent_banner_script');

    // Clear flash messages after 10 seconds
    setTimeout(function () {
        var messages = document.querySelectorAll('.typo3-messages');
        messages.forEach(function(message) {
            message.classList.add('hidden');
        });
    }, 10000);

});
