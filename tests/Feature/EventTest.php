<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create();
    }

    public function test_admin_can_create_acara(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.events.store'), [
            'type' => 'ACARA',
            'name' => '17 Agustus 2026',
            'event_date' => '2026-08-17',
            'description' => 'Perlombaan warga 17an',
            'status' => 'PUBLISHED',
        ]);

        $event = Event::where('name', '17 Agustus 2026')->first();
        $this->assertNotNull($event);
        $this->assertTrue($event->allow_resident_upload);
        $response->assertRedirect(route('admin.events.show', $event->id));
    }

    public function test_admin_can_create_kegiatan_with_resident_upload_allowed(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.events.store'), [
            'type' => 'KEGIATAN',
            'name' => 'Kerja Bakti Lingkungan',
            'event_date' => '2026-08-10',
            'allow_resident_upload' => '1',
            'status' => 'PUBLISHED',
        ]);

        $event = Event::where('name', 'Kerja Bakti Lingkungan')->first();
        $this->assertNotNull($event);
        $this->assertTrue($event->allow_resident_upload);
    }

    public function test_admin_can_create_kegiatan_without_resident_upload(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.events.store'), [
            'type' => 'KEGIATAN',
            'name' => 'Rapat Pengurus RT',
            'event_date' => '2026-08-01',
            'allow_resident_upload' => '0',
            'status' => 'PUBLISHED',
        ]);

        $event = Event::where('name', 'Rapat Pengurus RT')->first();
        $this->assertNotNull($event);
        $this->assertFalse($event->allow_resident_upload);
        $this->assertFalse($event->canAcceptResidentUpload());
    }
}
