<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;

class ProductStockExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $products;

    public function __construct($products)
    {
        $this->products = $products;
    }

    public function collection(): Collection
    {
        // Pastikan dikembalikan sebagai instance Collection Laravel
        return collect($this->products);
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Produk',
            'Kode / SKU',
            'Kategori & Hirarki',
            'Jenis',
            'Harga Jual',
            'Stok Saat Ini',
            'Status'
        ];
    }

    public function map($prod): array
    {
        static $index = 0;
        $index++;

        $catHierarchy = '-';
        if ($prod->category) {
            if ($prod->category->parent) {
                $catHierarchy = $prod->category->parent->name . ' > ' . $prod->category->name;
            } else {
                $catHierarchy = $prod->category->name;
            }
        }

        return [
            $index,
            $prod->name,
            $prod->code ?? '-',
            $catHierarchy,
            strtoupper($prod->type ?? 'PHYSICAL'),
            'Rp ' . number_format($prod->selling_price ?? 0, 0, ',', '.'), // <-- Gunakan selling_price
            ($prod->stock ?? 0) . ' Pcs',
            ucfirst($prod->status ?? 'Aktif')
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4F46E5']
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ]
            ],
        ];
    }
}