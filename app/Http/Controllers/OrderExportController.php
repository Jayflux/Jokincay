<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrderExportController extends Controller
{
    public function export(Request $request)
    {
        $validated = $request->validate([
            'period' => 'required|in:daily,weekly,monthly,custom',
            'format' => 'required|in:csv,print',
            'date' => 'nullable|date',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        $period = $validated['period'];
        $format = $validated['format'];

        $query = Order::with(['customer', 'payments'])->orderBy('created_at', 'desc');

        $periodLabel = '';
        $startDate = null;
        $endDate = null;

        switch ($period) {
            case 'daily':
                $targetDate = !empty($validated['date']) ? Carbon::parse($validated['date']) : Carbon::today();
                $startDate = $targetDate->copy()->startOfDay();
                $endDate = $targetDate->copy()->endOfDay();
                $periodLabel = 'Harian (' . $startDate->translatedFormat('d F Y') . ')';
                break;

            case 'weekly':
                $startDate = Carbon::now()->startOfWeek();
                $endDate = Carbon::now()->endOfWeek();
                $periodLabel = 'Mingguan (' . $startDate->translatedFormat('d M') . ' - ' . $endDate->translatedFormat('d M Y') . ')';
                break;

            case 'monthly':
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
                $periodLabel = 'Bulanan (' . $startDate->translatedFormat('F Y') . ')';
                break;

            case 'custom':
                $start = !empty($validated['start_date']) ? Carbon::parse($validated['start_date']) : Carbon::now()->subDays(7);
                $end = !empty($validated['end_date']) ? Carbon::parse($validated['end_date']) : Carbon::now();
                $startDate = $start->copy()->startOfDay();
                $endDate = $end->copy()->endOfDay();
                $periodLabel = 'Periode ' . $startDate->translatedFormat('d M Y') . ' s/d ' . $endDate->translatedFormat('d M Y');
                break;
        }

        $query->whereBetween('created_at', [$startDate, $endDate]);
        $orders = $query->get();

        if ($format === 'csv') {
            return $this->exportCsv($orders, $period, $startDate, $endDate);
        }

        return view('pages.reports.orders', [
            'orders' => $orders,
            'period' => $period,
            'periodLabel' => $periodLabel,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'totalOrders' => $orders->count(),
            'totalRevenue' => $orders->sum(fn ($order) => $order->price ?? 0),
            'verifiedRevenue' => $orders->sum(function ($order) {
                return $order->payments->where('status.value', 'verified')->sum('amount');
            }),
            'completedCount' => $orders->filter(fn ($o) => $o->status?->value === 'completed')->count(),
        ]);
    }

    protected function exportCsv($orders, string $period, Carbon $startDate, Carbon $endDate): StreamedResponse
    {
        $filename = 'Laporan-Pesanan-' . $period . '-' . $startDate->format('Ymd') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($orders) {
            $handle = fopen('php://output', 'w');

            // Prepend UTF-8 BOM so Microsoft Excel renders Indonesian characters correctly
            fwrite($handle, "\xEF\xBB\xBF");

            // CSV Header Row
            fputcsv($handle, [
                'No',
                'No. Order',
                'Tanggal Pesan',
                'Nama Customer',
                'Nomor WhatsApp',
                'Jenis Tugas',
                'Status Pesanan',
                'Progress (%)',
                'Harga (IDR)',
                'Status Pembayaran',
                'Deadline',
                'Catatan',
            ]);

            $no = 1;
            foreach ($orders as $order) {
                $latestPayment = $order->payments->sortByDesc('created_at')->first();
                $paymentStatus = $latestPayment?->status?->getLabel() ?? 'Belum Ada';

                fputcsv($handle, [
                    $no++,
                    $order->order_number,
                    $order->created_at ? $order->created_at->format('Y-m-d H:i') : '-',
                    $order->customer?->name ?? 'N/A',
                    $order->customer?->whatsapp ?? 'N/A',
                    $order->task_type,
                    $order->status?->getLabel() ?? $order->status?->value ?? '-',
                    $order->progress . '%',
                    $order->price ?? 0,
                    $paymentStatus,
                    $order->deadline ? $order->deadline->format('Y-m-d H:i') : '-',
                    $order->notes ?? '-',
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }
}
