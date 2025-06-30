<?php

require_once 'vendor/autoload.php';

use App\Models\Gallery;
use Illuminate\Support\Facades\Storage;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Starting to download and upload 1000 images...\n";

$count = 0;
$errors = 0;

// Create temp directory if it doesn't exist
$tempDir = storage_path('app/temp');
if (!is_dir($tempDir)) {
    mkdir($tempDir, 0755, true);
}

for ($i = 1; $i <= 1000; $i++) {
    $tempFile = null;
    try {
        // Generate random dimensions for variety
        $width = rand(400, 800);
        $height = rand(300, 600);

        // Lorem Picsum URL with random image - add more randomness
        $seed = rand(1, 10000);
        $imageUrl = "https://picsum.photos/seed/{$seed}/{$width}/{$height}.jpg";

        echo "Downloading image {$i}/1000 ({$width}x{$height})...\n";

        // Use cURL for better error handling
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $imageUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');

        $imageContent = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($imageContent === false || $httpCode !== 200 || !empty($error)) {
            echo "Failed to download image {$i} (HTTP: {$httpCode}, Error: {$error})\n";
            $errors++;
            continue;
        }

        // Create temporary file with proper extension
        $tempFile = $tempDir . '/gallery_img_' . $i . '_' . time() . '.jpg';
        $result = file_put_contents($tempFile, $imageContent);

        if ($result === false) {
            echo "Failed to save temp file for image {$i}\n";
            $errors++;
            continue;
        }

        // Verify file exists and has content
        if (!file_exists($tempFile) || filesize($tempFile) === 0) {
            echo "Temp file is empty or doesn't exist for image {$i}\n";
            $errors++;
            if (file_exists($tempFile)) unlink($tempFile);
            continue;
        }

        // Create Gallery record
        $gallery = Gallery::create();

        // Add image to media library
        $media = $gallery->addMedia($tempFile)
                        ->usingName("Random Image {$i}")
                        ->usingFileName("random_image_{$i}.jpg")
                        ->toMediaCollection('gallery');

        // Clean up temp file safely
        if (file_exists($tempFile)) {
            unlink($tempFile);
        }

        $count++;
        echo "✓ Successfully uploaded image {$i} (Gallery ID: {$gallery->id}, Media ID: {$media->id})\n";

        // Small delay to avoid overwhelming the service
        usleep(200000); // 0.2 second delay

    } catch (Exception $e) {
        echo "Error with image {$i}: " . $e->getMessage() . "\n";
        $errors++;

        // Clean up temp file on error
        if ($tempFile && file_exists($tempFile)) {
            unlink($tempFile);
        }
    }

    // Progress update every 25 images
    if ($i % 25 == 0) {
        echo "\n--- Progress: {$i}/1000 images processed. Successful: {$count}, Errors: {$errors} ---\n\n";
    }
}

echo "\n🎉 Completed! Successfully uploaded {$count} images with {$errors} errors.\n";

// Clean up temp directory
if (is_dir($tempDir)) {
    $files = glob($tempDir . '/*');
    foreach ($files as $file) {
        if (is_file($file)) unlink($file);
    }
}
