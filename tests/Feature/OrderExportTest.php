<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_export_route(): void
    {
        $response = $this->get(route('admin.orders.export', ['period' => 'daily', 'format' => 'csv']));

        $response->assertRedirect(route('login'));
    }

    public function test_admin_can_export_daily_orders_as_print_view(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $customer = Customer::create([
            'name' => 'Budi Sudarsono',
            'whatsapp' => '6281299998888',
        ]);

        $order = Order::create([
            'order_number' => 'JT-20260911-0001',
            'customer_id' => $customer->id,
            'task_type' => 'Makalah Sejarah',
            'description' => 'Makalah 10 halaman',
            'price' => 150000,
            'status' => 'in_progress',
            'progress' => 50,
            'created_at' => now(),
        ]);

        $response = $this->actingAs($admin)->get(route('admin.orders.export', [
            'period' => 'daily',
            'format' => 'print',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Laporan Rekapitulasi Pesanan');
        $response->assertSee('JT-20260911-0001');
        $response->assertSee('Budi Sudarsono');
        $response->assertSee('Makalah Sejarah');
        $response->assertSee('150.000');
    }

    public function test_admin_can_export_weekly_and_monthly_orders(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $customer = Customer::create([
            'name' => 'Siti Rahma',
            'whatsapp' => '6281277776666',
        ]);

        Order::create([
            'order_number' => 'JT-20260911-0002',
            'customer_id' => $customer->id,
            'task_type' => 'Skripsi Bab 1',
            'description' => 'Revisi latar belakang',
            'price' => 300000,
            'status' => 'waiting_payment',
            'progress' => 10,
            'created_at' => now(),
        ]);

        $weeklyResponse = $this->actingAs($admin)->get(route('admin.orders.export', [
            'period' => 'weekly',
            'format' => 'print',
        ]));
        $weeklyResponse->assertStatus(200);
        $weeklyResponse->assertSee('Mingguan');
        $weeklyResponse->assertSee('JT-20260911-0002');

        $monthlyResponse = $this->actingAs($admin)->get(route('admin.orders.export', [
            'period' => 'monthly',
            'format' => 'print',
        ]));
        $monthlyResponse->assertStatus(200);
        $monthlyResponse->assertSee('Bulanan');
        $monthlyResponse->assertSee('JT-20260911-0002');
    }

    public function test_admin_can_export_orders_as_csv_stream(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $customer = Customer::create([
            'name' => 'Doni Siregar',
            'whatsapp' => '6281233334444',
        ]);

        Order::create([
            'order_number' => 'JT-20260911-0003',
            'customer_id' => $customer->id,
            'task_type' => 'Pemrograman Python',
            'description' => 'Script scraping',
            'price' => 200000,
            'status' => 'completed',
            'progress' => 100,
            'created_at' => now(),
        ]);

        $response = $this->actingAs($admin)->get(route('admin.orders.export', [
            'period' => 'daily',
            'format' => 'csv',
        ]));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('Laporan-Pesanan-daily-', $response->headers->get('Content-Disposition'));

        ob_start();
        $response->sendContent();
        $content = ob_get_clean();

        $this->assertStringContainsString('No. Order', $content);
        $this->assertStringContainsString('JT-20260911-0003', $content);
        $this->assertStringContainsString('Doni Siregar', $content);
        $this->assertStringContainsString('Pemrograman Python', $content);
    }
}
