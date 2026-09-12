/* ===============================================
   BRAR PET SHOP - JAVASCRIPT
   Interactive Features & Functionality
   =============================================== */

// ===============================================
// Initialize on DOM Content Loaded
// ===============================================



// ===============================================
// Inheritance of Common Elements (Navbar & Footer)
// ===============================================

document.addEventListener("DOMContentLoaded", function () {

    // Load Navbar
    fetch("navbar.php")
        .then(response => response.text())
        .then(data => {
            document.getElementById("navbar").innerHTML = data;
        })
        .catch(error => console.error("Error loading navbar:", error));

    // Load Footer
    fetch("footer.php")
        .then(response => response.text())
        .then(data => {
            document.getElementById("footer").innerHTML = data;
        })
        .catch(error => console.error("Error loading footer:", error));

});

document.addEventListener('DOMContentLoaded', function() {
    initializeApp();
});

let petPagination = null;

// ===============================================
// Main Initialization Function
// ===============================================

function initializeApp() {
    // Initialize features
    initializeDarkMode();
    initializeScrollSpy();
    initializeScrollEffects();
    initializeCounterAnimation();
    initializeAOS();
    initializeTooltips();
    initializeFormValidation();
    initializeCartButtons();
    initializePagination();
    initializePetFilters();
    initializeSearchFunctionality();
    initializeBackToTopButton();
    initializeGalleryModal();
    initializeNewsletterForm();
    
    // Hide loading spinner
    setTimeout(() => {
        hideLoadingSpinner();
    }, 2500);
}

// ===============================================
// Loading Spinner
// ===============================================

function hideLoadingSpinner() {
    const spinner = document.getElementById('loadingSpinner');
    if (spinner) {
        spinner.style.opacity = '0';
        spinner.style.pointerEvents = 'none';
    }
}

// ===============================================
// Dark Mode Toggle
// ===============================================

function initializeDarkMode() {
    const darkModeToggle = document.getElementById('darkModeToggle');
    const body = document.body;
    
    // Check if dark mode is saved in localStorage
    const isDarkMode = localStorage.getItem('darkMode') === 'true';
    
    if (isDarkMode) {
        body.classList.add('dark-mode');
        updateDarkModeIcon();
    }
    
    // Toggle dark mode on button click
    if (darkModeToggle) {
        darkModeToggle.addEventListener('click', function() {
            body.classList.toggle('dark-mode');
            const isNowDark = body.classList.contains('dark-mode');
            localStorage.setItem('darkMode', isNowDark);
            updateDarkModeIcon();
        });
    }
}

function updateDarkModeIcon() {
    const darkModeToggle = document.getElementById('darkModeToggle');
    if (darkModeToggle) {
        const isDarkMode = document.body.classList.contains('dark-mode');
        const icon = darkModeToggle.querySelector('i');
        if (isDarkMode) {
            icon.classList.remove('bi-moon-fill');
            icon.classList.add('bi-sun-fill');
        } else {
            icon.classList.remove('bi-sun-fill');
            icon.classList.add('bi-moon-fill');
        }
    }
}

// ===============================================
// ScrollSpy - Highlight Active Navigation Links
// ===============================================

function initializeScrollSpy() {
    const sections = document.querySelectorAll('section[id]');
    const navLinks = document.querySelectorAll('.navbar-nav .nav-link');
    
    function updateActiveLink() {
        const scrollY = window.pageYOffset;
        
        sections.forEach(current => {
            const sectionHeight = current.offsetHeight;
            const sectionTop = current.offsetTop - 100;
            const sectionId = current.getAttribute('id');
            
            if (scrollY > sectionTop && scrollY <= sectionTop + sectionHeight) {
                navLinks.forEach(link => {
                    link.classList.remove('active');
                });
                
                const activeLink = document.querySelector(`.navbar-nav .nav-link[href="#${sectionId}"]`);
                if (activeLink) {
                    activeLink.classList.add('active');
                }
            }
        });
    }
    
    window.addEventListener('scroll', updateActiveLink);
    updateActiveLink(); // Call on load
}

// ===============================================
// Scroll Effects - Navbar Shadow on Scroll
// ===============================================

function initializeScrollEffects() {
    const navbar = document.getElementById('mainNavbar');
    
    window.addEventListener('scroll', function() {
        if (window.scrollY > 50) {
            navbar.classList.add('scroll-shadow');
        } else {
            navbar.classList.remove('scroll-shadow');
        }
    });
}

