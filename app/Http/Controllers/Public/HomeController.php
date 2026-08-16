<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $typeFilter = $request->query('type');
        $search = $request->query('search');

        try {
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

            $featuredEvent = Event::query()
                ->published()
                ->orderBy('event_date', 'desc')
                ->first();

            return view('public.home', compact('events', 'featuredEvent', 'typeFilter', 'search'));
        } catch (Exception $e) {
            // If database tables are not migrated or connection issue, display friendly setup helper
            return response()->view('public.db_setup', [
                'errorMessage' => $e->getMessage(),
            ], 200);
        }
    }
}
