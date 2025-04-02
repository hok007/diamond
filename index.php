<?php
require 'db.php';

$productLists = [];
$sql = "SELECT c.category_name, p.code, p.name, p.description, p.image, p.price
        FROM categories c INNER JOIN products p ON c.category_id = p.category_id 
        ORDER BY c.category_id ASC, p.product_id ASC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $categoryName = $row['category_name'];
        if (!isset($productLists[$categoryName])) {
            $productLists[$categoryName] = [];
        }
        $productLists[$categoryName][] = [
            "code" => $row['code'],
            "name" => $row['name'],
            "description" => $row['description'],
            "image" => $row['image'],
            "price" => $row['price']
        ];
    }
}

$conn->close();
?>

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
    <title>Diamond KTV III</title>
</head>
<body class="bg-gray-200 mx-auto w-full flex flex-col items-center">
    <header class="bg-white text-black w-full sm:w-2/3 p-4 sticky top-0">
        <h1 class="text-2xl font-bold">Diamond KTV III</h1>
        <nav class="bg-white text-black mt-2 overflow-x-scroll whitespace-nowrap">
            <ul class="flex space-x-2 py-4">
                <?php foreach ($productLists as $categoryName => $products): ?>
                    <li><a href="#" data-scroll-to="<?php echo str_replace(' ', '-', strtolower($categoryName)); ?>" class="border-2 px-3 py-1 border-red-500 rounded-3xl text-base font-bold nav-link"><?php echo $categoryName; ?></a></li>
                <?php endforeach; ?>
            </ul>
        </nav>
        <div class="mt-4">
            <input type="text" id="search" placeholder="Search Categories..." class="w-full border-2 border-gray-300 rounded-lg px-4 py-2">
        </div>        
    </header>

    <div class="bg-gray-100 pb-6 w-full sm:w-2/3">
        <?php foreach ($productLists as $categoryName => $products): ?>
            <section id="<?php echo str_replace(' ', '-', strtolower($categoryName)); ?>" class="px-6">
                <div class="flex items-center justify-center py-4">
                    <div class="flex-grow border-2 border-gray-300 ml-6"></div>
                    <span class="text-xl font-bold"><?php echo "  " . $categoryName . "  "; ?></span>
                    <div class="flex-grow border-2 border-gray-300 mr-6"></div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4 mb-4 cursor-pointer">
                    <?php foreach ($products as $index => $product): ?>
                        <div class="w-full bg-white rounded-lg shadow-md overflow-hidden text-left" data-product='<?php echo htmlspecialchars(json_encode($product), ENT_QUOTES, 'UTF-8'); ?>'>
                            <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" class="w-full h-96 object-cover">
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

                   setTimeout(() => {
                        navContainer.scrollTo({
                            left: linkOffsetLeft - containerWidth / 2 + linkWidth / 2 + containerScrollLeft,
                            behavior: 'smooth'
                        });
                    }, 100);
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

            Swal.fire({
                html: 
                    `<div class="fixed p-4">
                        <svg xmlns="http://www.w3.org/2000/svg" onclick="Swal.close()" class="h-6 w-6 text-red-500 cursor-pointer border-2 border-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>` +
                    `<img src="${product.image || 'default-image.jpg'}" alt="${product.name || 'No name'}" class="w-full h-96 object-cover rounded-tl-xl rounded-tr-xl">` +
                    `<p class="mt-2 mx-4 text-red-500 text-base font-bold text-left">${product.price ? '$' + parseFloat(product.price).toFixed(2) : 'Price not available'}</p>` +
                    `<p class="mt-4 mx-4 text-red-500 text-xs font-bold text-left">ID: ${product.code || 'N/A'}</p>` +
                    `<h2 class="mx-4 text-gray-800 text-base font-bold text-left">${product.name || 'Unnamed Product'}</h2>` +
                    `<p class="mt-2 mx-4 text-left text-base">${product.description || 'No description available'}</p>`,
                footer: 
                    `<div class="flex justify-center space-x-4">
                        <a href="https://facebook.com/yourfacebook" target="_blank" class="text-blue-700 hover:text-blue-900">
                            <img src="https://img.icons8.com/ios-filled/50/000000/facebook-new.png" class="h-6 w-6" alt="Facebook Icon"/>
                        </a>
                        <a href="https://t.me/yourtelegram" target="_blank" class="text-blue-500 hover:text-blue-700">
                            <img src="https://img.icons8.com/ios-filled/50/000000/telegram-app.png" class="h-6 w-6" alt="Telegram Icon"/>
                        </a>
                        <a href="https://maps.google.com/?q=yourlocation" target="_blank" class="text-red-500 hover:text-red-700">
                            <img src="https://img.icons8.com/ios-filled/50/000000/google-maps-new.png" class="h-6 w-6" alt="Google Maps Icon"/>
                        </a>
                        <a class="flex items-center" href="tel:012345678" class="text-green-500 hover:text-green-700">
                            <img src="https://img.icons8.com/ios-filled/50/000000/phone.png" class="h-6 w-6" alt="Phone Icon}"/>
                            <span class="ml-1">012 345 678</span>
                        </a>
                    </div>`,
                customClass: {
                    popup: 'p-0 rounded-xl shadow-lg',
                    content: 'p-0',
                    footer: 'pb-4',
                },
                showCloseButton: false,
                showCancelButton: false,
                showConfirmButton: false,
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
            const productSections = document.querySelectorAll('section');

            searchInput.addEventListener('input', () => {
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
        });
    </script>
</body>
</html>
