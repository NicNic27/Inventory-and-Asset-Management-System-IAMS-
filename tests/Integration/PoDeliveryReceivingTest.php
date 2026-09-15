<?php

namespace Tests\Integration;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supply;
use App\Models\SupplyBatch;
use App\Models\User;

/**
 * Whole-PO receiving flow: P.O. is created as a commitment (Pending), then the
 * "Receive Delivery" sheet posts real quantities into inventory and recomputes status.
 */
class PoDeliveryReceivingTest extends TestCase
{
    use RefreshDatabase;

    private function createPendingPo(): PurchaseOrder
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson('/po', [
            'po_type' => 'Supply',
            'entity_name' => 'Test Agency',
            'po_no' => 'PO-SUP-200',
            'supplier_name' => 'Supply Supplier',
            'supplier_address' => '123 Test Street',
            'po_date' => date('Y-m-d'),
            'procurement_mode' => 'Direct',
            'auth_official' => 'Authorized Official',
            'auth_official_designation' => 'Agency Head',
            'chief_accountant' => 'Chief Accountant',
            'chief_accountant_designation' => 'Accountant',
            'total_amount' => 200,
            'items' => [[
                'unit' => 'Piece(s)',
                'description' => 'Rubber Band Small',
                'qty' => 10,
                'cost' => 20,
            ]],
        ]);

        $response->assertOk()->assertJson(['success' => true]);

        $po = PurchaseOrder::where('po_no', 'PO-SUP-200')->firstOrFail();

        $this->assertEquals('Pending', $po->status, 'New P.O. must start as Pending');
        $this->assertEquals(0, Supply::where('description', 'Rubber Band Small')->count(),
            'Creating a P.O. must not post any stock');

