<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        body { 
            font-family: Arial, sans-serif; 
            font-size: 10pt; 
            color: #333333;
        }
        .title { 
            font-size: 14pt; 
            font-weight: bold; 
            color: #1e293b;
        }
        .subtitle { 
            font-size: 9pt; 
            color: #64748b; 
            margin-bottom: 15px; 
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .text-success { color: #059669; }
    </style>
</head>
<body>

    <!-- HEADER LAPORAN -->
    <table>
        <tr>
            <td colspan="11" class="title" style="font-size: 14pt; font-weight: bold; text-align: center;">LAPORAN RIWAYAT TRANSAKSI PENJUALAN</td>
        </tr>
        <tr>
            <td colspan="11" class="subtitle" style="text-align: center; color: #555555;">Dicetak pada: {{ date('d/m/Y H:i:s') }} WIB</td>
        </tr>
        <tr><td colspan="11"></td></tr>
    </table>

    <!-- WIDGET RINGKASAN KEUANGAN DENGAN GARIS & WARNA -->
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
        <tr>
            <th colspan="3" style="background-color: #e0e7ff; color: #3730a3; border: 1px solid #93c5fd; padding: 8px; font-weight: bold; text-align: center;">TOTAL TRANSAKSI</th>
            <th colspan="3" style="background-color: #e0e7ff; color: #3730a3; border: 1px solid #93c5fd; padding: 8px; font-weight: bold; text-align: center;">TOTAL OMSET (PENJUALAN)</th>
            <th colspan="3" style="background-color: #e0e7ff; color: #3730a3; border: 1px solid #93c5fd; padding: 8px; font-weight: bold; text-align: center;">TOTAL MODAL (HPP)</th>
            <th colspan="2" style="background-color: #d1fae5; color: #065f46; border: 1px solid #6ee7b7; padding: 8px; font-weight: bold; text-align: center;">TOTAL PROFIT (LABA)</th>
        </tr>
        <tr>
            <td colspan="3" style="background-color: #f8fafc; border: 1px solid #cbd5e1; padding: 8px; text-align: center; font-weight: bold;">{{ $transactions->count() }} Nota</td>
            <td colspan="3" style="background-color: #f8fafc; border: 1px solid #cbd5e1; padding: 8px; text-align: center; font-weight: bold;">Rp {{ number_format($totalOmset, 0, ',', '.') }}</td>
            <td colspan="3" style="background-color: #f8fafc; border: 1px solid #cbd5e1; padding: 8px; text-align: center; font-weight: bold;">Rp {{ number_format($totalCost, 0, ',', '.') }}</td>
            <td colspan="2" style="background-color: #f0fdf4; border: 1px solid #cbd5e1; padding: 8px; text-align: center; font-weight: bold; color: #059669;">Rp {{ number_format($totalProfit, 0, ',', '.') }}</td>
        </tr>
    </table>

    <!-- TABEL UTAMA DETAIL TRANSAKSI -->
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background-color: #4f46e5; color: #ffffff;">
                <th style="border: 1px solid #3730a3; padding: 8px; text-align: center; font-weight: bold;">No</th>
                <th style="border: 1px solid #3730a3; padding: 8px; text-align: center; font-weight: bold;">Waktu Transaksi</th>
                <th style="border: 1px solid #3730a3; padding: 8px; text-align: center; font-weight: bold;">Kode Nota</th>
                <th style="border: 1px solid #3730a3; padding: 8px; text-align: center; font-weight: bold;">Kasir / Penjual</th>
                <th style="border: 1px solid #3730a3; padding: 8px; text-align: center; font-weight: bold;">Shift</th>
                <th style="border: 1px solid #3730a3; padding: 8px; text-align: center; font-weight: bold;">Detail Item Belanja</th>
                <th style="border: 1px solid #3730a3; padding: 8px; text-align: center; font-weight: bold;">Target HP / Provider</th>
                <th style="border: 1px solid #3730a3; padding: 8px; text-align: center; font-weight: bold;">Metode Bayar</th>
                <th style="border: 1px solid #3730a3; padding: 8px; text-align: center; font-weight: bold;">Total Modal</th>
                <th style="border: 1px solid #3730a3; padding: 8px; text-align: center; font-weight: bold;">Total Tagihan</th>
                <th style="border: 1px solid #3730a3; padding: 8px; text-align: center; font-weight: bold;">Profit</th>
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

                    $staffNames = $trx->details
                        ->pluck('servedBy.name')
                        ->filter()
                        ->unique()
                        ->implode(', ');

                    $cashierDisplay = !empty($staffNames) ? $staffNames : ($trx->user->name ?? '-');
                @endphp
                <tr>
                    <td style="border: 1px solid #cbd5e1; padding: 6px; text-align: center;">{{ $index + 1 }}</td>
                    <td style="border: 1px solid #cbd5e1; padding: 6px; text-align: center;">{{ $trx->created_at ? $trx->created_at->format('d/m/Y H:i') : '-' }}</td>
                    <td style="border: 1px solid #cbd5e1; padding: 6px; text-align: center; font-weight: bold;">{{ $trx->invoice_code }}</td>
                    <td style="border: 1px solid #cbd5e1; padding: 6px;">{{ $cashierDisplay }}</td>
                    <td style="border: 1px solid #cbd5e1; padding: 6px; text-align: center;">Shift #{{ $trx->shift_id ?? '-' }}</td>
                    <td style="border: 1px solid #cbd5e1; padding: 6px;">{{ count($itemNames) > 0 ? implode(', ', $itemNames) : '-' }}</td>
                    <td style="border: 1px solid #cbd5e1; padding: 6px; text-align: center;">{{ count($targets) > 0 ? implode(', ', $targets) : '-' }}</td>
                    <td style="border: 1px solid #cbd5e1; padding: 6px; text-align: center;">{{ strtoupper($trx->payment_method ?? 'cash') }}</td>
                    <td style="border: 1px solid #cbd5e1; padding: 6px; text-align: right;">Rp {{ number_format($trx->total_cost ?? 0, 0, ',', '.') }}</td>
                    <td style="border: 1px solid #cbd5e1; padding: 6px; text-align: right; font-weight: bold;">Rp {{ number_format($trx->total_price ?? 0, 0, ',', '.') }}</td>
                    <td style="border: 1px solid #cbd5e1; padding: 6px; text-align: right; font-weight: bold; color: #059669;">+Rp {{ number_format($trx->total_profit ?? 0, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" style="border: 1px solid #cbd5e1; padding: 15px; text-align: center; color: #64748b;">Tidak ada data transaksi.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background-color: #f1f5f9; font-weight: bold;">
                <td colspan="8" style="border: 1px solid #94a3b8; padding: 8px; text-align: right;">TOTAL KESELURUHAN:</td>
                <td style="border: 1px solid #94a3b8; padding: 8px; text-align: right;">Rp {{ number_format($totalCost, 0, ',', '.') }}</td>
                <td style="border: 1px solid #94a3b8; padding: 8px; text-align: right;">Rp {{ number_format($totalOmset, 0, ',', '.') }}</td>
                <td style="border: 1px solid #94a3b8; padding: 8px; text-align: right; color: #059669;">+Rp {{ number_format($totalProfit, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

</body>
</html>