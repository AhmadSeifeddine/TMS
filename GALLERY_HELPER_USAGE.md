# GalleryZipHelper Usage Guide

The `GalleryZipHelper` is a utility class that provides easy-to-use methods for managing gallery zip operations in API endpoints and other controllers.

## Features

- **Queue-based zip creation** - Creates zips asynchronously
- **Status tracking** - Check zip job status
- **File management** - List, delete, and get info about zip files
- **Statistics** - Get gallery and zip statistics
- **Cleanup utilities** - Remove old zip files automatically

## Basic Usage

### 1. Create a Gallery Zip

```php
use App\Helpers\GalleryZipHelper;

$result = GalleryZipHelper::createZip();

if ($result['success']) {
    $jobId = $result['data']['job_id'];
    $filename = $result['data']['filename'];
    // Store jobId for status checking
} else {
    // Handle error: $result['message']
}
```

### 2. Check Zip Status

```php
$result = GalleryZipHelper::checkZipStatus($jobId);

$status = $result['data']['status']; // 'processing', 'completed', or 'failed'

if ($status === 'completed') {
    $downloadUrl = $result['data']['download_url'];
    $filename = $result['data']['filename'];
}
```

### 3. Get Available Zips

```php
$result = GalleryZipHelper::getAvailableZips();

foreach ($result['data'] as $zip) {
    echo "File: " . $zip['filename'];
    echo "Size: " . $zip['size_human'];
    echo "URL: " . $zip['download_url'];
}
```

### 4. Get Gallery Statistics

```php
$result = GalleryZipHelper::getGalleryStats();

$stats = $result['data'];
echo "Total Images: " . $stats['total_images'];
echo "Total Size: " . $stats['total_media_size_human'];
echo "Available Zips: " . $stats['available_zips'];
```

## API Controller Example

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Helpers\GalleryZipHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MyApiController extends Controller
{
    public function createGalleryZip(): JsonResponse
    {
        $result = GalleryZipHelper::createZip();
        return response()->json($result, $result['success'] ? 200 : 400);
    }

    public function checkZipProgress(string $jobId): JsonResponse
    {
        $result = GalleryZipHelper::checkZipStatus($jobId);
        return response()->json($result);
    }

    public function getZipList(): JsonResponse
    {
        $result = GalleryZipHelper::getAvailableZips();
        return response()->json($result);
    }
}
```

## Available API Endpoints

Based on the included `GalleryApiController`, you can use these endpoints:

```
GET    /api/gallery/stats                    - Get gallery statistics
GET    /api/gallery/images                   - Get all images
POST   /api/gallery/images                   - Upload new images
DELETE /api/gallery/images/{gallery}        - Delete an image

POST   /api/gallery/zip/create               - Create new zip
GET    /api/gallery/zip/status/{jobId}       - Check zip status
GET    /api/gallery/zip/available            - Get available zips
GET    /api/gallery/zip/info/{filename}      - Get zip file info
DELETE /api/gallery/zip/{filename}          - Delete zip file
POST   /api/gallery/zip/cleanup              - Clean up old zips
```

## Method Reference

### `createZip()`
Creates a new gallery zip file asynchronously.

**Returns:**
```php
[
    'success' => true,
    'message' => 'Zip creation started',
    'data' => [
        'job_id' => 'uuid-string',
        'filename' => 'gallery-2024-01-15-14-30-25.zip',
        'status' => 'processing'
    ]
]
```

### `checkZipStatus(string $jobId)`
Checks the status of a zip creation job.

**Returns:**
```php
[
    'success' => true,
    'data' => [
        'status' => 'completed', // or 'processing', 'failed'
        'download_url' => 'http://...', // if completed
        'filename' => 'gallery-2024-01-15-14-30-25.zip' // if completed
    ]
]
```

### `getAvailableZips()`
Gets list of all available zip files.

**Returns:**
```php
[
    'success' => true,
    'data' => [
        [
            'filename' => 'gallery-2024-01-15-14-30-25.zip',
            'size' => 1024000,
            'size_human' => '1.02 MB',
            'last_modified' => '2024-01-15T14:30:25.000000Z',
            'download_url' => 'http://...'
        ]
    ]
]
```

### `deleteZip(string $filename)`
Deletes a specific zip file.

### `cleanupOldZips(int $olderThanHours = 24)`
Removes zip files older than specified hours.

### `getGalleryStats()`
Gets comprehensive gallery statistics.

### `getZipDownloadInfo(string $filename)`
Gets detailed information about a specific zip file.

## Frontend Integration Example

```javascript
// Create zip
async function createZip() {
    const response = await fetch('/api/gallery/zip/create', { method: 'POST' });
    const result = await response.json();
    
    if (result.success) {
        pollZipStatus(result.data.job_id);
    }
}

// Poll for completion
async function pollZipStatus(jobId) {
    const response = await fetch(`/api/gallery/zip/status/${jobId}`);
    const result = await response.json();
    
    if (result.data.status === 'completed') {
        showDownloadLink(result.data.download_url);
    } else if (result.data.status === 'processing') {
        setTimeout(() => pollZipStatus(jobId), 2000); // Check again in 2s
    }
}
```

## Error Handling

All methods return a consistent format:

```php
[
    'success' => boolean,
    'message' => 'Human readable message',
    'data' => mixed|null
]
```

Always check the `success` field before using the `data`.

## Customization

You can extend the helper by adding your own static methods or create instance methods for more complex scenarios. The helper is designed to be flexible and reusable across different parts of your application. 
