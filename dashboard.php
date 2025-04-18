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
    <title>Dashboard</title>
</head>
<body>
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="w-1/6 bg-gray-700 text-white p-4">
            <h2 class="text-2xl font-bold mb-4">Menu</h2>
            <ul>
                <li class="mb-2">
                    <button class="w-full text-left p-2 text-white bg-red-500 hover:bg-red-100 hover:text-black"
                        onclick="showDetail('products'); setActiveMenu(this)">Products</button>
                </li>
                <li class="mb-2">
                    <button class="w-full text-left p-2 hover:bg-red-100 hover:text-black"
                        onclick="showDetail('categories'); setActiveMenu(this)">Categories</button>
                </li>
                <li class="mb-2">
                    <button class="w-full text-left p-2 hover:bg-red-100 hover:text-black"
                        onclick="showDetail('settings'); setActiveMenu(this)">Settings</button>
                </li>
            </ul>
        </div>

        <!-- Content -->
        <div class="w-5/6 bg-gray-100 p-0">
            <div id="content" class="text-gray-800">
                <div class="container mx-auto p-4">
                    <h2 class="text-3xl font-bold">Welcome to the Dashboard</h2>
                    <p>Select a menu item to view details.</p>
                </div>
            </div>
        </div>
    </div>

    <script src="src/admin_script.js"></script>
</body>
</html>
