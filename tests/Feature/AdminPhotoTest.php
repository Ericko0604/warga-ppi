<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Photo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminPhotoTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Event $event;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        $this->admin = User::factory()->create();
        $this->event = Event::create([
            'name' => 'Acara 17an',
            'type' => 'ACARA',
            'event_date' => '2026-08-17',
            'status' => 'PUBLISHED',
        ]);
    }

    public function test_admin_can_upload_photo(): void
    {
        $file = UploadedFile::fake()->image('admin1.jpg', 1920, 1080);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.events.photos.store', $this->event->id), [
                'photo' => $file,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('photos', [
            'event_id' => $this->event->id,
            'uploader_type' => 'ADMIN',
        ]);
    }

    public function test_admin_cannot_exceed_10_photos_limit(): void
    {
        // Pre-create 10 admin photos for this event
        for ($i = 1; $i <= 10; $i++) {
            Photo::create([
                'event_id' => $this->event->id,
                'uploader_type' => 'ADMIN',
                'file_path' => "events/2026/event-{$this->event->id}/admin/photo-{$i}.webp",
            ]);
        }

        $this->assertEquals(10, $this->event->adminPhotos()->count());

        // Attempt to upload 11th photo
        $file = UploadedFile::fake()->image('admin11.jpg', 1920, 1080);
        $response = $this->actingAs($this->admin)
            ->post(route('admin.events.photos.store', $this->event->id), [
                'photo' => $file,
            ]);

        $response->assertSessionHas('error_message');
        $this->assertEquals(10, $this->event->adminPhotos()->count());
    }
}
