<?php

namespace Tests\Feature;

use App\Models\Supply;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClassificationStockCardTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsStaff(): User
    {
        $user = User::factory()->create(['role' => 'staff']);
        $this->actingAs($user);

        return $user;
    }

    private function seedGroup(): array
    {
        $a4 = Supply::create([
            'article'        => 'Bond Paper',
            'description'    => 'Sub 20 Glossy',
            'classification' => 'A4',
            'unit_measure'   => 'Ream',
            'unit_value'     => 220,
            'quantity'       => 0,
        ]);

        $legal = Supply::create([
            'article'        => 'Bond Paper',
            'description'    => 'Sub 16 Offset',
            'classification' => 'A4',
            'unit_measure'   => 'Ream',
            'unit_value'     => 180,
            'quantity'       => 0,
        ]);

        $other = Supply::create([
            'article'        => 'Bond Paper',
            'description'    => 'Sub 20 Matte',
            'classification' => 'Legal',
            'unit_measure'   => 'Ream',
            'unit_value'     => 200,
            'quantity'       => 0,
        ]);

        return [$a4, $legal, $other];
    }

    public function test_classification_card_merges_ledgers_of_all_supplies_in_the_group(): void
    {
        $this->actingAsStaff();
        [$a4, $legal] = $this->seedGroup();

        // Group has two supplies; give each an IN and an OUT
        Transaction::create([
            'item_id' => $a4->id, 'item_type' => 'supplies', 'transaction_type' => 'IN',
            'quantity' => 10, 'transaction_date' => '2026-09-01', 'supplier' => 'Acme',
            'po_number' => 'PO-1',
        ]);
        Transaction::create([
            'item_id' => $a4->id, 'item_type' => 'supplies', 'transaction_type' => 'OUT',
            'quantity' => 3, 'transaction_date' => '2026-09-02', 'office' => 'Records Section',
        ]);
        Transaction::create([
            'item_id' => $legal->id, 'item_type' => 'supplies', 'transaction_type' => 'IN',
            'quantity' => 5, 'transaction_date' => '2026-09-03', 'supplier' => 'Acme',
        ]);
        Transaction::create([
            'item_id' => $legal->id, 'item_type' => 'supplies', 'transaction_type' => 'OUT',
            'quantity' => 1, 'transaction_date' => '2026-09-04', 'office' => 'Cash Section',
        ]);

        // On-hand: a4 = 7, legal = 4 → group total 11 (ledger net is also 11, no offset)
        $response = $this->get('/supplies/stock-card/Bond%20Paper/A4');

        $response->assertOk()
            ->assertSee('STOCK CARD')
            ->assertSee('Bond Paper')
            ->assertSee('A4');

        // Issuances from both supplies appear in one merged ledger
        $response->assertSee('Records Section')->assertSee('Cash Section');

        // Per-item surfaces are gone: no descriptions, no included-items block
        $this->assertStringNotContainsString('Sub 20 Glossy', $response->getContent());
        $this->assertStringNotContainsString('Included Stock Items', $response->getContent());
    }

    public function test_office_column_shows_supplier_for_po_receipts_and_ris_office_for_issuances(): void
    {
        $this->actingAsStaff();
        [$a4] = $this->seedGroup();

        $a4->update(['supplier' => 'Acme Paper Co.']);

        // Receipt that came from a PO: supplier goes in the Office column
        Transaction::create([
            'item_id' => $a4->id, 'item_type' => 'supplies', 'transaction_type' => 'IN',
            'quantity' => 10, 'transaction_date' => '2026-09-01', 'supplier' => 'Acme Paper Co.',
            'po_number' => 'PO-2026-001', 'office' => 'Warehouse',
        ]);
        // Issuance via approved RIS: requesting office goes in the Office column
        Transaction::create([
            'item_id' => $a4->id, 'item_type' => 'supplies', 'transaction_type' => 'OUT',
            'quantity' => 3, 'transaction_date' => '2026-09-02',
            'office' => 'Records Unit', 'remarks' => 'RIS Auto-Release: RIS-2026-09-0001',
        ]);

        $response = $this->get('/supplies/stock-card/' . rawurlencode('Bond Paper') . '/' . rawurlencode('A4'));
        $response->assertOk();

        $html = $response->getContent();

        // Receipt row: the supplier, not the place of delivery
        $this->assertStringContainsString('Acme Paper Co.', $html);
        $this->assertStringNotContainsString('Warehouse', $html);
        // Issuance row: the requesting office from the RIS
        $this->assertStringContainsString('Records Unit', $html);
    }

    public function test_new_transactions_always_append_to_the_next_row(): void
    {
        $this->actingAsStaff();
        [$a4] = $this->seedGroup();

        // PO receipt whose document date is typed ahead of today
        Transaction::create([
            'item_id' => $a4->id, 'item_type' => 'supplies', 'transaction_type' => 'IN',
            'quantity' => 10, 'transaction_date' => '2026-12-01', 'supplier' => 'Acme',
            'po_number' => 'PO-FUTURE-1', 'unit_price' => 220,
        ]);
        // RIS deduction processed today must land AFTER that receipt row
        Transaction::create([
            'item_id' => $a4->id, 'item_type' => 'supplies', 'transaction_type' => 'OUT',
            'quantity' => 3, 'transaction_date' => '2026-09-15', 'office' => 'Records Unit',
            'remarks' => 'RIS Auto-Release: RIS-2026-09-0002', 'supplier' => 'Acme',
        ]);

        $html = $this->get('/supplies/stock-card/' . rawurlencode('Bond Paper') . '/' . rawurlencode('A4'))
            ->assertOk()
            ->getContent();

        $receiptPos = strpos($html, 'PO-FUTURE-1');
        $issuePos = strpos($html, 'Records Unit');
        $this->assertNotFalse($receiptPos);
        $this->assertNotFalse($issuePos);
        $this->assertGreaterThan(
            $receiptPos,
            $issuePos,
            'The deduction must be logged on the next row, never the first row'
        );
    }

    public function test_unit_value_column_shows_running_weighted_average_per_row(): void
    {
        $this->actingAsStaff();
        [$a4] = $this->seedGroup();

        // Opening balance via Add Supply carries the item's unit value (220)
        Transaction::create([
            'item_id' => $a4->id, 'item_type' => 'supplies', 'transaction_type' => 'Added',
            'quantity' => 10, 'transaction_date' => '2026-09-01', 'supplier' => 'Acme',
            'unit_price' => 220, 'remarks' => 'Opening Balance / New Item',
        ]);
        // PO receipt at a different price (100) re-averages the stock
        Transaction::create([
            'item_id' => $a4->id, 'item_type' => 'supplies', 'transaction_type' => 'IN',
            'quantity' => 10, 'transaction_date' => '2026-09-02', 'supplier' => 'Acme',
            'po_number' => 'PO-1', 'unit_price' => 100,
        ]);
        // RIS deduction keeps the average
        Transaction::create([
            'item_id' => $a4->id, 'item_type' => 'supplies', 'transaction_type' => 'OUT',
            'quantity' => 5, 'transaction_date' => '2026-09-03', 'office' => 'Records Unit',
        ]);

        $html = $this->get('/supplies/stock-card/' . rawurlencode('Bond Paper') . '/' . rawurlencode('A4'))
            ->assertOk()
            ->getContent();

        // Rows: 220.00 → (10*220 + 10*100) / 20 = 160.00 → stays 160.00
        $this->assertStringContainsString('220.00', $html);
        $this->assertStringContainsString('160.00', $html);
    }

    public function test_classification_card_excludes_other_classifications(): void
    {
        $this->actingAsStaff();
        [, , $other] = $this->seedGroup();

        Transaction::create([
            'item_id' => $other->id, 'item_type' => 'supplies', 'transaction_type' => 'IN',
            'quantity' => 99, 'transaction_date' => '2026-09-01', 'supplier' => 'Acme',
        ]);

        $response = $this->get('/supplies/stock-card/Bond%20Paper/A4');
        $response->assertOk();

        $this->assertStringNotContainsString('Sub 20 Matte', $response->getContent());
        $this->assertStringNotContainsString('99', $response->getContent());
    }

    public function test_classification_card_aligns_running_balance_with_on_hand_stock(): void
    {
        $this->actingAsStaff();
        [$a4] = $this->seedGroup();

        Transaction::create([
            'item_id' => $a4->id, 'item_type' => 'supplies', 'transaction_type' => 'IN',
            'quantity' => 10, 'transaction_date' => '2026-09-01', 'supplier' => 'Acme',
        ]);

        // Stock drifted: recorded quantity is 12 but the ledger says 10
        $a4->update(['quantity' => 12]);

        $response = $this->get('/supplies/stock-card/Bond%20Paper/A4');
        $response->assertOk();

        // The last balance row must show the true on-hand total (12)
        $this->assertMatchesRegularExpression('/>\s*12\s*<\/td>\s*<td class="center">/', $response->getContent());
    }

    public function test_admin_can_open_the_classification_stock_card(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);
        $this->seedGroup();

        $this->get('/admin/supplies/stock-card/Bond%20Paper/A4')
            ->assertOk()
            ->assertSee('STOCK CARD');
    }

    public function test_staff_view_puts_the_stock_card_button_on_the_classification_header(): void
    {
        $this->actingAsStaff();
        $this->seedGroup();

        $response = $this->get('/supplies');
        $response->assertOk();

        $html = $response->getContent();

        // Header button present with a resolvable URL; the old per-row link is gone
        $this->assertStringContainsString('classification-stock-card-btn', $html);
        $this->assertStringContainsString('/supplies/stock-card/Bond%20Paper/A4', $html);
        $this->assertStringNotContainsString('title="Stock Card"', $html);
    }
}
