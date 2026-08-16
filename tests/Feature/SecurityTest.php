<?php

namespace Tests\Feature;

use App\Models\Resident;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_admin_dashboard(): void
    {
        $response = $this->get(route('admin.dashboard'));
        $response->assertRedirect(route('admin.login'));
    }

    public function test_guest_cannot_access_resident_management(): void
    {
        $response = $this->get(route('admin.residents.index'));
        $response->assertRedirect(route('admin.login'));
    }

    public function test_resident_upload_token_cannot_access_admin(): void
    {
        $resident = Resident::create([
            'block' => 'A1',
            'house_number' => '01',
            'upload_token' => 'token123456789012345678901234567890123456789',
            'status' => 'ACTIVE',
        ]);

        // Attempting to access admin route with token URL or as guest
        $response = $this->get('/admin/residents');
        $response->assertRedirect(route('admin.login'));
    }
}
