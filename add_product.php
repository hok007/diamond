<?php
    require 'db.php';

    try {
        $sql = "SELECT category_id, category_name FROM categories ORDER BY category_id ASC";
        $result = $conn->query($sql);
        if (!$result) {
            throw new Exception('Query error: ' . $conn->error);
        }
    
        $categories = [];
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row;
        }
    
        $conn->close();
    } catch (Exception $e) {
        $error = 'Error loading categories: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
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
    <title>Add Product</title>
    <style>
        select {
            color: #000000;
        }
        select:has(option[value=""]:checked) {
            color: #999999;
        }
        select option {
            color: #000000;
        }
    </style>
</head>
<body>
    <div class="container mx-auto p-4 h-screen overflow-y-scroll">
        <div class="flex items-center justify-between mb-4">
            <span class="text-2xl font-bold">Add New Product</span>
            <button class="bg-gray-400 text-white text-sm font-bold px-1 py-2 rounded hover:bg-gray-500" onclick="showDetail('products');">Back</button>
        </div>
        
        <div class="space-y-4 text-sm">
            <div>
                <label for="category" class="block text-sm font-medium text-gray-700 font-bold">Category</label>
                <select id="category" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 cursor-pointer">
                    <option value="" selected disabled hidden>Select a category</option>
                    <?php if (count($categories) > 0): ?>
                        <?php foreach ($categories as $row): ?>
                            <option value="<?php echo $row['category_id']; ?>"><?php echo $row['category_name']; ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div>
                <label for="code" class="block text-sm font-medium text-gray-700 font-bold">Product Code</label>
                <input type="text" id="code" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter product code">
            </div>
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 font-bold">Product Name</label>
                <input type="text" id="name" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter product name">
            </div>
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 font-bold">Description</label>
                <textarea id="description" rows="4" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter product description"></textarea>
            </div>
            <div>
                <label for="price" class="block text-sm font-medium text-gray-700 font-bold">Price</label>
                <input type="number" id="price" step="100" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter price">
            </div>
            <div>
                <label for="image" class="block text-sm font-medium text-gray-700 font-bold">Product Image</label>
                <input type="file" id="image" accept="image/*" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 file:cursor-pointer">
            </div>
            <div>
                <button type="button" onClick="addProduct()" class="mt-4 w-full bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">Add Product</button>
            </div>
        </div>  
    </div>
</body>
</html>
