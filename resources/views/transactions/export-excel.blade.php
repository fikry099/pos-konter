<table>
    {{-- Baris 1-2: Judul --}}
    <tr>
        <td colspan="11">LAPORAN RIWAYAT TRANSAKSI PENJUALAN</td>
    </tr>
    <tr>
        <td colspan="11">Diunduh pada: {{ date('d/m/Y H:i:s') }} WIB</td>
    </tr>

    {{-- Baris 3: Spasi --}}
    <tr><td colspan="11"></td></tr>

    {{-- Baris 4-5: Widget Ringkasan Keuangan --}}
    <tr>
        <td colspan="3">TOTAL TRANSAKSI</td>
        <td colspan="3">TOTAL OMSET (PENJUALAN)</td>
        <td colspan="3">TOTAL MODAL (HPP)</td>
        <td colspan="2">TOTAL PROFIT (LABA)</td>
    </tr>
    <tr>
        <td colspan="3">{{ $transactions->count() }} Nota</td>
        <td colspan="3">{{ $totalOmset }}</td>
        <td colspan="3">{{ $totalCost }}</td>
        <td colspan="2">{{ $totalProfit }}</td>
    </tr>

    {{-- Baris 6-7: Spasi --}}
    <tr><td colspan="11"></td></tr>

    {{-- Baris 8: Header Tabel --}}
    <tr>
        <th>No</th>
        <th>Waktu Transaksi</th>
        <th>Kode Nota</th>
        <th>Kasir / Penjual</th>
        <th>Shift</th>
        <th>Detail Item Belanja</th>
        <th>Target HP / Provider</th>
        <th>Metode &amp; Status</th>
        <th>Total Modal</th>
        <th>Total Tagihan</th>
        <th>Profit</th>
    </tr>

    {{-- Baris 9 dst: Data Transaksi --}}
    @forelse($transactions as $index => $trx)
        @php
            $allDetailsNames = strtolower($trx->details->pluck('custom_name')->implode(' '));
            $isWithdrawal    = str_starts_with($trx->invoice_code, 'WD-') || str_contains($allDetailsNames, 'tarik tunai');
            $isAdminTunai    = str_contains($allDetailsNames, 'admin tunai') || str_contains($allDetailsNames, 'admin cash');
            $isCancelled     = $trx->status === 'cancelled';

            // Logika Penyaringan Nama Kasir/Toko Sesuai Tabel Web
            $storeName = $trx->store->name 
                ?? $trx->user->store->name 
                ?? session('selected_store_name') 
                ?? 'Cabang';

            $accessoryStaffNames = $trx->details
                ? $trx->details->filter(function ($d) {
                    if (empty($d->served_by_user_id) || !$d->servedBy) {
                        return false;
                    }

                    $itemName = strtolower($d->custom_name ?? $d->product->name ?? '');
                    $ignoredKeywords = [
                        'voucher', 'pulsa', 'kuota', 'perdana', 'paket', 
                        'top-up', 'topup', 'dana', 'gopay', 'ovo', 
                        'shopee', 'linkaja', 'transfer', 'bank',
                        'admin', 'tarik', 'tunai', 'qris'
                    ];

                    foreach ($ignoredKeywords as $keyword) {
                        if (str_contains($itemName, $keyword)) {
                            return false;
                        }
                    }

                    return true;
                })
                ->map(fn($d) => $d->servedBy->name)
                ->filter()
                ->unique()
                ->implode(', ')
                : '';

            $rawDisplay = !empty($accessoryStaffNames) ? $accessoryStaffNames : $storeName;
            $cashierDisplay = strtoupper($rawDisplay);

            // Item Belanja & Target Phone
            $itemNames = [];
            $targets   = [];

            if ($trx->details) {
                foreach ($trx->details as $det) {
                    $pName = $det->custom_name ? $det->custom_name : ($det->product->name ?? 'Produk');
                    $itemNames[] = $pName . ' (x' . $det->qty . ')';

                    if (!empty($det->target_phone) && $det->target_phone !== '-') {
                        $targets[] = $det->target_phone . ($det->digital_provider ? ' [' . $det->digital_provider . ']' : '');
                    }
                }
            }

            // Label Metode & Status
            if ($isCancelled) {
                $statusLabel = 'BATAL' . ($trx->cancel_reason ? ' (' . $trx->cancel_reason . ')' : '');
            } elseif ($isWithdrawal) {
                $statusLabel = $isAdminTunai ? 'TARIK TUNAI (TUNAI)' : 'TARIK TUNAI (QRIS)';
            } elseif (strtolower($trx->payment_method) === 'qris') {
                $statusLabel = 'QRIS';
            } else {
                $statusLabel = 'TUNAI';
            }
        @endphp
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $trx->created_at ? $trx->created_at->format('d/m/Y H:i') : '-' }}</td>
            <td>{{ $trx->invoice_code }}</td>
            <td>{{ $cashierDisplay }}</td>
            <td>{{ $trx->shift_id ? 'Shift #' . $trx->shift_id : '-' }}</td>
            <td>{{ count($itemNames) > 0 ? implode(', ', $itemNames) : '-' }}</td>
            <td>{{ count($targets) > 0 ? implode(', ', $targets) : '-' }}</td>
            <td>{{ $statusLabel }}</td>
            <td>{{ $isCancelled ? 0 : ($trx->total_cost ?? 0) }}</td>
            <td>{{ $isCancelled ? 0 : ($trx->total_price ?? 0) }}</td>
            <td>{{ $isCancelled ? 0 : ($trx->total_profit ?? 0) }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="11">Belum ada data transaksi yang sesuai.</td>
        </tr>
    @endforelse

    {{-- Baris Total --}}
    <tr>
        <td colspan="8">TOTAL KESELURUHAN:</td>
        <td>{{ $totalCost }}</td>
        <td>{{ $totalOmset }}</td>
        <td>{{ $totalProfit }}</td>
    </tr>
</table>