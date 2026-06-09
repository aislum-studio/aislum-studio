/**
 * Aislum Studio - Documents Module
 * 
 * Handles document search, filtering, and interactions
 */

$(document).ready(function() {
    initializeDocumentsModule();
});

/**
 * Initialize the documents module
 */
function initializeDocumentsModule() {
    // Add event listeners for search and filter
    setupSearchFunctionality();
    setupFilterFunctionality();
}

/**
 * Setup search functionality
 */
function setupSearchFunctionality() {
    // Create search input if it doesn't exist
    var searchHtml = `
        <div class="search-box" style="margin-bottom: 1.5rem;">
            <input type="text" id="documentSearch" placeholder="Search documents..." style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 0.5rem;">
        </div>
    `;
    
    // Insert search box before documents list
    var documentsList = $('.documents-list');
    if (documentsList.length && !$('#documentSearch').length) {
        documentsList.before(searchHtml);
    }
    
    // Handle search input
    $(document).on('keyup', '#documentSearch', function() {
        var query = $(this).val().toLowerCase();
        filterDocuments(query);
    });
}

/**
 * Setup filter functionality
 */
function setupFilterFunctionality() {
    // Handle category filter clicks
    $(document).on('click', '.category-list a', function(e) {
        e.preventDefault();
        
        // Update active state
        $('.category-list a').removeClass('active');
        $(this).addClass('active');
        
        // Get category from URL
        var href = $(this).attr('href');
        window.location.href = href;
    });
}

/**
 * Filter documents based on search query
 * 
 * @param {string} query Search query
 */
function filterDocuments(query) {
    var documentItems = $('.document-item');
    var visibleCount = 0;
    
    documentItems.each(function() {
        var title = $(this).find('.document-title').text().toLowerCase();
        var description = $(this).find('.document-meta').text().toLowerCase();
        var category = $(this).find('.document-category').text().toLowerCase();
        
        if (title.includes(query) || description.includes(query) || category.includes(query)) {
            $(this).show();
            visibleCount++;
        } else {
            $(this).hide();
        }
    });
    
    // Show/hide empty state
    if (visibleCount === 0) {
        var emptyHtml = '<div class="empty-state"><p>No documents match your search.</p></div>';
        if ($('.documents-list .empty-state').length === 0) {
            $('.documents-list').append(emptyHtml);
        }
        $('.documents-list .empty-state').show();
    } else {
        $('.documents-list .empty-state').hide();
    }
}

/**
 * Delete document with confirmation
 * 
 * @param {int} documentId Document ID
 */
function deleteDocument(documentId) {
    if (confirm('Are you sure you want to delete this document? This action cannot be undone.')) {
        // Submit delete form
        $('form[data-document-id="' + documentId + '"]').submit();
    }
}

/**
 * Download document
 * 
 * @param {string} filePath File path
 */
function downloadDocument(filePath) {
    window.location.href = '/uploads/' + filePath;
}

/**
 * Format file size
 * 
 * @param {int} bytes File size in bytes
 * @returns {string} Formatted file size
 */
function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    
    var k = 1024;
    var sizes = ['Bytes', 'KB', 'MB', 'GB'];
    var i = Math.floor(Math.log(bytes) / Math.log(k));
    
    return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
}

/**
 * Get file icon based on extension
 * 
 * @param {string} fileType File type/extension
 * @returns {string} Icon emoji
 */
function getFileIcon(fileType) {
    var icons = {
        'pdf': '📄',
        'doc': '📝',
        'docx': '📝',
        'xls': '📊',
        'xlsx': '📊',
        'ppt': '🎯',
        'pptx': '🎯',
        'txt': '📋',
        'jpg': '🖼️',
        'jpeg': '🖼️',
        'png': '🖼️',
        'gif': '🖼️',
        'zip': '📦',
        'rar': '📦'
    };
    
    return icons[fileType.toLowerCase()] || '📎';
}
