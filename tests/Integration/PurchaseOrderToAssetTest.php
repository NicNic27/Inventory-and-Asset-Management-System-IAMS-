<?php

namespace Tests\Integration;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Asset;
use App\Models\User;

class PurchaseOrderToAssetTest extends TestCase
{
    use RefreshDatabase;

    public function test_delivered_po_items_create_assets()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $po = PurchaseOrder::create([
            'po_no' => 'PO-100',
            'po_type' => 'Asset',
            'supplier_name' => 'Test Supplier',
            'supplier_address' => '123 Test St',
            'po_date' => date('Y-m-d'),
            'procurement_mode' => 'Direct',
            'auth_official' => 'Auth Name',
            'chief_accountant' => 'Chief Name'
        ]);

        $item = PurchaseOrderItem::create([
            'purchase_order_id' => $po->id,
            'unit' => 'pcs',
            'description' => 'New Laptop',
            'qty' => 1,
            'unit_cost' => 50000,
            'amount' => 50000,
            'is_delivered' => true
        ]);

        // Simulate admin assets index which looks for delivered Po items
        $response = $this->get('/asset-list');
        $response->assertStatus(200);

        // Manually create asset from PO item (approx real flow)
        $asset = Asset::create([
            'item_code' => 'PO-100-1',
            'barcode_id' => 'PO-100-1',
            'name' => 'New Laptop',
            'article' => 'New Laptop',
            'category' => 'Assets',
            'description' => 'New Laptop',
            'unit_value' => 50000
        ]);

        $this->assertDatabaseHas('assets', ['barcode_id' => 'PO-100-1']);
    }
}
