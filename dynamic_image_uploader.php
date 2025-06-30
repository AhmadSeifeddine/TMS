<?php
/**
 * Dynamic Image Uploader for Spatie Media Library
 *
 * HOW TO USE THIS SCRIPT:
 * =====================
 *
 * 1. PREREQUISITES:
 *    - Laravel project with Spatie Media Library installed
 *    - A model that implements HasMedia interface
 *    - Internet connection for downloading images
 *
 * 2. CONFIGURATION:
 *    Edit the CONFIG section below to match your project:
 *    - MODEL_CLASS: Your model class (e.g., App\Models\Gallery, App\Models\Product)
 *    - COLLECTION_NAME: Media collection name (e.g., 'gallery', 'images', 'photos')
 *    - IMAGE_COUNT: Number of images to download and upload
 *    - IMAGE_SOURCE: Where to get images from ('picsum' or 'placeholder')
 *    - DIMENSIONS: Min/max width and height for random sizing
 *
 * 3. RUNNING THE SCRIPT:
 *    Place this file in your Laravel project root and run:
 *    php dynamic_image_uploader.php
 *
 * 4. WHAT IT DOES:
 *    - Downloads random images from external sources
 *    - Creates model records in your database
 *    - Stores images using Spatie Media Library
 *    - Provides progress updates and error handling
 *    - Cleans up temporary files automatically
 *
 * 5. SUPPORTED IMAGE SOURCES:
 *    - 'picsum': Lorem Picsum (https://picsum.photos) - Real photos
 *    - 'placeholder': Placeholder.com - Colored placeholders
 *
 * 6. CUSTOMIZATION:
 *    You can modify the createModelRecord() function to add
 *    custom fields or logic when creating your model records.
 */

// ============================================================================
// CONFIG SECTION - EDIT THESE VALUES FOR YOUR PROJECT
// ============================================================================

const CONFIG = [
    // Your model class that implements HasMedia
    'MODEL_CLASS' => 'App\Models\Gallery',

    // Media collection name
    'COLLECTION_NAME' => 'gallery',

    // Number of images to download
    'IMAGE_COUNT' => 5,

    // Image source: 'picsum' or 'placeholder'
    'IMAGE_SOURCE' => 'picsum',

    // Image dimensions (random between min and max)
    'MIN_WIDTH' => 400,
    'MAX_WIDTH' => 800,
    'MIN_HEIGHT' => 300,
    'MAX_HEIGHT' => 600,

    // Download settings
    'TIMEOUT' => 30,
    'DELAY_MS' => 200000, // Microseconds (0.2 seconds)
    'PROGRESS_INTERVAL' => 25, // Show progress every X images

    // File naming
    'NAME_PREFIX' => 'Random Image',
    'FILENAME_PREFIX' => 'random_image',
];

// ============================================================================
// SCRIPT LOGIC - NO NEED TO EDIT BELOW THIS LINE
// ============================================================================

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\Storage;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Validate configuration
validateConfig();

echo "🚀 Starting Dynamic Image Uploader\n";
echo "===================================\n";
echo "Model: " . CONFIG['MODEL_CLASS'] . "\n";
echo "Collection: " . CONFIG['COLLECTION_NAME'] . "\n";
echo "Count: " . CONFIG['IMAGE_COUNT'] . " images\n";
echo "Source: " . CONFIG['IMAGE_SOURCE'] . "\n";
echo "Dimensions: " . CONFIG['MIN_WIDTH'] . "-" . CONFIG['MAX_WIDTH'] . " x " . CONFIG['MIN_HEIGHT'] . "-" . CONFIG['MAX_HEIGHT'] . "\n";
echo "===================================\n\n";

$count = 0;
$errors = 0;

// Create temp directory if it doesn't exist
$tempDir = storage_path('app/temp_uploader');
if (!is_dir($tempDir)) {
    mkdir($tempDir, 0755, true);
}

