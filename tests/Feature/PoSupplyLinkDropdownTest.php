<?php

namespace Tests\Feature;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supply;
use App\Models\SupplySection;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PoSupplyLinkDropdownTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsStaff(): User
    {
        $user = User::factory()->create(['role' => 'staff']);
        $this->actingAs($user);

        return $user;
    }

    private function createPoWithItem(array $itemOverrides = []): PurchaseOrder
    {
        $po = PurchaseOrder::create([
            'po_type'          => 'Supply',
            'entity_name'      => 'DepEd',
            'po_no'            => 'PO-TEST-001',
            'supplier_name'    => 'Acme',
            'supplier_address' => 'Manila',
            'po_date'          => now()->toDateString(),
            'procurement_mode' => 'Public Bidding',
            'auth_official'    => 'Regional Director',
            'chief_accountant' => 'Chief Accountant',
            'total_amount'     => 0,
            'status'           => 'Pending',
        ]);

        PurchaseOrderItem::create(array_merge([
            'purchase_order_id' => $po->id,
            'unit'              => 'Ream',
            'description'       => 'Sub 20 Glossy',
            'qty'               => 5,
            'unit_cost'         => 220,
            'amount'            => 1100,
            'item_type'         => 'supply',
            'source_type'       => 'procurement_stock',
        ], $itemOverrides));

        return $po;
    }

    public function test_po_page_provides_the_sections_map_for_the_destination_picker(): void
    {
        $this->actingAsStaff();

        SupplySection::create(['name' => 'Bond Paper', 'classification' => 'A4']);
        SupplySection::create(['name' => 'Bond Paper', 'classification' => 'Legal']);
        Supply::create([
            'article'        => 'Ink Cartridge',
            'description'    => 'HP 680 Black',
            'classification' => 'Ink',
            'unit_measure'   => 'Piece(s)',
        ]);

        $response = $this->get('/po');
        $response->assertOk();

        $sections = $response->viewData('sections');

        $this->assertEqualsCanonicalizing(['A4', 'Legal'], $sections->get('Bond Paper')->values()->all());
        $this->assertEqualsCanonicalizing(['Ink'], $sections->get('Ink Cartridge')->values()->all());
    }

    public function test_receiving_files_stock_under_the_chosen_section_and_classification(): void
    {
        $this->actingAsStaff();

        $po = $this->createPoWithItem();
        $poItem = $po->items->first();

        $response = $this->postJson("/po/{$po->id}/receive", [
            'po_id'     => $po->id,
            'dr_number' => 'DR-2026-001',
            'dr_date'   => now()->toDateString(),
            'items'     => [
                [
                    'po_item_id'          => $poItem->id,
                    'quantity'            => 5,
                    'dest_section'        => 'Bond Paper',
                    'dest_classification' => 'A4',
                ],
            ],
        ]);

        $response->assertOk()->assertJson(['success' => true]);

        // A supply now exists under the chosen Section › Classification
        $supply = Supply::where('article', 'Bond Paper')
            ->where('classification', 'A4')
            ->where('description', 'Sub 20 Glossy')
            ->first();

        $this->assertNotNull($supply, 'Supply should be created under Bond Paper › A4');
        $this->assertSame(5, (int) $supply->quantity);

        // Stock-in transaction recorded against the PO
        $this->assertDatabaseHas('transactions', [
            'item_id'           => $supply->id,
            'item_type'         => 'supplies',
            'transaction_type'  => 'IN',
            'quantity'          => 5,
            'po_number'         => 'PO-TEST-001',
        ]);

        // The pair is registered in Manage Sections for future dropdowns
        $this->assertDatabaseHas('supply_sections', [
            'name'           => 'Bond Paper',
            'classification' => 'A4',
        ]);

        // The PO item remembers its destination
        $this->assertDatabaseHas('purchase_order_items', [
            'id'                  => $poItem->id,
            'supply_id'           => $supply->id,
            'dest_section'        => 'Bond Paper',
            'dest_classification' => 'A4',
        ]);
    }

    public function test_receiving_adds_to_the_existing_supply_under_the_same_destination(): void
    {
        $this->actingAsStaff();

        $existing = Supply::create([
            'article'        => 'Bond Paper',
            'description'    => 'Sub 20 Glossy',
            'classification' => 'A4',
            'unit_measure'   => 'Ream',
            'unit_value'     => 220,
            'quantity'       => 10,
        ]);

        $po = $this->createPoWithItem();
        $poItem = $po->items->first();

        $this->postJson("/po/{$po->id}/receive", [
            'po_id'     => $po->id,
            'dr_number' => 'DR-2026-002',
            'dr_date'   => now()->toDateString(),
            'items'     => [
                [
                    'po_item_id'          => $poItem->id,
                    'quantity'            => 5,
                    'dest_section'        => 'Bond Paper',
                    'dest_classification' => 'A4',
                ],
            ],
        ])->assertOk();

        $existing->refresh();
        $this->assertSame(15, (int) $existing->quantity);
        $this->assertSame(1, Supply::where('article', 'Bond Paper')->where('classification', 'A4')->count());
    }

    public function test_receiving_without_a_destination_section_is_rejected(): void
    {
        $this->actingAsStaff();

        $po = $this->createPoWithItem();
        $poItem = $po->items->first();

        $this->postJson("/po/{$po->id}/receive", [
            'po_id'     => $po->id,
            'dr_number' => 'DR-2026-003',
            'dr_date'   => now()->toDateString(),
            'items'     => [
                ['po_item_id' => $poItem->id, 'quantity' => 5],
            ],
        ])->assertStatus(422);

        $this->assertSame(0, Supply::count());
    }

    public function test_receive_sheet_returns_saved_destinations(): void
    {
        $this->actingAsStaff();

        $po = $this->createPoWithItem([
            'dest_section'        => 'Bond Paper',
            'dest_classification' => 'Legal',
        ]);

        $response = $this->getJson("/po/{$po->id}/receive-sheet");
        $response->assertOk();

        $item = $response->json('items.0');
        $this->assertSame('Bond Paper', $item['dest_section']);
        $this->assertSame('Legal', $item['dest_classification']);
    }
}
