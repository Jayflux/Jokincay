<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrackingTest extends TestCase
{
    use RefreshDatabase;

    public function test_tracking_page_can_be_rendered(): void
    {
        $response = $this->get('/track');
        $response->assertStatus(200);
        $response->assertSee('Lacak Pesanan Tugas');
    }

    public function test_customer_can_track_their_orders_and_orders_are_isolated(): void
    {
        // Customer 1
        $customer1 = Customer::create([
            'name' => 'Rina Melati',
            'whatsapp' => '6281111111111',
        ]);

        $order1 = Order::create([
            'order_number' => 'JT-20260911-RIN1',
            'customer_id' => $customer1->id,
            'task_type' => 'Makalah',
            'description' => 'Tugas Rina yang sangat rahasia',
            'status' => OrderStatus::WaitingPayment,
            'price' => 150000,
        ]);

        // Customer 2
        $customer2 = Customer::create([
            'name' => 'Doni Siregar',
            'whatsapp' => '6282222222222',
        ]);

        $order2 = Order::create([
            'order_number' => 'JT-20260911-DON1',
            'customer_id' => $customer2->id,
            'task_type' => 'Coding Web',
            'description' => 'Tugas Doni membuat API',
            'status' => OrderStatus::InProgress,
            'price' => 300000,
        ]);

        // Search using Customer 1 WhatsApp (in local format 081111111111)
        $response = $this->post('/track/search', [
            'whatsapp' => '081111111111',
        ]);

        $response->assertStatus(200);
        $response->assertSee('JT-20260911-RIN1');
        $response->assertSee('Tugas Rina yang sangat rahasia');

        // Customer 2's data MUST NOT be visible!
        $response->assertDontSee('JT-20260911-DON1');
        $response->assertDontSee('Tugas Doni membuat API');
    }

    public function test_non_existent_whatsapp_shows_friendly_not_found(): void
    {
        $response = $this->post('/track/search', [
            'whatsapp' => '089999999999',
        ]);

        $response->assertStatus(200);
        $response->assertSee('Pesanan Tidak Ditemukan');
    }
}
