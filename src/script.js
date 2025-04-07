// Function to highlight the active section in the navigation bar
// and scroll to the corresponding section smoothly
function highlightActiveSection() {
    const sections = document.querySelectorAll('section[id]');
    const navLinks = document.querySelectorAll('.nav-link');
    const navContainer = document.querySelector('nav');
    let currentSection = '';

    const headerHeight = document.querySelector('header').offsetHeight;
    sections.forEach(section => {
        const sectionTop = section.offsetTop - headerHeight;
        const sectionHeight = section.offsetHeight;
        if (window.scrollY >= sectionTop && window.scrollY < sectionTop + sectionHeight) {
            currentSection = section.getAttribute('id');
        }
    });

    navLinks.forEach(link => {
        link.classList.remove('bg-red-100', 'text-red-500');

        if (link.getAttribute('data-scroll-to') === currentSection) {
            link.classList.add('bg-red-100', 'text-red-500');

            const linkOffsetLeft = link.offsetLeft;
            const linkWidth = link.offsetWidth;
            const containerScrollLeft = navContainer.scrollLeft;
            const containerWidth = navContainer.offsetWidth;

            navContainer.scrollTo({
                left: linkOffsetLeft - containerWidth / 2 + linkWidth / 2,
                behavior: 'smooth'
            });
        }
    });
}

// Function to show the popup dialog with product details
function showPopupDialog(product) {
    if (typeof product === 'string')
        product = JSON.parse(product);

    if (!product) {
        console.error('No product data provided');
        Swal.fire({
            title: 'Error',
            text: 'Product not found',
            icon: 'error'
        });
        return;
    }

    let imageHtml = '';
    if (Array.isArray(product.image) && product.image.length > 0) {
        imageHtml = `<div class="relative w-full h-96">
            ${product.image.map((img, index) => `
                <img src="${img}" alt="${product.name} ${index + 1}" class="w-full h-full object-cover rounded-tl-xl rounded-tr-xl absolute top-0 left-0 transition-opacity duration-500 ${index === 0 ? 'opacity-100' : 'opacity-0'}" data-index="${index}">
            `).join('')}
            ${product.image.length > 1 ? `
                <button class="absolute left-2 top-1/2 transform -translate-y-1/2 bg-gray-800 bg-opacity-50 text-white p-2 rounded-full" onclick="changeImage(-1, this)">❮</button>
                <button class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-gray-800 bg-opacity-50 text-white p-2 rounded-full" onclick="changeImage(1, this)">❯</button>
            ` : ''}
            <div class="absolute bottom-2 right-1 transform -translate-x-1 bg-gray-800 bg-opacity-50 text-white text-base px-2 py-1 rounded" id="image-index">1 of ${product.image.length}</div>
        </div>`;
    } else {
        imageHtml = `<img src="default-image.jpg" alt="No image" class="w-full h-96 object-cover rounded-tl-xl rounded-tr-xl">`;
    }

    Swal.fire({
        html: 
            `<div class="absolute top-0 right-0 p-4 z-50">
                <svg xmlns="http://www.w3.org/2000/svg" onclick="Swal.close()" class="h-6 w-6 text-red-500 cursor-pointer border-2 border-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </div>` +
            imageHtml +
            `<p class="mt-2 mx-4 text-red-500 text-base font-bold text-left">${product.price ? '$' + parseFloat(product.price).toFixed(2) : 'Price not available'}</p>` +
            `<p class="mt-4 mx-4 text-red-500 text-xs font-bold text-left">ID: ${product.code || 'N/A'}</p>` +
            `<h2 class="mx-4 text-gray-800 text-base font-bold text-left">${product.name || 'Unnamed Product'}</h2>` +
            `<p class="mt-2 mx-4 text-left text-base">${product.description || 'No description available'}</p>`,
        footer: 
            `<div class="flex justify-center space-x-4">
                <a href="<?php echo $storeData['facebook']; ?>" target="_blank" class="text-blue-700 hover:text-blue-900">
                    <img src="https://img.icons8.com/ios-filled/50/000000/facebook-new.png" class="h-6 w-6" alt="Facebook Icon"/>
                </a>
                <a href="<?php echo $storeData['telegram']; ?>" target="_blank" class="text-blue-500 hover:text-blue-700">
                    <img src="https://img.icons8.com/ios-filled/50/000000/telegram-app.png" class="h-6 w-6" alt="Telegram Icon"/>
                </a>
                <a href="<?php echo $storeData['map']; ?>" target="_blank" class="text-red-500 hover:text-red-700">
                    <img src="https://img.icons8.com/ios-filled/50/000000/google-maps-new.png" class="h-6 w-6" alt="Google Maps Icon"/>
                </a>
                <a class="flex items-center" href="tel:<?php echo $storeData['tel']; ?>" class="text-green-500 hover:text-green-700">
                    <img src="https://img.icons8.com/ios-filled/50/000000/phone.png" class="h-6 w-6" alt="Phone Icon}"/>
                    <span class="ml-1"><?php echo $storeData['tel']; ?></span>
                </a>
            </div>`,
        customClass: {
            popup: 'p-0 rounded-xl shadow-lg relative',
            content: 'p-0',
            footer: 'pb-4',
        },
        showCloseButton: false,
        showCancelButton: false,
        showConfirmButton: false,
        didOpen: (popup) => {
            window.changeImage = (direction, button) => {
                const images = popup.querySelectorAll('img[data-index]');
                const indexDisplay = popup.querySelector('#image-index');
                
                if (!indexDisplay) {
                    console.error('Image index element not found');
                    return;
                }
                
                let currentIndex = Array.from(images).findIndex(img => img.classList.contains('opacity-100'));
                images[currentIndex].classList.remove('opacity-100');
                currentIndex = (currentIndex + direction + images.length) % images.length;
                images[currentIndex].classList.add('opacity-100');
                
                indexDisplay.textContent = `${currentIndex + 1} of ${images.length}`;
            };
        }
    });
}

