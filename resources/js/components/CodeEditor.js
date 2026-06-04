import { editor } from 'monaco-editor';

/**
 * Initialize Monaco Editor for code editing
 * @param {string} elementId - ID of the DOM element to attach the editor
 * @param {object} options - Configuration options
 * @returns {editor.IStandaloneCodeEditor} - Monaco editor instance
 */
export function initCodeEditor(elementId, options = {}) {
    const defaultOptions = {
        value: '',
        language: 'php',
        theme: 'vs-dark',
        automaticLayout: true,
        minimap: { enabled: false },
        fontSize: 14,
        lineNumbers: 'on',
        roundedSelection: false,
        scrollBeyondLastLine: true,
        wordWrap: 'on',
        tabSize: 4,
        ...options
    };

    const element = document.getElementById(elementId);
    if (!element) {
        console.warn(`Element with ID "${elementId}" not found for Monaco Editor`);
        return null;
    }

    const monacoEditor = editor.create(element, defaultOptions);
    
    // Auto-resize height based on content
    monacoEditor.onDidChangeModelContent(() => {
        const contentHeight = monacoEditor.getContentHeight();
        element.style.height = `${contentHeight}px`;
        monacoEditor.layout();
    });

    return monacoEditor;
}

/**
 * Destroy Monaco Editor instance
 * @param {editor.IStandaloneCodeEditor} monacoEditor - Editor instance to destroy
 */
export function destroyCodeEditor(monacoEditor) {
    if (monacoEditor && typeof monacoEditor.dispose === 'function') {
        monacoEditor.dispose();
    }
}

/**
 * Get editor content as markdown-compatible code block
 * @param {editor.IStandaloneCodeEditor} monacoEditor - Editor instance
 * @param {string} label - Optional label for the code block
 * @returns {string} - Formatted markdown string
 */
export function getEditorAsMarkdown(monacoEditor, label = '') {
    if (!monacoEditor) return '';
    
    const language = monacoEditor.getModel().getLanguageId();
    const code = monacoEditor.getValue();
    
    if (!code.trim()) return '';
    
    const labelLine = label ? ` ${label}` : '';
    return `\`\`\`${language}${labelLine}\n${code}\n\`\`\``;
}
