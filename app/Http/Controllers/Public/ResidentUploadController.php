<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Photo;
use App\Models\Resident;
use App\Services\PhotoService;
use Exception;
use Illuminate\Http\Request;
use InvalidArgumentException;

class ResidentUploadController extends Controller
{
    public function __construct(
        protected PhotoService $photoService
    ) {}

    public function show(string $token, Request $request)
    {
        $resident = Resident::where('upload_token', $token)->where('status', 'ACTIVE')->first();

        if (!$resident) {
            return response()->view('public.upload.invalid_token', [], 404);
        }

        // Get target event (either selected by event_id query param or latest published open event)
        $eventId = $request->query('event_id');
        $eventsQuery = Event::query()->published()->orderBy('event_date', 'desc');

        if ($eventId) {
            $currentEvent = (clone $eventsQuery)->where('id', $eventId)->first();
        } else {
            $currentEvent = (clone $eventsQuery)->first();
        }

        // Filter events list where resident upload is allowed
        $availableEvents = Event::query()
            ->published()
            ->orderBy('event_date', 'desc')
            ->get()
            ->filter(fn($e) => $e->canAcceptResidentUpload());

        if (!$currentEvent || !$currentEvent->canAcceptResidentUpload()) {
            $currentEvent = $availableEvents->first();
        }

        $existingPhoto = null;
        if ($currentEvent) {
            $existingPhoto = Photo::where('event_id', $currentEvent->id)
                ->where('resident_id', $resident->id)
                ->first();
        }

        return view('public.upload.form', compact('resident', 'currentEvent', 'availableEvents', 'existingPhoto'));
    }

    public function store(string $token, Request $request)
    {
        $resident = Resident::where('upload_token', $token)->where('status', 'ACTIVE')->first();

        if (!$resident) {
            return response()->view('public.upload.invalid_token', [], 404);
        }

        $request->validate([
            'event_id' => 'required|exists:events,id',
            'photo' => 'required|image|mimes:jpeg,png,jpg,webp|max:15360',
        ], [
            'event_id.required' => 'Pilih acara/kegiatan.',
            'photo.required' => 'Silakan pilih foto terlebih dahulu.',
            'photo.image' => 'File yang dipilih harus berupa gambar.',
            'photo.mimes' => 'Format gambar harus JPG, PNG, atau WebP.',
            'photo.max' => 'Ukuran foto maksimal 15MB.',
        ]);

        $event = Event::findOrFail($request->input('event_id'));

        try {
            $file = $request->file('photo');
            $this->photoService->storeResidentPhoto($event, $resident, $file);

            return redirect()
                ->route('resident.upload.success', ['token' => $token, 'event_id' => $event->id]);
        } catch (InvalidArgumentException $e) {
            return back()
                ->withInput()
                ->with('error_message', $e->getMessage());
        } catch (Exception $e) {
            return back()
                ->withInput()
                ->with('error_message', 'Maaf, foto tidak dapat dikirim. Silakan coba lagi.');
        }
    }

    public function success(string $token, Request $request)
    {
        $resident = Resident::where('upload_token', $token)->where('status', 'ACTIVE')->first();
        if (!$resident) {
            return response()->view('public.upload.invalid_token', [], 404);
        }

        $eventId = $request->query('event_id');
        $event = Event::find($eventId);

        $uploadedPhoto = null;
        if ($event) {
            $uploadedPhoto = Photo::where('event_id', $event->id)
                ->where('resident_id', $resident->id)
                ->first();
        }

        return view('public.upload.success', compact('resident', 'event', 'uploadedPhoto'));
    }
}
