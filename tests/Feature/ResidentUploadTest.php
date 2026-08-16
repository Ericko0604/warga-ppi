<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Photo;
use App\Models\Resident;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ResidentUploadTest extends TestCase
{
    use RefreshDatabase;

    protected Resident $resident;
    protected Event $event;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        $this->resident = Resident::create([
            'block' => 'A1',
            'house_number' => '07',
            'upload_token' => 'testtoken12345678901234567890123456789012',
            'status' => 'ACTIVE',
        ]);

        $this->event = Event::create([
            'name' => '17 Agustus',
            'type' => 'ACARA',
            'event_date' => '2026-08-17',
            'allow_resident_upload' => true,
            'status' => 'PUBLISHED',
        ]);
    }

    public function test_resident_can_upload_valid_landscape_photo(): void
    {
        // Landscape photo (width 1920 > height 1080)
        $file = UploadedFile::fake()->image('landscape.jpg', 1920, 1080);

        $response = $this->post(route('resident.upload.store', ['token' => $this->resident->upload_token]), [
            'event_id' => $this->event->id,
            'photo' => $file,
        ]);

        $response->assertRedirect(route('resident.upload.success', [
            'token' => $this->resident->upload_token,
            'event_id' => $this->event->id,
        ]));

        $this->assertDatabaseHas('photos', [
            'event_id' => $this->event->id,
            'resident_id' => $this->resident->id,
            'uploader_type' => 'RESIDENT',
        ]);
    }

    public function test_resident_cannot_upload_portrait_photo(): void
    {
        // Portrait photo (width 1080 < height 1920)
        $file = UploadedFile::fake()->image('portrait.jpg', 1080, 1920);

        $response = $this->from(route('resident.upload', ['token' => $this->resident->upload_token]))
            ->post(route('resident.upload.store', ['token' => $this->resident->upload_token]), [
                'event_id' => $this->event->id,
                'photo' => $file,
            ]);

        $response->assertRedirect(route('resident.upload', ['token' => $this->resident->upload_token]));
        $response->assertSessionHas('error_message');

        $this->assertDatabaseMissing('photos', [
            'event_id' => $this->event->id,
            'resident_id' => $this->resident->id,
        ]);
    }

    public function test_resident_second_upload_replaces_existing_photo(): void
    {
        $file1 = UploadedFile::fake()->image('first.jpg', 1920, 1080);
        $this->post(route('resident.upload.store', ['token' => $this->resident->upload_token]), [
            'event_id' => $this->event->id,
            'photo' => $file1,
        ]);

        $firstPhoto = Photo::where('event_id', $this->event->id)->where('resident_id', $this->resident->id)->first();
        $this->assertNotNull($firstPhoto);

        // Upload second photo
        $file2 = UploadedFile::fake()->image('second.jpg', 1600, 900);
        $this->post(route('resident.upload.store', ['token' => $this->resident->upload_token]), [
            'event_id' => $this->event->id,
            'photo' => $file2,
        ]);

        // Assert database count remains exactly 1 photo for this resident and event
        $photoCount = Photo::where('event_id', $this->event->id)->where('resident_id', $this->resident->id)->count();
        $this->assertEquals(1, $photoCount);
    }

    public function test_invalid_upload_token_returns_404(): void
    {
        $response = $this->get(route('resident.upload', ['token' => 'invalidtoken123']));
        $response->assertStatus(404);
    }

    public function test_archived_event_rejects_resident_upload(): void
    {
        $this->event->update(['status' => 'ARCHIVED']);

        $file = UploadedFile::fake()->image('landscape.jpg', 1920, 1080);
        $response = $this->post(route('resident.upload.store', ['token' => $this->resident->upload_token]), [
            'event_id' => $this->event->id,
            'photo' => $file,
        ]);

        $response->assertSessionHas('error_message');
    }
}
