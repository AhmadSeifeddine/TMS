<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <!-- Header -->
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Gallery</h2>
                        <div class="flex space-x-3">
                            @if($galleries->count() > 0)
                                <!-- Download Button -->
                                <form method="POST" action="{{ route('gallery.create-zip') }}">
                                    @csrf
                                    <button type="submit"
                                            class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors duration-200">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        Zip All
                                    </button>
                                </form>
                            @endif

                            <!-- Add Images Button -->
                            <button onclick="openUploadModal()"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Add Images
                            </button>
                        </div>
                    </div>

                    <!-- Success/Error Messages -->
                    @if(session('success'))
                        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                            {{ session('error') }}
                        </div>
                    @endif

                                        @if(session('job_id'))
                        <script>
                        // Check zip job status
                        const jobId = '{{ session('job_id') }}';
                        let statusInterval;

                        function checkZipStatus() {
                            fetch(`/gallery/zip-status/${jobId}`)
                                .then(response => response.json())
                                .then(data => {
                                    if (data.status === 'completed') {
                                        clearInterval(statusInterval);
                                        showDownloadModal(data.download_url, data.filename);
                                    } else if (data.status === 'failed') {
                                        clearInterval(statusInterval);
                                        alert('Zip creation failed: ' + (data.message || 'Unknown error'));
                                    }
                                })
                                .catch(error => {
                                    console.error('Error checking zip status:', error);
                                });
                        }

                        function showDownloadModal(downloadUrl, filename) {
                            const modal = document.createElement('div');
                            modal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50';
                            modal.innerHTML = `
                                <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
                                    <div class="mt-3 text-center">
                                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 dark:bg-green-900">
                                            <svg class="h-6 w-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </div>
                                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mt-2">Zip Ready!</h3>
                                        <div class="mt-2 px-7 py-3">
                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                Your gallery zip is ready for download. Share this link with anyone:
                                            </p>
                                            <div class="mt-3">
                                                <input type="text"
                                                       value="${downloadUrl}"
                                                       readonly
                                                       class="w-full px-3 py-2 text-sm bg-gray-50 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                                                       onclick="this.select()">
                                            </div>
                                        </div>
                                        <div class="flex justify-center space-x-3 px-4 py-3">
                                            <button onclick="this.closest('.fixed').remove()"
                                                    class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded text-sm font-medium transition-colors duration-200">
                                                Close
                                            </button>
                                            <a href="${downloadUrl}"
                                               class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded text-sm font-medium transition-colors duration-200">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3"></path>
                                                </svg>
                                                Download
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            `;
                            document.body.appendChild(modal);
                        }

                        // Start polling
                        statusInterval = setInterval(checkZipStatus, 3000); // Check every 3 seconds
                        checkZipStatus(); // Initial check
                        </script>
                    @endif

                    <!-- Gallery Grid -->
                    @if($galleries->count() > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                            @foreach($galleries as $gallery)
                                @if($gallery->getFirstMediaUrl('gallery'))
                                    <div class="relative group bg-white dark:bg-gray-700 rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300">
                                        <div class="aspect-square">
                                            <img src="{{ $gallery->getFirstMediaUrl('gallery') }}"
                                                 alt="Gallery Image"
                                                 class="w-full h-full object-cover cursor-pointer"
                                                 onclick="openImageModal('{{ $gallery->getFirstMediaUrl('gallery') }}')">
                                        </div>

                                        <!-- Delete Button -->
                                        <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                            <button onclick="confirmDelete({{ $gallery->id }})"
                                                    class="bg-red-500 hover:bg-red-600 text-white p-2 rounded-full shadow-lg transition-colors duration-200">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </div>

                                        <!-- Image Info -->
                                        <div class="p-3">
                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                Uploaded {{ $gallery->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 48 48">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L32 26.414m-5-5l1.586-1.586a2 2 0 012.828 0L44 34.414M14 14l.01.01M38 38l.01.01"/>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No images</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by uploading your first image.</p>
                            <div class="mt-6">
                                <button onclick="openUploadModal()"
                                        class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Add Images
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Upload Modal -->
    <div id="uploadModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Upload Images</h3>
                    <button onclick="closeUploadModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form action="{{ route('gallery.store') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                    @csrf
                    <div class="mb-4">
                        <label for="images" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Select Images (JPEG, PNG, JPG, GIF - Max 10MB each)
                        </label>
                        <input type="file"
                               name="images[]"
                               id="images"
                               multiple
                               accept="image/*"
                               required
                               class="block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-gray-700 dark:file:text-gray-300">
                    </div>

                    <!-- Preview Area -->
                    <div id="imagePreview" class="mb-4 grid grid-cols-2 md:grid-cols-3 gap-4 hidden"></div>

                    <div class="flex justify-end space-x-3">
                        <button type="button"
                                onclick="closeUploadModal()"
                                class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded text-sm font-medium transition-colors duration-200">
                            Cancel
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded text-sm font-medium transition-colors duration-200">
                            Upload Images
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Image View Modal -->
    <div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 overflow-y-auto h-full w-full hidden z-50" onclick="closeImageModal()">
        <div class="relative top-1/2 transform -translate-y-1/2 mx-auto max-w-4xl">
            <div class="relative">
                <img id="modalImage" src="" alt="Gallery Image" class="w-full h-auto max-h-screen object-contain">
                <button onclick="closeImageModal()"
                        class="absolute top-4 right-4 text-white hover:text-gray-300 bg-black bg-opacity-50 rounded-full p-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900">
                    <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.866-.833-2.636 0L3.178 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Delete Image</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Are you sure you want to delete this image? This action cannot be undone.
                    </p>
                </div>
                <div class="flex justify-center space-x-3 px-4 py-3">
                    <button onclick="closeDeleteModal()"
                            class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded text-sm font-medium transition-colors duration-200">
                        Cancel
                    </button>
                    <form id="deleteForm" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded text-sm font-medium transition-colors duration-200">
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openUploadModal() {
            document.getElementById('uploadModal').classList.remove('hidden');
        }

        function closeUploadModal() {
            document.getElementById('uploadModal').classList.add('hidden');
            document.getElementById('uploadForm').reset();
            document.getElementById('imagePreview').classList.add('hidden');
            document.getElementById('imagePreview').innerHTML = '';
        }

        function openImageModal(src) {
            document.getElementById('modalImage').src = src;
            document.getElementById('imageModal').classList.remove('hidden');
        }

        function closeImageModal() {
            document.getElementById('imageModal').classList.add('hidden');
        }

        function confirmDelete(galleryId) {
            document.getElementById('deleteForm').action = `/gallery/${galleryId}`;
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }

        // Image preview functionality
        document.getElementById('images').addEventListener('change', function(e) {
            const files = e.target.files;
            const preview = document.getElementById('imagePreview');

            if (files.length > 0) {
                preview.classList.remove('hidden');
                preview.innerHTML = '';

                Array.from(files).forEach(file => {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const div = document.createElement('div');
                        div.className = 'relative';
                        div.innerHTML = `
                            <img src="${e.target.result}" alt="Preview" class="w-full h-24 object-cover rounded">
                            <p class="text-xs text-gray-500 mt-1 truncate">${file.name}</p>
                        `;
                        preview.appendChild(div);
                    };
                    reader.readAsDataURL(file);
                });
            } else {
                preview.classList.add('hidden');
            }
        });

        // Close modals when clicking outside
        document.getElementById('uploadModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeUploadModal();
            }
        });

        document.getElementById('deleteModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeDeleteModal();
            }
        });
    </script>
</x-app-layout>
