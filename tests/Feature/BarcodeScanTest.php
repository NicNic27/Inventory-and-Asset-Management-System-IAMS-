<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Asset;
use App\Models\User;

class BarcodeScanTest extends TestCase
{
    use RefreshDatabase;

    public function test_qr_scan_endpoint_accepts_the_existing_asset_identifier()
    {
        $user = User::factory()->create(['role' => 'staff']);
        $this->actingAs($user);

        $asset = Asset::create([
            'item_code' => 'PPE-2026-08-20-00001-01-01',
            'barcode_id' => 'PPE-2026-08-20-00001-01-01',
            'name' => 'Asset A',
            'category' => 'Assets',
            'article' => 'Asset A',
            'description' => 'Asset Desc',
            'unit_value' => 1000,
        ]);

        $response = $this->get('/asset-custody/scan?qr_code=PPE-2026-08-20-00001-01-01');

        $response->assertOk()->assertJsonPath('asset.barcode_id', 'PPE-2026-08-20-00001-01-01');
    }
}
