<?php require 'fetch-product.php' ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flowbite@1.4.0/dist/flowbite.min.js"></script>
    <title><?php echo $storeData['store_name'] ?></title>
</head>
<body class="bg-gray-200 mx-auto w-full flex flex-col items-center">
    <header class="bg-white text-black w-full sm:w-2/3 p-4 sticky top-0">
        <h1 class="text-2xl font-bold"><?php echo $storeData['store_name'] ?></h1>
        <nav class="bg-white text-black mt-2 overflow-x-scroll whitespace-nowrap">
            <ul class="flex space-x-2 py-4">
                <?php foreach ($productLists as $categoryName => $products): ?>
                    <li><a href="#" data-scroll-to="<?php echo str_replace(' ', '-', strtolower($categoryName)); ?>" class="border-2 px-3 py-1 border-red-500 rounded-3xl text-base font-bold nav-link"><?php echo $categoryName; ?></a></li>
                <?php endforeach; ?>
            </ul>
        </nav>
        <div class="mt-4 flex space-x-2">
            <input type="text" id="search" placeholder="Search Categories..." class="flex-grow border-2 border-gray-300 rounded-lg px-4 py-2">
            <button id="searchButton" class="bg-blue-500 text-white px-4 py-2 rounded-lg">Search</button>
            <button id="clearButton" class="bg-gray-500 text-white px-4 py-2 rounded-lg">Clear</button>
        </div>
    </header>

    <div class="bg-gray-100 pb-6 w-full sm:w-2/3">
        <?php foreach ($productLists as $categoryName => $products): ?>
            <section id="<?php echo str_replace(' ', '-', strtolower($categoryName)); ?>" class="px-6">
                <div class="flex items-center justify-center py-4">
                    <div class="flex-grow border-2 border-gray-300 ml-6"></div>
                    <span class="text-lg sm:text-xl font-bold"><?php echo " &nbsp;&nbsp; " . $categoryName . " &nbsp;&nbsp; "; ?></span>
                    <div class="flex-grow border-2 border-gray-300 mr-6"></div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4 mb-4 cursor-pointer">
                    <?php foreach ($products as $index => $product): ?>
                        <div class="w-full bg-white rounded-lg shadow-md overflow-hidden text-left" data-product='<?php echo htmlspecialchars(json_encode($product), ENT_QUOTES, 'UTF-8'); ?>'>
                            <img src="<?php echo $product['image'][0]; ?>" alt="<?php echo $product['name']; ?>" class="w-full h-96 object-cover">
                            <div class="p-4">
                                <p class="text-gray-400 text-xs font-bold">ID: <?php echo $product['code']; ?></p>
                                <h2 class="text-gray-800 text-base font-bold"><?php echo $product['name']; ?></h2>
                                <p class="mt-2 text-red-500 text-sm font-bold"><?php echo isset($product['price']) ? '$' . number_format($product['price'], 2) : 'Price not available'; ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endforeach; ?>
    </div>

    <footer class="bg-gray-800 text-white p-4 text-center w-full sm:w-2/3">
        <p>© 2025 <a href="#" target="_blank" class="underline hover:text-blue-400">Diamond</a>. All rights reserved.</p>
    </footer>

    <script>
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
                    <div class="absolute bottom-2 right-1 transform -translate-x-1 bg-gray-800 bg-opacity-50 text-white px-2 py-1 rounded" id="image-index">1 of ${product.image.length}</div>
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

        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.grid > div').forEach(productCard => {
                productCard.addEventListener('click', () => {
                    const productData = productCard.getAttribute('data-product');
                    showPopupDialog(productData);
                });
            });
        });

        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('[data-scroll-to]').forEach(link => {
                link.addEventListener('click', (e) => {
                    e.preventDefault();
                    const sectionId = link.getAttribute('data-scroll-to');
                    const section = document.getElementById(sectionId);
                    const headerOffset = document.querySelector('header').offsetHeight;
                    const sectionPosition = section.getBoundingClientRect().top + window.scrollY - headerOffset;
                    window.scrollTo({ top: sectionPosition, behavior: 'smooth' });
                });
            });

            window.addEventListener('scroll', highlightActiveSection);
            highlightActiveSection();
        });

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
    </script>
</body>
</html>
