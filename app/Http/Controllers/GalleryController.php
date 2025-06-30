<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use App\Helpers\GalleryZipHelper;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

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
        $result = GalleryZipHelper::createZip();

        if (!$result['success']) {
            return redirect()->route('gallery.index')
                        ->with('error', $result['message']);
        }

        return redirect()->route('gallery.index')
                    ->with('success', 'Zipping...')
                    ->with('job_id', $result['data']['job_id']);
    }

    /**
     * Check zip job status
     */
    public function checkZipStatus($jobId)
    {
        $result = GalleryZipHelper::checkZipStatus($jobId);
        return response()->json($result['data']);
    }

    /**
     * Download zip file
     */
    public function downloadZip($filename)
    {
        $result = GalleryZipHelper::getZipDownloadInfo($filename);

        if (!$result['success']) {
            abort(404, $result['message']);
        }

        return response()->download($result['data']['path']);
    }
}
