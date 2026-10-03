<?php

namespace App\Exports;

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

class TransactionHistoryExport implements FromView, WithStyles, WithColumnWidths, WithColumnFormatting
{
    private const HEADER_ROW = 7;
    private const FIRST_DATA_ROW = 8;

    public function __construct(
        protected Collection $transactions,
        protected float $totalOmset,
        protected float $totalCost,
        protected float $totalProfit
    ) {}

    public function view(): View
    {
        return view('transactions.export-excel', [
            'transactions' => $this->transactions,
            'totalOmset'   => $this->totalOmset,
            'totalCost'    => $this->totalCost,
            'totalProfit'  => $this->totalProfit,
        ]);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,   // No
            'B' => 18,  // Waktu
            'C' => 24,  // Kode Nota
            'D' => 20,  // Kasir / Penjual
            'E' => 12,  // Shift
            'F' => 45,  // Detail Item Belanja
            'G' => 22,  // Target HP / Provider
            'H' => 22,  // Metode & Status
            'I' => 16,  // Total Modal
            'J' => 16,  // Total Tagihan
            'K' => 16,  // Profit
        ];
    }

    public function columnFormats(): array
    {
        return [
            'I' => '#,##0',
            'J' => '#,##0',
            'K' => '#,##0',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $dataCount   = max($this->transactions->count(), 1);
        $lastDataRow = self::FIRST_DATA_ROW + $dataCount - 1;
        $totalRow    = $lastDataRow + 1;

        // 1. Merge Header Judul & Subjudul
        $sheet->mergeCells('A1:K1');
        $sheet->mergeCells('A2:K2');

        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16)->getColor()->setRGB('1E1B4B');
        $sheet->getStyle('A2')->getFont()->setSize(10)->getColor()->setRGB('64748B');
        $sheet->getRowDimension(1)->setRowHeight(25);
        $sheet->getRowDimension(2)->setRowHeight(18);

        // 2. Widget Ringkasan Keuangan (Baris 4 & 5)
        $sheet->mergeCells('A4:C4');
        $sheet->mergeCells('D4:F4');
        $sheet->mergeCells('G4:I4');
        $sheet->mergeCells('J4:K4');

        $sheet->mergeCells('A5:C5');
        $sheet->mergeCells('D5:F5');
        $sheet->mergeCells('G5:I5');
        $sheet->mergeCells('J5:K5');

        $sheet->getStyle('A4:K4')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => '3730A3']],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E0E7FF'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $sheet->getStyle('J4:K4')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => '065F46']],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'D1FAE5'],
            ],
        ]);

        $sheet->getStyle('A5:K5')->applyFromArray([
            'font' => ['bold' => true, 'size' => 11],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F8FAFC'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $sheet->getStyle('J5:K5')->getFont()->getColor()->setRGB('059669');

        // Border Widget Ringkasan
        $sheet->getStyle('A4:K5')->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['rgb' => 'CBD5E1'],
                ],
            ],
        ]);

        // 3. Header Tabel Utama (Baris 7)
        $sheet->getRowDimension(self::HEADER_ROW)->setRowHeight(28);
        $sheet->getStyle('A' . self::HEADER_ROW . ':K' . self::HEADER_ROW)->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4338CA'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // 4. Border Seluruh Tabel Utama
        $sheet->getStyle('A' . self::HEADER_ROW . ':K' . $totalRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['rgb' => 'CBD5E1'],
                ],
            ],
        ]);

        // 5. Vertical Alignment & Height Baris Data (Baris 8 dst)
        for ($r = self::FIRST_DATA_ROW; $r <= $lastDataRow; $r++) {
            $sheet->getRowDimension($r)->setRowHeight(22);
            $sheet->getStyle("A{$r}:K{$r}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        }

        // Horizontal Alignment Kolom Data
        if ($this->transactions->count() > 0) {
            $sheet->getStyle("A" . self::FIRST_DATA_ROW . ":E{$lastDataRow}")
                ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("H" . self::FIRST_DATA_ROW . ":H{$lastDataRow}")
                ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("I" . self::FIRST_DATA_ROW . ":K{$lastDataRow}")
                ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        } else {
            $sheet->getStyle("A" . self::FIRST_DATA_ROW . ":K{$lastDataRow}")
                ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        // 6. Baris Total Keseluruhan
        $sheet->mergeCells("A{$totalRow}:H{$totalRow}");
        $sheet->getRowDimension($totalRow)->setRowHeight(26);

        $sheet->getStyle("A{$totalRow}:H{$totalRow}")->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F1F5F9'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_RIGHT,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $sheet->getStyle("I{$totalRow}:K{$totalRow}")->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E0E7FF'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_RIGHT,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $sheet->getStyle("K{$totalRow}")->getFont()->getColor()->setRGB('059669');

        return [
            'F' => ['alignment' => ['wrapText' => true]],
        ];
    }
}