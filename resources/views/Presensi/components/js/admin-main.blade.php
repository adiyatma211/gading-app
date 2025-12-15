/**
 * Admin Main JavaScript Component
 * Initializes all components and handles global functionality
 */

/**
 * Initialize all components when DOM is ready
 */
async function initializeComponents() {
    // Initialize map first
    if (typeof initMap === 'function') {
        initMap();
    }
    
    // Load all data
    if (typeof loadStores === 'function') {
        await loadStores();
    }
    
    if (typeof loadSetting === 'function') {
        await loadSetting();
    }
    
    if (typeof loadHolidays === 'function') {
        await loadHolidays();
    }
    
    if (typeof loadApprovals === 'function') {
        await loadApprovals();
    }
}

// Initialize when DOM is fully loaded
document.addEventListener('DOMContentLoaded', initializeComponents);