        return $po->fresh(['items']);
    }

    public function test_po_starts_pending_and_posts_no_stock_until_delivery_is_received()
    {
        $po = $this->createPendingPo();

        $poItem = $po->items->first();
        $this->assertNotNull($poItem);
        $this->assertFalse((bool) $poItem->is_delivered);
    }

    public function test_receiving_a_full_delivery_posts_stock_and_completes_the_po()
    {
        $po = $this->createPendingPo();
        $poItem = $po->items->first();

        $response = $this->postJson("/po/{$po->id}/receive", [
            'po_id' => $po->id,
            'dr_number' => 'DR-2026-001',
            'dr_date' => date('Y-m-d'),
            'remarks' => 'Full delivery',
            'items' => [
                ['po_item_id' => $poItem->id, 'quantity' => 10, 'dest_section' => 'General Supplies', 'dest_classification' => 'Small'],
            ],
        ]);

        $response->assertOk()->assertJson(['success' => true, 'po_status' => 'Complete']);

        $this->assertDatabaseHas('supplies', [
            'description' => 'Rubber Band Small',
            'unit_measure' => 'Piece(s)',
            'quantity' => 10,
            'unit_value' => 20,
        ]);

        $this->assertDatabaseHas('supply_batches', [
            'po_item_id' => $poItem->id,
            'dr_number' => 'DR-2026-001',
            'quantity' => 10,
        ]);

        $this->assertEquals('Complete', $po->fresh()->status);

        $this->assertDatabaseHas('transactions', [
            'po_number' => 'PO-SUP-200',
            'delivery_receipt' => 'DR-2026-001',
            'quantity' => 10,
            'transaction_type' => 'IN',
        ]);
    }

    public function test_partial_delivery_marks_po_partial_and_remaining_stays_receivable()
    {
        $po = $this->createPendingPo();
        $poItem = $po->items->first();

        $response = $this->postJson("/po/{$po->id}/receive", [
            'po_id' => $po->id,
            'dr_number' => 'DR-2026-002',
            'dr_date' => date('Y-m-d'),
            'items' => [
                ['po_item_id' => $poItem->id, 'quantity' => 4, 'dest_section' => 'General Supplies', 'dest_classification' => 'Small'],
            ],
        ]);

        $response->assertOk()->assertJson(['success' => true, 'po_status' => 'Partial']);

        $this->assertEquals('Partial', $po->fresh()->status);
        $this->assertEquals(4, (int) Supply::where('description', 'Rubber Band Small')->value('quantity'));

        // Second delivery completes the remaining 6
        $response = $this->postJson("/po/{$po->id}/receive", [
            'po_id' => $po->id,
            'dr_number' => 'DR-2026-003',
            'dr_date' => date('Y-m-d'),
            'items' => [
                ['po_item_id' => $poItem->id, 'quantity' => 6, 'dest_section' => 'General Supplies', 'dest_classification' => 'Small'],
            ],
        ]);

        $response->assertOk()->assertJson(['success' => true, 'po_status' => 'Complete']);
        $this->assertEquals('Complete', $po->fresh()->status);
        $this->assertEquals(10, (int) Supply::where('description', 'Rubber Band Small')->value('quantity'));

        // Receiving beyond the ordered quantity is rejected
        $response = $this->postJson("/po/{$po->id}/receive", [
            'po_id' => $po->id,
            'dr_number' => 'DR-2026-004',
            'dr_date' => date('Y-m-d'),
            'items' => [
                ['po_item_id' => $poItem->id, 'quantity' => 1, 'dest_section' => 'General Supplies', 'dest_classification' => 'Small'],
            ],
        ]);
        $response->assertStatus(422);
    }

    public function test_receiving_sheet_data_endpoint_returns_lines_and_history()
    {
        $po = $this->createPendingPo();
        $poItem = $po->items->first();

        $this->postJson("/po/{$po->id}/receive", [
            'po_id' => $po->id,
            'dr_number' => 'DR-2026-005',
            'dr_date' => date('Y-m-d'),
            'items' => [['po_item_id' => $poItem->id, 'quantity' => 3, 'dest_section' => 'General Supplies', 'dest_classification' => 'Small']],
        ])->assertOk();

        $response = $this->getJson("/po/{$po->id}/receive-sheet");

        $response->assertOk()
            ->assertJsonStructure([
                'id', 'po_no', 'supplier_name', 'status',
                'items' => [['po_item_id', 'description', 'ordered', 'received', 'remaining', 'history']],
            ]);

        $data = $response->json();
        $this->assertEquals(3, $data['items'][0]['received']);
        $this->assertEquals(7, $data['items'][0]['remaining']);
        $this->assertSame('DR-2026-005', $data['items'][0]['history'][0]['dr_number']);
    }

    public function test_legacy_checkbox_items_still_count_as_delivered_when_recomputing()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $po = PurchaseOrder::create([
            'po_no' => 'PO-LEGACY-1',
            'po_type' => 'Supply',
            'supplier_name' => 'Legacy Supplier',
            'supplier_address' => '1 Legacy St',
            'po_date' => date('Y-m-d'),
            'procurement_mode' => 'Direct',
            'auth_official' => 'Auth',
            'chief_accountant' => 'Chief',
            'status' => 'Complete',
        ]);

        PurchaseOrderItem::create([
            'purchase_order_id' => $po->id,
            'unit' => 'Piece(s)',
            'description' => 'Legacy Item',
            'qty' => 5,
            'unit_cost' => 10,
            'amount' => 50,
            'is_delivered' => true,
            'inventory_synced' => true,
        ]);

        $this->assertEquals('complete', $po->items->first()->getDeliveryStatus());
        $this->assertEquals('Complete', $po->recomputeStatus());
    }

    public function test_editing_a_po_does_not_overwrite_delivery_derived_status()
    {
        $po = $this->createPendingPo();
        $poItem = $po->items->first();

        $this->postJson("/po/{$po->id}/receive", [
            'po_id' => $po->id,
            'dr_number' => 'DR-2026-006',
            'dr_date' => date('Y-m-d'),
            'items' => [['po_item_id' => $poItem->id, 'quantity' => 10, 'dest_section' => 'General Supplies', 'dest_classification' => 'Small']],
        ])->assertOk();
        $this->assertEquals('Complete', $po->fresh()->status);

        // Edit the P.O. (rename supplier) without any is_delivered payload
        $response = $this->putJson("/po/{$po->id}", [
            'po_type' => 'Supply',
            'entity_name' => 'Test Agency',
            'po_no' => 'PO-SUP-200',
            'supplier_name' => 'Renamed Supplier',
            'supplier_address' => '123 Test Street',
            'po_date' => date('Y-m-d'),
            'procurement_mode' => 'Direct',
            'auth_official' => 'Authorized Official',
            'auth_official_designation' => 'Agency Head',
            'chief_accountant' => 'Chief Accountant',
            'chief_accountant_designation' => 'Accountant',
            'total_amount' => 200,
            'items' => [[
                'id' => $poItem->id,
                'unit' => 'Piece(s)',
                'description' => 'Rubber Band Small',
                'qty' => 10,
                'cost' => 20,
            ]],
        ]);

        $response->assertOk()->assertJson(['success' => true]);
        $this->assertEquals('Complete', $po->fresh()->status,
            'Editing a P.O. must not reset delivery-derived status');
        $this->assertTrue((bool) $po->items->first()->fresh()->is_delivered,
            'Editing must not clear the delivery flag on items with recorded batches');
    }
}
