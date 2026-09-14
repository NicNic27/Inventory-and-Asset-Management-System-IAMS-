<?php

namespace Tests\Feature;

use App\Models\SupplySection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupplySectionQuickAddTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsStaff(): void
    {
        $user = \App\Models\User::factory()->create(['role' => 'staff']);
        $this->actingAs($user);
    }

    public function test_quick_add_creates_section_and_classification_pair(): void
    {
        $this->actingAsStaff();

        $response = $this->postJson('/supplies/sections', [
            'name'           => 'Bond Paper',
            'classification' => 'A4',
        ]);

        $response->assertOk()
            ->assertJson(['status' => 'created', 'name' => 'Bond Paper', 'classification' => 'A4']);

        $this->assertDatabaseHas('supply_sections', [
            'name'           => 'Bond Paper',
            'classification' => 'A4',
        ]);
    }

    public function test_quick_add_is_idempotent_on_the_same_pair(): void
    {
        $this->actingAsStaff();

        $payload = ['name' => 'Bond Paper', 'classification' => 'Legal'];

        $this->postJson('/supplies/sections', $payload)->assertOk()->assertJson(['status' => 'created']);
        $this->postJson('/supplies/sections', $payload)->assertOk()->assertJson(['status' => 'exists']);

        $this->assertSame(1, SupplySection::where('name', 'Bond Paper')->where('classification', 'Legal')->count());
    }

    public function test_same_section_can_hold_multiple_classifications(): void
    {
        $this->actingAsStaff();

        foreach (['A4', 'Legal', 'Letter'] as $cls) {
            $this->postJson('/supplies/sections', ['name' => 'Bond Paper', 'classification' => $cls])
                ->assertOk();
        }

        $map = SupplySection::mergeWithExisting(null);

        $this->assertEquals(['A4', 'Legal', 'Letter'], $map->get('Bond Paper')->values()->all());
    }

    public function test_quick_added_sections_without_supplies_appear_in_the_list(): void
    {
        $this->actingAsStaff();

        SupplySection::create(['name' => 'Ink Cartridge', 'classification' => 'HP 680']);

        $response = $this->get('/supplies');
        $response->assertOk();

        $suppliesGrouped = $response->viewData('suppliesGrouped');
        $this->assertTrue($suppliesGrouped->has('Ink Cartridge'));
        $this->assertTrue($suppliesGrouped->get('Ink Cartridge')->has('HP 680'));
        $this->assertCount(0, $suppliesGrouped->get('Ink Cartridge')->get('HP 680'));
    }

    public function test_quick_added_classifications_merge_under_existing_section_groups(): void
    {
        $this->actingAsStaff();

        SupplySection::create(['name' => 'Bond Paper', 'classification' => 'Legal']);

        \App\Models\Supply::create([
            'article'       => 'Bond Paper',
            'description'   => 'Copy paper',
            'unit_measure'  => 'Ream',
            'unit_value'    => 120,
            'quantity'      => 5,
            'status'        => 'Available',
            'classification' => 'A4',
        ]);

        $response = $this->get('/supplies');
        $response->assertOk();

        $suppliesGrouped = $response->viewData('suppliesGrouped');
        $bondPaper = $suppliesGrouped->get('Bond Paper');

        $this->assertTrue($bondPaper->has('A4'));
        $this->assertCount(1, $bondPaper->get('A4'));
        $this->assertTrue($bondPaper->has('Legal'));
        $this->assertCount(0, $bondPaper->get('Legal'));
    }

    public function test_sections_merge_into_dropdown_data_from_supply_rows(): void
    {
        $this->actingAsStaff();

        SupplySection::create(['name' => 'Bond Paper', 'classification' => 'A4']);

        // An existing supply row that already uses the section with a different classification
        \App\Models\Supply::create([
            'article'       => 'Bond Paper',
            'description'   => 'Copy paper',
            'unit_measure'  => 'Ream',
            'unit_value'    => 120,
            'quantity'      => 5,
            'status'        => 'Available',
            'classification' => 'Letter',
        ]);

        $response = $this->get('/supplies');
        $response->assertOk();

        $sections = $response->viewData('sections');
        $this->assertEquals(['A4', 'Letter'], $sections->get('Bond Paper')->values()->all());
    }

    public function test_sections_manage_endpoint_groups_pairs_with_id(): void
    {
        $this->actingAsStaff();

        $this->postJson('/supplies/sections', ['name' => 'Bond Paper', 'classification' => 'A4']);
        $this->postJson('/supplies/sections', ['name' => 'Bond Paper', 'classification' => 'Legal']);

        $response = $this->getJson('/supplies/sections');
        $response->assertOk()->assertJsonCount(1);

        $group = $response->json()[0];
        $this->assertSame('Bond Paper', $group['name']);
        $this->assertEquals(['A4', 'Legal'], $group['classifications']);
        $this->assertNotNull($group['id']);
    }

    public function test_section_can_be_renamed_and_moves_all_classifications(): void
    {
        $this->actingAsStaff();

        $this->postJson('/supplies/sections', ['name' => 'Bond Papers', 'classification' => 'A4']);
        $this->postJson('/supplies/sections', ['name' => 'Bond Papers', 'classification' => 'Legal']);

        $id = SupplySection::where('name', 'Bond Papers')->value('id');

        $this->putJson("/supplies/sections/{$id}", ['name' => 'Bond Paper'])
            ->assertOk()
            ->assertJson(['status' => 'success', 'name' => 'Bond Paper']);

        $this->assertSame(0, SupplySection::where('name', 'Bond Papers')->count());
        $this->assertSame(2, SupplySection::where('name', 'Bond Paper')->count());
    }

    public function test_single_classification_can_be_removed(): void
    {
        $this->actingAsStaff();

        $this->postJson('/supplies/sections', ['name' => 'Bond Paper', 'classification' => 'A4']);
        $this->postJson('/supplies/sections', ['name' => 'Bond Paper', 'classification' => 'Legal']);

        $id = SupplySection::where('name', 'Bond Paper')->value('id');

        $this->deleteJson("/supplies/sections/{$id}", ['classification' => 'Legal'])
            ->assertOk()
            ->assertJson(['status' => 'success']);

        $this->assertSame(0, SupplySection::where('name', 'Bond Paper')->where('classification', 'Legal')->count());
        $this->assertSame(1, SupplySection::where('name', 'Bond Paper')->where('classification', 'A4')->count());
    }

    public function test_whole_section_with_all_classifications_can_be_removed(): void
    {
        $this->actingAsStaff();

        $this->postJson('/supplies/sections', ['name' => 'Bond Papers', 'classification' => 'A4']);
        $this->postJson('/supplies/sections', ['name' => 'Bond Papers', 'classification' => 'Legal']);

        $id = SupplySection::where('name', 'Bond Papers')->value('id');

        $this->deleteJson("/supplies/sections/{$id}")
            ->assertOk()
            ->assertJson(['status' => 'success']);

        $this->assertSame(0, SupplySection::where('name', 'Bond Papers')->count());
    }

    public function test_removing_a_section_leaves_recorded_supplies_untouched(): void
    {
        $this->actingAsStaff();

        $this->postJson('/supplies/sections', ['name' => 'Bond Papers', 'classification' => 'A4']);

        $supply = \App\Models\Supply::create([
            'article'       => 'Bond Papers',
            'description'   => 'Copy paper',
            'unit_measure'  => 'Ream',
            'unit_value'    => 120,
            'quantity'      => 5,
            'status'        => 'Available',
            'classification' => 'A4',
        ]);

        $id = SupplySection::where('name', 'Bond Papers')->value('id');
        $this->deleteJson("/supplies/sections/{$id}")->assertOk();

        $this->assertSame(0, SupplySection::where('name', 'Bond Papers')->count());
        $this->assertDatabaseHas('supplies', ['id' => $supply->id, 'article' => 'Bond Papers']);
    }

    public function test_validation_requires_both_fields(): void
    {
        $this->actingAsStaff();

        $this->postJson('/supplies/sections', ['name' => 'Bond Paper'])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['classification']);
    }

    public function test_guests_cannot_quick_add(): void
    {
        $this->postJson('/supplies/sections', ['name' => 'X', 'classification' => 'Y'])
            ->assertStatus(401);
    }

    /**
     * Registering a supply with a brand-new section + classification through
     * the Add Supply form must file the pair into supply_sections, so Manage
     * Sections shows it exactly as if the dedicated quick-add button was used.
     */
    public function test_add_supply_form_registers_the_pair_for_manage_sections(): void
    {
        $this->actingAsStaff();

        $this->postJson('/supplies', [
            'article'          => 'Bond Paper',
            'description'      => 'Copy paper',
            'classification'   => 'A4',
            'unit_measure'     => 'Ream',
            'unit_value'       => 120,
            'initial_quantity' => 5,
        ])->assertOk()->assertJson(['status' => 'success']);

        $response = $this->getJson('/supplies/sections');
        $response->assertOk();

        $group = collect($response->json())->firstWhere('name', 'Bond Paper');
        $this->assertNotNull($group, 'Section created via Add Supply form must appear in Manage Sections');
        $this->assertEquals(['A4'], $group['classifications']);
    }

    /**
     * Both entry points write the same registry: quick-adding a pair that was
     * already used by the Add Supply form must not create a second row.
     */
    public function test_quick_add_after_add_supply_form_does_not_duplicate_the_pair(): void
    {
        $this->actingAsStaff();

        $this->postJson('/supplies', [
            'article'          => 'Bond Paper',
            'description'      => 'Copy paper',
            'classification'   => 'Legal',
            'unit_measure'     => 'Ream',
            'unit_value'       => 120,
            'initial_quantity' => 5,
        ])->assertOk();

        $this->postJson('/supplies/sections', ['name' => 'Bond Paper', 'classification' => 'Legal'])
            ->assertOk()
            ->assertJson(['status' => 'exists']);

        $this->assertSame(1, SupplySection::where('name', 'Bond Paper')->where('classification', 'Legal')->count());
    }

    /**
     * A supply registered without a classification must not file an empty
     * pair into the registry.
     */
    public function test_add_supply_form_without_classification_creates_no_registry_pair(): void
    {
        $this->actingAsStaff();

        $this->postJson('/supplies', [
            'article'          => 'Stapler',
            'description'      => 'Heavy Duty',
            'unit_measure'     => 'Piece',
            'unit_value'       => 80,
            'initial_quantity' => 3,
        ])->assertOk();

        $this->assertSame(0, SupplySection::where('name', 'Stapler')->count());
    }

    /**
     * The admin Add Supply form syncs the registry the same way.
     */
    public function test_admin_add_supply_form_registers_the_pair_for_manage_sections(): void
    {
        \App\Models\User::factory()->create(['role' => 'admin']);
        $this->actingAs(\App\Models\User::where('role', 'admin')->first());

        $this->postJson('/admin/supplies', [
            'article'          => 'Ink Cartridge',
            'description'      => 'HP 680 Black',
            'classification'   => 'HP 680',
            'unit_measure'     => 'Piece',
            'unit_value'       => 450,
            'initial_quantity' => 10,
        ])->assertOk()->assertJson(['status' => 'success']);

        $response = $this->getJson('/admin/supplies/sections');
        $response->assertOk();

        $group = collect($response->json())->firstWhere('name', 'Ink Cartridge');
        $this->assertNotNull($group, 'Section created via admin Add Supply form must appear in Manage Sections');
        $this->assertEquals(['HP 680'], $group['classifications']);
    }
}
