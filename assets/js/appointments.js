// Wait for DOM content to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    
    // Add animation to buttons with btn-action class
    const actionButtons = document.querySelectorAll('.btn-action');
    actionButtons.forEach(button => {
        button.addEventListener('mouseenter', function() {
            this.classList.add('btn-pulse');
        });
        
        button.addEventListener('mouseleave', function() {
            this.classList.remove('btn-pulse');
        });
    });
    
    // Enhance cancel confirmation
    const cancelButtons = document.querySelectorAll('.cancel-appointment');
    cancelButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to cancel this appointment? This action cannot be undone.')) {
                e.preventDefault();
            }
        });
    });
    
    // Search form validation
    const searchForm = document.querySelector('.search-form');
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            const searchInput = this.querySelector('input[name="search"]');
            if (searchInput.value.trim() === '') {
                e.preventDefault();
                searchInput.classList.add('is-invalid');
                
                // Remove invalid class after 3 seconds
                setTimeout(() => {
                    searchInput.classList.remove('is-invalid');
                }, 3000);
            }
        });
    }
    
    // Add tooltips to status badges
    const statusBadges = document.querySelectorAll('.status-badge');
    statusBadges.forEach(badge => {
        const status = badge.textContent.trim();
        let tooltipText = '';
        
        if (status === 'Scheduled') {
            tooltipText = 'Your appointment is confirmed';
        } else if (status === 'Completed') {
            tooltipText = 'This appointment has been completed';
        } else if (status === 'Cancelled') {
            tooltipText = 'This appointment was cancelled';
        }
        
        if (tooltipText) {
            badge.setAttribute('title', tooltipText);
            badge.style.cursor = 'help';
        }
    });
    
    // Initialize Bootstrap tooltips
    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
});