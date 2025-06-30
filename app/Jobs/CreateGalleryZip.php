<?php

namespace App\Jobs;

use App\Models\Gallery;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use STS\ZipStream\Facades\Zip;

class CreateGalleryZip implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $zipFileName;
    protected $jobId;

    public function __construct($zipFileName, $jobId)
    {
        $this->zipFileName = $zipFileName;
        $this->jobId = $jobId;
    }

        public function handle(): void
    {
        try {
            $galleries = Gallery::with('media')->get();

            if ($galleries->isEmpty()) {
                Cache::put("zip_job_{$this->jobId}", [
                    'status' => 'failed',
                    'message' => 'No images found'
                ], now()->addHour());
                return;
            }

                        $zip = Zip::create($this->zipFileName);

            $counter = 1;
            foreach ($galleries as $gallery) {
                $media = $gallery->getFirstMedia('gallery');
                if ($media && file_exists($media->getPath())) {
                    $originalFileName = $media->file_name ?: ($media->name . '.' . $media->extension);

                    // Make each filename unique with counter and gallery ID
                    $pathInfo = pathinfo($originalFileName);
                    $baseName = $pathInfo['filename'] ?? 'image';
                    $extension = $pathInfo['extension'] ?? 'jpg';
                    $fileName = $baseName . '_' . $counter . '_id' . $gallery->id . '.' . $extension;

                    $zip->add($media->getPath(), $fileName);
                    $counter++;
                }
            }

            $zip->saveToDisk('local', 'gallery-zips');

            // Mark as completed with download URL
            $downloadUrl = route('gallery.download-zip', ['filename' => $this->zipFileName]);
            Cache::put("zip_job_{$this->jobId}", [
                'status' => 'completed',
                'download_url' => $downloadUrl,
                'filename' => $this->zipFileName
            ], now()->addHour());

        } catch (\Exception $e) {
            Cache::put("zip_job_{$this->jobId}", [
                'status' => 'failed',
                'message' => 'Failed to create zip: ' . $e->getMessage()
            ], now()->addHour());
        }
    }
}
