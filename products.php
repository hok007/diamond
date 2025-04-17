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
    <title>Products</title>
</head>
<body>
    <div class="full-height-container">
        <div class="flex items-center justify-between">
            <span class="text-2xl font-bold">Products</span>
            <button class="bg-green-500 text-white text-sm font-bold px-1 py-2 rounded" onclick="window.location.href='add_product.php'">Add New</button>
        </div>

        <?php
            include 'db.php';
            $sql = "SELECT * FROM products";
            $result = $conn->query($sql);
        ?>

        <div class="container mx-auto mt-5">
            <table class="table-auto w-full border-collapse border border-gray-300">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="border border-gray-300 px-4 py-2 text-left">Code</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Name</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Description</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Price</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Category</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Image</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $rows_per_page = 10;
                        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                        $offset = ($page - 1) * $rows_per_page;

                        $sql_paginated = "SELECT * FROM products LIMIT $offset, $rows_per_page";
                        $result_paginated = $conn->query($sql_paginated);

                        $total_rows = $result->num_rows;
                        $total_pages = ceil($total_rows / $rows_per_page);
                    ?>

                    <?php if ($result_paginated->num_rows > 0): ?>
                        <?php while ($row = $result_paginated->fetch_assoc()): ?>
                            <tr class="hover:bg-gray-100">
                                <td class="border border-gray-300 px-4 py-2 text-left whitespace-nowrap"><?php echo $row['code']; ?></td>
                                <td class="border border-gray-300 px-4 py-2 text-left whitespace-nowrap"><?php echo $row['name']; ?></td>
                                <td class="border border-gray-300 px-4 py-2 text-left"><?php echo $row['description']; ?></td>
                                <td class="border border-gray-300 px-4 py-2 text-left whitespace-nowrap"><?php echo isset($row['price']) ? '$' . number_format($row['price'], 2) : 'Price not available'; ?></td>
                                <td class="border border-gray-300 px-4 py-2 text-left whitespace-nowrap"><?php echo $row['category_id']; ?></td>
                                <td class="border border-gray-300 px-4 py-2 text-left">
                                    <?php $images = json_decode($row['image'], true); foreach ($images as $image): ?>
                                        <img src="<?php echo $image; ?>" alt="<?php echo $row['name']; ?>" class="w-16 h-16 object-cover inline-block m-[2px]">
                                    <?php endforeach; ?></td>
                                <td class="border border-gray-300 px-4 py-2 text-left whitespace-nowrap">
                                    <button onclick="editProduct('<?php echo htmlspecialchars($row['code'], ENT_QUOTES, 'UTF-8'); ?>')" class="bg-blue-500 text-white px-2 py-1 rounded">Edit</button> 
                                    <button onclick="deleteProduct('<?php echo htmlspecialchars($row['code'], ENT_QUOTES, 'UTF-8'); ?>')" class="bg-red-500 text-white px-2 py-1 rounded">Delete</button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php endif; ?>

                    <tr>
                        <td colspan="7" class="border border-gray-300 px-4 py-2 text-center">
                            <div class="flex justify-center space-x-2">
                                <?php if ($page > 1): ?>
                                    <a href="?page=<?php echo $page - 1; ?>" class="bg-gray-300 px-3 py-1 rounded">Previous</a>
                                <?php endif; ?>
                                <?php if ($page < $total_pages): ?>
                                    <a href="?page=<?php echo $page + 1; ?>" class="bg-gray-300 px-3 py-1 rounded">Next</a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <?php $conn->close(); ?>
    </div>
</body>
</html>
