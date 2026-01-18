/**
 * Dızo Wear - Main JavaScript
 * Theme System, Particles, Animations
 */

document.addEventListener('DOMContentLoaded', function () {
    // Initialize all modules
    ThemeManager.init();
    ParticleSystem.init();
    ScrollEffects.init();
    CartManager.init();
    SearchManager.init();
    NotificationManager.init();
});

/**
 * Theme Manager - Dark/Light Mode
 */
const ThemeManager = {
    init() {
        this.themeToggle = document.querySelector('.theme-toggle');
        this.currentTheme = localStorage.getItem('theme') || 'light';
        this.applyTheme(this.currentTheme);
        this.bindEvents();
    },

    bindEvents() {
        if (this.themeToggle) {
            this.themeToggle.addEventListener('click', () => this.toggle());
        }
    },

    toggle() {
        this.currentTheme = this.currentTheme === 'light' ? 'dark' : 'light';
        this.applyTheme(this.currentTheme);
        localStorage.setItem('theme', this.currentTheme);
    },

    applyTheme(theme) {
        document.documentElement.setAttribute('data-theme', theme);
        ParticleSystem.updateColors();
    }
};

/**
 * Particle System
 */
const ParticleSystem = {
    particles: [],
    container: null,

    init() {
        this.container = document.getElementById('particles-js');
        if (!this.container) {
            this.container = document.createElement('div');
            this.container.id = 'particles-js';
            document.body.prepend(this.container);
        }
        this.createParticles(30);
        this.animate();
    },

    createParticles(count) {
        for (let i = 0; i < count; i++) {
            const particle = document.createElement('div');
            particle.className = 'particle';
            particle.style.cssText = `
                left: ${Math.random() * 100}%;
                top: ${Math.random() * 100 + 100}%;
                animation-delay: ${Math.random() * 20}s;
                animation-duration: ${15 + Math.random() * 15}s;
            `;
            this.container.appendChild(particle);
            this.particles.push(particle);
        }
    },

    updateColors() {
        const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
        this.particles.forEach(p => {
            p.style.background = isDark ? '#fff' : '#000';
        });
    },

    animate() {
        // Handled by CSS animations
    }
};

/**
 * Scroll Effects
 */
const ScrollEffects = {
    init() {
        this.scrollTopBtn = document.querySelector('.scroll-top');
        this.header = document.querySelector('.main-header');
        this.observeElements();
        this.bindEvents();
    },

    bindEvents() {
        window.addEventListener('scroll', () => this.onScroll());
        if (this.scrollTopBtn) {
            this.scrollTopBtn.addEventListener('click', () => this.scrollToTop());
        }
    },

    onScroll() {
        const scrollY = window.scrollY;

        // Scroll to top button visibility
        if (this.scrollTopBtn) {
            this.scrollTopBtn.classList.toggle('visible', scrollY > 500);
        }

        // Header shadow
        if (this.header) {
            this.header.classList.toggle('scrolled', scrollY > 50);
        }
    },

    scrollToTop() {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    },

    observeElements() {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('fade-in');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });

        document.querySelectorAll('.animate-on-scroll').forEach(el => {
            observer.observe(el);
        });
    }
};

/**
 * Cart Manager
 */
const CartManager = {
    init() {
        this.bindEvents();
    },

    bindEvents() {
        document.querySelectorAll('[data-add-to-cart]').forEach(btn => {
            btn.addEventListener('click', (e) => this.handleAddToCart(e));
        });

        document.querySelectorAll('.qty-btn').forEach(btn => {
            btn.addEventListener('click', (e) => this.handleQuantity(e));
        });
    },

    handleAddToCart(e) {
        e.preventDefault();
        const btn = e.currentTarget;
        const productId = btn.dataset.productId;
        const size = document.querySelector('input[name="size"]:checked')?.value;
        const quantity = document.querySelector('.qty-input')?.value || 1;

        if (!size) {
            NotificationManager.show('Lütfen bir beden seçin', 'warning');
            return;
        }

        this.addToCart(productId, size, quantity);
    },

    addToCart(productId, size, quantity = 1) {
        const formData = new FormData();
        formData.append('product_id', productId);
        formData.append('size', size);
        formData.append('quantity', quantity);
        formData.append('csrf_token', window.CONFIG?.csrfToken || '');

        fetch((window.CONFIG?.baseUrl || '') + '/cart/add', {
            method: 'POST',
            body: formData
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    this.updateCartCount(data.cart_count);
                    NotificationManager.show(data.message || 'Ürün sepete eklendi!', 'success');
                } else {
                    NotificationManager.show(data.message || 'Bir hata oluştu', 'error');
                }
            })
            .catch(() => {
                NotificationManager.show('Bağlantı hatası', 'error');
            });
    },

    updateCartCount(count) {
        const cartCount = document.getElementById('cartCount');
        if (cartCount) {
            cartCount.textContent = count;
            cartCount.classList.add('pulse');
            setTimeout(() => cartCount.classList.remove('pulse'), 300);
        }
    },

    handleQuantity(e) {
        const btn = e.currentTarget;
        const input = btn.parentElement.querySelector('.qty-input');
        let value = parseInt(input.value) || 1;

        if (btn.dataset.action === 'increase') {
            value = Math.min(10, value + 1);
        } else {
            value = Math.max(1, value - 1);
        }

        input.value = value;
    }
};

