<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Gallery extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('gallery');
    }

    public function getImageAttribute(){
        return $this->getFirstMediaUrl('gallery');
    }
}
