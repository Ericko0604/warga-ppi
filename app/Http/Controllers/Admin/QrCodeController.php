<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Resident;
use App\Services\QrCodeService;
use Illuminate\Http\Request;

class QrCodeController extends Controller
{
    public function __construct(
        protected QrCodeService $qrService
    ) {}

    public function show(Resident $resident)
    {
        $uploadUrl = route('resident.upload', ['token' => $resident->upload_token]);
        $qrSvg = $this->qrService->generateSvg($uploadUrl, 250);

        return view('admin.residents.qr', compact('resident', 'uploadUrl', 'qrSvg'));
    }

    public function printAll(Request $request)
    {
        $blockFilter = $request->query('block');

        $query = Resident::active()->orderBy('block')->orderBy('house_number')->orderBy('family_head_name');

        if ($blockFilter && in_array(strtoupper($blockFilter), ['A1', 'A2', 'A3', 'A4', 'KAVLING'])) {
            $query->byBlock($blockFilter);
        }

        $residents = $query->get();

        $residentsWithQr = $residents->map(function ($resident) {
            $uploadUrl = route('resident.upload', ['token' => $resident->upload_token]);
            return [
                'resident' => $resident,
                'url' => $uploadUrl,
                'qr_svg' => $this->qrService->generateSvg($uploadUrl, 160),
            ];
        });

        return view('admin.residents.qr-print', compact('residentsWithQr', 'blockFilter'));
    }
}
