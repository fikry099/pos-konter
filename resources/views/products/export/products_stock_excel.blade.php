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
    </style>
</head>
<body>

    <!-- HEADER LAPORAN -->
    <table>
        <tr>
            <td colspan="7" class="title" style="font-size: 14pt; font-weight: bold; text-align: center;">LAPORAN STOK & INVENTARIS PRODUK</td>
        </tr>
        <tr>
            <td colspan="7" class="subtitle" style="text-align: center; color: #555555;">Dicetak pada: {{ date('d/m/Y H:i:s') }} WIB</td>
        </tr>
        <tr><td colspan="7"></td></tr>
    </table>

    <!-- TABEL UTAMA STOK PRODUK -->
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background-color: #4f46e5; color: #ffffff;">
                <th style="border: 1px solid #3730a3; padding: 8px; text-align: center; font-weight: bold;">No</th>
                <th style="border: 1px solid #3730a3; padding: 8px; text-align: left; font-weight: bold;">Kode / Nama Produk</th>
                <th style="border: 1px solid #3730a3; padding: 8px; text-align: left; font-weight: bold;">Kategori & Hirarki</th>
                <th style="border: 1px solid #3730a3; padding: 8px; text-align: center; font-weight: bold;">Jenis</th>
                <th style="border: 1px solid #3730a3; padding: 8px; text-align: right; font-weight: bold;">Harga Jual</th>
                <th style="border: 1px solid #3730a3; padding: 8px; text-align: center; font-weight: bold;">Stok Saat Ini</th>
                <th style="border: 1px solid #3730a3; padding: 8px; text-align: center; font-weight: bold;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $index => $prod)
                @php
                    // Menyusun hirarki kategori (Parent > Sub Kategori)
                    $catHierarchy = '-';
                    if ($prod->category) {
                        if ($prod->category->parent) {
                            $catHierarchy = $prod->category->parent->name . ' > ' . $prod->category->name;
                        } else {
                            $catHierarchy = $prod->category->name;
                        }
                    }
                @endphp
                <tr>
                    <td style="border: 1px solid #cbd5e1; padding: 6px; text-align: center;">{{ $index + 1 }}</td>
                    <td style="border: 1px solid #cbd5e1; padding: 6px;">
                        <div style="font-weight: bold;">{{ $prod->name }}</div>
                        <div style="font-size: 8.5pt; color: #64748b;">{{ $prod->sku ?? '-' }}</div>
                    </td>
                    <td style="border: 1px solid #cbd5e1; padding: 6px;">{{ $catHierarchy }}</td>
                    <td style="border: 1px solid #cbd5e1; padding: 6px; text-align: center;">{{ strtoupper($prod->type ?? 'PHYSICAL') }}</td>
                    <td style="border: 1px solid #cbd5e1; padding: 6px; text-align: right;">Rp {{ number_format($prod->price ?? 0, 0, ',', '.') }}</td>
                    <td style="border: 1px solid #cbd5e1; padding: 6px; text-align: center; font-weight: bold;">{{ $prod->stock ?? 0 }} Pcs</td>
                    <td style="border: 1px solid #cbd5e1; padding: 6px; text-align: center; color: {{ ($prod->status ?? 'active') === 'active' ? '#059669' : '#dc2626' }};">
                        {{ ucfirst($prod->status ?? 'Aktif') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="border: 1px solid #cbd5e1; padding: 15px; text-align: center; color: #64748b;">Tidak ada data produk.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background-color: #f1f5f9; font-weight: bold;">
                <td colspan="5" style="border: 1px solid #94a3b8; padding: 8px; text-align: right;">TOTAL PRODUK TERDAFTAR:</td>
                <td colspan="2" style="border: 1px solid #94a3b8; padding: 8px; text-align: center;">{{ $products->count() }} Jenis Produk</td>
            </tr>
        </tfoot>
    </table>

</body>
</html>