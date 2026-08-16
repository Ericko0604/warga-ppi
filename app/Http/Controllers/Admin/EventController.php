<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreEventRequest;
use App\Models\Event;
use App\Models\Photo;
use App\Models\Resident;
use App\Services\AuditLogService;
use App\Services\ImageProcessingService;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function __construct(
        protected ImageProcessingService $imageProcessor
    ) {}

    public function index(Request $request)
    {
        $typeFilter = $request->query('type');
        $statusFilter = $request->query('status');

        $query = Event::query()->withCount(['photos as resident_photos_count' => function ($q) {
            $q->where('uploader_type', 'RESIDENT');
        }, 'photos as admin_photos_count' => function ($q) {
            $q->where('uploader_type', 'ADMIN');
        }])->orderBy('event_date', 'desc');

        if ($typeFilter && in_array(strtoupper($typeFilter), ['ACARA', 'KEGIATAN'])) {
            $query->where('type', strtoupper($typeFilter));
        }

        if ($statusFilter && in_array(strtoupper($statusFilter), ['DRAFT', 'PUBLISHED', 'ARCHIVED'])) {
            $query->where('status', strtoupper($statusFilter));
        }

        $events = $query->paginate(15)->withQueryString();

        return view('admin.events.index', compact('events', 'typeFilter', 'statusFilter'));
    }

    public function create()
    {
        return view('admin.events.create');
    }

    public function store(StoreEventRequest $request)
    {
        $validated = $request->validated();

        if ($validated['type'] === 'ACARA') {
            $validated['allow_resident_upload'] = true;
        } else {
            $validated['allow_resident_upload'] = $request->boolean('allow_resident_upload');
        }

        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $this->imageProcessor->cropAndSave16to9Thumbnail($request->file('thumbnail'));
            $validated['thumbnail_path'] = $thumbnailPath;
        }

        $event = Event::create($validated);
        AuditLogService::log('event_created', "Acara/Kegiatan dibuat: {$event->name}");

        return redirect()->route('admin.events.show', $event->id)->with('success', 'Acara/Kegiatan berhasil dibuat.');
    }

    public function show(Event $event)
    {
        $adminPhotos = $event->adminPhotos()->latest()->get();

        $activeResidents = Resident::active()->orderBy('block')->orderBy('house_number')->orderBy('family_head_name')->get();
        $residentPhotos = Photo::where('event_id', $event->id)
            ->where('uploader_type', 'RESIDENT')
            ->get()
            ->keyBy('resident_id');

        $blocksStats = [
            'A1' => ['total' => 0, 'uploaded' => 0, 'residents' => []],
            'A2' => ['total' => 0, 'uploaded' => 0, 'residents' => []],
            'A3' => ['total' => 0, 'uploaded' => 0, 'residents' => []],
            'A4' => ['total' => 0, 'uploaded' => 0, 'residents' => []],
            'KAVLING' => ['total' => 0, 'uploaded' => 0, 'residents' => []],
        ];

        $totalActiveResidents = 0;
        $totalUploadedCount = 0;

        foreach ($activeResidents as $resident) {
            $block = $resident->block;
            if (!isset($blocksStats[$block])) {
                $blocksStats[$block] = ['total' => 0, 'uploaded' => 0, 'residents' => []];
            }

            $photo = $residentPhotos->get($resident->id);
            $hasUploaded = ($photo !== null);

            $blocksStats[$block]['total']++;
            $totalActiveResidents++;

            if ($hasUploaded) {
                $blocksStats[$block]['uploaded']++;
                $totalUploadedCount++;
            }

            $blocksStats[$block]['residents'][] = [
                'resident' => $resident,
                'photo' => $photo,
                'has_uploaded' => $hasUploaded,
            ];
        }

        return view('admin.events.show', compact(
            'event',
            'adminPhotos',
            'blocksStats',
            'totalActiveResidents',
            'totalUploadedCount'
        ));
    }

    public function edit(Event $event)
    {
        return view('admin.events.edit', compact('event'));
    }

    public function update(StoreEventRequest $request, Event $event)
    {
        $validated = $request->validated();

        if ($validated['type'] === 'ACARA') {
            $validated['allow_resident_upload'] = true;
        } else {
            $validated['allow_resident_upload'] = $request->boolean('allow_resident_upload');
        }

        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $this->imageProcessor->cropAndSave16to9Thumbnail($request->file('thumbnail'));
            $validated['thumbnail_path'] = $thumbnailPath;
        }

        $event->update($validated);
        AuditLogService::log('event_updated', "Acara/Kegiatan diperbarui: {$event->name}");

        return redirect()->route('admin.events.show', $event->id)->with('success', 'Acara/Kegiatan berhasil diperbarui.');
    }

    public function destroy(Event $event)
    {
        $name = $event->name;
        $event->delete();

        AuditLogService::log('event_deleted', "Acara/Kegiatan dihapus: {$name}");

        return redirect()->route('admin.events.index')->with('success', 'Acara/Kegiatan berhasil dihapus.');
    }
}
