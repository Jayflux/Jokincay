<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Order;

class WhatsAppService
{
    /**
     * Generate WhatsApp deep link for customer redirect after creating order.
     */
    public function orderRedirectUrl(Order $order): string
    {
        $adminNumber = config('services.whatsapp.admin_number', '6281234567890');
        $adminNumber = WhatsAppNormalizer::normalize($adminNumber);

        $attachmentInfo = $order->attachment_name 
            ? "Lampiran: Ada berkas terlampir ({$order->attachment_name})\n\n" 
            : "";

        $message = "Halo Admin, saya ingin memesan jasa joki tugas.\n\n"
            . "No. Order: #{$order->order_number}\n"
            . "Nama: {$order->customer->name}\n"
            . "Jenis Tugas: {$order->task_type}\n"
            . "Detail Tugas: {$order->description}\n\n"
            . $attachmentInfo
            . "Mohon info harga dan estimasi pengerjaannya. Terima kasih!";

        return 'https://wa.me/' . $adminNumber . '?text=' . urlencode($message);
    }

    /**
     * Generate WhatsApp deep link for admin to chat directly with customer.
     */
    public function customerChatUrl(Customer $customer, ?string $customMessage = null): string
    {
        $customerNumber = WhatsAppNormalizer::normalize($customer->whatsapp);

        $message = $customMessage ?? "Halo {$customer->name}, kami dari Joki Tugas mengenai pesanan Anda.";

        return 'https://wa.me/' . $customerNumber . '?text=' . urlencode($message);
    }
}
