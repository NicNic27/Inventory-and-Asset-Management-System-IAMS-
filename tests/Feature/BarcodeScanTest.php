<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Supply;
use App\Models\Asset;
use App\Models\User;

class BarcodeScanTest extends TestCase
{
    use RefreshDatabase;

    public function test_scan_in_and_out_for_supply_and_asset()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $supply = Supply::create([
            'barcode_id' => 'SUP-TEST-1',
            'article' => 'Supply A',
            'description' => 'Desc',
            'quantity' => 10,
            'unit_value' => 1,
        ]);

        $asset = Asset::create([
            'item_code' => 'ASSET-TEST-1',
            'barcode_id' => 'ASSET-TEST-1',
            'name' => 'Asset A',
            'category' => 'Assets',
            'article' => 'Asset A',
            'description' => 'Asset Desc',
            'unit_value' => 1000,
        ]);

        // IN supply
        $inSup = $this->post('/barcodes/scan', ['barcode' => 'SUP-TEST-1', 'qty' => 5, 'mode' => 'IN', 'context' => 'supplies']);
        $inSup->assertJson(['status' => 'success']);
        $this->assertEquals(15, $supply->fresh()->quantity);

        // OUT supply
        $outSup = $this->post('/barcodes/scan', ['barcode' => 'SUP-TEST-1', 'qty' => 3, 'mode' => 'OUT', 'context' => 'supplies']);
        $outSup->assertJson(['status' => 'success']);
        $this->assertEquals(12, $supply->fresh()->quantity);

        // IN asset
        $inAsset = $this->post('/barcodes/scan', ['barcode' => 'ASSET-TEST-1', 'qty' => 1, 'mode' => 'IN', 'context' => 'assets']);
        $inAsset->assertJson(['status' => 'success']);
        $this->assertEquals(1, $asset->fresh()->quantity ?? 1);
    }
}
