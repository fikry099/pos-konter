<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; font-size: 11pt; }
        .title { font-size: 14pt; font-weight: bold; text-align: center; }
        .subtitle { font-size: 10pt; color: #555555; text-align: center; margin-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background-color: #4f46e5; color: #ffffff; font-weight: bold; text-align: center; border: 1px solid #cccccc; padding: 6px; }
        td { border: 1px solid #cccccc; padding: 6px; vertical-align: top; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .bg-summary { background-color: #f3f4f6; }
    </style>
</head>
<body>

    <div class="title">LAPORAN RIWAYAT TRANSAKSI PENJUALAN</div>
    <div class="subtitle">Tanggal Cetak: {{ date('d/m/Y H:i:s') }} WIB</div>

    <!-- WIDGET RINGKASAN KEUANGAN -->
    <table>
        <tr class="bg-summary">
            <td colspan="3" class="font-bold text-center">TOTAL TRANSAKSI</td>
            <td colspan="3" class="font-bold text-center">TOTAL OMSET (PENJUALAN)</td>
            <td colspan="3" class="font-bold text-center">TOTAL MODAL (HPP)</td>
            <td colspan="2" class="font-bold text-center">TOTAL PROFIT (LABA)</td>
        </tr>
        <tr>
            <td colspan="3" class="text-center font-bold">{{ $transactions->count() }} Nota</td>
            <td colspan="3" class="text-center font-bold">Rp {{ number_format($totalOmset, 0, ',', '.') }}</td>
            <td colspan="3" class="text-center font-bold">Rp {{ number_format($totalCost, 0, ',', '.') }}</td>
            <td colspan="2" class="text-center font-bold" style="color: #059669;">Rp {{ number_format($totalProfit, 0, ',', '.') }}</td>
        </tr>
    </table>

    <br>

    <!-- TABEL DETAIL TRANSAKSI -->
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Waktu Transaksi</th>
                <th>Kode Nota</th>
                <th>Kasir / Penjual</th>
                <th>Shift</th>
                <th>Detail Item Belanja</th>
                <th>Target HP / Provider</th>
                <th>Metode Bayar</th>
                <th>Total Modal</th>
                <th>Total Tagihan</th>
                <th>Profit</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $index => $trx)
                @php
                    $itemNames = [];
                    $targets = [];
                    if($trx->details) {
                        foreach($trx->details as $det) {
                            $pName = $det->product ? $det->product->name : 'Produk Custom';
                            $itemNames[] = $pName . ' (x' . $det->qty . ')';
                            if(!empty($det->target_phone)) {
                                $targets[] = $det->target_phone . ($det->digital_provider ? ' ['.$det->digital_provider.']' : '');
                            }
                        }
                    }

                    // Ambil nama karyawan pelayan item aksesoris
                    $staffNames = $trx->details
                        ->pluck('servedBy.name')
                        ->filter()
                        ->unique()
                        ->implode(', ');

                    $cashierDisplay = !empty($staffNames) ? $staffNames : ($trx->user->name ?? '-');
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ $trx->created_at ? $trx->created_at->format('d/m/Y H:i') : '-' }}</td>
                    <td class="text-center font-bold">{{ $trx->invoice_code }}</td>
                    <td>{{ $cashierDisplay }}</td>
                    <td class="text-center">Shift #{{ $trx->shift_id ?? '-' }}</td>
                    <td>{{ count($itemNames) > 0 ? implode(', ', $itemNames) : '-' }}</td>
                    <td class="text-center">{{ count($targets) > 0 ? implode(', ', $targets) : '-' }}</td>
                    <td class="text-center">{{ strtoupper($trx->payment_method ?? 'cash') }}</td>
                    <td class="text-right">Rp {{ number_format($trx->total_cost ?? 0, 0, ',', '.') }}</td>
                    <td class="text-right font-bold">Rp {{ number_format($trx->total_price ?? 0, 0, ',', '.') }}</td>
                    <td class="text-right font-bold" style="color: #059669;">+Rp {{ number_format($trx->total_profit ?? 0, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="text-center">Tidak ada data transaksi.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="bg-summary font-bold">
                <td colspan="8" class="text-right">TOTAL KESELURUHAN:</td>
                <td class="text-right">Rp {{ number_format($totalCost, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($totalOmset, 0, ',', '.') }}</td>
                <td class="text-right" style="color: #059669;">+Rp {{ number_format($totalProfit, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

</body>
</html>