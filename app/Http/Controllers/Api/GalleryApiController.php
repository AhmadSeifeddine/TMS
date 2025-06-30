<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Helpers\GalleryZipHelper;
use App\Models\Gallery;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GalleryApiController extends Controller
{
    /**
     * Get gallery statistics
     *
     * @return JsonResponse
     */
    public function stats(): JsonResponse
    {
        $result = GalleryZipHelper::getGalleryStats();

        return response()->json($result, $result['success'] ? 200 : 400);
    }

    /**
     * Create a new gallery zip
     *
     * @return JsonResponse
     */
    public function createZip(): JsonResponse
    {
        $result = GalleryZipHelper::createZip();

        return response()->json($result, $result['success'] ? 200 : 400);
    }

    /**
     * Check zip job status
     *
     * @param string $jobId
     * @return JsonResponse
     */
    public function checkZipStatus(string $jobId): JsonResponse
    {
        $result = GalleryZipHelper::checkZipStatus($jobId);

        return response()->json($result, 200);
    }

    /**
     * Get all available zip files
     *
     * @return JsonResponse
     */
    public function getAvailableZips(): JsonResponse
    {
        $result = GalleryZipHelper::getAvailableZips();

        return response()->json($result, 200);
    }

    /**
     * Get zip download information
     *
     * @param string $filename
     * @return JsonResponse
     */
    public function getZipInfo(string $filename): JsonResponse
    {
        $result = GalleryZipHelper::getZipDownloadInfo($filename);

        return response()->json($result, $result['success'] ? 200 : 404);
    }

    /**
     * Delete a zip file
     *
     * @param string $filename
     * @return JsonResponse
     */
    public function deleteZip(string $filename): JsonResponse
    {
        $result = GalleryZipHelper::deleteZip($filename);

        return response()->json($result, $result['success'] ? 200 : 404);
    }

    /**
     * Clean up old zip files
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function cleanupZips(Request $request): JsonResponse
    {
        $olderThanHours = $request->input('older_than_hours', 24);

        $result = GalleryZipHelper::cleanupOldZips($olderThanHours);

        return response()->json($result, 200);
    }

    /**
     * Get all gallery images with metadata
     *
     * @return JsonResponse
     */
    public function getImages(): JsonResponse
    {
        try {
            $galleries = Gallery::with('media')->latest()->get();

            $images = $galleries->map(function ($gallery) {
                $media = $gallery->getFirstMedia('gallery');

                return [
                    'id' => $gallery->id,
                    'created_at' => $gallery->created_at->toISOString(),
                    'media' => $media ? [
                        'id' => $media->id,
                        'name' => $media->name,
                        'file_name' => $media->file_name,
                        'size' => $media->size,
                        'size_human' => $this->formatBytes($media->size),
                        'mime_type' => $media->mime_type,
                        'url' => $media->getUrl(),
                        'path' => $media->getPath(),
                    ] : null
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'images' => $images,
                    'count' => $images->count()
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve images: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * Upload new images
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function uploadImages(Request $request): JsonResponse
    {
        $request->validate([
            'images' => 'required|array|min:1',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240', // 10MB max per image
        ]);

        try {
            $uploadedImages = [];

            foreach ($request->file('images') as $image) {
                $gallery = Gallery::create();
                $media = $gallery->addMedia($image)->toMediaCollection('gallery');

                $uploadedImages[] = [
                    'gallery_id' => $gallery->id,
                    'media_id' => $media->id,
                    'filename' => $media->file_name,
                    'size' => $media->size,
                    'url' => $media->getUrl()
                ];
            }

            return response()->json([
                'success' => true,
                'message' => count($uploadedImages) . ' images uploaded successfully',
                'data' => [
                    'uploaded_images' => $uploadedImages,
                    'count' => count($uploadedImages)
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload images: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * Delete an image
     *
     * @param Gallery $gallery
     * @return JsonResponse
     */
    public function deleteImage(Gallery $gallery): JsonResponse
    {
        try {
            $gallery->clearMediaCollection('gallery');
            $gallery->delete();

            return response()->json([
                'success' => true,
                'message' => 'Image deleted successfully',
                'data' => ['gallery_id' => $gallery->id]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete image: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * Format bytes to human readable format
     *
     * @param int $bytes
     * @param int $precision
     * @return string
     */
    private function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
