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

function setActiveMenu(button) {
    const buttons = document.querySelectorAll('button');
    buttons.forEach(btn => btn.classList.remove('bg-red-500', 'text-white'));
    button.classList.add('bg-red-500', 'text-white');
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

function debounce(func, wait) {
    let timeout;
    return function (...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), wait);
    };
}

const debouncedSearch = debounce(searchProducts, 300);

function fetchAndRenderProducts(page = 1, searchTerm = '') {
    const rowsPerPage = document.querySelector('select[name="rows_per_page"]')?.value || 10;
    const url = `fetch_admin_product.php?page=${page}&rows_per_page=${rowsPerPage}&search=${encodeURIComponent(searchTerm)}`;

    axios.get(url)
        .then(response => {
            const data = response.data;
            const tBody = document.querySelector('#products_table tbody');
            const tFooter = document.querySelector('#products_table tfoot');

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
                                ${data.current_page > 1 ? `<button onclick="fetchAndRenderProducts(${data.current_page - 1}, '${encodeURIComponent(searchTerm)}')" class="bg-gray-300 px-3 py-1 rounded hover:bg-gray-400">Previous</button>` : ''}
                                ${data.current_page < data.total_pages ? `<button onclick="fetchAndRenderProducts(${data.current_page + 1}, '${encodeURIComponent(searchTerm)}')" class="bg-gray-300 px-3 py-1 rounded hover:bg-gray-400">Next</button>` : ''}
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
    fetchAndRenderProducts(1, searchTerm);
}

function changePageProducts(page, searchTerm = '') {
    fetchAndRenderProducts(page, searchTerm);
}

function changeRowsPerPageProducts(selectElement) {
    fetchAndRenderProducts(1);
}
