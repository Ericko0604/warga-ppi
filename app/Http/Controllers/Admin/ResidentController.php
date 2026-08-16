<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreResidentRequest;
use App\Models\Resident;
use App\Services\AuditLogService;
use App\Services\UploadTokenService;
use Illuminate\Http\Request;

class ResidentController extends Controller
{
    public function index(Request $request)
    {
        $blockFilter = $request->query('block');
        $search = $request->query('search');

        $query = Resident::query()->orderBy('block')->orderBy('house_number')->orderBy('family_head_name');

        if ($blockFilter && in_array(strtoupper($blockFilter), ['A1', 'A2', 'A3', 'A4', 'KAVLING'])) {
            $query->byBlock($blockFilter);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('family_head_name', 'like', "%{$search}%")
                  ->orWhere('house_number', 'like', "%{$search}%")
                  ->orWhere('block', 'like', "%{$search}%");
            });
        }

        $residents = $query->paginate(20)->withQueryString();

        return view('admin.residents.index', compact('residents', 'blockFilter', 'search'));
    }

    public function store(StoreResidentRequest $request)
    {
        $validated = $request->validated();
        $validated['upload_token'] = UploadTokenService::generateToken();
        $validated['status'] = 'ACTIVE';

        if ($validated['block'] === 'KAVLING') {
            $validated['house_number'] = null;
        } else {
            $validated['house_number'] = str_pad($validated['house_number'], 2, '0', STR_PAD_LEFT);
        }

        $resident = Resident::create($validated);
        AuditLogService::log('resident_created', "Warga baru ditambahkan: {$resident->display_label}");

        return redirect()->route('admin.residents.index')->with('success', 'Data warga berhasil ditambahkan.');
    }

    public function update(StoreResidentRequest $request, Resident $resident)
    {
        $validated = $request->validated();

        if ($validated['block'] === 'KAVLING') {
            $validated['house_number'] = null;
        } else {
            $validated['house_number'] = str_pad($validated['house_number'], 2, '0', STR_PAD_LEFT);
        }

        $resident->update($validated);
        AuditLogService::log('resident_updated', "Data warga diperbarui: {$resident->display_label}");

        return redirect()->route('admin.residents.index')->with('success', 'Data warga berhasil diperbarui.');
    }

    public function toggleStatus(Resident $resident)
    {
        $newStatus = ($resident->status === 'ACTIVE') ? 'INACTIVE' : 'ACTIVE';
        $resident->update(['status' => $newStatus]);

        AuditLogService::log('resident_status_changed', "Status warga {$resident->display_label} diubah ke {$newStatus}");

        return redirect()->back()->with('success', "Status warga berhasil diubah menjadi {$newStatus}.");
    }

    public function regenerateToken(Resident $resident)
    {
        $newToken = UploadTokenService::generateToken();
        $resident->update(['upload_token' => $newToken]);

        AuditLogService::log('resident_token_regenerated', "Token upload warga {$resident->display_label} diregenerasi.");

        return redirect()->back()->with('success', 'Token upload warga berhasil diperbarui.');
    }

    public function destroy(Resident $resident)
    {
        $label = $resident->display_label;
        $resident->delete();

        AuditLogService::log('resident_deleted', "Warga dihapus: {$label}");

        return redirect()->route('admin.residents.index')->with('success', 'Data warga berhasil dihapus.');
    }
}
