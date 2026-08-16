<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $typeFilter = $request->query('type');
        $search = $request->query('search');

        $query = Event::query()->notDraft()->orderBy('event_date', 'desc');

        if ($typeFilter && in_array(strtoupper($typeFilter), ['ACARA', 'KEGIATAN'])) {
            $query->where('type', strtoupper($typeFilter));
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $events = $query->paginate(12)->withQueryString();

        // Get latest published event for Hero banner
        $featuredEvent = Event::query()
            ->published()
            ->orderBy('event_date', 'desc')
            ->first();

        return view('public.home', compact('events', 'featuredEvent', 'typeFilter', 'search'));
    }
}
