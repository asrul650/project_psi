// JavaScript untuk fungsionalitas interaktif di sini 

// Landing page interactions

// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    // Hover effect for explore button
    const exploreBtn = document.querySelector('.btn-explore');
    if (exploreBtn) {
        exploreBtn.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
            this.style.boxShadow = '0 12px 25px rgba(0, 0, 0, 0.6)';
        });
        exploreBtn.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '0 8px 20px rgba(0, 0, 0, 0.4)';
        });
    }

    // Hover effect for detail buttons in spotlight cards
    const detailBtns = document.querySelectorAll('.btn-detail');
    detailBtns.forEach(btn => {
        btn.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-3px)';
            this.style.boxShadow = '0 8px 15px rgba(0, 0, 0, 0.4)';
        });
        btn.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '0 4px 10px rgba(0, 0, 0, 0.3)';
        });
    });

    // Hover effect for all heroes button
    const allHeroesBtn = document.querySelector('.btn-all-heroes');
    if (allHeroesBtn) {
        allHeroesBtn.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-3px)';
            this.style.boxShadow = '0 8px 15px rgba(0, 0, 0, 0.4)';
        });
        allHeroesBtn.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '0 4px 10px rgba(0, 0, 0, 0.3)';
        });
    }

    // Add click functionality for navigation links
    const navLinks = document.querySelectorAll('nav a');
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            // Only prevent default for internal links that should be handled
            const href = this.getAttribute('href');
            // Jangan preventDefault untuk logout.php
            if ((href === '#' || href === 'index.php') && !href.includes('logout.php')) {
                e.preventDefault();
                // For home link, just scroll to top
                if (href === 'index.php') {
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            }
            // For other links (like auth.php, logout.php), let them work normally
        });
    });

    // Add click functionality for spotlight cards
    const spotlightCards = document.querySelectorAll('.spotlight-card');
    spotlightCards.forEach(card => {
        card.addEventListener('click', function() {
            // Add your hero detail page navigation logic here
            console.log('Hero card clicked:', this.querySelector('h4').textContent);
            // Example: window.location.href = 'User/detailhero.php?hero=' + heroName;
        });
    });

    // Optional: Add scroll animation for features section
    const featuresSection = document.querySelector('.features');
    if (featuresSection) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, { threshold: 0.1 });
        
        observer.observe(featuresSection);
    }

    // Add animation for feature items
    const featureItems = document.querySelectorAll('.feature-item');
    if (featureItems.length > 0) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, { threshold: 0.1 });
        
        featureItems.forEach(item => {
            item.style.opacity = '0';
            item.style.transform = 'translateY(30px)';
            item.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            observer.observe(item);
        });
    }

    // Mobile menu toggle functionality
    const navToggle = document.getElementById('nav-toggle');
    const navToggleLabel = document.querySelector('.nav-toggle-label');
    
    if (navToggle && navToggleLabel) {
        // Close mobile menu when clicking outside
        document.addEventListener('click', function(e) {
            if (!navToggleLabel.contains(e.target) && !navToggle.checked) {
                return;
            }
            
            if (!navToggleLabel.contains(e.target) && navToggle.checked) {
                navToggle.checked = false;
            }
        });

        // Close mobile menu when clicking on a link
        navLinks.forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth <= 768) {
                    navToggle.checked = false;
                }
            });
        });
    }
}); 