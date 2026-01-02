/* ============================================
   Admin Panel Enhancements JavaScript
   ============================================ */

(function() {
    'use strict';

    // ========================
    // Dark Mode Toggle
    // ========================
    const DarkMode = {
        init: function() {
            // Check for saved theme preference or default to light mode
            const currentTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', currentTheme);
            if (document.body) {
                document.body.setAttribute('data-theme', currentTheme);
            }
            
            // Get dark mode toggle button
            const toggleBtn = document.getElementById('darkModeToggle') || document.querySelector('.dark-mode-toggle');
            
            if (toggleBtn) {
                // Remove any existing listeners
                const newBtn = toggleBtn.cloneNode(true);
                toggleBtn.parentNode.replaceChild(newBtn, toggleBtn);
                
                // Add event listener if button already exists
                newBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    console.log('Dark mode button clicked!');
                    this.toggle();
                }, { once: false, passive: false });
                
                // Also use onclick as backup
                newBtn.onclick = (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    console.log('Dark mode button clicked (onclick)!');
                    this.toggle();
                    return false;
                };
                
                // Update icon based on current theme
                this.updateIcon();
            } else {
                // Try to create button if it doesn't exist
                this.createToggleButton();
            }
        },
        
        createToggleButton: function() {
            // Try admin panel header first
            let header = document.querySelector('.main-header .user-menu');
            let insertBefore = header ? header.querySelector('.user-info') : null;
            
            // If not found, try front-end header
            if (!header) {
                header = document.querySelector('.header-buttons');
                insertBefore = header ? header.querySelector('.profile-btn, .btn-header') : null;
            }
            
            if (!header) return;
            
            const toggleBtn = document.createElement('button');
            toggleBtn.id = 'darkModeToggle';
            toggleBtn.className = 'dark-mode-toggle';
            toggleBtn.innerHTML = '<i class="fas fa-moon"></i>';
            
            // Different styles for admin vs front
            if (document.querySelector('.main-header .user-menu')) {
                // Admin panel style
                toggleBtn.style.cssText = `
                    background: rgba(255,255,255,0.15);
                    border: none;
                    color: white;
                    padding: 8px 12px;
                    border-radius: 8px;
                    cursor: pointer;
                    font-size: 18px;
                    transition: all 0.3s ease;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    min-width: 40px;
                    min-height: 40px;
                `;
            } else {
                // Front-end style
                toggleBtn.style.cssText = `
                    background: rgba(116, 36, 169, 0.1);
                    border: none;
                    color: var(--primary-color);
                    padding: 8px 12px;
                    border-radius: 8px;
                    cursor: pointer;
                    font-size: 18px;
                    transition: all 0.3s ease;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    min-width: 40px;
                    min-height: 40px;
                    margin-left: 10px;
                `;
            }
            
            toggleBtn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                console.log('Dark mode button clicked (created)!');
                this.toggle();
            }, { once: false, passive: false });
            
            // Also use onclick as backup
            toggleBtn.onclick = (e) => {
                e.preventDefault();
                e.stopPropagation();
                console.log('Dark mode button clicked (onclick created)!');
                this.toggle();
                return false;
            };
            
            // Insert button
            if (insertBefore) {
                header.insertBefore(toggleBtn, insertBefore);
            } else {
                header.insertBefore(toggleBtn, header.firstChild);
            }
            
            // Update icon based on current theme
            this.updateIcon();
        },
        
        toggle: function() {
            const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            
            // Set new theme on both html and body
            document.documentElement.setAttribute('data-theme', newTheme);
            if (document.body) {
                document.body.setAttribute('data-theme', newTheme);
            }
            localStorage.setItem('theme', newTheme);
            
            // Update icon
            this.updateIcon();
            
            // Log for debugging
            console.log('Theme changed to:', newTheme);
            console.log('HTML data-theme:', document.documentElement.getAttribute('data-theme'));
        },
        
        updateIcon: function() {
            const toggleBtn = document.getElementById('darkModeToggle') || document.querySelector('.dark-mode-toggle');
            if (!toggleBtn) return;
            
            const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
            const icon = toggleBtn.querySelector('i');
            
            if (icon) {
                if (currentTheme === 'dark') {
                    icon.className = 'fas fa-sun';
                } else {
                    icon.className = 'fas fa-moon';
                }
            }
        }
    };

    // ========================
    // Smart Search
    // ========================
    const SmartSearch = {
        init: function() {
            const searchContainer = document.querySelector('.smart-search-container');
            if (!searchContainer) {
                console.log('Smart search container not found');
                return;
            }
            
            const searchInput = searchContainer.querySelector('.smart-search-input');
            const resultsContainer = searchContainer.querySelector('.smart-search-results');
            
            if (!searchInput || !resultsContainer) {
                console.log('Search input or results container not found');
                return;
            }
            
            console.log('Smart search initialized successfully');
            
            let searchTimeout;
            
            searchInput.addEventListener('input', (e) => {
                clearTimeout(searchTimeout);
                const query = e.target.value.trim();
                
                console.log('Input event triggered, query:', query);
                
                if (query.length < 2) {
                    resultsContainer.classList.remove('show');
                    resultsContainer.innerHTML = '';
                    return;
                }
                
                searchTimeout = setTimeout(() => {
                    console.log('Timeout triggered, performing search');
                    this.performSearch(query, resultsContainer);
                }, 300);
            });
            
            // Also handle Enter key
            searchInput.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    clearTimeout(searchTimeout);
                    const query = e.target.value.trim();
                    if (query.length >= 2) {
                        this.performSearch(query, resultsContainer);
                    }
                }
            });
            
            // Close results when clicking outside
            document.addEventListener('click', (e) => {
                if (!searchContainer.contains(e.target)) {
                    resultsContainer.classList.remove('show');
                }
            });
        },
        
        performSearch: function(query, resultsContainer) {
            console.log('Starting search for:', query);
            
            // Show loading state
            resultsContainer.innerHTML = '<div class="smart-search-result-item"><div class="text-center p-3">جاري البحث...</div></div>';
            resultsContainer.classList.add('show');
            
            // Perform AJAX search
            const searchUrl = `/admin/search?q=${encodeURIComponent(query)}`;
            console.log('Search URL:', searchUrl);
            
            fetch(searchUrl, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
            })
            .then(response => {
                console.log('Response status:', response.status);
                console.log('Response ok:', response.ok);
                
                if (!response.ok) {
                    return response.text().then(text => {
                        console.error('Error response:', text);
                        throw new Error(`HTTP error! status: ${response.status}`);
                    });
                }
                return response.json();
            })
            .then(data => {
                console.log('Search results:', data);
                this.displayResults(data, resultsContainer);
            })
            .catch(error => {
                console.error('Search error:', error);
                resultsContainer.innerHTML = '<div class="smart-search-result-item"><div class="text-center p-3 text-danger">حدث خطأ أثناء البحث: ' + error.message + '</div></div>';
            });
        },
        
        displayResults: function(data, resultsContainer) {
            if (!data.results || data.results.length === 0) {
                resultsContainer.innerHTML = '<div class="smart-search-result-item"><div class="text-center p-3 text-muted">لا توجد نتائج</div></div>';
                resultsContainer.classList.add('show');
                return;
            }
            
            let html = '';
            data.results.forEach(item => {
                html += `
                    <a href="${item.url}" class="smart-search-result-item" style="text-decoration: none; display: flex; align-items: center; gap: 12px;">
                        <div class="result-icon">
                            <i class="fas ${item.icon}"></i>
                        </div>
                        <div style="flex: 1;">
                            <div class="result-title">${item.title}</div>
                            <div class="result-subtitle">${item.subtitle || ''}</div>
                        </div>
                    </a>
                `;
            });
            
            resultsContainer.innerHTML = html;
            resultsContainer.classList.add('show');
        }
    };

    // ========================
    // Toast Notifications
    // ========================
    const Toast = {
        container: null,
        
        init: function() {
            // Create toast container
            this.container = document.createElement('div');
            this.container.className = 'toast-container';
            document.body.appendChild(this.container);
            
            // Convert existing alerts to toasts
            this.convertAlerts();
        },
        
        show: function(message, type = 'info', title = null) {
            if (!this.container) this.init();
            
            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            
            const icons = {
                success: 'fa-check-circle',
                error: 'fa-exclamation-circle',
                warning: 'fa-exclamation-triangle',
                info: 'fa-info-circle'
            };
            
            const titles = {
                success: 'نجح',
                error: 'خطأ',
                warning: 'تحذير',
                info: 'معلومة'
            };
            
            toast.innerHTML = `
                <div class="toast-icon">
                    <i class="fas ${icons[type] || icons.info}"></i>
                </div>
                <div class="toast-content">
                    <div class="toast-title">${title || titles[type]}</div>
                    <div class="toast-message">${message}</div>
                </div>
                <button class="toast-close" onclick="this.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            `;
            
            this.container.appendChild(toast);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                toast.classList.add('hiding');
                setTimeout(() => toast.remove(), 300);
            }, 5000);
        },
        
        convertAlerts: function() {
            // Convert session alerts
            const alerts = document.querySelectorAll('.alert-modern');
            alerts.forEach(alert => {
                const text = alert.textContent.trim();
                let type = 'info';
                
                if (alert.classList.contains('alert-success')) type = 'success';
                else if (alert.classList.contains('alert-danger')) type = 'error';
                else if (alert.classList.contains('alert-warning')) type = 'warning';
                
                if (text) {
                    this.show(text, type);
                    alert.remove();
                }
            });
        }
    };

    // ========================
    // Progress Indicator
    // ======================== 
    const Progress = {
        overlay: null,
        
        init: function() {
            this.overlay = document.createElement('div');
            this.overlay.className = 'progress-overlay';
            this.overlay.innerHTML = `
                <div style="text-align: center;">
                    <div class="progress-spinner"></div>
                    <div class="progress-text">جاري المعالجة...</div>
                </div>
            `;
            document.body.appendChild(this.overlay);
        },
        
        show: function(message = 'جاري المعالجة...') {
            if (!this.overlay) this.init();
            
            const textEl = this.overlay.querySelector('.progress-text');
            if (textEl) textEl.textContent = message;
            
            this.overlay.classList.add('show');
        },
        
        hide: function() {
            if (this.overlay) {
                this.overlay.classList.remove('show');
            }
        }
    };

    // ========================
    // Pull to Refresh
    // ========================
    const PullToRefresh = {
        init: function() {
            if (window.innerWidth > 768) return; // Only on mobile
            
            const content = document.querySelector('.content');
            if (!content) return;
            
            let startY = 0;
            let currentY = 0;
            let isPulling = false;
            
            const refreshIndicator = document.createElement('div');
            refreshIndicator.className = 'pull-to-refresh';
            refreshIndicator.innerHTML = `
                <div class="pull-to-refresh-icon"></div>
                <div class="pull-to-refresh-text">اسحب للتحديث</div>
            `;
            content.insertBefore(refreshIndicator, content.firstChild);
            
            content.addEventListener('touchstart', (e) => {
                if (window.scrollY === 0) {
                    startY = e.touches[0].clientY;
                    isPulling = true;
                }
            });
            
            content.addEventListener('touchmove', (e) => {
                if (!isPulling) return;
                
                currentY = e.touches[0].clientY;
                const pullDistance = currentY - startY;
                
                if (pullDistance > 0 && pullDistance < 100) {
                    refreshIndicator.style.top = `${pullDistance - 60}px`;
                    refreshIndicator.classList.add('active');
                }
            });
            
            content.addEventListener('touchend', () => {
                if (isPulling && currentY - startY > 80) {
                    refreshIndicator.querySelector('.pull-to-refresh-text').textContent = 'جاري التحديث...';
                    window.location.reload();
                } else {
                    refreshIndicator.classList.remove('active');
                    refreshIndicator.style.top = '-60px';
                }
                
                isPulling = false;
                startY = 0;
                currentY = 0;
            });
        }
    };

    // ========================
    // Bottom Navigation (Mobile)
    // ========================
    const BottomNav = {
        init: function() {
            if (window.innerWidth > 768) return;
            
            const sidebar = document.querySelector('.sidebar');
            if (!sidebar) return;
            
            const nav = document.createElement('div');
            nav.className = 'bottom-nav';
            
            // Get main menu items
            const menuItems = sidebar.querySelectorAll('.menu-section > a');
            const mainItems = Array.from(menuItems).slice(0, 5); // First 5 items
            
            mainItems.forEach(item => {
                const navItem = document.createElement('a');
                navItem.className = 'bottom-nav-item';
                navItem.href = item.href;
                
                const icon = item.querySelector('i');
                const text = item.querySelector('span');
                
                navItem.innerHTML = `
                    <i class="${icon ? icon.className : 'fas fa-circle'}"></i>
                    <span>${text ? text.textContent : ''}</span>
                `;
                
                if (item.classList.contains('active')) {
                    navItem.classList.add('active');
                }
                
                nav.appendChild(navItem);
            });
            
            document.body.appendChild(nav);
        }
    };

    // ========================
    // Swipe Gestures
    // ========================
    const SwipeGestures = {
        init: function() {
            if (window.innerWidth > 768) return;
            
            const sidebar = document.getElementById('sidebar');
            if (!sidebar) return;
            
            let startX = 0;
            let startY = 0;
            let isSwiping = false;
            
            document.addEventListener('touchstart', (e) => {
                startX = e.touches[0].clientX;
                startY = e.touches[0].clientY;
                isSwiping = true;
            });
            
            document.addEventListener('touchmove', (e) => {
                if (!isSwiping) return;
                
                const currentX = e.touches[0].clientX;
                const currentY = e.touches[0].clientY;
                const diffX = startX - currentX;
                const diffY = startY - currentY;
                
                // Swipe right to open sidebar
                if (diffX < -50 && Math.abs(diffY) < 50) {
                    sidebar.classList.add('show');
                    isSwiping = false;
                }
                // Swipe left to close sidebar
                else if (diffX > 50 && Math.abs(diffY) < 50) {
                    sidebar.classList.remove('show');
                    isSwiping = false;
                }
            });
            
            document.addEventListener('touchend', () => {
                isSwiping = false;
            });
        }
    };

    // ========================
    // Form Progress Indicator
    // ========================
    const FormProgress = {
        init: function() {
            const forms = document.querySelectorAll('form');
            
            forms.forEach(form => {
                form.addEventListener('submit', (e) => {
                    // Check if form has file inputs or will take time
                    const hasFiles = form.querySelector('input[type="file"]');
                    const isLongForm = form.querySelectorAll('input, textarea, select').length > 10;
                    
                    if (hasFiles || isLongForm) {
                        Progress.show('جاري الحفظ...');
                    }
                });
            });
        }
    };

    // ========================
    // Initialize All
    // ========================
    // Initialize immediately if DOM is already loaded
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            initializeAll();
        });
    } else {
        // DOM is already loaded
        initializeAll();
    }
    
    function initializeAll() {
        DarkMode.init();
        SmartSearch.init();
        Toast.init();
        Progress.init();
        PullToRefresh.init();
        BottomNav.init();
        SwipeGestures.init();
        FormProgress.init();
        
        // Add fade-in animation to content
        const content = document.querySelector('.content');
        if (content) {
            content.classList.add('fade-in');
        }
    }

    // Export to global scope
    window.Toast = Toast;
    window.Progress = Progress;
    window.SmartSearch = SmartSearch;
    
    // Initialize on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM Content Loaded - Initializing modules');
            DarkMode.init();
            SmartSearch.init();
            Toast.init();
            Progress.init();
            PullToRefresh.init();
            BottomNav.init();
            SwipeGestures.init();
            FormProgress.init();
        });
    } else {
        // DOM already loaded
        console.log('DOM already loaded - Initializing modules');
        DarkMode.init();
        SmartSearch.init();
        Toast.init();
        Progress.init();
        PullToRefresh.init();
        BottomNav.init();
        SwipeGestures.init();
        FormProgress.init();
    }
})();

