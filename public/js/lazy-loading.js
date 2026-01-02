/**
 * Lazy Loading for Images
 */
(function() {
    'use strict';
    
    // Check if IntersectionObserver is supported
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    const dataSrc = img.getAttribute('data-src');
                    
                    if (dataSrc) {
                        img.src = dataSrc;
                        img.classList.remove('lazy-load');
                        img.classList.add('lazy-loaded');
                        observer.unobserve(img);
                    }
                }
            });
        }, {
            rootMargin: '50px' // Start loading 50px before image enters viewport
        });
        
        // Observe all lazy-load images
        document.addEventListener('DOMContentLoaded', function() {
            const lazyImages = document.querySelectorAll('img.lazy-load, img[loading="lazy"]');
            lazyImages.forEach(img => {
                imageObserver.observe(img);
            });
        });
    } else {
        // Fallback for browsers without IntersectionObserver
        document.addEventListener('DOMContentLoaded', function() {
            const lazyImages = document.querySelectorAll('img.lazy-load, img[loading="lazy"]');
            
            const loadImages = () => {
                lazyImages.forEach(img => {
                    const rect = img.getBoundingClientRect();
                    if (rect.top < window.innerHeight + 100) {
                        const dataSrc = img.getAttribute('data-src');
                        if (dataSrc) {
                            img.src = dataSrc;
                            img.classList.remove('lazy-load');
                            img.classList.add('lazy-loaded');
                        }
                    }
                });
            };
            
            loadImages();
            window.addEventListener('scroll', loadImages);
            window.addEventListener('resize', loadImages);
        });
    }
    
    // Add loading placeholder styles
    const style = document.createElement('style');
    style.textContent = `
        img.lazy-load {
            opacity: 0;
            transition: opacity 0.3s;
            background: #f0f0f0;
        }
        img.lazy-loaded {
            opacity: 1;
        }
        img.lazy-load[src*="data:image"] {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
        }
        @keyframes loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
    `;
    document.head.appendChild(style);
})();




