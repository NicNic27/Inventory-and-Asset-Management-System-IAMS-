<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Supply;
use App\Models\User;

class SupplyCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_update_stock_transaction_and_delete_supply()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create supply
        $resp = $this->post('/supplies', [
            'article' => 'Test Supply',
            'description' => 'Test Description',
            'supplier' => 'Test Supplier',
            'unit_measure' => 'pcs',
            'unit_value' => 10.5,
            'initial_quantity' => 20
        ]);

        $resp->assertStatus(302);

        $supply = Supply::first();
        $this->assertNotNull($supply);
        $this->assertEquals(20, $supply->quantity);

        // Details
        $details = $this->get('/supplies/'.$supply->id.'/details');
        $details->assertStatus(200);

        // Update
        $update = $this->put('/supplies/'.$supply->id, [
            'barcode_id' => $supply->barcode_id,
            'article' => 'Updated Supply',
            'description' => 'Updated',
            'supplier' => 'Test Supplier',
            'unit_measure' => 'pcs',
            'unit_value' => 12.0,
            'quantity' => 25
        ]);

        $update->assertStatus(302);
        $supply->refresh();
        $this->assertEquals('Updated Supply', $supply->article);
        $this->assertEquals(25, $supply->quantity);

        // Stock transaction IN
        $in = $this->post('/supplies/'.$supply->id.'/transaction', [
            'qty' => 5,
            'transaction_type' => 'IN',
            'supplier' => 'Test Supplier',
            'transaction_date' => date('Y-m-d'),
            'remarks' => 'Stock IN'
        ]);
        $in->assertStatus(302);
        $supply->refresh();
        $this->assertEquals(30, $supply->quantity);

        // Stock transaction OUT
        $out = $this->post('/supplies/'.$supply->id.'/transaction', [
            'qty' => 10,
            'transaction_type' => 'OUT',
            'supplier' => 'Test Supplier',
            'transaction_date' => date('Y-m-d'),
            'remarks' => 'Stock OUT'
        ]);
        $out->assertStatus(302);
        $supply->refresh();
        $this->assertEquals(20, $supply->quantity);

        // Delete
        $del = $this->delete('/supplies/'.$supply->id);
        $del->assertStatus(302);
        $this->assertDatabaseMissing('supplies', ['id' => $supply->id]);
    }
}
