<?php

namespace Database\Seeders;

use App\Models\Resident;
use App\Services\UploadTokenService;
use Illuminate\Database\Seeder;

class ResidentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $blocks = [
            'A1' => 25,
            'A2' => 30,
            'A3' => 25,
            'A4' => 30,
        ];

        foreach ($blocks as $block => $maxHouseNumber) {
            for ($i = 1; $i <= $maxHouseNumber; $i++) {
                $houseNumber = str_pad((string)$i, 2, '0', STR_PAD_LEFT);
                Resident::firstOrCreate(
                    [
                        'block' => $block,
                        'house_number' => $houseNumber,
                    ],
                    [
                        'family_head_name' => null,
                        'upload_token' => UploadTokenService::generateToken(),
                        'status' => 'ACTIVE',
                    ]
                );
            }
        }

        // Kavling Block (No house numbers, identified by Family Head Name)
        $kavlingResidents = [
            'Bapak Ahmad',
            'Bapak Budi',
            'Bapak Candra',
            'Bapak Eko',
            'Bapak Hendra',
            'Bapak Gunawan',
            'Ibu Ratna',
            'Bapak Santoso',
        ];

        foreach ($kavlingResidents as $familyHead) {
            Resident::firstOrCreate(
                [
                    'block' => 'KAVLING',
                    'family_head_name' => $familyHead,
                ],
                [
                    'house_number' => null,
                    'upload_token' => UploadTokenService::generateToken(),
                    'status' => 'ACTIVE',
                ]
            );
        }
    }
}
