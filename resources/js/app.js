import './bootstrap';
import Alpine from 'alpinejs';
import focus from '@alpinejs/focus';
import hljs from 'highlight.js';
import php from 'highlight.js/lib/languages/php';
import bash from 'highlight.js/lib/languages/bash';
import sql from 'highlight.js/lib/languages/sql';
import javascript from 'highlight.js/lib/languages/javascript';
import xml from 'highlight.js/lib/languages/xml';
import json from 'highlight.js/lib/languages/json';
import yaml from 'highlight.js/lib/languages/yaml';

// Register languages
hljs.registerLanguage('php',        php);
hljs.registerLanguage('bash',       bash);
hljs.registerLanguage('shell',      bash);
hljs.registerLanguage('sql',        sql);
hljs.registerLanguage('javascript', javascript);
hljs.registerLanguage('blade',      xml);
hljs.registerLanguage('livewire',   php);
hljs.registerLanguage('xml',        xml);
hljs.registerLanguage('json',       json);
hljs.registerLanguage('yaml',       yaml);
hljs.registerLanguage('env',        bash);

// Setup Alpine
window.Alpine = Alpine;
Alpine.plugin(focus);
Alpine.start();

// Highlight all code blocks after Livewire updates
function highlightCode() {
    document.querySelectorAll('pre code').forEach((block) => {
        if (!block.dataset.highlighted) {
            hljs.highlightElement(block);
            block.dataset.highlighted = 'true';
        }
    });
}

// Run on initial load
document.addEventListener('DOMContentLoaded', highlightCode);

// Re-run after Livewire navigations and component updates
document.addEventListener('livewire:navigated', highlightCode);
document.addEventListener('livewire:update', () => setTimeout(highlightCode, 100));

// Global keyboard shortcut: Ctrl+K to focus search
document.addEventListener('keydown', (e) => {
    if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
        e.preventDefault();
        document.querySelector('input[placeholder*="Search"]')?.focus();
    }
});
