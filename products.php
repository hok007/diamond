<?php
    require 'db.php';

    try {
        $rows_per_page = 10;
        $page = 1;
        $offset = ($page - 1) * $rows_per_page;

        $sql_paginated = "SELECT c.category_name, p.product_id, p.code, p.name, p.description, p.image, p.price
            FROM categories c INNER JOIN products p ON c.category_id = p.category_id
            ORDER BY c.category_id ASC, p.product_id ASC LIMIT ?, ?";
        $stmt = $conn->prepare($sql_paginated);
        $stmt->bind_param('ii', $offset, $rows_per_page);
        $stmt->execute();
        $result_paginated = $stmt->get_result();

        $total_rows_query = "SELECT COUNT(*) as total FROM products";
        $total_rows_result = $conn->query($total_rows_query);
        if (!$total_rows_result) {
            throw new Exception('Total rows query error: ' . $conn->error);
        }
        $total_rows = $total_rows_result->fetch_assoc()['total'];
        $total_pages = ceil($total_rows / $rows_per_page);

        $adminProducts = [];
        while ($row = $result_paginated->fetch_assoc()) {
            $adminProducts[] = $row;
        }

        $stmt->close();
        $conn->close();
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
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
    <title>Products</title>
</head>
<body>
    <div class="container mx-auto p-4 h-screen overflow-y-scroll">
        <div class="flex items-center justify-between mb-4">
            <span class="text-2xl font-bold">Products</span>
            <button class="bg-green-500 text-white text-sm font-bold px-1 py-2 rounded" onclick="window.location.href='add_product.php'">Add New</button>
        </div>
        
        <div class="flex justify-between items-center mt-5 mb-3">
            <div class="flex items-center space-x-2">
                <label for="rows_per_page" class="text-sm">Rows per page:</label>
                <select id="rows_per_page" name="rows_per_page" class="border border-gray-300 rounded px-2 py-1" onchange="changeRowsPerPageProducts(this)">
                    <option value="10" <?php echo $rows_per_page == 10 ? 'selected' : ''; ?>>10</option>
                    <option value="20" <?php echo $rows_per_page == 20 ? 'selected' : ''; ?>>20</option>
                    <option value="50" <?php echo $rows_per_page == 50 ? 'selected' : ''; ?>>50</option>
                </select>
            </div>
            <div class="flex items-center space-x-2">
                <label for="searchInput" class="text-sm">Search:</label>
                <input type="text" id="searchInput" name="search" class="border border-gray-300 rounded px-2 py-1" placeholder="" oninput="debouncedSearchProducts(this.value)">
                <button onclick="document.getElementById('searchInput').value = ''; searchProducts('')" class="bg-gray-300 text-sm px-2 py-1 rounded ml-2 hover:bg-gray-400">Clear</button>
            </div>
        </div>

        <table id="products_table" class="w-full border-collapse border border-gray-300">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border border-gray-300 px-4 py-2 text-left">No</th>
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
                <?php if (count($adminProducts) > 0): ?>
                    <?php foreach ($adminProducts as $row): ?>
                        <tr>
                            <td class="border border-gray-300 px-4 py-2 text-center text-base whitespace-nowrap"><?php echo $row['product_id']; ?></td>
                            <td class="border border-gray-300 px-4 py-2 text-left text-base whitespace-nowrap"><?php echo $row['code']; ?></td>
                            <td class="border border-gray-300 px-4 py-2 text-left text-base whitespace-nowrap"><?php echo $row['name']; ?></td>
                            <td class="border border-gray-300 px-4 py-2 text-left text-base"><?php echo $row['description']; ?></td>
                            <td class="border border-gray-300 px-4 py-2 text-left text-base whitespace-nowrap"><?php echo isset($row['price']) ? '$' . number_format($row['price'], 2) : ''; ?></td>
                            <td class="border border-gray-300 px-4 py-2 text-left text-base whitespace-nowrap"><?php echo $row['category_name']; ?></td>
                            <td class="border border-gray-300 px-4 py-2 text-left text-base">
                                <?php 
                                    $images = json_decode($row['image'], true) ?? []; 
                                    echo implode('', array_map(function($img) use ($row) {
                                        return "<img src=\"{$img}\" alt=\"{$row['name']}\" class=\"w-12 h-12 object-cover inline-block m-[2px]\">";
                                    }, $images));
                                ?>
                            </td>
                            <td class="border border-gray-300 px-4 py-2 text-left text-base whitespace-nowrap">
                                <button onclick="editProduct('<?php echo htmlspecialchars($row['code'], ENT_QUOTES, 'UTF-8'); ?>')" class="bg-blue-500 text-white px-2 py-1 rounded">Edit</button> 
                                <button onclick="deleteProduct('<?php echo htmlspecialchars($row['code'], ENT_QUOTES, 'UTF-8'); ?>')" class="bg-red-500 text-white px-2 py-1 rounded">Delete</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="border border-gray-300 px-4 py-2 text-center">No records found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="8" class="border border-gray-300 px-4 py-2 text-center">
                        <div class="flex justify-between items-center space-x-2">
                            <span class="text-sm">Showing <?php echo $offset + 1; ?> to <?php echo min($offset + $rows_per_page, $total_rows); ?> of <?php echo $total_rows; ?> entries</span>
                            <div class="flex items-center space-x-2">
                                <?php if ($page > 1): ?>
                                    <button onclick="changePageProducts(<?php echo $page - 1; ?>)" class="bg-gray-300 px-3 py-1 rounded hover:bg-gray-400">Previous</button>
                                <?php endif; ?>
                                <?php if ($page < $total_pages): ?>
                                    <button onclick="changePageProducts(<?php echo $page + 1; ?>)" class="bg-gray-300 px-3 py-1 rounded hover:bg-gray-400">Next</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</body>
</html>