// ===============================================
// Counter Animation
// ===============================================

function initializeCounterAnimation() {
    const counters = document.querySelectorAll('.counter-number');
    const speed = 200; // Speed of animation
    
    function runCounter(counter) {
        const target = parseInt(counter.getAttribute('data-target'));
        const increment = target / speed;
        
        let current = 0;
        
        const updateCount = setInterval(() => {
            current += increment;
            
            if (current >= target) {
                counter.textContent = target.toLocaleString();
                clearInterval(updateCount);
            } else {
                counter.textContent = Math.floor(current).toLocaleString();
            }
        }, 10);
    }
    
    // Trigger counter animation when section is in view
    const observerOptions = {
        threshold: 0.5,
        rootMargin: '0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting && !entry.target.classList.contains('animated')) {
                entry.target.classList.add('animated');
                runCounter(entry.target);
            }
        });
    }, observerOptions);
    
    counters.forEach(counter => observer.observe(counter));
}

// ===============================================
// AOS (Animate On Scroll) Initialization
// ===============================================

function initializeAOS() {
    if (typeof AOS !== 'undefined') {
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true,
            offset: 100
        });
    }
}

// ===============================================
// Bootstrap Tooltips
// ===============================================

function initializeTooltips() {
    // Initialize all tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
}

// ===============================================
// Form Validation
// ===============================================

function initializeFormValidation() {
    const forms = document.querySelectorAll('form[novalidate]');
    
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', function(event) {
            event.preventDefault();
            
            if (!form.checkValidity()) {
                event.stopPropagation();
            } else {
                // Form is valid - handle submission
                handleFormSubmit(form);
            }
            
            form.classList.add('was-validated');
        }, false);
    });
}

function handleFormSubmit(form) {
    const formId = form.id;
    
    if (formId === 'contactForm') {
        // Handle contact form
        const fullName = document.getElementById('fullName').value;
        const email = document.getElementById('email').value;
        
        alert(`Thank you, ${fullName}! Your message has been received. We'll contact you at ${email} soon.`);
        form.reset();
        form.classList.remove('was-validated');
    } else if (formId === 'newsletterForm') {
        // Handle newsletter form
        const email = form.querySelector('input[type="email"]').value;
        alert(`Thank you! You've been subscribed with ${email}`);
        form.reset();
    }
}

// ===============================================
// Cart Functionality Demo
// ===============================================

function initializeCartButtons() {
    const cartButtons = document.querySelectorAll('.add-to-cart');
    const cartBadge = document.getElementById('cartBadge');
    const cartBtn = document.getElementById('cartBtn');
    
    let cartCount = parseInt(localStorage.getItem('cartCount')) || 0;
    updateCartBadge();
    
    cartButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Add animation
            const card = this.closest('.card');
            const petName = card.querySelector('.card-title').textContent;
            
            cartCount++;
            localStorage.setItem('cartCount', cartCount);
            updateCartBadge();
            
            // Show success message
            showToast(`${petName} added to cart!`);
            
            // Button animation
            this.innerHTML = '<i class="bi bi-check-circle"></i> Added';
            this.disabled = true;
            setTimeout(() => {
                this.innerHTML = 'Add to Cart';
                this.disabled = false;
            }, 2000);
        });
    });
    
    // Cart button click
    if (cartBtn) {
        cartBtn.addEventListener('click', function() {
            alert(`You have ${cartCount} items in your cart!`);
        });
    }
}

function updateCartBadge() {
    const cartCount = parseInt(localStorage.getItem('cartCount')) || 0;
    const cartBadge = document.getElementById('cartBadge');
    if (cartBadge) {
        cartBadge.textContent = cartCount;
    }
}

