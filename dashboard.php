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
                    <button class="w-full text-left p-2 hover:bg-red-100 hover:text-black focus:bg-red-500"
                        onclick="showDetail('categories')">Categories</button>
                </li>
                <li class="mb-2">
                    <button class="w-full text-left p-2 hover:bg-red-100 hover:text-black focus:bg-red-500"
                        onclick="showDetail('products')">Products</button>
                </li>
                <li class="mb-2">
                    <button class="w-full text-left p-2 hover:bg-red-100 hover:text-black focus:bg-red-500"
                        onclick="showDetail('settings')">Settings</button>
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

    <script>
        function showDetail(section) {
            const content = document.getElementById('content');
            content.innerHTML = '<p class="p-4">Loading...</p>';
            if (section === 'categories') {
                axios.get('categories.php')
                    .then(response => { content.innerHTML = response.data; })
                    .catch(error => {
                        Swal.fire('Error', 'Failed to load content. Please try again.', 'error');
                        console.error('Error loading categories content:', error);
                        content.innerHTML = `<p class="text-red-500">Failed to load categories content.</p>`;
                    });
            } else if (section === 'products') {
                axios.get('products.php')
                    .then(response => { content.innerHTML = response.data; })
                    .catch(error => {
                        Swal.fire('Error', 'Failed to load content. Please try again.', 'error');
                        console.error('Error loading products content:', error);
                        content.innerHTML = `<p class="text-red-500">Failed to load products content.</p>`;
                    });
            } else if (section === 'settings') {
                axios.get('settings.php')
                    .then(response => { content.innerHTML = response.data; })
                    .catch(error => {
                        Swal.fire('Error', 'Failed to load content. Please try again.', 'error');
                        console.error('Error loading settings content:', error);
                        content.innerHTML = `<p class="text-red-500">Failed to load settings content.</p>`;
                    });
            }
        }

        function editProduct(productCode) {
            Swal.fire({
                title: 'Edit Product',
                html: `<input type="text" id="productCode" class="swal2-input" value="${productCode}" readonly>`,
                focusConfirm: false,
                showCancelButton: true,
                confirmButtonText: 'Save',
                cancelButtonText: 'Cancel',
                preConfirm: () => {
                    const productCode = document.getElementById('productCode').value;
                    console.log('Product edited:', productCode);
                    Swal.fire('Saved!', '', 'success');
                }
            });
        }

        function deleteProduct(productCode) {
            Swal.fire({
                title: 'Delete Product',
                text: "Are you sure you want to delete this product?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    console.log('Product deleted:', productCode);
                    Swal.fire('Deleted!', 'Your product has been deleted.', 'success');
                }
            });
        }

        function changeRowsPerPage(selectElement) {
            const rowsPerPage = selectElement.value;

            axios.get(`fetch_admin_product.php?rows_per_page=${rowsPerPage}`)
                .then(response => {
                    const data = response.data;
                    const productsTable = document.getElementById('products_table_detail');

                    if (!data.success || data.products.length === 0) {
                        productsTable.innerHTML = `
                            <tr><td colspan="8" class="text-center py-4">No products found.</td></tr>
                        `;
                        return;
                    }

                    const rowsHTML = data.products.map(product => {
                        const images = JSON.parse(product.image);
                        const imageTags = images.map(img => `<img src="${img}" alt="${img}" class="w-12 h-12 object-cover inline-block m-[2px]" >`).join('');
                        const price = product.price ? '$' + parseFloat(product.price).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) : '';

                        return  `
                            <tr>
                                <td class="border border-gray-300 px-4 py-2 text-center text-base whitespace-nowrap">${product.product_id}</td>
                                <td class="border border-gray-300 px-4 py-2 text-left text-base whitespace-nowrap">${product.code}</td>
                                <td class="border border-gray-300 px-4 py-2 text-left text-base whitespace-nowrap">${product.name}</td>
                                <td class="border border-gray-300 px-4 py-2 text-left text-base whitespace-nowrap">${product.description}</td>
                                <td class="border border-gray-300 px-4 py-2 text-left text-base whitespace-nowrap">${price}</td>
                                <td class="border border-gray-300 px-4 py-2 text-left text-base whitespace-nowrap">${product.category_name}</td>
                                <td class="border border-gray-300 px-4 py-2 text-left text-base">${imageTags}</td>
                                <td class="border border-gray-300 px-4 py-2 text-left text-base whitespace-nowrap">
                                    <button onclick="editProduct('${product.code}')" class="bg-blue-500 text-white px-2 py-1 rounded">Edit</button> 
                                    <button onclick="deleteProduct('${product.code}')" class="bg-red-500 text-white px-2 py-1 rounded">Delete</button>
                                </td>
                            </tr>
                        `;
                    }).join('');

                    productsTable.innerHTML = ``;
                    productsTable.innerHTML = rowsHTML;
                })
                .catch(error => {
                    Swal.fire('Error', 'Failed to load content. Please try again.', 'error');
                    console.error('Error loading products content:', error);
                });
        }
    </script>
</body>

</html>