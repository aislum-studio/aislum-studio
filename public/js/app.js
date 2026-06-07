/**
 * Aislum Studio - Main Application JavaScript
 * 
 * Handles common functionality and interactions
 */

$(document).ready(function() {
    console.log('Aislum Studio initialized');
    
    // Initialize tooltips and other features
    initializeApp();
});

/**
 * Initialize application features
 */
function initializeApp() {
    // Add any global event listeners or initializations here
    
    // Example: Auto-hide flash messages after 5 seconds
    $('.alert').each(function() {
        var $alert = $(this);
        setTimeout(function() {
            $alert.fadeOut('slow', function() {
                $(this).remove();
            });
        }, 5000);
    });
    
    // Add active class to current navigation item
    highlightCurrentNav();
}

/**
 * Highlight the current navigation item
 */
function highlightCurrentNav() {
    var currentPage = getUrlParameter('page') || 'dashboard';
    var $navLinks = $('.nav-links a');
    
    $navLinks.each(function() {
        var href = $(this).attr('href');
        if (href.includes('page=' + currentPage)) {
            $(this).css('color', '#667eea').css('font-weight', 'bold');
        }
    });
}

/**
 * Get URL parameter value
 * 
 * @param {string} param Parameter name
 * @returns {string|null} Parameter value or null
 */
function getUrlParameter(param) {
    var url = new URL(window.location);
    return url.searchParams.get(param);
}

/**
 * Show notification
 * 
 * @param {string} message Message to display
 * @param {string} type Type of notification (success, error, warning, info)
 */
function showNotification(message, type = 'info') {
    var alertClass = 'alert-' + type;
    var $alert = $('<div class="alert ' + alertClass + '">' + message + '</div>');
    
    $('main').prepend($alert);
    
    // Auto-hide after 5 seconds
    setTimeout(function() {
        $alert.fadeOut('slow', function() {
            $(this).remove();
        });
    }, 5000);
}

/**
 * Confirm action before proceeding
 * 
 * @param {string} message Confirmation message
 * @returns {boolean} True if confirmed, false otherwise
 */
function confirmAction(message) {
    return confirm(message);
}

/**
 * Format date to readable string
 * 
 * @param {string} dateString Date string
 * @returns {string} Formatted date
 */
function formatDate(dateString) {
    var date = new Date(dateString);
    var options = { year: 'numeric', month: 'short', day: 'numeric' };
    return date.toLocaleDateString('en-US', options);
}

/**
 * Validate email address
 * 
 * @param {string} email Email address
 * @returns {boolean} True if valid, false otherwise
 */
function validateEmail(email) {
    var re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

/**
 * Trim whitespace from string
 * 
 * @param {string} str String to trim
 * @returns {string} Trimmed string
 */
function trimString(str) {
    return str.trim();
}
