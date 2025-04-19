<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image Preview</title>
    <!-- Include SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Include Tailwind CSS for styling (optional, since your classes suggest Tailwind) -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <div>
        <label for="image" class="block text-sm font-medium text-gray-700 font-bold">Product Image</label>
        <input type="file" id="image" name="image[]" accept="image/*" multiple class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 file:cursor-pointer">
        <div id="preview-list" class="mt-2 flex flex-wrap gap-2"></div>
        <button onclick="previewImagesSwal()" class="mt-2 px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">View Images in Swal</button>
    </div>

    <!-- Place the script at the end of the body to ensure DOM is loaded -->
    <script>
        // Global array to store files in the order of display
        let selectedFiles = [];

        function setupFileInput() {
            const input = document.getElementById('image');
            const previewList = document.getElementById('preview-list');

            // Check if elements exist
            if (!input || !previewList) {
                console.error('Required elements not found: input=', input, 'previewList=', previewList);
                return;
            }

            input.addEventListener('change', (event) => {
                const newFiles = Array.from(event.target.files);

                // Add new files to selectedFiles (browser may have sorted them)
                newFiles.forEach(file => {
                    if (!selectedFiles.some(f => f.name === file.name && f.size === f.size && f.lastModified === f.lastModified)) {
                        selectedFiles.push(file);
                    }
                });

                // Update the preview list
                updatePreviewList();
            });

            // Make the preview list sortable (basic drag-and-drop simulation)
            previewList.addEventListener('dragstart', (e) => {
                e.dataTransfer.setData('text/plain', e.target.dataset.index);
            });

            previewList.addEventListener('dragover', (e) => {
                e.preventDefault();
            });

            previewList.addEventListener('drop', (e) => {
                e.preventDefault();
                const fromIndex = parseInt(e.dataTransfer.getData('text/plain'));
                const toIndex = parseInt(e.target.closest('.preview-item')?.dataset.index);

                if (!isNaN(fromIndex) && !isNaN(toIndex) && fromIndex !== toIndex) {
                    // Reorder selectedFiles
                    const [movedFile] = selectedFiles.splice(fromIndex, 1);
                    selectedFiles.splice(toIndex, 0, movedFile);
                    updatePreviewList();
                }
            });
        }

        function updatePreviewList() {
            const previewList = document.getElementById('preview-list');
            previewList.innerHTML = '';

            selectedFiles.forEach((file, index) => {
                const previewItem = document.createElement('div');
                previewItem.className = 'preview-item p-2 border rounded flex items-center gap-2';
                previewItem.draggable = true;
                previewItem.dataset.index = index;

                const fileName = document.createElement('span');
                fileName.textContent = file.name;
                fileName.className = 'text-sm text-gray-700';

                const removeBtn = document.createElement('button');
                removeBtn.textContent = 'Remove';
                removeBtn.className = 'text-red-500 text-sm';
                removeBtn.onclick = () => {
                    selectedFiles.splice(index, 1);
                    updatePreviewList();
                };

                previewItem.appendChild(fileName);
                previewItem.appendChild(removeBtn);
                previewList.appendChild(previewItem);
            });

            console.log('Current order:', selectedFiles.map(file => file.name));
        }

        function previewImagesSwal() {
            if (selectedFiles.length === 0) {
                Swal.fire('No images selected', 'Please choose one or more images.', 'warning');
                return;
            }

            console.log('Selected files (final order):', selectedFiles.map(file => file.name));

            const readPromises = [];

            for (let i = 0; i < selectedFiles.length; i++) {
                const file = selectedFiles[i];
                const reader = new FileReader();

                const filePromise = new Promise((resolve, reject) => {
                    reader.onload = function (e) {
                        resolve({
                            dataUrl: e.target.result,
                            name: file.name
                        });
                    };

                    reader.onerror = function () {
                        reject(`Failed to read ${file.name}`);
                    };

                    reader.readAsDataURL(file);
                });

                readPromises.push(filePromise);
            }

            Promise.all(readPromises)
                .then((results) => {
                    console.log('Processed results:', results.map(result => result.name));

                    let htmlContent = '';
                    results.forEach((result) => {
                        htmlContent += `
                            <div class="image-preview-container">
                                <img src="${result.dataUrl}" class="w-48 h-48 object-cover rounded m-1 shadow">
                                <span class="text-sm text-gray-500">${result.name}</span>
                            </div>
                        `;
                    });

                    Swal.fire({
                        title: 'Selected Images',
                        html: `<div class="flex flex-wrap justify-center">${htmlContent}</div>`,
                        showCloseButton: true,
                        showConfirmButton: false
                    });
                })
                .catch((error) => {
                    Swal.fire('Error', error, 'error');
                });
        }

        // Wait for the DOM to be fully loaded before running setupFileInput
        document.addEventListener('DOMContentLoaded', () => {
            setupFileInput();
        });
    </script>
</body>
</html>