// Toast Notification
function showToast(message) {
    // Create toast element
    const toast = document.createElement('div');
    toast.className = 'toast position-fixed bottom-0 end-0 m-3';
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');
    
    toast.innerHTML = `
        <div class="toast-body bg-success text-white rounded">
            <i class="bi bi-check-circle me-2"></i>${message}
        </div>
    `;
    
    document.body.appendChild(toast);
    
    // Show toast
    setTimeout(() => {
        toast.classList.add('show');
    }, 100);
    
    // Hide and remove toast
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// ===============================================
// Search Functionality
// ===============================================

function initializeSearchFunctionality() {
    const searchInput = document.getElementById('searchInput');
    const searchBtn = document.getElementById('searchBtn');
    const petCards = document.querySelectorAll('.pet-card');
    
    function filterPets(searchTerm) {
        const term = searchTerm.toLowerCase();
        
        petCards.forEach(card => {
            const petName = card.querySelector('.card-title').textContent.toLowerCase();
            const petDescription = card.querySelector('.card-text').textContent.toLowerCase();
            
            if (petName.includes(term) || petDescription.includes(term) || term === '') {
                card.style.display = 'block';
                card.style.animation = 'fadeIn 0.3s ease';
            } else {
                card.style.display = 'none';
            }
        });
    }
    
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            filterPets(this.value);
        });
    }
    
    if (searchBtn) {
        searchBtn.addEventListener('click', function() {
            const searchTerm = searchInput.value;
            if (searchTerm) {
                document.getElementById('pets').scrollIntoView({ behavior: 'smooth' });
            }
        });
    }
}

// ===============================================
// Pet Search and Filter Functionality
// ===============================================

function initializePetFilters() {
    const searchInput = document.getElementById('petSearchInput');
    const petTypeFilter = document.getElementById('petTypeFilter');
    const priceRangeFilter = document.getElementById('priceRangeFilter');
    const resetBtn = document.getElementById('resetFiltersBtn');
    const petCardsContainer = document.getElementById('petCardsContainer');
    const petCardWrappers = document.querySelectorAll('.pet-card-wrapper');
    const noResultsMessage = document.getElementById('noResultsMessage');

    // Function to extract price value from the pet wrapper
    function getPetPrice(wrapper) {
        const priceText = wrapper.querySelector('.card-body small:nth-of-type(2) .text-danger').textContent;
        return parseInt(priceText.replace(/[₹,]/g, ''));
    }

    // Function to check if price is in range
    function isPriceInRange(price, rangeFilter) {
        if (!rangeFilter) return true;
        
        if (rangeFilter === '0-5000') return price <= 5000;
        if (rangeFilter === '5000-20000') return price > 5000 && price <= 20000;
        if (rangeFilter === '20000-50000') return price > 20000 && price <= 50000;
        if (rangeFilter === '50000+') return price > 50000;
        
        return true;
    }

    // Main filter function
    function applyFilters() {
        const searchTerm = searchInput.value.toLowerCase().trim();
        const selectedType = petTypeFilter.value;
        const selectedPriceRange = priceRangeFilter.value;
        
        let visibleCount = 0;

        petCardWrappers.forEach(wrapper => {
            const petName = wrapper.getAttribute('data-pet-name').toLowerCase();
            const petType = wrapper.getAttribute('data-pet-type');
            const petPrice = parseInt(wrapper.getAttribute('data-pet-price'));

            // Check search term
            const matchesSearch = searchTerm === '' || petName.includes(searchTerm);
            
            // Check pet type
            const matchesType = selectedType === '' || petType === selectedType;
            
            // Check price range
            const matchesPrice = isPriceInRange(petPrice, selectedPriceRange);

            // If all filters match, show the card
            if (matchesSearch && matchesType && matchesPrice) {
                wrapper.classList.remove('hidden');
                visibleCount++;
            } else {
                wrapper.classList.add('hidden');
            }
        });

        // Show or hide no results message
        if (visibleCount === 0) {
            noResultsMessage.classList.remove('d-none');
        } else {
            noResultsMessage.classList.add('d-none');
        }

        if (petPagination && typeof petPagination.refresh === 'function') {
            petPagination.refresh();
        }
    }

    // Function to reset all filters
    function resetFilters() {
        searchInput.value = '';
        petTypeFilter.value = '';
        priceRangeFilter.value = '';
        
        petCardWrappers.forEach(wrapper => {
            wrapper.classList.remove('hidden');
        });
        
        noResultsMessage.classList.add('d-none');
        
        if (petPagination && typeof petPagination.refresh === 'function') {
            petPagination.refresh();
        }
        
        // Trigger AOS animation refresh if available
        if (typeof AOS !== 'undefined') {
            AOS.refresh();
        }
    }

    // Event listeners
    if (searchInput) {
        searchInput.addEventListener('input', applyFilters);
    }

    if (petTypeFilter) {
        petTypeFilter.addEventListener('change', applyFilters);
    }

    if (priceRangeFilter) {
        priceRangeFilter.addEventListener('change', applyFilters);
    }

    if (resetBtn) {
        resetBtn.addEventListener('click', resetFilters);
    }
}

