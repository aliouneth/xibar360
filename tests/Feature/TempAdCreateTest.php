<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TempAdCreateTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_ad_with_dates_and_image(): void
    {
        $admin = User::where('email', 'admin@admin.com')->first()
            ?? User::factory()->create(['email' => 'admin@admin.com', 'role' => 'admin']);

        Storage::fake('public');

        $this->actingAs($admin)
            ->get('/admin/ads/create')
            ->assertOk();

        $file = UploadedFile::fake()->image('banner.jpg', 600, 200)->size(50);

        $this->actingAs($admin)
            ->post('/admin/ads/store', [
                'title' => 'Tmp Center Ad',
                'zone' => 'header_center',
                'target_url' => 'https://example.com',
                'is_active' => true,
                'order' => 0,
                'start_date' => now()->toDateString(),
                'end_date' => now()->addDays(3)->toDateString(),
                'image' => $file,
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->assertDatabaseHas('ads', ['title' => 'Tmp Center Ad', 'zone' => 'header_center']);
    }
}
