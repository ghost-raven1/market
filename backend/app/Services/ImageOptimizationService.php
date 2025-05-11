<?php

namespace App\Services;

use Intervention\Image\ImageManagerStatic as Image;

class ImageOptimizationService
{
    public function optimizeImage(string $imagePath)
    {
        $image = Image::make($imagePath);
        $image->save($imagePath, 75); // Save with 75% quality
    }

    public function resizeImage(string $imagePath, int $width, int $height)
    {
        $image = Image::make($imagePath);
        $image->resize($width, $height);
        $image->save($imagePath);
    }

    public function convertImageFormat(string $imagePath, string $format)
    {
        $image = Image::make($imagePath);
        $newPath = pathinfo($imagePath, PATHINFO_FILENAME) . '.' . $format;
        $image->save($newPath);
    }
}
