<!-- SECTION 1: RINGKASAN KEUANGAN -->
<table>
    <!-- TITLE HEADER -->
    <tr>
        <td colspan="2" style="font-size: 14pt; font-weight: bold; text-align: center; background-color: #4f46e5; color: #ffffff;">
            LAPORAN PEMBUKUAN KEUANGAN
        </td>
    </tr>
    <tr>
        <td colspan="2" style="font-size: 10pt; text-align: center; color: #64748b;">
            Periode: {{ date('F', mktime(0, 0, 0, (int)$month, 1)) }} {{ $year }}
        </td>
    </tr>
    <tr><td colspan="2"></td></tr>

    <!-- TABLE HEADER -->
    <thead>
        <tr>
            <th style="font-weight: bold; background-color: #f1f5f9; border: 1px solid #cbd5e1; padding: 8px;">Kategori Keuangan</th>
            <th style="font-weight: bold; background-color: #f1f5f9; border: 1px solid #cbd5e1; padding: 8px; text-align: right;">Jumlah (Rp)</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td style="border: 1px solid #cbd5e1; padding: 6px;">Total Omset (Pendapatan Kotor)</td>
            <td style="border: 1px solid #cbd5e1; padding: 6px; text-align: right;">Rp {{ number_format($summary['total_omset'], 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td style="border: 1px solid #cbd5e1; padding: 6px; font-weight: bold;">Laba Kotor (Gross Profit)</td>
            <td style="border: 1px solid #cbd5e1; padding: 6px; text-align: right; font-weight: bold; color: #4f46e5;">Rp {{ number_format($summary['gross_profit'], 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td style="border: 1px solid #cbd5e1; padding: 6px;">Total Beban Operasional</td>
            <td style="border: 1px solid #cbd5e1; padding: 6px; text-align: right; color: #e11d48;">Rp {{ number_format($summary['total_expenses'], 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td style="border: 1px solid #cbd5e1; padding: 6px;">Total Alokasi Bonus Karyawan</td>
            <td style="border: 1px solid #cbd5e1; padding: 6px; text-align: right; color: #e11d48;">Rp {{ number_format($summary['total_bonus_allocation'], 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td style="border: 1px solid #cbd5e1; padding: 6px; font-weight: bold; background-color: #e0e7ff;">Laba Bersih Owner (Net Profit)</td>
            <td style="border: 1px solid #cbd5e1; padding: 6px; text-align: right; font-weight: bold; background-color: #e0e7ff; color: #3730a3;">Rp {{ number_format($summary['net_profit'], 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td style="border: 1px solid #cbd5e1; padding: 6px;">Total Uang Tunai (Laci Kasir)</td>
            <td style="border: 1px solid #cbd5e1; padding: 6px; text-align: right; color: #047857;">Rp {{ number_format($summary['total_cash'] ?? 0, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td style="border: 1px solid #cbd5e1; padding: 6px;">Total Saldo QRIS / Transfer Bank</td>
            <td style="border: 1px solid #cbd5e1; padding: 6px; text-align: right; color: #6b21a8;">Rp {{ number_format($summary['total_qris'] ?? 0, 0, ',', '.') }}</td>
        </tr>
    </tbody>
</table>

<br><br>

<!-- SECTION 2: REKAPITULASI BONUS KARYAWAN -->
<table>
    <tr>
        <td colspan="8" style="font-size: 12pt; font-weight: bold; background-color: #f8fafc; border: 1px solid #cbd5e1; padding: 6px;">
            REKAPITULASI BONUS &amp; ABSENSI KARYAWAN
        </td>
    </tr>
    <thead>
        <tr>
            <th style="font-weight: bold; background-color: #4f46e5; color: #ffffff; border: 1px solid #cbd5e1; text-align: center;">Nama Karyawan</th>
            <th style="font-weight: bold; background-color: #4f46e5; color: #ffffff; border: 1px solid #cbd5e1; text-align: center;">Jabatan</th>
            <th style="font-weight: bold; background-color: #4f46e5; color: #ffffff; border: 1px solid #cbd5e1; text-align: center;">Hadir Tepat</th>
            <th style="font-weight: bold; background-color: #4f46e5; color: #ffffff; border: 1px solid #cbd5e1; text-align: center;">Terlambat</th>
            <th style="font-weight: bold; background-color: #4f46e5; color: #ffffff; border: 1px solid #cbd5e1; text-align: center;">Qty Terjual</th>
            <th style="font-weight: bold; background-color: #4f46e5; color: #ffffff; border: 1px solid #cbd5e1; text-align: right;">Omset Aksesoris</th>
            <th style="font-weight: bold; background-color: #4f46e5; color: #ffffff; border: 1px solid #cbd5e1; text-align: center;">Rate Insentif</th>
            <th style="font-weight: bold; background-color: #4f46e5; color: #ffffff; border: 1px solid #cbd5e1; text-align: right;">Total Komisi</th>
        </tr>
    </thead>
    <tbody>
        @foreach($employees as $emp)
        <tr>
            <td style="border: 1px solid #cbd5e1; font-weight: bold;">{{ $emp['name'] }}</td>
            <td style="border: 1px solid #cbd5e1; text-align: center;">{{ strtoupper($emp['role']) }}</td>
            <td style="border: 1px solid #cbd5e1; text-align: center; color: #047857;">{{ $emp['total_ontime'] }}x Tepat</td>
            <td style="border: 1px solid #cbd5e1; text-align: center; color: #b91c1c;">{{ $emp['total_late'] }}x Telat</td>
            <td style="border: 1px solid #cbd5e1; text-align: center; font-weight: bold;">{{ $emp['accessories_qty'] }} Pcs</td>
            <td style="border: 1px solid #cbd5e1; text-align: right;">Rp {{ number_format($emp['accessories_omset'], 0, ',', '.') }}</td>
            <td style="border: 1px solid #cbd5e1; text-align: center;">Rp {{ number_format($emp['bonus_rate'], 0, ',', '.') }}</td>
            <td style="border: 1px solid #cbd5e1; text-align: right; font-weight: bold; color: #047857;">+Rp {{ number_format($emp['total_bonus'], 0, ',', '.') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>