for ($i = 1; $i <= CONFIG['IMAGE_COUNT']; $i++) {
    $tempFile = null;
    try {
        // Generate random dimensions
        $width = rand(CONFIG['MIN_WIDTH'], CONFIG['MAX_WIDTH']);
        $height = rand(CONFIG['MIN_HEIGHT'], CONFIG['MAX_HEIGHT']);

        // Get image URL based on source
        $imageUrl = getImageUrl($width, $height, $i);

        echo "Downloading image {$i}/" . CONFIG['IMAGE_COUNT'] . " ({$width}x{$height})...\n";

        // Download image using cURL
        $imageContent = downloadImage($imageUrl);

        if ($imageContent === false) {
            echo "❌ Failed to download image {$i}\n";
            $errors++;
            continue;
        }

        // Create temporary file
        $tempFile = $tempDir . '/uploader_img_' . $i . '_' . time() . '.jpg';
        $result = file_put_contents($tempFile, $imageContent);

        if ($result === false) {
            echo "❌ Failed to save temp file for image {$i}\n";
            $errors++;
            continue;
        }

        // Verify file exists and has content
        if (!file_exists($tempFile) || filesize($tempFile) === 0) {
            echo "❌ Temp file is empty or doesn't exist for image {$i}\n";
            $errors++;
            if (file_exists($tempFile)) unlink($tempFile);
            continue;
        }

        // Create model record
        $model = createModelRecord($i);

        // Add image to media library
        $media = $model->addMedia($tempFile)
                      ->usingName(CONFIG['NAME_PREFIX'] . " {$i}")
                      ->usingFileName(CONFIG['FILENAME_PREFIX'] . "_{$i}.jpg")
                      ->toMediaCollection(CONFIG['COLLECTION_NAME']);

        // Clean up temp file safely
        if (file_exists($tempFile)) {
            unlink($tempFile);
        }

        $count++;
        echo "✅ Successfully uploaded image {$i} (Model ID: {$model->id}, Media ID: {$media->id})\n";

        // Delay to avoid overwhelming the service
        usleep(CONFIG['DELAY_MS']);

    } catch (Exception $e) {
        echo "❌ Error with image {$i}: " . $e->getMessage() . "\n";
        $errors++;

        // Clean up temp file on error
        if ($tempFile && file_exists($tempFile)) {
            unlink($tempFile);
        }
    }

    // Progress update
    if ($i % CONFIG['PROGRESS_INTERVAL'] == 0) {
        $successRate = round(($count / $i) * 100, 1);
        echo "\n📊 Progress: {$i}/" . CONFIG['IMAGE_COUNT'] . " images processed. ✅ {$count} successful, ❌ {$errors} errors ({$successRate}% success rate)\n\n";
    }
}

echo "\n🎉 Upload Complete!\n";
echo "==================\n";
echo "✅ Successfully uploaded: {$count} images\n";
echo "❌ Errors: {$errors}\n";
echo "📈 Success rate: " . round(($count / CONFIG['IMAGE_COUNT']) * 100, 1) . "%\n";

// Clean up temp directory
cleanupTempDirectory($tempDir);

// ============================================================================
// HELPER FUNCTIONS
// ============================================================================

function validateConfig() {
    $modelClass = CONFIG['MODEL_CLASS'];

    if (!class_exists($modelClass)) {
        die("❌ Error: Model class '{$modelClass}' does not exist.\n");
    }

    if (CONFIG['IMAGE_COUNT'] <= 0) {
        die("❌ Error: IMAGE_COUNT must be greater than 0.\n");
    }

    if (!in_array(CONFIG['IMAGE_SOURCE'], ['picsum', 'placeholder'])) {
        die("❌ Error: IMAGE_SOURCE must be 'picsum' or 'placeholder'.\n");
    }

    echo "✅ Configuration validated successfully.\n\n";
}

function getImageUrl($width, $height, $index) {
    switch (CONFIG['IMAGE_SOURCE']) {
        case 'picsum':
            $seed = rand(1, 10000);
            return "https://picsum.photos/seed/{$seed}/{$width}/{$height}.jpg";

        case 'placeholder':
            $colors = ['FF0000', '00FF00', '0000FF', 'FFFF00', 'FF00FF', '00FFFF', 'FFA500', '800080'];
            $color = $colors[array_rand($colors)];
            return "https://via.placeholder.com/{$width}x{$height}/{$color}/FFFFFF.jpg";

        default:
            throw new Exception("Unsupported image source: " . CONFIG['IMAGE_SOURCE']);
    }
}

function downloadImage($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, CONFIG['TIMEOUT']);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $imageContent = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    if ($imageContent === false || $httpCode !== 200 || !empty($error)) {
        return false;
    }

    return $imageContent;
}

function createModelRecord($index) {
    $modelClass = CONFIG['MODEL_CLASS'];

    // Create basic model record
    // You can customize this function to add additional fields
    $model = new $modelClass();

    // Add any custom fields here based on your model
    // Example for a Gallery model:
    // $model->title = "Random Image {$index}";
    // $model->description = "Auto-generated image {$index}";

    $model->save();

    return $model;
}

function cleanupTempDirectory($tempDir) {
    if (is_dir($tempDir)) {
        $files = glob($tempDir . '/*');
        foreach ($files as $file) {
            if (is_file($file)) unlink($file);
        }
        rmdir($tempDir);
        echo "🧹 Cleaned up temporary files.\n";
    }
}
