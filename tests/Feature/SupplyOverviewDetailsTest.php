<?php

namespace Tests\Feature;

use App\Models\Supply;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupplyOverviewDetailsTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsRole(string $role): User
    {
        $user = User::factory()->create(['role' => $role]);
        $this->actingAs($user);

        return $user;
    }

    private function createSupply(array $overrides = []): Supply
    {
        return Supply::create(array_merge([
            'article'        => 'Bond Paper',
            'description'    => 'Sub 20 Glossy',
            'classification' => 'A4',
            'unit_measure'   => 'Ream',
            'unit_value'     => 120,
            'quantity'       => 25,
            'status'         => 'Available',
        ], $overrides));
    }

    public function test_overview_shows_only_current_stock_without_total_or_progress(): void
    {
        $this->actingAsRole('staff');
        $supply = $this->createSupply();

        $html = $this->get("/supplies/{$supply->id}/details")->getContent();

        $this->assertStringContainsString('Current Stock', $html);
        $this->assertStringContainsString('<span class="ov-stock-number">25</span>', $html);

        // Total / progress elements removed per design change
        $this->assertStringNotContainsString('progress-bar', $html);
        $this->assertStringNotContainsString('Total Stock Value', $html);
        $this->assertStringNotContainsString('Remaining', $html);
        $this->assertStringNotContainsString('Total', $html);
    }

    public function test_overview_renders_the_three_stock_states(): void
    {
        $this->actingAsRole('staff');

        $available = $this->createSupply(['article' => 'Available Item', 'quantity' => 25]);
        $low       = $this->createSupply(['article' => 'Low Item', 'quantity' => 3, 'low_stock_threshold' => 5]);
        $out       = $this->createSupply(['article' => 'Out Item', 'quantity' => 0]);

        $availableHtml = $this->get("/supplies/{$available->id}/details")->getContent();
        $this->assertStringContainsString('ov-status-success', $availableHtml);
        $this->assertStringContainsString('Available', $availableHtml);

        $lowHtml = $this->get("/supplies/{$low->id}/details")->getContent();
        $this->assertStringContainsString('ov-status-warning', $lowHtml);
        $this->assertStringContainsString('Low Stock', $lowHtml);

        $outHtml = $this->get("/supplies/{$out->id}/details")->getContent();
        $this->assertStringContainsString('ov-status-danger', $outHtml);
        $this->assertStringContainsString('Out of Stock', $outHtml);
    }

    public function test_overview_renders_key_details_without_supplier_and_brand_duplicating_labels(): void
    {
        $this->actingAsRole('staff');
        $supply = $this->createSupply(['supplier' => 'Pandayan', 'brand' => 'HP', 'model' => '680']);

        $html = $this->get("/supplies/{$supply->id}/details")->getContent();

        // Detail tiles
        $this->assertStringContainsString('Section', $html);
        $this->assertStringContainsString('Classification', $html);
        $this->assertStringContainsString('Unit Value', $html);
        $this->assertStringContainsString('Low Stock Threshold', $html);

        // Chips hide when empty, show when filled
        $this->assertStringContainsString('Pandayan', $html);
        $this->assertStringContainsString('HP', $html);

        // No old-style label rows remain
        $this->assertStringNotContainsString('Article:', $html);
        $this->assertStringNotContainsString('Brand:', $html);
    }

    public function test_overview_hides_empty_chips(): void
    {
        $this->actingAsRole('staff');
        $supply = $this->createSupply(['brand' => null, 'model' => null, 'supplier' => null]);

        $html = $this->get("/supplies/{$supply->id}/details")->getContent();

        // The chips container only renders when at least one chip exists
        $this->assertStringNotContainsString('class="ov-chips"', $html);
    }

    public function test_admin_overview_matches_the_new_design(): void
    {
        $this->actingAsRole('admin');
        $supply = $this->createSupply(['quantity' => 25]);

        $html = $this->get("/admin/supplies/{$supply->id}/details")->getContent();

        $this->assertStringContainsString('Current Stock', $html);
        $this->assertStringContainsString('<span class="ov-stock-number">25</span>', $html);
        $this->assertStringNotContainsString('progress-bar', $html);
        $this->assertStringNotContainsString('Total', $html);
    }

    public function test_missing_supply_returns_friendly_error(): void
    {
        $this->actingAsRole('staff');

        $html = $this->get('/supplies/999999/details')->getContent();

        $this->assertStringContainsString('Supply details not found', $html);
    }
}
