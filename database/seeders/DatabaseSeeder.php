<?php

namespace Database\Seeders;

use App\Models\Event;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            ResidentSeeder::class,
        ]);

        // Create initial sample events as per prompt requirements
        Event::firstOrCreate(
            ['name' => 'Peringatan HUT RI ke-81'],
            [
                'uuid' => (string) Str::uuid(),
                'type' => 'ACARA',
                'event_date' => '2026-08-17',
                'description' => 'Dokumentasi perayaan dan perlombaan 17 Agustus warga perumahan.',
                'allow_resident_upload' => true,
                'status' => 'PUBLISHED',
            ]
        );

        Event::firstOrCreate(
            ['name' => 'Kerja Bakti Lingkungan'],
            [
                'uuid' => (string) Str::uuid(),
                'type' => 'KEGIATAN',
                'event_date' => '2026-08-10',
                'description' => 'Pembersihan got dan fasilitas umum perumahan.',
                'allow_resident_upload' => true,
                'status' => 'PUBLISHED',
            ]
        );

        Event::firstOrCreate(
            ['name' => 'Rapat Koordinasi RT/RW'],
            [
                'uuid' => (string) Str::uuid(),
                'type' => 'KEGIATAN',
                'event_date' => '2026-08-01',
                'description' => 'Rapat bulanan pengurus perumahan.',
                'allow_resident_upload' => false,
                'status' => 'PUBLISHED',
            ]
        );
    }
}
