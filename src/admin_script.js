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
    } else if (section === 'addProduct') {
        axios.get('add_product.php')
            .then(response => { content.innerHTML = response.data; })
            .catch(error => {
                Swal.fire('Error', 'Failed to load content. Please try again.', 'error');
                console.error('Error loading settings content:', error);
                content.innerHTML = `<p class="text-red-500">Failed to load add product content.</p>`;
            });
    }
}

function setActiveMenu(button) {
    const buttons = document.querySelectorAll('button');
    buttons.forEach(btn => btn.classList.remove('bg-red-500', 'text-white'));
    button.classList.add('bg-red-500', 'text-white');
}

function debounce(func, wait) {
    let timeout;
    return function (...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), wait);
    };
}

function init() {
    const initialSection = 'products';
    showDetail(initialSection);
}

window.addEventListener('DOMContentLoaded', init);

function previewImagesSwal() {
    const files = document.getElementById('image').files;

    if (files.length === 0) {
        Swal.fire('No images selected', 'Please choose one or more images.', 'warning');
        return;
    }

    let htmlContent = '';
    const readers = [];

    for (let i = 0; i < files.length; i++) {
        const reader = new FileReader();
        readers.push(reader);

        reader.onload = function(e) {
            const fileName = files[readers.indexOf(reader)].name;
            htmlContent += `
                <div class="flex flex-col items-center m-1">
                    <img src="${e.target.result}" class="w-48 h-48 object-cover rounded shadow">
                    <span class="text-sm mt-1 text-center">${fileName}</span>
                </div>`;
        
            if (readers.length === files.length && readers.every(r => r.readyState === 2)) {
                Swal.fire({
                    title: 'Selected Images',
                    html: `<div class="flex flex-wrap justify-center">${htmlContent}</div>`,
                    showCloseButton: true,
                    showConfirmButton: false
                });
            }
        };

        reader.readAsDataURL(files[i]);
    }
}

let currentSearchTermProducts = '';

async function addProduct() {
    const category = document.getElementById('category').value;
    const code = document.getElementById('code').value;
    const name = document.getElementById('name').value;
    const description = document.getElementById('description').value;
    const price = document.getElementById('price').value;
    const images = document.getElementById('image').files;

    if (!category || !code || !name || !price) {
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Please fill in all required fields!',
        });
        return;
    }

    const formData = new FormData();
    formData.append('category', category);
    formData.append('code', code);
    formData.append('name', name);
    formData.append('description', description);
    formData.append('price', price);

    for (let i = 0; i < images.length; i++) {
        formData.append('image[]', images[i]);
    }

    try {
        const response = await fetch('admin_insert_product.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (!response.ok || result.error) {
            throw new Error(result.error || 'Failed to add product');
        }

        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: 'Product added successfully!',
        }).then(() => {
            document.getElementById('category').value = '';
            document.getElementById('code').value = '';
            document.getElementById('name').value = '';
            document.getElementById('description').value = '';
            document.getElementById('price').value = '';
            document.getElementById('image').value = '';
        });
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Failed to add product: ' + error.message,
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

