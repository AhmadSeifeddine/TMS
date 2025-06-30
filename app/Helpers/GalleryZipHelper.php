<?php

namespace App\Helpers;

use App\Models\Gallery;
use App\Jobs\CreateGalleryZip;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class GalleryZipHelper
{
    /**
     * Create a new gallery zip
     *
     * @return array
     */
    public static function createZip(): array
    {
        $galleries = Gallery::with('media')->get();

        if ($galleries->isEmpty()) {
            return [
                'success' => false,
                'message' => 'No images found to zip.',
                'data' => null
            ];
        }

        $zipFileName = 'gallery-' . now()->format('Y-m-d-H-i-s') . '.zip';
        $jobId = Str::uuid()->toString();

        // Dispatch the job
        CreateGalleryZip::dispatch($zipFileName, $jobId);

        return [
            'success' => true,
            'message' => 'Zip creation started',
            'data' => [
                'job_id' => $jobId,
                'filename' => $zipFileName,
                'status' => 'processing'
            ]
        ];
    }

    /**
     * Check the status of a zip job
     *
     * @param string $jobId
     * @return array
     */
    public static function checkZipStatus(string $jobId): array
    {
        $status = Cache::get("zip_job_{$jobId}");

        if (!$status) {
            return [
                'success' => true,
                'data' => [
                    'status' => 'processing',
                    'message' => 'Job is still processing'
                ]
            ];
        }

        return [
            'success' => true,
            'data' => $status
        ];
    }

    /**
     * Get zip download information
     *
     * @param string $filename
     * @return array
     */
    public static function getZipDownloadInfo(string $filename): array
    {
        $filePath = 'gallery-zips/' . $filename;

        if (!Storage::disk('local')->exists($filePath)) {
            return [
                'success' => false,
                'message' => 'Zip file not found or still being created.',
                'data' => null
            ];
        }

        $fullPath = Storage::disk('local')->path($filePath);
        $fileSize = Storage::disk('local')->size($filePath);
        $lastModified = Storage::disk('local')->lastModified($filePath);

        return [
            'success' => true,
            'data' => [
                'filename' => $filename,
                'path' => $fullPath,
                'size' => $fileSize,
                'size_human' => self::formatBytes($fileSize),
                'last_modified' => Carbon::createFromTimestamp($lastModified)->toISOString(),
                'download_url' => route('gallery.download-zip', ['filename' => $filename])
            ]
        ];
    }

    /**
     * Get all available zip files
     *
     * @return array
     */
    public static function getAvailableZips(): array
    {
        $files = Storage::disk('local')->files('gallery-zips');
        $zips = [];

        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'zip') {
                $filename = basename($file);
                $size = Storage::disk('local')->size($file);
                $lastModified = Storage::disk('local')->lastModified($file);

                $zips[] = [
                    'filename' => $filename,
                    'size' => $size,
                    'size_human' => self::formatBytes($size),
                    'last_modified' => Carbon::createFromTimestamp($lastModified)->toISOString(),
                    'download_url' => route('gallery.download-zip', ['filename' => $filename])
                ];
            }
        }

        // Sort by last modified (newest first)
        usort($zips, function ($a, $b) {
            return strtotime($b['last_modified']) - strtotime($a['last_modified']);
        });

        return [
            'success' => true,
            'data' => $zips
        ];
    }

    /**
     * Delete a zip file
     *
     * @param string $filename
     * @return array
     */
    public static function deleteZip(string $filename): array
    {
        $filePath = 'gallery-zips/' . $filename;

        if (!Storage::disk('local')->exists($filePath)) {
            return [
                'success' => false,
                'message' => 'Zip file not found.',
                'data' => null
            ];
        }

        try {
            Storage::disk('local')->delete($filePath);

            return [
                'success' => true,
                'message' => 'Zip file deleted successfully.',
                'data' => ['filename' => $filename]
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to delete zip file: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }

    /**
     * Clean up old zip files (older than specified hours)
     *
     * @param int $olderThanHours
     * @return array
     */
    public static function cleanupOldZips(int $olderThanHours = 24): array
    {
        $files = Storage::disk('local')->files('gallery-zips');
        $deletedFiles = [];
        $cutoffTime = now()->subHours($olderThanHours)->timestamp;

        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'zip') {
                $lastModified = Storage::disk('local')->lastModified($file);

                if ($lastModified < $cutoffTime) {
                    try {
                        Storage::disk('local')->delete($file);
                        $deletedFiles[] = basename($file);
                    } catch (\Exception $e) {
                        // Log error but continue with other files
                        Log::error("Failed to delete old zip file {$file}: " . $e->getMessage());
                    }
                }
            }
        }

        return [
            'success' => true,
            'message' => count($deletedFiles) . ' old zip files cleaned up.',
            'data' => [
                'deleted_files' => $deletedFiles,
                'count' => count($deletedFiles)
            ]
        ];
    }

    /**
     * Get gallery statistics
     *
     * @return array
     */
    public static function getGalleryStats(): array
    {
        $totalImages = Gallery::count();
        $totalMediaSize = 0;

        // Calculate total media size
        $galleries = Gallery::with('media')->get();
        foreach ($galleries as $gallery) {
            $media = $gallery->getFirstMedia('gallery');
            if ($media && file_exists($media->getPath())) {
                $totalMediaSize += $media->size;
            }
        }

        $availableZips = self::getAvailableZips();
        $zipCount = count($availableZips['data']);

        return [
            'success' => true,
            'data' => [
                'total_images' => $totalImages,
                'total_media_size' => $totalMediaSize,
                'total_media_size_human' => self::formatBytes($totalMediaSize),
                'available_zips' => $zipCount,
                'last_updated' => now()->toISOString()
            ]
        ];
    }

    /**
     * Format bytes to human readable format
     *
     * @param int $bytes
     * @param int $precision
     * @return string
     */
    private static function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
