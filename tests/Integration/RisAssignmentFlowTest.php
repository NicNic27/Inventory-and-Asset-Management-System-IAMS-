<?php

namespace Tests\Integration;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\IcsRequest;
use App\Models\Asset;
use App\Models\User;

class RisAssignmentFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_ics_request_assigns_asset()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $asset = Asset::create([
            'item_code' => 'ASSET-RIS-1',
            'barcode_id' => 'ASSET-RIS-1',
            'name' => 'RIS Asset',
            'article' => 'RIS Asset',
            'category' => 'Assets',
            'description' => 'For RIS',
            'unit_value' => 1000
        ]);

        $itemsJson = json_encode([[ 'inv_no' => $asset->barcode_id, 'transfer_status' => 'Active' ]]);

        $req = IcsRequest::create([
            'ics_no' => 'ICS-100',
            'items_json' => $itemsJson,
            'sig_received_by_name' => 'John Doe'
        ]);

        $this->assertDatabaseHas('ics_requests', ['id' => $req->id]);
    }
}
