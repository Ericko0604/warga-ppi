<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Photo;
use App\Services\AuditLogService;
use App\Services\ImageProcessingService;
use App\Services\PhotoService;
use Exception;
use Illuminate\Http\Request;
use InvalidArgumentException;

class PhotoController extends Controller
{
    public function __construct(
        protected PhotoService $photoService,
        protected ImageProcessingService $imageProcessor
    ) {}

    public function storeAdminPhoto(Request $request, Event $event)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,webp|max:15360',
        ], [
            'photo.required' => 'Pilih foto dokumentasi.',
            'photo.image' => 'File harus berupa gambar.',
            'photo.max' => 'Ukuran foto maksimal 15MB.',
        ]);

        try {
            $photo = $this->photoService->storeAdminPhoto($event, $request->file('photo'));
            AuditLogService::log('admin_photo_uploaded', "Admin mengunggah foto untuk event: {$event->name}");

            return back()->with('success', 'Foto dokumentasi admin berhasil diunggah.');
        } catch (InvalidArgumentException $e) {
            return back()->with('error_message', $e->getMessage());
        } catch (Exception $e) {
            return back()->with('error_message', 'Gagal mengunggah foto. Silakan coba lagi.');
        }
    }

    public function destroy(Photo $photo)
    {
        $eventId = $photo->event_id;
        $this->photoService->deletePhoto($photo);

        AuditLogService::log('photo_deleted', "Foto ID {$photo->id} dihapus oleh admin.");

        return back()->with('success', 'Foto berhasil dihapus.');
    }

    public function updateThumbnail(Request $request, Event $event)
    {
        $request->validate([
            'thumbnail' => 'required|image|mimes:jpeg,png,jpg,webp|max:15360',
            'crop_x' => 'nullable|numeric',
            'crop_y' => 'nullable|numeric',
            'crop_w' => 'nullable|numeric',
            'crop_h' => 'nullable|numeric',
        ]);

        try {
            $thumbnailPath = $this->imageProcessor->cropAndSave16to9Thumbnail(
                $request->file('thumbnail'),
                (float) $request->input('crop_x', 0),
                (float) $request->input('crop_y', 0),
                (float) $request->input('crop_w', 0),
                (float) $request->input('crop_h', 0)
            );

            $event->update(['thumbnail_path' => $thumbnailPath]);
            AuditLogService::log('thumbnail_updated', "Thumbnail event {$event->name} diperbarui.");

            return back()->with('success', 'Thumbnail acara berhasil diperbarui.');
        } catch (Exception $e) {
            return back()->with('error_message', 'Gagal memproses thumbnail.');
        }
    }
}
