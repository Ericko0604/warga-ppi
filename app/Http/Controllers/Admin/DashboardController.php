<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Photo;
use App\Models\Resident;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalResidents = Resident::count();
        $totalActiveHouses = Resident::active()->count();
        $totalEvents = Event::acara()->count();
        $totalActivities = Event::kegiatan()->count();
        $totalPhotos = Photo::count();
        $recentUploads = Photo::with(['event', 'resident'])->latest()->take(8)->get();

        $latestEvent = Event::published()->orderBy('event_date', 'desc')->first();
        $latestEventProgress = null;

        if ($latestEvent) {
            $totalActive = Resident::active()->count();
            $uploadedCount = Photo::where('event_id', $latestEvent->id)
                ->where('uploader_type', 'RESIDENT')
                ->count();

            $latestEventProgress = [
                'event' => $latestEvent,
                'uploaded' => $uploadedCount,
                'total' => $totalActive,
                'percentage' => $totalActive > 0 ? round(($uploadedCount / $totalActive) * 100) : 0,
            ];
        }

        return view('admin.dashboard', compact(
            'totalResidents',
            'totalActiveHouses',
            'totalEvents',
            'totalActivities',
            'totalPhotos',
            'recentUploads',
            'latestEventProgress'
        ));
    }
}
