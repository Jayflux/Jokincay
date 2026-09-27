<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PaymentFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_upload_payment_proof(): void
    {
        Storage::fake('local');

        $customer = Customer::create([
            'name' => 'Fajar Pratama',
            'whatsapp' => '6281333333333',
        ]);

        $order = Order::create([
            'order_number' => 'JT-20260911-FAJ1',
            'customer_id' => $customer->id,
            'task_type' => 'Makalah',
            'description' => 'Tugas makalah etika profesi',
            'price' => 100000,
            'status' => OrderStatus::WaitingPayment,
        ]);

        $file = UploadedFile::fake()->image('bukti_transfer.jpg');

        $response = $this->post("/orders/{$order->id}/payment-proof", [
            'proof' => $file,
        ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('payments', [
            'order_id' => $order->id,
            'amount' => 100000,
            'status' => PaymentStatus::PendingVerification->value,
        ]);

        $payment = Payment::where('order_id', $order->id)->first();
        Storage::disk('local')->assertExists($payment->proof_path);
    }

    public function test_admin_verification_advances_order_to_in_progress(): void
    {
        $admin = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('secret'),
            'role' => 'admin',
        ]);

        $customer = Customer::create([
            'name' => 'Dewi Lestari',
            'whatsapp' => '6281444444444',
        ]);

        $order = Order::create([
            'order_number' => 'JT-20260911-DEW1',
            'customer_id' => $customer->id,
            'task_type' => 'PPT Bisnis',
            'description' => 'Slide presentasi bisnis',
            'price' => 200000,
            'status' => OrderStatus::WaitingPayment,
            'progress' => 10,
        ]);

        $payment = Payment::create([
            'order_id' => $order->id,
            'amount' => 200000,
            'proof_path' => 'payment-proofs/test.png',
            'status' => PaymentStatus::PendingVerification,
            'uploaded_at' => now(),
        ]);

        $paymentService = app(PaymentService::class);
        $paymentService->verifyPayment($payment, $admin);

        // Payment status verified
        $payment->refresh();
        $this->assertEquals(PaymentStatus::Verified, $payment->status);
        $this->assertEquals($admin->id, $payment->verified_by);
        $this->assertNotNull($payment->verified_at);

        // Order status automatically advanced to InProgress
        $order->refresh();
        $this->assertEquals(OrderStatus::InProgress, $order->status);
        $this->assertGreaterThanOrEqual(20, $order->progress);
    }

    public function test_payment_proof_file_can_be_downloaded_by_admin(): void
    {
        Storage::fake('local');

        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin_dl@test.com',
            'password' => bcrypt('secret'),
            'role' => 'admin',
        ]);

        $customer = Customer::create([
            'name' => 'Budi',
            'whatsapp' => '6281555555555',
        ]);

        $order = Order::create([
            'order_number' => 'JT-20260911-BUD1',
            'customer_id' => $customer->id,
            'task_type' => 'Makalah',
            'description' => 'Deskripsi',
            'price' => 150000,
        ]);

        Storage::disk('local')->put('payment-proofs/bukti.png', 'fake image content');

        $payment = Payment::create([
            'order_id' => $order->id,
            'amount' => 150000,
            'proof_path' => 'payment-proofs/bukti.png',
            'status' => PaymentStatus::PendingVerification,
        ]);

        // Unauthenticated access redirects to login
        $guestResponse = $this->get(route('admin.payments.proof', $payment));
        $guestResponse->assertRedirect(route('login'));

        // Authenticated admin can view/download
        $adminResponse = $this->actingAs($admin)->get(route('admin.payments.proof', $payment));
        $adminResponse->assertStatus(200);
    }
}