function initializePagination() {
    const wrappers = Array.from(document.querySelectorAll('.pet-card-wrapper'));
    const paginationContainer = document.getElementById('pagination');
    let currentPage = 1;
    let itemsPerPage = getItemsPerPage();

    function getItemsPerPage() {
        return window.innerWidth >= 992 ? 8 : 4;
    }

    function getVisibleWrappers() {
        return wrappers.filter(wrapper => !wrapper.classList.contains('hidden'));
    }

    function renderPagination() {
        if (!paginationContainer) {
            return;
        }

        const visibleWrappers = getVisibleWrappers();
        const totalPages = Math.max(1, Math.ceil(visibleWrappers.length / itemsPerPage));

        paginationContainer.innerHTML = '';

        if (visibleWrappers.length <= itemsPerPage) {
            const nav = paginationContainer.closest('nav');
            if (nav) {
                nav.style.display = 'none';
            }
            return;
        }

        const nav = paginationContainer.closest('nav');
        if (nav) {
            nav.style.display = '';
        }

        const prevItem = document.createElement('li');
        prevItem.className = `page-item${currentPage === 1 ? ' disabled' : ''}`;
        prevItem.innerHTML = `<button class="page-link" type="button" aria-label="Previous page" ${currentPage === 1 ? 'tabindex="-1" aria-disabled="true"' : ''}>&laquo;</button>`;
        if (currentPage > 1) {
            prevItem.querySelector('button').addEventListener('click', () => changePage(currentPage - 1));
        }
        paginationContainer.appendChild(prevItem);

        for (let page = 1; page <= totalPages; page++) {
            const pageItem = document.createElement('li');
            pageItem.className = `page-item${page === currentPage ? ' active' : ''}`;
            pageItem.innerHTML = `<button class="page-link" type="button">${page}</button>`;
            if (page === currentPage) {
                pageItem.setAttribute('aria-current', 'page');
            }
            pageItem.querySelector('button').addEventListener('click', () => changePage(page));
            paginationContainer.appendChild(pageItem);
        }

        const nextItem = document.createElement('li');
        nextItem.className = `page-item${currentPage === totalPages ? ' disabled' : ''}`;
        nextItem.innerHTML = `<button class="page-link" type="button" aria-label="Next page" ${currentPage === totalPages ? 'tabindex="-1" aria-disabled="true"' : ''}>&raquo;</button>`;
        if (currentPage < totalPages) {
            nextItem.querySelector('button').addEventListener('click', () => changePage(currentPage + 1));
        }
        paginationContainer.appendChild(nextItem);
    }

    function updatePageView() {
        const visibleWrappers = getVisibleWrappers();
        const totalPages = Math.max(1, Math.ceil(visibleWrappers.length / itemsPerPage));

        if (currentPage > totalPages) {
            currentPage = totalPages;
        }

        visibleWrappers.forEach((wrapper, index) => {
            const isVisibleOnPage = index >= (currentPage - 1) * itemsPerPage && index < currentPage * itemsPerPage;
            wrapper.classList.toggle('pagination-hidden', !isVisibleOnPage);
        });

        wrappers.forEach(wrapper => {
            if (wrapper.classList.contains('hidden')) {
                wrapper.classList.add('pagination-hidden');
            }
        });

        renderPagination();
    }

    function refreshPagination() {
        itemsPerPage = getItemsPerPage();
        currentPage = 1;
        updatePageView();
    }

    function changePage(page) {
        currentPage = page;
        updatePageView();
    }

    function handleResize() {
        const nextItemsPerPage = getItemsPerPage();
        if (nextItemsPerPage !== itemsPerPage) {
            itemsPerPage = nextItemsPerPage;
            currentPage = 1;
            updatePageView();
        }
    }

    function debounce(fn, delay) {
        let timer;
        return function(...args) {
            clearTimeout(timer);
            timer = setTimeout(() => fn.apply(this, args), delay);
        };
    }

    if (paginationContainer) {
        window.addEventListener('resize', debounce(handleResize, 200));
    }

    updatePageView();

    petPagination = {
        refresh: refreshPagination,
        changePage: changePage
    };
}

