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
    <title>Categories</title>
</head>
<body>
    <div class="flex items-center justify-between">
        <span class="text-2xl font-bold">Categories</span>
        <button class="bg-green-500 text-white text-sm font-bold px-1 py-2 rounded" onclick="window.location.href='add_category.php'">Add New</button>
    </div>

    <?php
        include 'db.php';

        $sql = "SELECT * FROM categories";
        $result = $conn->query($sql);
    ?>

    <div class="container mx-auto mt-5">
        <table class="table-auto w-full border-collapse border border-gray-300">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border border-gray-300 px-4 py-2 text-left">ID</th>
                    <th class="border border-gray-300 px-4 py-2 text-left">Name</th>
                    <th class="border border-gray-300 px-4 py-2 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr class="hover:bg-gray-100">
                            <td class="border border-gray-300 px-4 py-2 text-left"><?php echo $row['category_id']; ?></td>
                            <td class="border border-gray-300 px-4 py-2 text-left"><?php echo $row['category_name']; ?></td>
                            <td class="border border-gray-300 px-4 py-2 text-left">
                                <button class="bg-blue-500 text-white px-2 py-1 rounded">Edit</button>
                                <button class="bg-red-500 text-white px-2 py-1 rounded">Delete</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="border border-gray-300 px-4 py-2 text-center">No categories found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php $conn->close(); ?>
</body>
</html>