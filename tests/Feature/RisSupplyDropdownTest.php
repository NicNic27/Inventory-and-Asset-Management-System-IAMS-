<?php

namespace Tests\Feature;

use App\Models\Supply;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RisSupplyDropdownTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsStaff(): User
    {
        $user = User::factory()->create(['role' => 'staff']);
        $this->actingAs($user);

        return $user;
    }

    private function actingAsFrontUser(): User
    {
        $user = User::factory()->create(['role' => 'frontuser']);
        $this->actingAs($user);

        return $user;
    }

    public function test_staff_ris_dropdown_includes_section_and_classification(): void
    {
        $this->actingAsStaff();

        Supply::create([
            'article'        => 'Bond Paper',
            'barcode_id'     => 'SUP-001',
            'description'    => 'Sub 20 Glossy',
            'classification' => 'A4',
            'unit_measure'   => 'Ream',
            'quantity'       => 25,
        ]);

        Supply::create([
            'article'        => 'Bond Paper',
            'barcode_id'     => 'SUP-002',
            'description'    => 'Sub 20 Glossy',
            'classification' => 'Legal',
            'unit_measure'   => 'Ream',
            'quantity'       => 10,
        ]);

        $response = $this->get('/ris/create');

        $response->assertOk();

        $html = $response->getContent();

        // Each supply must appear as "Article — Description (Classification)"
        $this->assertStringContainsString(
            'Bond Paper — Sub 20 Glossy (A4)',
            $html
        );
        $this->assertStringContainsString(
            'Bond Paper — Sub 20 Glossy (Legal)',
            $html
        );

        // The option value now carries the classification as a third segment
        $this->assertMatchesRegularExpression(
            '/value="Bond Paper, Sub 20 Glossy, A4"/',
            $html
        );
        $this->assertMatchesRegularExpression(
            '/value="Bond Paper, Sub 20 Glossy, Legal"/',
            $html
        );
    }

    public function test_staff_ris_dropdown_omits_classification_when_empty(): void
    {
        $this->actingAsStaff();

        Supply::create([
            'article'      => 'Stapler',
            'barcode_id'   => 'SUP-003',
            'description'  => 'Heavy Duty',
            'unit_measure' => 'Piece',
            'quantity'     => 5,
        ]);

        $response = $this->get('/ris/create')->assertOk();

        $this->assertStringContainsString('Stapler — Heavy Duty</option>', $response->getContent());
        $this->assertStringNotContainsString('Stapler — Heavy Duty ()', $response->getContent());
    }

    public function test_frontuser_ris_dropdown_includes_section_and_classification(): void
    {
        $this->actingAsFrontUser();

        Supply::create([
            'article'        => 'Bond Paper',
            'barcode_id'     => 'SUP-004',
            'description'    => 'Sub 24',
            'classification' => 'Legal',
            'unit_measure'   => 'Ream',
            'quantity'       => 7,
        ]);

        $response = $this->get('/user/ris/create');

        $response->assertOk()
            ->assertSee('Bond Paper — Sub 24 (Legal)', false)
            ->assertSee('value="Bond Paper, Sub 24, Legal"', false);
    }
}
