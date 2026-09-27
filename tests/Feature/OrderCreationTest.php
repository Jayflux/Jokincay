<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderCreationTest extends TestCase
{
    use RefreshDatabase;

    public function test_landing_page_can_be_rendered(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Formulir Pesanan');
        $response->assertSee('Tanpa Buat Akun');
    }

    public function test_customer_can_create_order_without_account(): void
    {
        $orderData = [
            'name' => 'Budi Santoso',
            'whatsapp' => '081234567899',
            'task_type' => 'Makalah / Paper',
            'description' => 'Makalah 10 halaman tentang Transformasi Digital untuk UKM.',
        ];

        $response = $this->post('/orders', $orderData);

        // Verify customer created with normalized number
        $this->assertDatabaseHas('customers', [
            'name' => 'Budi Santoso',
            'whatsapp' => '6281234567899',
        ]);

        $customer = Customer::where('whatsapp', '6281234567899')->first();
        $this->assertNotNull($customer);

        // Verify order created with PendingNego status
        $this->assertDatabaseHas('orders', [
            'customer_id' => $customer->id,
            'task_type' => 'Makalah / Paper',
            'status' => OrderStatus::PendingNego->value,
            'progress' => 0,
        ]);

        $order = Order::where('customer_id', $customer->id)->first();
        $this->assertNotNull($order);
        $this->assertStringStartsWith('JT-', $order->order_number);

        // Check redirect to success page
        $response->assertRedirect(route('orders.success', ['order' => $order->order_number]));
    }

    public function test_order_creation_fails_with_invalid_data(): void
    {
        $response = $this->post('/orders', [
            'name' => '',
            'whatsapp' => '',
            'task_type' => '',
            'description' => 'pendek',
        ]);

        $response->assertSessionHasErrors(['name', 'whatsapp', 'task_type', 'description']);
    }

    public function test_customer_can_create_order_with_task_file_attachment(): void
    {
        \Illuminate\Support\Facades\Storage::fake('local');

        $file = \Illuminate\Http\UploadedFile::fake()->create('soal_tugas_matematika.pdf', 1024, 'application/pdf');

        $orderData = [
            'name' => 'Alif Firmansyah',
            'whatsapp' => '085712345678',
            'task_type' => 'Laporan Praktikum',
            'description' => 'Pengerjaan soal praktikum sesuai modul terlampir di file PDF.',
            'attachment' => $file,
        ];

        $response = $this->post('/orders', $orderData);

        $order = Order::where('task_type', 'Laporan Praktikum')->latest()->first();
        $this->assertNotNull($order);
        $this->assertEquals('soal_tugas_matematika.pdf', $order->attachment_name);
        $this->assertNotNull($order->attachment_path);

        \Illuminate\Support\Facades\Storage::disk('local')->assertExists($order->attachment_path);

        $response->assertRedirect(route('orders.success', ['order' => $order->order_number]));
    }

    public function test_admin_can_download_order_attachment(): void
    {
        \Illuminate\Support\Facades\Storage::fake('local');

        $admin = \App\Models\User::create([
            'name' => 'Admin Test',
            'email' => 'admin_test@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $customer = Customer::create([
            'name' => 'Rizky',
            'whatsapp' => '628999888777',
        ]);

        $path = \Illuminate\Support\Facades\Storage::disk('local')->put('order-attachments/sample.pdf', 'konten tugas');

        $order = Order::create([
            'order_number' => 'JT-20260911-TEST',
            'customer_id' => $customer->id,
            'task_type' => 'Makalah',
            'description' => 'Deskripsi tugas',
            'attachment_path' => 'order-attachments/sample.pdf',
            'attachment_name' => 'modul_tugas.pdf',
        ]);

        // Unauthenticated access forbidden
        $guestResponse = $this->get(route('admin.orders.attachment', $order));
        $guestResponse->assertRedirect(route('login'));

        // Authenticated admin can download
        $adminResponse = $this->actingAs($admin)->get(route('admin.orders.attachment', $order));
        $adminResponse->assertStatus(200);
        $adminResponse->assertDownload('modul_tugas.pdf');
    }

    public function test_payment_is_automatically_created_when_order_is_created(): void
    {
        $customer = Customer::create([
            'name' => 'Maya Sari',
            'whatsapp' => '628177777777',
        ]);

        // 1. Create order without price (pending nego)
        $order = Order::create([
            'order_number' => 'JT-20260911-MAY1',
            'customer_id' => $customer->id,
            'task_type' => 'Makalah',
            'description' => 'Tugas makalah biologi',
            'status' => OrderStatus::PendingNego,
        ]);

        // Payment must be automatically created with Unpaid status
        $this->assertDatabaseHas('payments', [
            'order_id' => $order->id,
            'amount' => 0,
            'status' => \App\Enums\PaymentStatus::Unpaid->value,
        ]);

        $payment = $order->payments()->first();
        $this->assertNotNull($payment);
        $this->assertEquals(\App\Enums\PaymentStatus::Unpaid, $payment->status);

        // 2. Update price on order
        $order->update(['price' => 175000]);

        // Unpaid payment amount must be automatically updated
        $payment->refresh();
        $this->assertEquals(175000, $payment->amount);
    }
}
