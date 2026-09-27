<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function __construct(
        protected WhatsAppService $whatsAppService
    ) {}

    /**
     * Create an order and ensure customer is tracked by WhatsApp.
     */
    public function createOrder(array $data, ?\Illuminate\Http\UploadedFile $attachment = null): array
    {
        return DB::transaction(function () use ($data, $attachment) {
            $whatsapp = WhatsAppNormalizer::normalize($data['whatsapp']);

            // Find or create customer
            $customer = Customer::firstOrCreate(
                ['whatsapp' => $whatsapp],
                ['name' => $data['name']]
            );

            // Update customer name if provided and changed
            if ($customer->name !== $data['name']) {
                $customer->update(['name' => $data['name']]);
            }

            // Create order
            $orderNumber = OrderNumberGenerator::generate();

            $attachmentPath = null;
            $attachmentName = null;

            if ($attachment) {
                $attachmentName = $attachment->getClientOriginalName();
                $extension = $attachment->getClientOriginalExtension();
                $safeFilename = 'task_' . $orderNumber . '_' . \Illuminate\Support\Str::random(8) . ($extension ? '.' . $extension : '');
                $attachmentPath = $attachment->storeAs('order-attachments', $safeFilename, 'local');
            }

            $order = Order::create([
                'order_number' => $orderNumber,
                'customer_id' => $customer->id,
                'task_type' => $data['task_type'],
                'description' => $data['description'],
                'attachment_path' => $attachmentPath,
                'attachment_name' => $attachmentName,
                'status' => OrderStatus::PendingNego,
                'progress' => 0,
            ]);

            $redirectUrl = $this->whatsAppService->orderRedirectUrl($order);

            return [
                'order' => $order,
                'redirect_url' => $redirectUrl,
            ];
        });
    }
}
