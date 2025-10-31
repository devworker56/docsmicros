// main.js - Documentation portal functionality
document.addEventListener('DOMContentLoaded', function() {
    // Search functionality
    const searchForm = document.querySelector('form[action="search.php"]');
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            const searchInput = this.querySelector('input[name="q"]');
            if (searchInput.value.trim() === '') {
                e.preventDefault();
                searchInput.focus();
            }
        });
    }

    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Auto-expand active sidebar sections
    const activePageLink = document.querySelector('.sidebar-pages a.active');
    if (activePageLink) {
        const parentChapter = activePageLink.closest('.sidebar-chapter');
        if (parentChapter) {
            const chapterLink = parentChapter.querySelector('> a');
            if (chapterLink) {
                chapterLink.classList.add('active');
            }
        }
    }

    // Add copy to clipboard functionality for code blocks
    document.querySelectorAll('pre code').forEach(block => {
        const button = document.createElement('button');
        button.className = 'btn btn-sm btn-outline-secondary copy-btn';
        button.innerHTML = '<i class="fas fa-copy"></i>';
        button.style.position = 'absolute';
        button.style.top = '0.5rem';
        button.style.right = '0.5rem';
        
        const pre = block.closest('pre');
        if (pre) {
            pre.style.position = 'relative';
            pre.appendChild(button);
            
            button.addEventListener('click', function() {
                const text = block.textContent;
                navigator.clipboard.writeText(text).then(() => {
                    button.innerHTML = '<i class="fas fa-check"></i>';
                    setTimeout(() => {
                        button.innerHTML = '<i class="fas fa-copy"></i>';
                    }, 2000);
                });
            });
        }
    });

    // Mobile sidebar toggle
    const sidebar = document.querySelector('.docs-sidebar');
    const sidebarToggle = document.createElement('button');
    sidebarToggle.className = 'btn btn-primary d-lg-none mb-3';
    sidebarToggle.innerHTML = '<i class="fas fa-bars me-2"></i> Menu';
    sidebarToggle.style.width = '100%';
    
    if (sidebar && window.innerWidth < 992) {
        sidebar.parentNode.insertBefore(sidebarToggle, sidebar);
        
        sidebarToggle.addEventListener('click', function() {
            sidebar.style.display = sidebar.style.display === 'none' ? 'block' : 'none';
        });
    }

    // Language switcher persistence
    const langLinks = document.querySelectorAll('a[href*="lang="]');
    langLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            const url = new URL(this.href);
            const lang = url.searchParams.get('lang');
            // Language will be handled by PHP session
        });
    });
});

// Utility function for admin features
function confirmDelete(message) {
    return confirm(message || 'Are you sure you want to delete this item?');
}