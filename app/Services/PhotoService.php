<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Photo;
use App\Models\Resident;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;

class PhotoService
{
    public function __construct(
        protected ImageProcessingService $imageProcessor
    ) {}

    /**
     * Process and store or replace a resident photo upload.
     * Enforces single photo per resident per event limit.
     */
    public function storeResidentPhoto(Event $event, Resident $resident, UploadedFile $file): Photo
    {
        if (!$event->canAcceptResidentUpload()) {
            throw new InvalidArgumentException('Pengiriman foto untuk acara/kegiatan ini sedang tidak aktif.');
        }

        if ($resident->status !== 'ACTIVE') {
            throw new InvalidArgumentException('Status warga tidak aktif.');
        }

        // Process image (validates landscape format automatically)
        $storageDir = 'events/' . date('Y') . '/event-' . $event->id . '/residents';
        $processed = $this->imageProcessor->processAndSavePhoto($file, $storageDir);

        // Check if resident already uploaded a photo for this event
        $existingPhoto = Photo::where('event_id', $event->id)
            ->where('resident_id', $resident->id)
            ->first();

        if ($existingPhoto) {
            // Delete old files from storage
            if ($existingPhoto->file_path && Storage::disk('public')->exists($existingPhoto->file_path)) {
                Storage::disk('public')->delete($existingPhoto->file_path);
            }
            if ($existingPhoto->thumbnail_path && Storage::disk('public')->exists($existingPhoto->thumbnail_path)) {
                Storage::disk('public')->delete($existingPhoto->thumbnail_path);
            }

            // Update existing photo record
            $existingPhoto->update([
                'file_path' => $processed['file_path'],
                'thumbnail_path' => $processed['thumbnail_path'],
                'original_filename' => $file->getClientOriginalName(),
                'mime_type' => $processed['mime_type'],
                'file_size' => $processed['file_size'],
                'width' => $processed['width'],
                'height' => $processed['height'],
            ]);

            return $existingPhoto->fresh();
        }

        // Create new photo record
        return Photo::create([
            'event_id' => $event->id,
            'resident_id' => $resident->id,
            'uploader_type' => 'RESIDENT',
            'file_path' => $processed['file_path'],
            'thumbnail_path' => $processed['thumbnail_path'],
            'original_filename' => $file->getClientOriginalName(),
            'mime_type' => $processed['mime_type'],
            'file_size' => $processed['file_size'],
            'width' => $processed['width'],
            'height' => $processed['height'],
        ]);
    }

    /**
     * Process and store an admin documentation photo upload.
     * Enforces max 10 photos limit per event for admin photos.
     */
    public function storeAdminPhoto(Event $event, UploadedFile $file): Photo
    {
        $currentAdminPhotoCount = Photo::where('event_id', $event->id)
            ->where('uploader_type', 'ADMIN')
            ->count();

        if ($currentAdminPhotoCount >= 10) {
            throw new InvalidArgumentException('Batas foto admin sudah mencapai 10 foto.');
        }

        $storageDir = 'events/' . date('Y') . '/event-' . $event->id . '/admin';
        $processed = $this->imageProcessor->processAndSavePhoto($file, $storageDir);

        return Photo::create([
            'event_id' => $event->id,
            'resident_id' => null,
            'uploader_type' => 'ADMIN',
            'file_path' => $processed['file_path'],
            'thumbnail_path' => $processed['thumbnail_path'],
            'original_filename' => $file->getClientOriginalName(),
            'mime_type' => $processed['mime_type'],
            'file_size' => $processed['file_size'],
            'width' => $processed['width'],
            'height' => $processed['height'],
        ]);
    }

    /**
     * Delete a photo and its physical storage files.
     */
    public function deletePhoto(Photo $photo): bool
    {
        if ($photo->file_path && Storage::disk('public')->exists($photo->file_path)) {
            Storage::disk('public')->delete($photo->file_path);
        }
        if ($photo->thumbnail_path && Storage::disk('public')->exists($photo->thumbnail_path)) {
            Storage::disk('public')->delete($photo->thumbnail_path);
        }

        return $photo->delete();
    }
}