// ===============================================
// Back To Top Button
// ===============================================

function initializeBackToTopButton() {
    const backToTopBtn = document.getElementById('backToTopBtn');
    
    window.addEventListener('scroll', function() {
        if (window.pageYOffset > 300) {
            backToTopBtn.classList.add('show');
        } else {
            backToTopBtn.classList.remove('show');
        }
    });
    
    backToTopBtn.addEventListener('click', function(e) {
        e.preventDefault();
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
}

// ===============================================
// Gallery Modal
// ===============================================

function initializeGalleryModal() {
    const galleryModal = document.getElementById('galleryModal');
    const galleryModalImage = document.getElementById('galleryModalImage');
    
    if (galleryModal) {
        galleryModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const imageUrl = button.getAttribute('data-image');
            
            if (galleryModalImage && imageUrl) {
                galleryModalImage.src = imageUrl;
                galleryModalImage.style.animation = 'fadeIn 0.3s ease';
            }
        });
    }
}

// ===============================================
// Newsletter Form
// ===============================================

function initializeNewsletterForm() {
    const newsletterForm = document.getElementById('newsletterForm');
    
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const emailInput = this.querySelector('input[type="email"]');
            const email = emailInput.value;
            
            if (email) {
                showToast(`Subscribed with ${email}!`);
                this.reset();
            }
        });
    }
}

// ===============================================
// Smooth Scrolling for Navigation Links
// ===============================================

document.addEventListener('DOMContentLoaded', function() {
    const navLinks = document.querySelectorAll('a[href^="#"]');
    
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            
            // Skip if href is just '#'
            if (href === '#') {
                e.preventDefault();
                return;
            }
            
            const target = document.querySelector(href);
            
            if (target) {
                e.preventDefault();
                
                // Close offcanvas if open
                const offcanvasElement = document.querySelector('.offcanvas.show');
                if (offcanvasElement) {
                    const bsOffcanvas = bootstrap.Offcanvas.getInstance(offcanvasElement);
                    if (bsOffcanvas) {
                        bsOffcanvas.hide();
                    }
                }
                
                // Close navbar if open
                const navbarCollapse = document.querySelector('.navbar-collapse.show');
                if (navbarCollapse) {
                    const bsCollapse = bootstrap.Collapse.getInstance(navbarCollapse);
                    if (bsCollapse) {
                        bsCollapse.hide();
                    }
                }
                
                // Scroll to target
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
});

// ===============================================
// Phone Number Validation
// ===============================================

document.addEventListener('DOMContentLoaded', function() {
    const phoneInput = document.getElementById('phone');
    
    if (phoneInput) {
        phoneInput.addEventListener('input', function() {
            // Allow only numbers and basic phone format characters
            this.value = this.value.replace(/[^0-9+\-\s]/g, '');
        });
    }
});

// ===============================================
// Email Validation Helper
// ===============================================

function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

// ===============================================
// Utility: Get Element by ID
// ===============================================

function getElement(id) {
    return document.getElementById(id);
}

function getElements(selector) {
    return document.querySelectorAll(selector);
}

// ===============================================
// Utility: Add Event Listener
// ===============================================

function addEvent(element, event, callback) {
    if (element) {
        element.addEventListener(event, callback);
    }
}

function addEvents(selector, event, callback) {
    const elements = document.querySelectorAll(selector);
    elements.forEach(element => {
        element.addEventListener(event, callback);
    });
}

// ===============================================
// Utility: Has Class
// ===============================================

function hasClass(element, className) {
    return element.classList.contains(className);
}

// ===============================================
// Utility: Toggle Class
// ===============================================

function toggleClass(element, className) {
    element.classList.toggle(className);
}

// ===============================================
// Keyboard Shortcuts
// ===============================================

document.addEventListener('keydown', function(event) {
    // Ctrl/Cmd + / for search focus
    if ((event.ctrlKey || event.metaKey) && event.key === '/') {
        event.preventDefault();
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.focus();
        }
    }
    
    // Escape key to close modals
    if (event.key === 'Escape') {
        const modals = document.querySelectorAll('.modal.show');
        modals.forEach(modal => {
            const bsModal = bootstrap.Modal.getInstance(modal);
            if (bsModal) {
                bsModal.hide();
            }
        });
    }
});

// ===============================================
// Performance: Lazy Loading Images
// ===============================================