// Update the top position of the category dividers based on the header height
function updateDividerTops() {
    const header = document.getElementById('mainHeader');
    const dividers = document.querySelectorAll('.category-divider');

    if (header && dividers.length) {
        const headerHeight = header.offsetHeight;

        dividers.forEach(divider => {
            divider.style.top = `${headerHeight}px`;
        });
    }
}

// Initialize the popup dialog for product cards
document.addEventListener('DOMContentLoaded', () => {
    const productCards = document.querySelectorAll('.grid > div[data-product]');
    productCards.forEach(card => {
        card.addEventListener('click', () => {
            const productData = card.getAttribute('data-product');
            showPopupDialog(productData);
        });
    });
});

// Smooth scroll to section functionality
function smoothScrollToSection(sectionId) {
    const section = document.getElementById(sectionId);
    if (section) {
        const headerOffset = document.querySelector('header').offsetHeight;
        const sectionPosition = section.getBoundingClientRect().top + window.scrollY - headerOffset;
        window.scrollTo({ top: sectionPosition, behavior: 'auto' });
    }
}

// Smooth scroll to section on link click and highlight active section
document.addEventListener('DOMContentLoaded', () => {
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const sectionId = link.getAttribute('data-scroll-to');
            smoothScrollToSection(sectionId);
        });
    });
});

// Search functionality to filter product sections based on input
// and clear button to reset the search input and show all sections
// Highlight active section on scroll
// and update the divider tops based on the header height
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('search');
    const searchButton = document.getElementById('searchButton');
    const clearButton = document.getElementById('clearButton');
    const productSections = document.querySelectorAll('section');

    searchButton.addEventListener('click', () => {
        const query = searchInput.value.toLowerCase();

        productSections.forEach(section => {
            const categoryName = section.querySelector('span').textContent.toLowerCase().trim();

            if (categoryName.includes(query)) {
                section.style.display = 'block';
            } else {
                section.style.display = 'none';
            }
        });

        window.addEventListener('scroll', highlightActiveSection);
        highlightActiveSection();
    });

    clearButton.addEventListener('click', () => {
        searchInput.value = '';
        productSections.forEach(section => {
            section.style.display = 'block';
        });

        window.addEventListener('scroll', highlightActiveSection);
        highlightActiveSection();
    });
});

// Clear button functionality to hide when input is empty and show when there is text
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('search');
    const clearButton = document.getElementById('clearButton');

    searchInput.addEventListener('input', () => {
        if (searchInput.value.trim() === '') {
            clearButton.classList.add('hidden');
        } else {
            clearButton.classList.remove('hidden');
        }
    });

    clearButton.addEventListener('click', () => {
        searchInput.value = '';
        clearButton.classList.add('hidden');
    });
});

// Update divider tops on load and resize events
// and highlight active section on scroll
highlightActiveSection();
updateDividerTops();
window.addEventListener('scroll', highlightActiveSection);
window.addEventListener('DOMContentLoaded', updateDividerTops);
window.addEventListener('resize', updateDividerTops);