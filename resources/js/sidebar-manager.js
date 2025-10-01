/**
 * Sidebar Dropdown Manager
 * Handles all sidebar dropdown functionality with improved reliability
 */

class SidebarManager {
    constructor() {
        this.dropdowns = new Map();
        this.isInitialized = false;
        this.debugMode = false; // Set to true for debugging
        
        this.userRole = (window.currentUserRole || 'guest').toLowerCase();

        // Wait for DOM to be ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.init());
        } else {
            this.init();
        }
    }
    
    init() {
        if (this.isInitialized) return;
        
        this.log('Initializing Sidebar Manager...');
        
        // Clean up old localStorage keys
        this.migrateOldKeys();

        this.filterMenuByRole();
        
        // Initialize all dropdowns
        this.initializeDropdowns();
        
        // Set up event listeners
        this.setupEventListeners();
        
        // Mark as initialized
        this.isInitialized = true;
        
        this.log('Sidebar Manager initialized successfully');
    }
    
    migrateOldKeys() {
        const keys = Object.keys(localStorage);
        let migrated = 0;
        
        keys.forEach(key => {
            if (key.startsWith('sidebar_')) {
                const newKey = key.replace('sidebar_', 'dropdown_');
                const value = localStorage.getItem(key);
                localStorage.setItem(newKey, value);
                localStorage.removeItem(key);
                migrated++;
            }
        });
        
        if (migrated > 0) {
            this.log(`Migrated ${migrated} old localStorage keys`);
        }
    }

    filterMenuByRole() {
    // Remove menu items the current role isn’t allowed to see
    document.querySelectorAll('[data-roles]').forEach(el => {
        const allowed = (el.getAttribute('data-roles') || '')
            .split(',')
            .map(r => r.trim().toLowerCase())
            .filter(Boolean);
        if (!allowed.includes(this.userRole)) {
            el.remove();
        }
    });

    // Remove empty dropdown groups (and their toggles) after filtering
    document.querySelectorAll('.collapse').forEach(group => {
        // If group has no visible <li.nav-item>, remove it and its toggle
        const hasAnyItem = !!group.querySelector('li.nav-item');
        if (!hasAnyItem) {
            const toggle = document.querySelector(`.nav-link-collapse[href="#${group.id}"]`);
            if (toggle) toggle.remove();
            group.remove();
        }
    });

    // Clean up saved states for groups that no longer exist
    Object.keys(localStorage).forEach(k => {
        if (k.startsWith('dropdown_')) {
            const sel = k.replace('dropdown_', ''); // e.g. '#supportMenu'
            if (!document.querySelector(sel)) {
                localStorage.removeItem(k);
            }
        }
    });
}
    
    initializeDropdowns() {
        const toggles = document.querySelectorAll('.nav-link-collapse');
        
        toggles.forEach(toggle => {
            const targetId = toggle.getAttribute('href');
            const target = document.querySelector(targetId);
            
            if (!target) {
                this.log(`Warning: Target not found for ${targetId}`, 'warn');
                return;
            }
            
            // Remove Bootstrap's auto-initialization
            toggle.removeAttribute('data-bs-toggle');
            
            // Create Bootstrap Collapse instance
            let bsCollapse;
            try {
                bsCollapse = new bootstrap.Collapse(target, {
                    toggle: false
                });
            } catch (error) {
                this.log(`Error creating collapse for ${targetId}: ${error.message}`, 'error');
                return;
            }
            
            // Store the instance
            this.dropdowns.set(targetId, {
                toggle: toggle,
                target: target,
                collapse: bsCollapse
            });
            
            // Restore saved state
            this.restoreDropdownState(targetId);
        });
        
        this.log(`Initialized ${this.dropdowns.size} dropdowns`);
    }
    
    restoreDropdownState(targetId) {
        const dropdown = this.dropdowns.get(targetId);
        if (!dropdown) return;
        
        const savedState = localStorage.getItem('dropdown_' + targetId);
        const hasActiveChild = dropdown.target.querySelector('.nav-link.active') !== null;
        
        let shouldOpen = false;
        
        // Determine if dropdown should be open
        if (savedState === 'open') {
            shouldOpen = true;
            this.log(`${targetId}: Opening (saved state)`);
        } else if (hasActiveChild && savedState !== 'closed') {
            shouldOpen = true;
            this.log(`${targetId}: Opening (has active child)`);
            // Save this state
            localStorage.setItem('dropdown_' + targetId, 'open');
        } else {
            this.log(`${targetId}: Keeping closed`);
        }
        
        // Apply state
        if (shouldOpen) {
            try {
                dropdown.collapse.show();
                dropdown.toggle.setAttribute('aria-expanded', 'true');
            } catch (error) {
                this.log(`Error opening ${targetId}: ${error.message}`, 'error');
            }
        } else {
            dropdown.toggle.setAttribute('aria-expanded', 'false');
        }
    }
    
    setupEventListeners() {
        // Handle dropdown toggle clicks
        this.dropdowns.forEach((dropdown, targetId) => {
            dropdown.toggle.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                this.toggleDropdown(targetId);
            });
        });
        
        // Listen for Bootstrap collapse events
        document.querySelectorAll('.collapse').forEach(collapseEl => {
            collapseEl.addEventListener('shown.bs.collapse', (e) => {
                this.handleCollapseShown(e.target);
            });
            
            collapseEl.addEventListener('hidden.bs.collapse', (e) => {
                this.handleCollapseHidden(e.target);
            });
        });
        
        // Prevent dropdowns from closing when clicking inside
        document.addEventListener('click', (e) => {
            if (e.target.closest('.collapse-menu')) {
                e.stopPropagation();
            }
        });
        
        // Handle window resize
        let resizeTimer;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(() => this.handleResize(), 250);
        });
    }
    
    toggleDropdown(targetId) {
        const dropdown = this.dropdowns.get(targetId);
        if (!dropdown) return;
        
        const isOpen = dropdown.target.classList.contains('show');
        
        this.log(`Toggling ${targetId}: ${isOpen ? 'closing' : 'opening'}`);
        
        try {
            if (isOpen) {
                dropdown.collapse.hide();
                localStorage.setItem('dropdown_' + targetId, 'closed');
            } else {
                dropdown.collapse.show();
                localStorage.setItem('dropdown_' + targetId, 'open');
            }
        } catch (error) {
            this.log(`Error toggling ${targetId}: ${error.message}`, 'error');
        }
    }
    
    handleCollapseShown(target) {
        const targetId = '#' + target.id;
        const dropdown = this.dropdowns.get(targetId);
        
        if (dropdown) {
            dropdown.toggle.setAttribute('aria-expanded', 'true');
            const chevron = dropdown.toggle.querySelector('.fa-chevron-down');
            if (chevron) {
                chevron.style.transform = 'rotate(180deg)';
            }
        }
    }
    
    handleCollapseHidden(target) {
        const targetId = '#' + target.id;
        const dropdown = this.dropdowns.get(targetId);
        
        if (dropdown) {
            dropdown.toggle.setAttribute('aria-expanded', 'false');
            const chevron = dropdown.toggle.querySelector('.fa-chevron-down');
            if (chevron) {
                chevron.style.transform = 'rotate(0deg)';
            }
        }
    }
    
    handleResize() {
        // Re-apply saved states after resize
        this.dropdowns.forEach((dropdown, targetId) => {
            const savedState = localStorage.getItem('dropdown_' + targetId);
            if (savedState === 'open' && !dropdown.target.classList.contains('show')) {
                try {
                    dropdown.collapse.show();
                    dropdown.toggle.setAttribute('aria-expanded', 'true');
                } catch (error) {
                    this.log(`Error restoring ${targetId} after resize: ${error.message}`, 'error');
                }
            }
        });
    }
    
    // Utility method for debugging
    log(message, level = 'info') {
        if (!this.debugMode) return;
        
        const timestamp = new Date().toLocaleTimeString();
        const prefix = `[SidebarManager ${timestamp}]`;
        
        switch(level) {
            case 'error':
                console.error(prefix, message);
                break;
            case 'warn':
                console.warn(prefix, message);
                break;
            default:
                console.log(prefix, message);
        }
    }
    
    // Public API methods
    openDropdown(targetId) {
        const dropdown = this.dropdowns.get(targetId);
        if (dropdown && !dropdown.target.classList.contains('show')) {
            dropdown.collapse.show();
            localStorage.setItem('dropdown_' + targetId, 'open');
        }
    }
    
    closeDropdown(targetId) {
        const dropdown = this.dropdowns.get(targetId);
        if (dropdown && dropdown.target.classList.contains('show')) {
            dropdown.collapse.hide();
            localStorage.setItem('dropdown_' + targetId, 'closed');
        }
    }
    
    closeAllDropdowns() {
        this.dropdowns.forEach((dropdown, targetId) => {
            this.closeDropdown(targetId);
        });
    }
    
    openAllDropdowns() {
        this.dropdowns.forEach((dropdown, targetId) => {
            this.openDropdown(targetId);
        });
    }
    
    resetDropdowns() {
        // Clear all saved states
        const keys = Object.keys(localStorage);
        keys.forEach(key => {
            if (key.startsWith('dropdown_')) {
                localStorage.removeItem(key);
            }
        });
        
        // Reinitialize
        this.dropdowns.forEach((dropdown, targetId) => {
            this.restoreDropdownState(targetId);
        });
    }
    
    // Enable debug mode
    enableDebug() {
        this.debugMode = true;
        this.log('Debug mode enabled');
    }
    
    // Disable debug mode
    disableDebug() {
        this.log('Debug mode disabled');
        this.debugMode = false;
    }
}

// Create global instance
window.sidebarManager = new SidebarManager();

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = SidebarManager;
}