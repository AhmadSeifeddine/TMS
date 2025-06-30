<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use App\Jobs\CreateGalleryZip;

class GalleryController extends Controller
{
    /**
     * Display the gallery with all images
     */
    public function index(): View
    {
        $galleries = Gallery::with('media')->latest()->get();

        return view('gallery.index', compact('galleries'));
    }

    /**
     * Store a new image in the gallery
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'images' => 'required|array|min:1',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240', // 10MB max per image
        ]);

        try {
            foreach ($request->file('images') as $image) {
                $gallery = Gallery::create();
                $gallery->addMedia($image)
                        ->toMediaCollection('gallery');
            }

            return redirect()->route('gallery.index')
                        ->with('success', 'Images uploaded successfully!');
        } catch (\Exception $e) {
            return redirect()->route('gallery.index')
                        ->with('error', 'Failed to upload images. Please try again.');
        }
    }

    /**
     * Delete an image from the gallery
     */
    public function destroy(Gallery $gallery): RedirectResponse
    {
        try {
            $gallery->clearMediaCollection('gallery');
            $gallery->delete();

            return redirect()->route('gallery.index')
                        ->with('success', 'Image deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->route('gallery.index')
                            ->with('error', 'Failed to delete image. Please try again.');
        }
    }

        /**
     * Create gallery zip
     */
    public function createZip(): RedirectResponse
    {
        $galleries = Gallery::with('media')->get();

        if ($galleries->isEmpty()) {
            return redirect()->route('gallery.index')
                        ->with('error', 'No images found to zip.');
        }

        $zipFileName = 'gallery-' . now()->format('Y-m-d-H-i-s') . '.zip';
        $jobId = Str::uuid()->toString();

        // Dispatch the job
        CreateGalleryZip::dispatch($zipFileName, $jobId);

        return redirect()->route('gallery.index')
                    ->with('success', 'Zipping...')
                    ->with('job_id', $jobId);
    }

    /**
     * Check zip job status
     */
    public function checkZipStatus($jobId)
    {
        $status = Cache::get("zip_job_{$jobId}");

        if (!$status) {
            return response()->json([
                'status' => 'processing'
            ]);
        }

        return response()->json($status);
    }

    /**
     * Download zip file
     */
    public function downloadZip($filename)
    {
        $filePath = 'gallery-zips/' . $filename;

        if (!Storage::disk('local')->exists($filePath)) {
            abort(404, 'Zip file not found or still being created.');
        }

        $fullPath = Storage::disk('local')->path($filePath);
        return response()->download($fullPath);
    }
}
