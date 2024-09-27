// Import our custom CSS
import '../scss/styles.scss'

// Import all of Bootstrap's JS
import * as bootstrap from 'bootstrap'
import '@fortawesome/fontawesome-free/css/all.min.css'
import * as monaco from 'monaco-editor'

// Initialize Monaco Editor once the DOM is fully loaded
document.addEventListener('DOMContentLoaded', () => {

    function initMonaco(monacoId, scriptId) {
        // Get references to DOM elements
        const editorContainer = document.getElementById(monacoId);
        const textarea = document.getElementById(scriptId);
    
        if (editorContainer && textarea) {
            // Initialize Monaco Editor
            const editor = monaco.editor.create(editorContainer, {
                value: textarea.value || '',
                language: 'javascript',
                theme: 'vs-dark', // Choose from 'vs', 'vs-dark', 'hc-black'
                // Enable word wrapping
                wordWrap: 'on', // Enables word wrapping
                wrappingStrategy: 'advanced', // More sophisticated wrapping similar to VS Code
                wordWrapColumn: 80, // Optional: Sets the column at which to wrap
                wordWrapMinified: true, // Optional: Optimize word wrapping for minified code
                automaticLayout: true, // Automatically adjust layout

                // Configure scrollbars
                scrollbar: {
                    horizontal: 'hidden', // Hide horizontal scrollbar
                    vertical: 'auto', // Show vertical scrollbar as needed
                    alwaysConsumeMouseWheel: false,
                    verticalHasArrows: true,
                    horizontalHasArrows: false,
                    // Additional scrollbar configurations if needed
                },
            });
        
            // Sync Monaco Editor content with the textarea before form submission
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
            message.classList.add('hidden'); // Or message.style.display = 'none';
        });
    }, 10000); // 10 second

  });