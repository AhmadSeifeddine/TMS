# Dynamic Image Uploader for Spatie Media Library

A flexible PHP script to bulk upload images to any Laravel project using Spatie Media Library.

## 🚀 Quick Start

1. Copy `dynamic_image_uploader.php` to your Laravel project root
2. Edit the CONFIG section to match your project
3. Run: `php dynamic_image_uploader.php`

## 📋 Configuration Examples

### Example 1: Gallery/Portfolio Website
```php
const CONFIG = [
    'MODEL_CLASS' => 'App\Models\Gallery',
    'COLLECTION_NAME' => 'gallery',
    'IMAGE_COUNT' => 500,
    'IMAGE_SOURCE' => 'picsum',  // Real photos
    'MIN_WIDTH' => 800,
    'MAX_WIDTH' => 1200,
    'MIN_HEIGHT' => 600,
    'MAX_HEIGHT' => 900,
    'NAME_PREFIX' => 'Portfolio Image',
    'FILENAME_PREFIX' => 'portfolio',
];
```

### Example 2: E-commerce Product Images
```php
const CONFIG = [
    'MODEL_CLASS' => 'App\Models\Product',
    'COLLECTION_NAME' => 'images',
    'IMAGE_COUNT' => 100,
    'IMAGE_SOURCE' => 'placeholder',  // Colored placeholders
    'MIN_WIDTH' => 400,
    'MAX_WIDTH' => 600,
    'MIN_HEIGHT' => 400,
    'MAX_HEIGHT' => 600,
    'NAME_PREFIX' => 'Product Image',
    'FILENAME_PREFIX' => 'product',
];
```

### Example 3: User Avatars
```php
const CONFIG = [
    'MODEL_CLASS' => 'App\Models\User',
    'COLLECTION_NAME' => 'avatars',
    'IMAGE_COUNT' => 50,
    'IMAGE_SOURCE' => 'picsum',
    'MIN_WIDTH' => 200,
    'MAX_WIDTH' => 300,
    'MIN_HEIGHT' => 200,
    'MAX_HEIGHT' => 300,
    'NAME_PREFIX' => 'User Avatar',
    'FILENAME_PREFIX' => 'avatar',
];
```

## 🛠 Custom Model Fields

To add custom fields when creating records, modify the `createModelRecord()` function:

```php
function createModelRecord($index) {
    $modelClass = CONFIG['MODEL_CLASS'];
    $model = new $modelClass();
    
    // Example for Gallery model
    if ($modelClass === 'App\Models\Gallery') {
        $model->title = "Random Image {$index}";
        $model->description = "Auto-generated gallery image";
        $model->is_featured = rand(0, 1);
    }
    
    // Example for Product model
    if ($modelClass === 'App\Models\Product') {
        $model->name = "Test Product {$index}";
        $model->price = rand(10, 100);
        $model->status = 'active';
    }
    
    $model->save();
    return $model;
}
```

## 🎯 Image Sources

### Lorem Picsum (picsum)
- Real, high-quality photos
- Perfect for portfolios, blogs, galleries
- URL: `https://picsum.photos/seed/{seed}/{width}/{height}.jpg`

### Placeholder.com (placeholder)
- Colored placeholder images
- Great for testing layouts, e-commerce
- URL: `https://via.placeholder.com/{width}x{height}/{color}/FFFFFF.jpg`

## ⚙️ Configuration Options

| Option | Description | Example |
|--------|-------------|---------|
| `MODEL_CLASS` | Your model class | `'App\Models\Gallery'` |
| `COLLECTION_NAME` | Media collection | `'gallery'` |
| `IMAGE_COUNT` | Number of images | `1000` |
| `IMAGE_SOURCE` | Image provider | `'picsum'` or `'placeholder'` |
| `MIN_WIDTH` | Minimum width | `400` |
| `MAX_WIDTH` | Maximum width | `800` |
| `MIN_HEIGHT` | Minimum height | `300` |
| `MAX_HEIGHT` | Maximum height | `600` |
| `TIMEOUT` | Download timeout | `30` |
| `DELAY_MS` | Delay between downloads (microseconds) | `200000` |
| `PROGRESS_INTERVAL` | Progress update frequency | `25` |
| `NAME_PREFIX` | Media name prefix | `'Random Image'` |
| `FILENAME_PREFIX` | Filename prefix | `'random_image'` |

## 🔧 Requirements

- Laravel project with Spatie Media Library installed
- Model implementing `Spatie\MediaLibrary\HasMedia` interface
- PHP cURL extension enabled
- Internet connection

## 📊 Output Example

```
🚀 Starting Dynamic Image Uploader
===================================
Model: App\Models\Gallery
Collection: gallery
Count: 100 images
Source: picsum
Dimensions: 400-800 x 300-600
===================================

✅ Configuration validated successfully.

Downloading image 1/100 (520x524)...
✅ Successfully uploaded image 1 (Model ID: 1, Media ID: 1)
Downloading image 2/100 (753x505)...
✅ Successfully uploaded image 2 (Model ID: 2, Media ID: 2)

📊 Progress: 25/100 images processed. ✅ 25 successful, ❌ 0 errors (100.0% success rate)

🎉 Upload Complete!
==================
✅ Successfully uploaded: 100 images
❌ Errors: 0
📈 Success rate: 100.0%
🧹 Cleaned up temporary files.
```

## 🐛 Troubleshooting

### Common Issues

1. **Class not found error**
   - Check your `MODEL_CLASS` path
   - Ensure the model exists and is autoloaded

2. **Media collection errors**
   - Verify your model implements `HasMedia`
   - Check collection name spelling

3. **Download failures**
   - Check internet connection
   - Try increasing `TIMEOUT` value
   - Some services may have rate limits

4. **Permission errors**
   - Ensure storage directory is writable
   - Check file permissions

### Performance Tips

- Reduce `IMAGE_COUNT` for testing
- Increase `DELAY_MS` if getting rate limited
- Use `placeholder` source for faster downloads
- Run during off-peak hours for better performance

## 📝 License

This script is open source and free to use in any project. 