function fetchAndRenderProducts(page = 1) {
    if (currentSearchTermProducts.includes('%')) currentSearchTermProducts = decodeURIComponent(currentSearchTermProducts);

    const rowsPerPage = document.querySelector('select[name="rows_per_page"]')?.value || 10;
    const url = `admin_fetch_product.php?page=${page}&rows_per_page=${rowsPerPage}&search=${encodeURIComponent(currentSearchTermProducts)}`;

    axios.get(url)
        .then(response => {
            const data = response.data;
            const tBody = document.querySelector('#products_table tbody');
            const tFooter = document.querySelector('#products_table tfoot');

            tBody.innerHTML = '<tr><td colspan="8" class="text-center py-4">Loading...</td></tr>';

            if (!data.success || data.products.length === 0) {
                tBody.innerHTML = `
                    <tr><td colspan="8" class="text-center py-4">No products found.</td></tr>
                `;
                tFooter.innerHTML = '';
                return;
            }

            const rowsBodyHTML = data.products.map(product => {
                const images = JSON.parse(product.image);
                const imageTags = images.map(img => `<img src="${img}" alt="${img}" class="w-12 h-12 object-cover inline-block m-[2px]" >`).join('');
                const price = product.price ? '$' + parseFloat(product.price).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) : '';

                return `
                    <tr>
                        <td class="border border-gray-300 px-4 py-2 text-center text-base whitespace-nowrap">${product.product_id}</td>
                        <td class="border border-gray-300 px-4 py-2 text-left text-base whitespace-nowrap">${product.code}</td>
                        <td class="border border-gray-300 px-4 py-2 text-left text-base whitespace-nowrap">${product.name}</td>
                        <td class="border border-gray-300 px-4 py-2 text-left text-base">${product.description}</td>
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

            const rowsFooterHTML = `
                <tr>
                    <td colspan="8" class="border border-gray-300 px-4 py-2 text-center">
                        <div class="flex justify-between items-center space-x-2">
                            <span class="text-sm">Showing ${data.offset + 1} to ${Math.min(data.offset + data.rows_per_page, data.total_rows)} of ${data.total_rows} entries</span>
                            <div class="flex items-center space-x-2">
                                ${data.current_page > 1 ? `<button onclick="fetchAndRenderProducts(${data.current_page - 1})" class="bg-gray-300 px-3 py-1 rounded hover:bg-gray-400">Previous</button>` : ''}
                                ${data.current_page < data.total_pages ? `<button onclick="fetchAndRenderProducts(${data.current_page + 1})" class="bg-gray-300 px-3 py-1 rounded hover:bg-gray-400">Next</button>` : ''}
                            </div>
                        </div>
                    </td>
                </tr>
            `;

            tBody.innerHTML = rowsBodyHTML;
            tFooter.innerHTML = rowsFooterHTML;
        })
        .catch(error => {
            Swal.fire('Error', 'Failed to load content. Please try again.', 'error');
            console.error('Error loading products content:', error);
        });
}

function searchProducts(searchTerm) {
    currentSearchTermProducts = searchTerm;
    fetchAndRenderProducts(1);
}

function changePageProducts(page) {
    fetchAndRenderProducts(page);
}

function changeRowsPerPageProducts(selectElement) {
    fetchAndRenderProducts(1);
}

const debouncedSearchProducts = debounce(searchProducts, 300);

let currentSearchTermCategories = '';

function editCategory(categoryId) {
    Swal.fire({
        title: 'Edit Category',
        html: `<input type="text" id="categoryId" class="swal2-input" value="${categoryId}" readonly>`,
        focusConfirm: false,
        showCancelButton: true,
        confirmButtonText: 'Save',
        cancelButtonText: 'Cancel',
        preConfirm: () => {
            const categoryId = document.getElementById('categoryId').value;
            console.log('Category edited:', categoryId);
            Swal.fire('Saved!', '', 'success');
        }
    });
}

function deleteCategory(categoryId) {
    Swal.fire({
        title: 'Delete Category',
        text: "Are you sure you want to delete this category?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            console.log('Category deleted:', categoryId);
            Swal.fire('Deleted!', 'Your category has been deleted.', 'success');
        }
    });
}

function fetchAndRenderCategories(page = 1) {
    if (currentSearchTermCategories.includes('%')) currentSearchTermCategories = decodeURIComponent(currentSearchTermCategories);

    const rowsPerPage = document.querySelector('select[name="rows_per_page"]')?.value || 10;
    const url = `admin_fetch_category.php?page=${page}&rows_per_page=${rowsPerPage}&search=${encodeURIComponent(currentSearchTermCategories)}`;

    axios.get(url)
        .then(response => {
            const data = response.data;
            const tBody = document.querySelector('#categories_table tbody');
            const tFooter = document.querySelector('#categories_table tfoot');

            tBody.innerHTML = '<tr><td colspan="8" class="text-center py-4">Loading...</td></tr>';

            if (!data.success || data.categories.length === 0) {
                tBody.innerHTML = `
                    <tr><td colspan="8" class="text-center py-4">No categories found.</td></tr>
                `;
                tFooter.innerHTML = '';
                return;
            }

            const rowsBodyHTML = data.categories.map(category => {
                return `
                    <tr>
                        <td class="border border-gray-300 px-4 py-2 text-left text-base whitespace-nowrap">${category.category_id}</td>
                        <td class="border border-gray-300 px-4 py-2 text-left text-base whitespace-nowrap">${category.category_name}</td>
                        <td class="border border-gray-300 px-4 py-2 text-left text-base whitespace-nowrap">
                            <button onclick="editCategory('${category.category_id}')" class="bg-blue-500 text-white px-2 py-1 rounded">Edit</button> 
                            <button onclick="deleteCategory('${category.category_id}')" class="bg-red-500 text-white px-2 py-1 rounded">Delete</button>
                        </td>
                    </tr>
                `;
            }).join('');

            const rowsFooterHTML = `
                <tr>
                    <td colspan="8" class="border border-gray-300 px-4 py-2 text-center">
                        <div class="flex justify-between items-center space-x-2">
                            <span class="text-sm">Showing ${data.offset + 1} to ${Math.min(data.offset + data.rows_per_page, data.total_rows)} of ${data.total_rows} entries</span>
                            <div class="flex items-center space-x-2">
                                ${data.current_page > 1 ? `<button onclick="fetchAndRenderCategories(${data.current_page - 1})" class="bg-gray-300 px-3 py-1 rounded hover:bg-gray-400">Previous</button>` : ''}
                                ${data.current_page < data.total_pages ? `<button onclick="fetchAndRenderCategories(${data.current_page + 1})" class="bg-gray-300 px-3 py-1 rounded hover:bg-gray-400">Next</button>` : ''}
                            </div>
                        </div>
                    </td>
                </tr>
            `;

            tBody.innerHTML = rowsBodyHTML;
            tFooter.innerHTML = rowsFooterHTML;
        })
        .catch(error => {
            Swal.fire('Error', 'Failed to load content. Please try again.', 'error');
            console.error('Error loading categories content:', error);
        });
}

function searchCategories(searchTerm) {
    currentSearchTermCategories = searchTerm;
    fetchAndRenderCategories(1);
}

function changePageCategories(page) {
    fetchAndRenderCategories(page);
}

function changeRowsPerPageCategories(selectElement) {
    fetchAndRenderCategories(1);
}

const debouncedSearchCategories = debounce(searchCategories, 300);
