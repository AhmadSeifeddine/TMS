<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\GalleryApiController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Gallery API routes
Route::prefix('gallery')->group(function () {
    // Get gallery statistics
    Route::get('stats', [GalleryApiController::class, 'stats']);

    // Get all images
    Route::get('images', [GalleryApiController::class, 'getImages']);

    // Upload new images
    Route::post('images', [GalleryApiController::class, 'uploadImages']);

    // Delete an image
    Route::delete('images/{gallery}', [GalleryApiController::class, 'deleteImage']);

    // Zip operations
    Route::prefix('zip')->group(function () {
        // Create new zip
        Route::post('create', [GalleryApiController::class, 'createZip']);

        // Check zip job status
        Route::get('status/{jobId}', [GalleryApiController::class, 'checkZipStatus']);

        // Get all available zips
        Route::get('available', [GalleryApiController::class, 'getAvailableZips']);

        // Get zip file info
        Route::get('info/{filename}', [GalleryApiController::class, 'getZipInfo']);

        // Delete zip file
        Route::delete('{filename}', [GalleryApiController::class, 'deleteZip']);

        // Clean up old zip files
        Route::post('cleanup', [GalleryApiController::class, 'cleanupZips']);
    });
});
