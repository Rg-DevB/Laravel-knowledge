/**
 * Notification Manager for real-time updates
 * Handles toast notifications and real-time events via Laravel Echo
 */

class NotificationManager {
    constructor() {
        this.toasts = [];
        this.maxToasts = 5;
        this.toastContainer = null;
        this.init();
    }

    init() {
        // Create toast container if it doesn't exist
        if (!document.getElementById('toast-container')) {
            const container = document.createElement('div');
            container.id = 'toast-container';
            container.className = 'fixed top-4 right-4 z-50 flex flex-col gap-2 max-w-sm';
            document.body.appendChild(container);
            this.toastContainer = container;
        } else {
            this.toastContainer = document.getElementById('toast-container');
        }

        // Listen for Livewire events
        document.addEventListener('livewire:init', () => {
            Livewire.on('notification', (data) => {
                this.show(data.message, data.type || 'info', data.title);
            });

            Livewire.on('notify-success', (message, title) => {
                this.show(message, 'success', title);
            });

            Livewire.on('notify-error', (message, title) => {
                this.show(message, 'error', title);
            });

            Livewire.on('notify-warning', (message, title) => {
                this.show(message, 'warning', title);
            });
        });
    }

    /**
     * Show a toast notification
     * @param {string} message - Notification message
     * @param {string} type - Type: success, error, warning, info
     * @param {string} title - Optional title
     */
    show(message, type = 'info', title = '') {
        const toast = this.createToast(message, type, title);
        this.toasts.push(toast);
        this.render();

        // Auto-remove after 5 seconds
        setTimeout(() => {
            this.remove(toast.id);
        }, 5000);
    }

    createToast(message, type, title) {
        return {
            id: Date.now() + Math.random(),
            message,
            type,
            title,
            createdAt: Date.now()
        };
    }

    remove(id) {
        this.toasts = this.toasts.filter(t => t.id !== id);
        this.render();
    }

    clear() {
        this.toasts = [];
        this.render();
    }

    render() {
        // Keep only maxToasts
        if (this.toasts.length > this.maxToasts) {
            this.toasts = this.toasts.slice(-this.maxToasts);
        }

        this.toastContainer.innerHTML = this.toasts.map(toast => {
            const colors = {
                success: 'bg-emerald-500/10 border-emerald-500/30 text-emerald-400',
                error: 'bg-red-500/10 border-red-500/30 text-red-400',
                warning: 'bg-amber-500/10 border-amber-500/30 text-amber-400',
                info: 'bg-blue-500/10 border-blue-500/30 text-blue-400'
            };

            const icons = {
                success: '<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>',
                error: '<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
                warning: '<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>',
                info: '<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/></svg>'
            };

            return `
                <div class="flex items-start gap-3 p-4 rounded-xl border backdrop-blur-sm shadow-lg animate-[slideIn_0.3s_ease-out] ${colors[toast.type] || colors.info}" 
                     role="alert">
                    <div class="flex-shrink-0">${icons[toast.type] || icons.info}</div>
                    <div class="flex-1 min-w-0">
                        ${toast.title ? `<p class="text-sm font-semibold mb-0.5">${this.escapeHtml(toast.title)}</p>` : ''}
                        <p class="text-sm opacity-90">${this.escapeHtml(toast.message)}</p>
                    </div>
                    <button onclick="window.notificationManager.remove(${toast.id})" 
                            class="flex-shrink-0 opacity-60 hover:opacity-100 transition-opacity">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            `;
        }).join('');
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    /**
     * Setup real-time notifications via Laravel Echo
     * @param {number} userId - Authenticated user ID
     */
    setupRealTimeNotifications(userId) {
        if (!window.Echo) {
            console.warn('Laravel Echo not initialized');
            return;
        }

        // Private channel for user-specific notifications
        window.Echo.private(`App.Models.User.${userId}`)
            .notification((notification) => {
                this.show(
                    notification.message || 'You have a new notification',
                    notification.type || 'info',
                    notification.title
                );
            })
            .listen('.solution.added', (event) => {
                this.show('A new solution was posted!', 'info', 'New Solution');
            })
            .listen('.comment.added', (event) => {
                this.show('Someone commented on your post', 'info', 'New Comment');
            })
            .listen('.problem.solved', (event) => {
                this.show('Your problem has been solved!', 'success', 'Problem Solved');
            });

        // Public channel for site-wide announcements
        window.Echo.channel('site-announcements')
            .listen('.announcement.posted', (event) => {
                this.show(event.message, 'info', event.title || 'Announcement');
            });
    }
}

// Initialize global instance
window.notificationManager = new NotificationManager();

export default NotificationManager;
