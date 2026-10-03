<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExpensesExport implements FromView, WithStyles, WithColumnWidths, WithColumnFormatting
{
    private const HEADER_ROW = 8;
    private const FIRST_DATA_ROW = 9;

    public function __construct(
        private Collection $expenses,
        private ?string $date = null,
        private ?string $shiftId = null,
    ) {}

    public function view(): View
    {
        return view('expenses.partials.export-excel', [
            'expenses'     => $this->expenses,
            'filterInfo'   => $this->date
                ? Carbon::parse($this->date)->translatedFormat('d F Y')
                : 'Semua Tanggal',
            'shiftInfo'    => $this->shiftId ? 'Shift #' . $this->shiftId : 'Semua Shift',
            'totalNominal' => $this->expenses->sum('amount'),
        ]);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8,   // No
            'B' => 20,  // Waktu
            'C' => 45,  // Keterangan Pengeluaran
            'D' => 20,  // Kategori
            'E' => 25,  // Kasir / User
            'F' => 14,  // Shift
            'G' => 22,  // Nominal (Rp)
        ];
    }

    public function columnFormats(): array
    {
        return [
            'G' => '#,##0',
        ];
    }

    /**
     * Menyesuaikan return type ": ?array" agar kompatibel dengan PHP 8.4 Strict Requirement Interface WithStyles
     */
    public function styles(Worksheet $sheet): ?array
    {
        $dataCount   = max($this->expenses->count(), 1); // Fallback 1 baris jika kosong
        $lastDataRow = self::FIRST_DATA_ROW + $dataCount - 1;
        $totalRow    = $lastDataRow + 1;

        // 1. Merge Title & Merge Baris Total
        $sheet->mergeCells('A1:G1');
        $sheet->mergeCells('A2:G2');
        $sheet->mergeCells("A{$totalRow}:F{$totalRow}");

        // 2. Styling Judul & Subjudul KOP
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16)->getColor()->setRGB('1E1B4B');
        $sheet->getStyle('A2')->getFont()->setSize(10)->getColor()->setRGB('64748B');
        $sheet->getRowDimension(1)->setRowHeight(25);
        $sheet->getRowDimension(2)->setRowHeight(18);

        // 3. Styling Metadata Filter
        $sheet->getStyle('A4:A6')->getFont()->setBold(true)->getColor()->setRGB('475569');

        // 4. Styling Header Tabel (Baris 8)
        $sheet->getRowDimension(self::HEADER_ROW)->setRowHeight(28);
        $sheet->getStyle('A' . self::HEADER_ROW . ':G' . self::HEADER_ROW)->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4338CA'], // Indigo Modern
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // 5. Border Seluruh Tabel (Header s/d Baris Total)
        $sheet->getStyle('A' . self::HEADER_ROW . ':G' . $totalRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['rgb' => 'CBD5E1'],
                ],
            ],
        ]);

        // 6. Vertical Alignment & Height Baris Data
        for ($r = self::FIRST_DATA_ROW; $r <= $lastDataRow; $r++) {
            $sheet->getRowDimension($r)->setRowHeight(22);
            $sheet->getStyle("A{$r}:G{$r}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        }

        // Horizontal Alignment Kolom Data
        if ($this->expenses->count() > 0) {
            $sheet->getStyle("A" . self::FIRST_DATA_ROW . ":B{$lastDataRow}")
                ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("D" . self::FIRST_DATA_ROW . ":D{$lastDataRow}")
                ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("F" . self::FIRST_DATA_ROW . ":F{$lastDataRow}")
                ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("G" . self::FIRST_DATA_ROW . ":G{$lastDataRow}")
                ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        } else {
            $sheet->getStyle("A" . self::FIRST_DATA_ROW . ":G{$lastDataRow}")
                ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        // 7. Styling Baris Total
        $sheet->getRowDimension($totalRow)->setRowHeight(26);
        $sheet->getStyle("A{$totalRow}:F{$totalRow}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 11],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F1F5F9'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_RIGHT,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $sheet->getStyle("G{$totalRow}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '3730A3']],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E0E7FF'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_RIGHT,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
        ]);

        return [];
    }
}