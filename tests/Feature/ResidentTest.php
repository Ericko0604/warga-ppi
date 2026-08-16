<?php

namespace Tests\Feature;

use App\Models\Resident;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResidentTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create();
    }

    public function test_admin_can_add_new_resident(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.residents.store'), [
            'block' => 'A1',
            'house_number' => '07',
            'family_head_name' => 'Budi Santoso',
        ]);

        $response->assertRedirect(route('admin.residents.index'));

        $this->assertDatabaseHas('residents', [
            'block' => 'A1',
            'house_number' => '07',
            'family_head_name' => 'Budi Santoso',
            'status' => 'ACTIVE',
        ]);

        $resident = Resident::where('block', 'A1')->where('house_number', '07')->first();
        $this->assertNotNull($resident->upload_token);
        $this->assertEquals(40, strlen($resident->upload_token));
    }

    public function test_admin_can_add_kavling_resident(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.residents.store'), [
            'block' => 'KAVLING',
            'family_head_name' => 'Bapak Ahmad',
        ]);

        $response->assertRedirect(route('admin.residents.index'));

        $this->assertDatabaseHas('residents', [
            'block' => 'KAVLING',
            'house_number' => null,
            'family_head_name' => 'Bapak Ahmad',
        ]);
    }

    public function test_admin_can_edit_resident(): void
    {
        $resident = Resident::create([
            'block' => 'A2',
            'house_number' => '05',
            'family_head_name' => 'Lama',
            'upload_token' => 'token12345678901234567890123456789012345678',
            'status' => 'ACTIVE',
        ]);

        $response = $this->actingAs($this->admin)->put(route('admin.residents.update', $resident->id), [
            'block' => 'A2',
            'house_number' => '05',
            'family_head_name' => 'Baru Updated',
        ]);

        $response->assertRedirect(route('admin.residents.index'));
        $this->assertDatabaseHas('residents', [
            'id' => $resident->id,
            'family_head_name' => 'Baru Updated',
        ]);
    }

    public function test_admin_can_toggle_resident_status(): void
    {
        $resident = Resident::create([
            'block' => 'A3',
            'house_number' => '10',
            'upload_token' => 'token123456789012345678901234567890123456789',
            'status' => 'ACTIVE',
        ]);

        $this->actingAs($this->admin)->post(route('admin.residents.toggle_status', $resident->id));
        $this->assertEquals('INACTIVE', $resident->fresh()->status);

        $this->actingAs($this->admin)->post(route('admin.residents.toggle_status', $resident->id));
        $this->assertEquals('ACTIVE', $resident->fresh()->status);
    }
}
