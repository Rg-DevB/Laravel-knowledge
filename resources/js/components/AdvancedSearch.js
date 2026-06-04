/**
 * Advanced Search Component
 * Supports fuzzy search, filters, and keyboard shortcuts
 */

class AdvancedSearch {
    constructor(options = {}) {
        this.searchUrl = options.searchUrl || '/api/search';
        this.minQueryLength = options.minQueryLength || 2;
        this.debounceMs = options.debounceMs || 300;
        this.maxResults = options.maxResults || 10;
        this.results = [];
        this.isOpen = false;
        this.selectedIndex = -1;
        this.init();
    }

    init() {
        // Create search modal if it doesn't exist
        this.createSearchModal();

        // Global keyboard shortcut: Ctrl+K or Cmd+K
        document.addEventListener('keydown', (e) => {
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                this.toggle();
            }
            if (e.key === 'Escape' && this.isOpen) {
                this.close();
            }
        });

        // Listen for Livewire events
        document.addEventListener('livewire:init', () => {
            Livewire.on('open-search', () => this.open());
            Livewire.on('search-focus', () => this.focus());
        });
    }

    createSearchModal() {
        if (document.getElementById('search-modal')) return;

        const modal = document.createElement('div');
        modal.id = 'search-modal';
        modal.className = 'fixed inset-0 z-50 hidden';
        modal.innerHTML = `
            <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="window.advancedSearch.close()"></div>
            <div class="relative top-20 mx-auto w-full max-w-2xl px-4">
                <div class="bg-zinc-900 border border-zinc-700 rounded-2xl shadow-2xl overflow-hidden">
                    <div class="flex items-center gap-3 p-4 border-b border-zinc-800">
                        <svg class="w-5 h-5 text-zinc-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
                        </svg>
                        <input type="text" id="search-input" 
                               placeholder="Search problems, solutions, users..." 
                               class="flex-1 bg-transparent text-zinc-100 placeholder-zinc-500 focus:outline-none text-base"
                               autocomplete="off" autofocus>
                        <div class="flex items-center gap-1 text-xs text-zinc-500">
                            <kbd class="px-1.5 py-0.5 rounded bg-zinc-800 border border-zinc-700">ESC</kbd>
                            <span>to close</span>
                        </div>
                    </div>
                    <div id="search-filters" class="flex gap-2 px-4 py-2 border-b border-zinc-800/50 overflow-x-auto">
                        <button data-filter="all" class="filter-btn active px-3 py-1 rounded-lg text-xs font-medium bg-zinc-800 text-zinc-300">All</button>
                        <button data-filter="problems" class="filter-btn px-3 py-1 rounded-lg text-xs font-medium text-zinc-500 hover:text-zinc-300">Problems</button>
                        <button data-filter="solutions" class="filter-btn px-3 py-1 rounded-lg text-xs font-medium text-zinc-500 hover:text-zinc-300">Solutions</button>
                        <button data-filter="users" class="filter-btn px-3 py-1 rounded-lg text-xs font-medium text-zinc-500 hover:text-zinc-300">Users</button>
                    </div>
                    <div id="search-results" class="max-h-96 overflow-y-auto p-2"></div>
                    <div id="search-loading" class="hidden p-8 text-center">
                        <div class="inline-block w-6 h-6 border-2 border-zinc-700 border-t-zinc-400 rounded-full animate-spin"></div>
                    </div>
                    <div id="search-empty" class="hidden p-8 text-center text-zinc-500 text-sm">
                        No results found
                    </div>
                </div>
            </div>
        `;

        document.body.appendChild(modal);

        // Setup event listeners
        const input = document.getElementById('search-input');
        input.addEventListener('input', (e) => this.handleInput(e.target.value));
        
        // Filter buttons
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                document.querySelectorAll('.filter-btn').forEach(b => {
                    b.classList.remove('active', 'bg-zinc-800', 'text-zinc-300');
                    b.classList.add('text-zinc-500');
                });
                e.target.classList.add('active', 'bg-zinc-800', 'text-zinc-300');
                e.target.classList.remove('text-zinc-500');
                this.currentFilter = e.target.dataset.filter;
                this.search(this.lastQuery);
            });
        });

        // Keyboard navigation
        input.addEventListener('keydown', (e) => this.handleKeydown(e));
    }

    toggle() {
        this.isOpen ? this.close() : this.open();
    }

    open() {
        this.isOpen = true;
        const modal = document.getElementById('search-modal');
        modal.classList.remove('hidden');
        this.focus();
        this.lastQuery = '';
        this.results = [];
        this.renderResults();
    }

    close() {
        this.isOpen = false;
        document.getElementById('search-modal').classList.add('hidden');
        this.selectedIndex = -1;
    }

    focus() {
        const input = document.getElementById('search-input');
        if (input) input.focus();
    }

    handleInput(query) {
        this.lastQuery = query;
        
        // Debounce
        clearTimeout(this.searchTimeout);
        if (query.length < this.minQueryLength) {
            this.results = [];
            this.renderResults();
            return;
        }

        this.searchTimeout = setTimeout(() => {
            this.search(query);
        }, this.debounceMs);
    }

    async search(query) {
        if (!query || query.length < this.minQueryLength) {
            this.results = [];
            this.renderResults();
            return;
        }

        this.showLoading(true);
        
        try {
            const response = await fetch(`${this.searchUrl}?q=${encodeURIComponent(query)}&type=${this.currentFilter || 'all'}&limit=${this.maxResults}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) throw new Error('Search failed');
            
            const data = await response.json();
            this.results = data.results || [];
            this.showLoading(false);
            this.renderResults();
        } catch (error) {
            console.error('Search error:', error);
            this.showLoading(false);
            this.results = [];
            this.renderResults();
        }
    }

    showLoading(show) {
        document.getElementById('search-loading').classList.toggle('hidden', !show);
        document.getElementById('search-results').classList.toggle('hidden', show);
        document.getElementById('search-empty').classList.toggle('hidden', show || this.results.length > 0);
    }

    renderResults() {
        const container = document.getElementById('search-results');
        
        if (this.results.length === 0) {
            document.getElementById('search-empty').classList.remove('hidden');
            container.innerHTML = '';
            return;
        }

        document.getElementById('search-empty').classList.add('hidden');
        
        container.innerHTML = this.results.map((result, index) => {
            const icons = {
                problem: '<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0zm-9 3.75h.008v.008H12v-.008z"/></svg>',
                solution: '<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>',
                user: '<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/></svg>'
            };

            const colors = {
                problem: 'text-blue-400 bg-blue-500/10',
                solution: 'text-emerald-400 bg-emerald-500/10',
                user: 'text-violet-400 bg-violet-500/10'
            };

            return `
                <a href="${result.url}" wire:navigate class="block p-3 rounded-xl hover:bg-zinc-800 transition-colors ${index === this.selectedIndex ? 'bg-zinc-800 ring-1 ring-zinc-700' : ''}">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-8 h-8 rounded-lg ${colors[result.type] || colors.problem} flex items-center justify-center">
                            ${icons[result.type] || icons.problem}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-zinc-200 truncate">${this.escapeHtml(result.title)}</p>
                            <p class="text-xs text-zinc-500 mt-0.5">${this.escapeHtml(result.excerpt || '')}</p>
                            ${result.meta ? `<p class="text-xs text-zinc-600 mt-1">${this.escapeHtml(result.meta)}</p>` : ''}
                        </div>
                    </div>
                </a>
            `;
        }).join('');
    }

    handleKeydown(e) {
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            this.selectedIndex = Math.min(this.selectedIndex + 1, this.results.length - 1);
            this.renderResults();
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            this.selectedIndex = Math.max(this.selectedIndex - 1, -1);
            this.renderResults();
        } else if (e.key === 'Enter' && this.selectedIndex >= 0 && this.results[this.selectedIndex]) {
            e.preventDefault();
            window.location.href = this.results[this.selectedIndex].url;
        }
    }

    escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

// Initialize global instance
window.advancedSearch = new AdvancedSearch();

export default AdvancedSearch;
