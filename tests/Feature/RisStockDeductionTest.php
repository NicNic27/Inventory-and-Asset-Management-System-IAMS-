<?php

namespace Tests\Feature;

use App\Models\RisItem;
use App\Models\RisRequest;
use App\Models\Supply;
use App\Models\Transaction;
use App\Models\User;
use App\Services\RisService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RisStockDeductionTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsAdmin(): User
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        return $admin;
    }

    private function actingAsStaff(): User
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $this->actingAs($staff);

        return $staff;
    }

    private function createRisWithItem(array $itemAttributes, string $status = 'Forwarded to Admin'): RisRequest
    {
        $ris = RisRequest::create([
            'user_id'        => User::factory()->create(['role' => 'staff'])->id,
            'ris_no'         => 'RIS-TEST-0001',
            'entity_name'    => 'Test Entity',
            'division'       => 'Test Division',
            'office'         => 'Test Office',
            'purpose'        => 'Testing',
            'date_requested' => now()->toDateString(),
            'status'         => $status,
        ]);

        RisItem::create(['ris_id' => $ris->id] + $itemAttributes);

        return $ris;
    }

    // --- Resolver behavior ---

    public function test_resolver_prefers_barcode_over_description(): void
    {
        $supplyByBarcode = Supply::create([
            'article'       => 'Bond Paper',
            'barcode_id'    => 'SUP-BARCODE-1',
            'description'   => 'Copy paper',
            'unit_measure'  => 'Ream',
            'unit_value'    => 120,
            'quantity'      => 10,
            'status'        => 'Available',
        ]);

        // A decoy with no barcode whose article would also match
        Supply::create([
            'article'      => 'Bond Paper',
            'description'  => 'Copy paper',
            'unit_measure' => 'Ream',
            'unit_value'   => 120,
            'quantity'     => 99,
            'status'       => 'Available',
        ]);

        $item = new RisItem([
            'stock_no'    => 'SUP-BARCODE-1',
            'description' => 'Bond Paper, Copy paper, A4',
            'req_quantity' => 1,
        ]);

        $resolved = app(RisService::class)->resolveSupplyForItem($item);

        $this->assertNotNull($resolved);
        $this->assertSame($supplyByBarcode->id, $resolved->id);
    }

    public function test_resolver_matches_combined_description_without_barcode(): void
    {
        $supply = Supply::create([
            'article'        => 'Bond Paper',
            'description'    => 'Sub 20 Glossy',
            'classification' => 'A4',
            'unit_measure'   => 'Ream',
            'unit_value'     => 120,
            'quantity'       => 10,
            'status'         => 'Available',
        ]);

        $item = new RisItem([
            'stock_no'     => null,
            'description'  => 'Bond Paper, Sub 20 Glossy, A4',
            'req_quantity' => 1,
        ]);

        $resolved = app(RisService::class)->resolveSupplyForItem($item);

        $this->assertNotNull($resolved);
        $this->assertSame($supply->id, $resolved->id);
    }

    public function test_resolver_uses_classification_to_disambiguate(): void
    {
        $a4 = Supply::create([
            'article'        => 'Bond Paper',
            'description'    => 'Sub 20 Glossy',
            'classification' => 'A4',
            'unit_measure'   => 'Ream',
            'unit_value'     => 120,
            'quantity'       => 10,
            'status'         => 'Available',
        ]);

        Supply::create([
            'article'        => 'Bond Paper',
            'description'    => 'Sub 20 Glossy',
            'classification' => 'Legal',
            'unit_measure'   => 'Ream',
            'unit_value'     => 130,
            'quantity'       => 20,
            'status'         => 'Available',
        ]);

        $item = new RisItem([
            'stock_no'     => null,
            'description'  => 'Bond Paper, Sub 20 Glossy, Legal',
            'req_quantity' => 1,
        ]);

        $resolved = app(RisService::class)->resolveSupplyForItem($item);

        $this->assertNotNull($resolved);
        $this->assertSame('Legal', $resolved->classification);
        $this->assertNotSame($a4->id, $resolved->id);
    }

    public function test_resolver_falls_back_to_loose_match_for_typed_descriptions(): void
    {
        $supply = Supply::create([
            'article'      => 'Stapler',
            'description'  => 'Heavy Duty Full Strip',
            'unit_measure' => 'Piece',
            'unit_value'   => 80,
            'quantity'     => 5,
            'status'       => 'Available',
        ]);

        $item = new RisItem([
            'stock_no'     => null,
            'description'  => 'Stapler, Heavy Duty',
            'req_quantity' => 1,
        ]);

        $resolved = app(RisService::class)->resolveSupplyForItem($item);

        $this->assertNotNull($resolved);
        $this->assertSame($supply->id, $resolved->id);
    }

    // --- Approval deduction ---

    public function test_approval_deducts_stock_for_item_without_barcode(): void
    {
        $this->actingAsAdmin();

        $supply = Supply::create([
            'article'        => 'Bond Paper',
            'description'    => 'Sub 20 Glossy',
            'classification' => 'A4',
            'unit_measure'   => 'Ream',
            'unit_value'     => 120,
            'quantity'       => 25,
            'status'         => 'Available',
        ]);

        $ris = $this->createRisWithItem([
            'stock_no'      => null,
            'unit'          => 'Ream',
            'description'   => 'Bond Paper, Sub 20 Glossy, A4',
            'req_quantity'  => 5,
            'issue_quantity' => 5,
            'stock_avail'   => 'yes',
        ]);

        $this->post("/admin/ris/{$ris->id}/process", ['new_status' => 'Approved'])
            ->assertRedirect('/admin/ris');

        $this->assertSame(20, $supply->fresh()->quantity);
        $this->assertDatabaseHas('transactions', [
            'item_id'          => $supply->id,
            'item_type'        => 'supplies',
            'transaction_type' => 'OUT',
            'quantity'         => 5,
        ]);
    }

    public function test_approval_via_full_staff_path_deducts_stock(): void
    {
        $this->actingAsStaff();

        $supply = Supply::create([
            'article'        => 'Ink Cartridge',
            'description'    => 'HP 680 Black',
            'classification' => 'HP 680',
            'unit_measure'   => 'Piece',
            'unit_value'     => 450,
            'quantity'       => 12,
            'status'         => 'Available',
        ]);

        // Staff encodes the physical RIS form (combined description, auto barcode)
        $this->post('/ris', [
            'entity_name'     => 'Test Entity',
            'division'        => 'Test Division',
            'unit_section'    => 'Test Office',
            'purpose'         => 'Testing',
            'requested_by'    => 'Juan Dela Cruz',
            'desig_requested' => 'Staff',
            'description'     => ['Ink Cartridge, HP 680 Black, HP 680'],
            'manual_description' => [''],
            'stock_no'        => [$supply->barcode_id],
            'unit_measure'    => ['Piece'],
            'quantity'        => [3],
            'remarks'         => [''],
        ])->assertRedirect();

        $ris = RisRequest::latest('id')->first();
        $this->assertNotNull($ris);
        $this->assertSame('Pending Staff Review', $ris->status);

        // Staff forwards to admin
        $this->post("/ris/{$ris->id}/update", ['action' => 'forward'])->assertRedirect();

        // Admin approves — stock must drop
        $this->actingAsAdmin();
        $this->post("/admin/ris/{$ris->id}/process", ['new_status' => 'Approved'])
            ->assertRedirect('/admin/ris');

        $this->assertSame(9, $supply->fresh()->quantity);
        $this->assertDatabaseHas('transactions', [
            'item_id'          => $supply->id,
            'transaction_type' => 'OUT',
            'quantity'         => 3,
        ]);
    }

    public function test_approval_records_the_requesting_office_on_the_release_transaction(): void
    {
        $this->actingAsAdmin();

        $supply = Supply::create([
            'article'      => 'Bond Paper',
            'description'  => 'Sub 20',
            'unit_measure' => 'Ream',
            'unit_value'   => 120,
            'quantity'     => 30,
            'status'       => 'Available',
        ]);

        // The RIS carries both a division and the specific office/unit/section
        $ris = $this->createRisWithItem([
            'stock_no'      => null,
            'unit'          => 'Ream',
            'description'   => 'Bond Paper, Sub 20',
            'req_quantity'  => 4,
            'issue_quantity' => 4,
            'stock_avail'   => 'yes',
        ]);

        $this->post("/admin/ris/{$ris->id}/process", ['new_status' => 'Approved'])->assertRedirect();

        $this->assertDatabaseHas('transactions', [
            'item_id'          => $supply->id,
            'item_type'        => 'supplies',
            'transaction_type' => 'OUT',
            'quantity'         => 4,
            // The office from the RIS — not the division, not a generic label
            'office'           => 'Test Office',
        ]);
    }

    public function test_approval_is_idempotent_and_does_not_double_deduct(): void
    {
        $this->actingAsAdmin();

        $supply = Supply::create([
            'article'      => 'Bond Paper',
            'description'  => 'Sub 20',
            'unit_measure' => 'Ream',
            'unit_value'   => 120,
            'quantity'     => 30,
            'status'       => 'Available',
        ]);

        $ris = $this->createRisWithItem([
            'stock_no'      => null,
            'unit'          => 'Ream',
            'description'   => 'Bond Paper, Sub 20',
            'req_quantity'  => 4,
            'issue_quantity' => 4,
            'stock_avail'   => 'yes',
        ]);

        $this->post("/admin/ris/{$ris->id}/process", ['new_status' => 'Approved'])->assertRedirect();
        $this->post("/admin/ris/{$ris->id}/process", ['new_status' => 'Approved'])->assertRedirect();

        $this->assertSame(26, $supply->fresh()->quantity);
    }

    public function test_items_marked_unavailable_are_skipped(): void
    {
        $this->actingAsAdmin();

        $supply = Supply::create([
            'article'      => 'Bond Paper',
            'description'  => 'Sub 20',
            'unit_measure' => 'Ream',
            'unit_value'   => 120,
            'quantity'     => 0,
            'status'       => 'Out of Stock',
        ]);

        $ris = $this->createRisWithItem([
            'stock_no'      => null,
            'unit'          => 'Ream',
            'description'   => 'Bond Paper, Sub 20',
            'req_quantity'  => 4,
            'stock_avail'   => 'no',
        ]);

        $this->post("/admin/ris/{$ris->id}/process", ['new_status' => 'Approved'])->assertRedirect();

        $this->assertSame(0, $supply->fresh()->quantity);
        $this->assertDatabaseMissing('transactions', [
            'item_id'          => $supply->id,
            'transaction_type' => 'OUT',
        ]);
    }

    // --- Revoke / restore ---

    public function test_revoking_approval_restores_deducted_stock(): void
    {
        $this->actingAsAdmin();

        $supply = Supply::create([
            'article'      => 'Bond Paper',
            'description'  => 'Sub 20',
            'unit_measure' => 'Ream',
            'unit_value'   => 120,
            'quantity'     => 30,
            'status'       => 'Available',
        ]);

        $ris = $this->createRisWithItem([
            'stock_no'      => null,
            'unit'          => 'Ream',
            'description'   => 'Bond Paper, Sub 20',
            'req_quantity'  => 6,
            'issue_quantity' => 6,
            'stock_avail'   => 'yes',
        ]);

        $this->post("/admin/ris/{$ris->id}/process", ['new_status' => 'Approved'])->assertRedirect();
        $this->assertSame(24, $supply->fresh()->quantity);

        $this->post("/admin/ris/{$ris->id}/process", ['new_status' => 'Pending Staff Review'])->assertRedirect();
        $this->assertSame(30, $supply->fresh()->quantity);
        $this->assertDatabaseHas('transactions', [
            'item_id'          => $supply->id,
            'transaction_type' => 'IN',
            'quantity'         => 6,
        ]);
    }

    // --- Staff review view ---

    public function test_staff_review_page_shows_correct_current_stock_without_barcode(): void
    {
        $this->actingAsStaff();

        Supply::create([
            'article'        => 'Bond Paper',
            'description'    => 'Sub 20 Glossy',
            'classification' => 'A4',
            'unit_measure'   => 'Ream',
            'unit_value'     => 120,
            'quantity'       => 15,
            'status'         => 'Available',
        ]);

        $ris = $this->createRisWithItem([
            'stock_no'     => null,
            'unit'         => 'Ream',
            'description'  => 'Bond Paper, Sub 20 Glossy, A4',
            'req_quantity' => 5,
            'stock_avail'  => 'N/A',
        ]);

        $response = $this->get("/ris/{$ris->id}/review");
        $response->assertOk();

        $html = $response->getContent();
        // The review page must show the resolved stock (15), not 0
        $this->assertStringContainsString('>15</span>', $html);
    }
}
