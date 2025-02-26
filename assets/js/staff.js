/**
 * Care Compass Staff Dashboard JavaScript
 * Handles carousel initialization and active navigation highlighting
 */

$(document).ready(function() {
    // Initialize the owl carousel for the banner
    $("#banner-slider").owlCarousel({
        items: 1,
        itemsDesktop: [1199, 1],
        itemsDesktopSmall: [979, 1],
        itemsTablet: [768, 1],
        itemsMobile: [479, 1],
        navigation: true,
        navigationText: ["<i class='fa fa-angle-left'></i>","<i class='fa fa-angle-right'></i>"],
        pagination: true,
        autoPlay: true,
        autoPlayTimeout: 5000,
        autoPlayHoverPause: true,
        slideSpeed: 800,
        paginationSpeed: 800,
        rewindSpeed: 1000,
        singleItem: true,
        transitionStyle: "fade"
    });
    
    // Highlight active navigation item
    highlightActiveNavItem();
});

/**
 * Highlights the current page in the navigation menu
 */
function highlightActiveNavItem() {
    const currentPage = window.location.pathname.split('/').pop();
    const navLinks = document.querySelectorAll('.nav-link');
    
    navLinks.forEach(link => {
        if (link.getAttribute('href') === currentPage) {
            link.classList.add('active');
        }
    });
}