if ('IntersectionObserver' in window) {
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                if (img.dataset.src) {
                    img.src = img.dataset.src;
                    img.removeAttribute('data-src');
                }
                observer.unobserve(img);
            }
        });
    });
    
    document.querySelectorAll('img[data-src]').forEach(img => imageObserver.observe(img));
}

// ===============================================
// Prevent Multiple Form Submissions
// ===============================================

document.addEventListener('submit', function(e) {
    const button = e.target.querySelector('button[type="submit"]');
    if (button) {
        button.disabled = true;
        setTimeout(() => {
            button.disabled = false;
        }, 1000);
    }
});

// ===============================================
// Console Welcome Message
// ===============================================

console.log('%cBrar Pet Shop', 'font-size: 24px; font-weight: bold; color: #0d6efd;');
console.log('%cYour Trusted Partner for Happy Pets', 'font-size: 16px; color: #666;');
console.log('%cThank you for visiting our website!', 'font-size: 14px; color: #999;');


// ===============================================
// common elements inherited by multiple pages
// ===============================================

document.addEventListener("DOMContentLoaded", function () {

    // Load Navbar
    fetch("navbar.php")
        .then(response => response.text())
        .then(data => {
            document.getElementById("navbar").innerHTML = data;
        })
        .catch(error => console.error("Navbar not loaded:", error));

    // Load Footer
    fetch("footer.php")
        .then(response => response.text())
        .then(data => {
            document.getElementById("footer").innerHTML = data;
        })
        .catch(error => console.error("Footer not loaded:", error));

});


// ===============================================
// read more button in about page
// ===============================================


const aboutMore = document.getElementById('aboutMore');
const readMoreBtn = document.getElementById('readMoreBtn');

aboutMore.addEventListener('shown.bs.collapse', function () {
    readMoreBtn.innerHTML = '<i class="bi bi-arrow-up-circle"></i> Read Less';
});

aboutMore.addEventListener('hidden.bs.collapse', function () {
    readMoreBtn.innerHTML = '<i class="bi bi-arrow-down-circle"></i> Read More';
});


/* ===========================
   PET PAGINATION
=========================== */

document.addEventListener("DOMContentLoaded", function () {

    const petCards = document.querySelectorAll(".pet-card-wrapper");
    const pagination = document.getElementById("pagination");

    // Stop if pagination doesn't exist
    if (!pagination || petCards.length === 0) return;

    let currentPage = 1;

    // Desktop: 6 cards, Mobile: 4 cards
    function getCardsPerPage() {
        return window.innerWidth >= 992 ? 8 : 4;
    }

    function showPage(page) {

        currentPage = page;

        const cardsPerPage = getCardsPerPage();

        // Hide all cards
        petCards.forEach(card => {
            card.style.display = "none";
        });

        // Show cards for current page
        const start = (page - 1) * cardsPerPage;
        const end = start + cardsPerPage;

        for (let i = start; i < end && i < petCards.length; i++) {
            petCards[i].style.display = "block";
        }

        // Update active page
        document.querySelectorAll("#pagination .page-item")
            .forEach(item => item.classList.remove("active"));

        const activeItem = pagination.children[page - 1];

        if (activeItem) {
            activeItem.classList.add("active");
        }
    }

    function createPagination() {

        pagination.innerHTML = "";

        const cardsPerPage = getCardsPerPage();

        const totalPages = Math.ceil(
            petCards.length / cardsPerPage
        );

        for (let i = 1; i <= totalPages; i++) {

            pagination.innerHTML += `
                <li class="page-item ${i === currentPage ? "active" : ""}">
                    <a class="page-link" href="#">
                        ${i}
                    </a>
                </li>
            `;
        }

        showPage(currentPage);
    }

    // Pagination click
    pagination.addEventListener("click", function (e) {

        if (e.target.classList.contains("page-link")) {

            e.preventDefault();

            currentPage = parseInt(e.target.textContent);

            showPage(currentPage);
        }
    });

    // Rebuild pagination on screen resize
    window.addEventListener("resize", function () {

        const cardsPerPage = getCardsPerPage();

        const totalPages = Math.ceil(
            petCards.length / cardsPerPage
        );

        if (currentPage > totalPages) {
            currentPage = totalPages;
        }

        createPagination();
    });

    // Initial load
    createPagination();

});


               