/**
 * Search Manager
 */
const SearchManager = {
    init() {
        this.searchInput = document.getElementById('searchInput');
        this.searchResults = document.getElementById('searchResults');
        this.debounceTimer = null;

        if (this.searchInput) {
            this.bindEvents();
        }
    },

    bindEvents() {
        this.searchInput.addEventListener('input', () => this.handleSearch());
        this.searchInput.addEventListener('focus', () => this.showResults());
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.search-container')) {
                this.hideResults();
            }
        });
    },

    handleSearch() {
        const query = this.searchInput.value.trim();

        clearTimeout(this.debounceTimer);

        if (query.length < 2) {
            this.hideResults();
            return;
        }

        this.debounceTimer = setTimeout(() => {
            this.search(query);
        }, 300);
    },

    search(query) {
        fetch((window.CONFIG?.baseUrl || '') + '/products/search?q=' + encodeURIComponent(query))
            .then(res => res.json())
            .then(data => {
                this.renderResults(data.results || []);
            })
            .catch(() => {
                this.searchResults.innerHTML = '<p class="p-3 text-muted">Arama yapılamadı</p>';
            });
    },

    renderResults(results) {
        if (results.length === 0) {
            this.searchResults.innerHTML = '<p class="p-3 text-muted">Sonuç bulunamadı</p>';
        } else {
            this.searchResults.innerHTML = results.map(r => `
                <a href="${r.url}" class="search-result-item">
                    <img src="${r.image}" alt="${r.name}">
                    <div class="result-info">
                        <strong>${r.name}</strong>
                        <span>${r.price}</span>
                    </div>
                </a>
            `).join('');
        }
        this.showResults();
    },

    showResults() {
        if (this.searchResults && this.searchResults.innerHTML) {
            this.searchResults.style.display = 'block';
        }
    },

    hideResults() {
        if (this.searchResults) {
            this.searchResults.style.display = 'none';
        }
    }
};

/**
 * Notification Manager
 */
const NotificationManager = {
    container: null,

    init() {
        this.container = document.createElement('div');
        this.container.id = 'notification-container';
        this.container.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 99999;
            display: flex;
            flex-direction: column;
            gap: 10px;
        `;
        document.body.appendChild(this.container);
    },

    show(message, type = 'info') {
        const icons = {
            success: 'check-circle-fill',
            error: 'x-circle-fill',
            warning: 'exclamation-triangle-fill',
            info: 'info-circle-fill'
        };

        const colors = {
            success: '#10b981',
            error: '#ef4444',
            warning: '#f59e0b',
            info: '#3b82f6'
        };

        const notification = document.createElement('div');
        notification.style.cssText = `
            background: ${colors[type]};
            color: white;
            padding: 16px 24px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            transform: translateX(120%);
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            font-weight: 500;
            font-size: 14px;
        `;
        notification.innerHTML = `
            <i class="bi bi-${icons[type]}"></i>
            <span>${message}</span>
        `;

        this.container.appendChild(notification);

        // Animate in
        requestAnimationFrame(() => {
            notification.style.transform = 'translateX(0)';
        });

        // Auto remove
        setTimeout(() => {
            notification.style.transform = 'translateX(120%)';
            setTimeout(() => notification.remove(), 300);
        }, 4000);
    }
};

/**
 * Page Loader
 */
window.addEventListener('load', function () {
    const loader = document.querySelector('.page-loader');
    if (loader) {
        setTimeout(() => loader.classList.add('hidden'), 500);
    }
});

/**
 * Utility: Format Price
 */
function formatPrice(price) {
    return new Intl.NumberFormat('tr-TR', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(price) + ' TL';
}

/**
 * Utility: Smooth Scroll Links
 */
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            e.preventDefault();
            target.scrollIntoView({ behavior: 'smooth' });
        }
    });
});

/**
 * Global Toast Function (wrapper for NotificationManager)
 */
function showToast(message, type = 'success') {
    if (typeof NotificationManager !== 'undefined' && NotificationManager.show) {
        NotificationManager.show(message, type);
    } else {
        console.log(`[${type.toUpperCase()}] ${message}`);
    }
}

// Expose globally
window.showToast = showToast;
