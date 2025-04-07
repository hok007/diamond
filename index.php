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
    <header id="mainHeader" class="bg-white text-black w-full sm:w-2/3 p-4 sticky top-0 z-50">
        <h1 class="text-2xl font-bold"><?php echo $storeData['store_name'] ?></h1>
        <nav class="bg-white text-black mt-2 overflow-x-scroll whitespace-nowrap">
            <ul class="flex space-x-2 py-4">
                <?php foreach ($productLists as $categoryName => $products): ?>
                    <li><a href="#" data-scroll-to="<?php echo str_replace(' ', '-', strtolower($categoryName)); ?>" class="border-2 px-3 py-1 border-red-500 rounded-3xl text-base font-bold nav-link"><?php echo $categoryName; ?></a></li>
                <?php endforeach; ?>
            </ul>
        </nav>
        <div class="mt-4 flex space-x-2 items-center">
            <div class="relative flex-grow">
                <input type="text" id="search" placeholder="Search Categories..." class="w-full border-2 border-gray-300 rounded-lg px-4 py-2 pr-10 placeholder:text-sm sm:placeholder:text-base"/>
                <button id="clearButton" class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700 hidden">
                    <img src="https://img.icons8.com/ios-filled/50/ff0000/x.png" class="h-4 w-4" alt="Clear Icon"/>
                </button>
            </div>
            <button id="searchButton" class="border-2 bg-red-100 border-red-500 text-sm sm:text-base font-bold text-red-500 px-3 pt-1 pb-2 rounded-lg items-center">
                <img src="https://img.icons8.com/ios-filled/50/ff0000/search.png" class="h-5 w-5 inline" alt="Search Icon"/>
            </button>
        </div>
    </header>

    <div class="bg-gray-100 pb-6 w-full sm:w-2/3">
        <?php foreach ($productLists as $categoryName => $products): ?>
            <section id="<?php echo str_replace(' ', '-', strtolower($categoryName)); ?>">
                <div class="category-divider bg-gray-100 flex items-center justify-center py-4 sticky z-10">
                    <div class="flex-grow border-2 border-gray-300 ml-6"></div>
                    <span class="text-lg sm:text-xl font-bold"><?php echo " &nbsp;&nbsp; " . $categoryName . " &nbsp;&nbsp; "; ?></span>
                    <div class="flex-grow border-2 border-gray-300 mr-6"></div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4 mb-4 cursor-pointer px-6">
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

    <script src="script.js"></script>
</body>
</html>
