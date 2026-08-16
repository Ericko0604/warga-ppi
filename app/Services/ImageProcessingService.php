<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use InvalidArgumentException;

class ImageProcessingService
{
    /**
     * Inspect image dimensions from an uploaded file.
     * Returns ['width' => int, 'height' => int, 'is_landscape' => bool]
     */
    public function inspectDimensions(UploadedFile $file): array
    {
        $path = $file->getRealPath();
        $info = getimagesize($path);

        if (!$info) {
            throw new InvalidArgumentException('File yang diunggah bukan gambar yang valid.');
        }

        $width = $info[0];
        $height = $info[1];
        $isLandscape = ($width > $height);

        return [
            'width' => $width,
            'height' => $height,
            'is_landscape' => $isLandscape,
        ];
    }

    /**
     * Process, resize, auto-orient, compress, and save a landscape photo.
     * Generates both a display image (max width 1920px) and a thumbnail image (max width 600px).
     * Format: WebP or JPEG.
     */
    public function processAndSavePhoto(UploadedFile $file, string $storageSubdir = 'events'): array
    {
        $dimensions = $this->inspectDimensions($file);

        if (!$dimensions['is_landscape']) {
            throw new InvalidArgumentException('Foto harus berformat landscape (lebar lebih besar dari tinggi). Silakan pilih foto lain.');
        }

        $extension = strtolower($file->getClientOriginalExtension());
        $mime = $file->getMimeType();

        // Create GD resource based on MIME type
        $sourceImage = match ($mime) {
            'image/jpeg', 'image/jpg' => @imagecreatefromjpeg($file->getRealPath()),
            'image/png' => @imagecreatefrompng($file->getRealPath()),
            'image/webp' => @imagecreatefromwebp($file->getRealPath()),
            default => null,
        };

        if (!$sourceImage) {
            throw new InvalidArgumentException('Format gambar tidak didukung. Gunakan JPG, PNG, atau WebP.');
        }

        $origWidth = imagesx($sourceImage);
        $origHeight = imagesy($sourceImage);

        // 1. Process Main Display Image (Max width 1920px)
        $maxWidth = 1920;
        if ($origWidth > $maxWidth) {
            $newWidth = $maxWidth;
            $newHeight = (int) round(($origHeight / $origWidth) * $newWidth);
        } else {
            $newWidth = $origWidth;
            $newHeight = $origHeight;
        }

        $displayImage = imagecreatetruecolor($newWidth, $newHeight);
        // Handle transparency for WebP/PNG
        imagealphablending($displayImage, false);
        imagesavealpha($displayImage, true);
        imagecopyresampled($displayImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);

        // 2. Process Thumbnail (Max width 600px)
        $thumbMaxWidth = 600;
        $thumbWidth = min($origWidth, $thumbMaxWidth);
        $thumbHeight = (int) round(($origHeight / $origWidth) * $thumbWidth);

        $thumbImage = imagecreatetruecolor($thumbWidth, $thumbHeight);
        imagealphablending($thumbImage, false);
        imagesavealpha($thumbImage, true);
        imagecopyresampled($thumbImage, $sourceImage, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $origWidth, $origHeight);

        // Generate clean random UUID filename (never use client filename)
        $uuid = Str::uuid()->toString();
        $fileName = $uuid . '.webp';
        $thumbFileName = $uuid . '_thumb.webp';

        $relativeDisplayPath = trim($storageSubdir, '/') . '/' . $fileName;
        $relativeThumbPath = trim($storageSubdir, '/') . '/' . $thumbFileName;

        // Ensure storage directory exists
        Storage::disk('public')->makeDirectory(dirname($relativeDisplayPath));

        $fullDisplayPath = Storage::disk('public')->path($relativeDisplayPath);
        $fullThumbPath = Storage::disk('public')->path($relativeThumbPath);

        // Save as compressed WebP (quality 80)
        imagewebp($displayImage, $fullDisplayPath, 80);
        imagewebp($thumbImage, $fullThumbPath, 80);

        // Free GD memory resources
        imagedestroy($sourceImage);
        imagedestroy($displayImage);
        imagedestroy($thumbImage);

        $fileSize = filesize($fullDisplayPath);

        return [
            'file_path' => $relativeDisplayPath,
            'thumbnail_path' => $relativeThumbPath,
            'mime_type' => 'image/webp',
            'file_size' => $fileSize,
            'width' => $newWidth,
            'height' => $newHeight,
        ];
    }

    /**
     * Process and crop a custom 16:9 thumbnail for an event.
     */
    public function cropAndSave16to9Thumbnail(UploadedFile $file, float $cropX = 0, float $cropY = 0, float $cropW = 0, float $cropH = 0): string
    {
        $mime = $file->getMimeType();
        $sourceImage = match ($mime) {
            'image/jpeg', 'image/jpg' => @imagecreatefromjpeg($file->getRealPath()),
            'image/png' => @imagecreatefrompng($file->getRealPath()),
            'image/webp' => @imagecreatefromwebp($file->getRealPath()),
            default => null,
        };

        if (!$sourceImage) {
            throw new InvalidArgumentException('Format gambar tidak didukung.');
        }

        $origWidth = imagesx($sourceImage);
        $origHeight = imagesy($sourceImage);

        // If no explicit crop coordinates are passed, calculate center 16:9 crop
        if ($cropW <= 0 || $cropH <= 0) {
            $targetRatio = 16 / 9;
            $currentRatio = $origWidth / $origHeight;

            if ($currentRatio > $targetRatio) {
                // Image is wider than 16:9
                $cropH = $origHeight;
                $cropW = $origHeight * $targetRatio;
                $cropX = ($origWidth - $cropW) / 2;
                $cropY = 0;
            } else {
                // Image is taller than 16:9
                $cropW = $origWidth;
                $cropH = $origWidth / $targetRatio;
                $cropX = 0;
                $cropY = ($origHeight - $cropH) / 2;
            }
        }

        $targetW = 1280;
        $targetH = 720;

        $croppedCanvas = imagecreatetruecolor($targetW, $targetH);
        imagealphablending($croppedCanvas, false);
        imagesavealpha($croppedCanvas, true);

        imagecopyresampled(
            $croppedCanvas,
            $sourceImage,
            0, 0,
            (int)$cropX, (int)$cropY,
            $targetW, $targetH,
            (int)$cropW, (int)$cropH
        );

        $uuid = Str::uuid()->toString();
        $relativeThumbnailPath = 'thumbnails/' . $uuid . '.webp';

        Storage::disk('public')->makeDirectory('thumbnails');
        $fullPath = Storage::disk('public')->path($relativeThumbnailPath);

        imagewebp($croppedCanvas, $fullPath, 85);

        imagedestroy($sourceImage);
        imagedestroy($croppedCanvas);

        return $relativeThumbnailPath;
    }
}
