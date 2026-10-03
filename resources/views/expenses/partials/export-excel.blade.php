<table>
    {{-- Baris 1-2: Judul --}}
    <tr>
        <td colspan="7">LAPORAN PENGELUARAN KAS TOKO</td>
    </tr>
    <tr>
        <td colspan="7">Diunduh pada: {{ date('d/m/Y H:i') }} WIB</td>
    </tr>

    {{-- Baris 3: Spasi --}}
    <tr>
        <td colspan="7"></td>
    </tr>

    {{-- Baris 4-6: Metadata Filter --}}
    <tr>
        <td>Filter Tanggal</td>
        <td colspan="6">: {{ $filterInfo }}</td>
    </tr>
    <tr>
        <td>Filter Shift</td>
        <td colspan="6">: {{ $shiftInfo }}</td>
    </tr>
    <tr>
        <td>Total Transaksi</td>
        <td colspan="6">: {{ $expenses->count() }} Catatan</td>
    </tr>

    {{-- Baris 7: Spasi --}}
    <tr>
        <td colspan="7"></td>
    </tr>

    {{-- Baris 8: Header Tabel --}}
    <tr>
        <th>No</th>
        <th>Waktu</th>
        <th>Keterangan Pengeluaran</th>
        <th>Kategori</th>
        <th>Kasir / User</th>
        <th>Shift</th>
        <th>Nominal (Rp)</th>
    </tr>

    {{-- Baris 9 dst: Data --}}
    @forelse($expenses as $index => $exp)
        @php
            $categoryLabel = match ($exp->category ?? 'operational') {
                'operational'      => 'Operasional',
                'owner_withdrawal' => 'Diambil Owner',
                'cash_out'         => 'Tarik Tunai',
                default            => 'Restok/Modal',
            };
        @endphp
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $exp->created_at ? $exp->created_at->format('d/m/Y H:i') : '-' }}</td>
            <td>{{ $exp->description }}</td>
            <td>{{ $categoryLabel }}</td>
            <td>{{ $exp->user->name ?? '-' }}</td>
            <td>{{ $exp->shift_id ? 'Shift #' . $exp->shift_id : '-' }}</td>
            <td>{{ $exp->amount }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="7">Belum ada data pengeluaran kas yang sesuai.</td>
        </tr>
    @endforelse

    {{-- Baris Total --}}
    <tr>
        <td colspan="6">TOTAL PENGELUARAN KAS:</td>
        <td>{{ $totalNominal }}</td>
    </tr>
</table>