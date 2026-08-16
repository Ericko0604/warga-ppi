<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Photo;
use App\Models\Resident;
use Illuminate\Http\Request;

class PublicEventController extends Controller
{
    public function show(string $uuid)
    {
        $event = Event::where('uuid', $uuid)->notDraft()->firstOrFail();

        // Admin documentation photos (max 10)
        $adminPhotos = $event->adminPhotos()->latest()->take(10)->get();

        // Get all active residents with their photos for this event
        $activeResidents = Resident::query()
            ->active()
            ->orderBy('block')
            ->orderBy('house_number')
            ->orderBy('family_head_name')
            ->get();

        // Get all resident photos indexed by resident_id
        $residentPhotos = Photo::where('event_id', $event->id)
            ->where('uploader_type', 'RESIDENT')
            ->get()
            ->keyBy('resident_id');

        // Group active residents by Block ('A1', 'A2', 'A3', 'A4', 'KAVLING')
        $blocksData = [
            'A1' => [],
            'A2' => [],
            'A3' => [],
            'A4' => [],
            'KAVLING' => [],
        ];

        foreach ($activeResidents as $resident) {
            $photo = $residentPhotos->get($resident->id);
            $blocksData[$resident->block][] = [
                'resident' => $resident,
                'photo' => $photo,
            ];
        }

        return view('public.events.show', compact('event', 'adminPhotos', 'blocksData'));
    }
}
