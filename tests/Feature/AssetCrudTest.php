<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Asset;
use App\Models\User;

class AssetCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_read_update_delete_asset()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        // Create
        $post = [
            'barcode_id' => 'ASSET-1001',
            'article' => 'Test Laptop',
            'description' => 'A test laptop',
            'unit_measure' => 'pc',
            'supplier' => 'Vendor',
            'unit_value' => 50000,
            'status' => 'Serviceable'
        ];

        $createResp = $this->post('/asset-list', $post);
        $createResp->assertStatus(302);

        $this->assertDatabaseHas('assets', ['barcode_id' => 'ASSET-1001']);

        $asset = Asset::where('barcode_id', 'ASSET-1001')->first();

        // Read details
        $details = $this->get('/asset-list/'.$asset->id.'/details');
        $details->assertStatus(200);

        // Update
        $update = $this->put('/asset-list/'.$asset->id, array_merge($post, ['article' => 'Updated Laptop']));
        $update->assertStatus(302);

        $this->assertDatabaseHas('assets', ['id' => $asset->id, 'article' => 'Updated Laptop']);

        // Delete
        $delete = $this->delete('/asset-list/'.$asset->id);
        $delete->assertStatus(302);

        $this->assertDatabaseMissing('assets', ['id' => $asset->id]);
    }
}
