<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TransactionHistoryExport implements FromView, ShouldAutoSize, WithStyles
{
    protected $transactions;
    protected $totalOmset;
    protected $totalCost;
    protected $totalProfit;

    public function __construct($transactions, $totalOmset, $totalCost, $totalProfit)
    {
        $this->transactions = $transactions;
        $this->totalOmset   = $totalOmset;
        $this->totalCost    = $totalCost;
        $this->totalProfit  = $totalProfit;
    }

    public function view(): View
    {
        return view('transactions.export-excel', [
            'transactions' => $this->transactions,
            'totalOmset'   => $this->totalOmset,
            'totalCost'    => $this->totalCost,
            'totalProfit'  => $this->totalProfit,
        ]);
    }

    /**
     * Tambahkan return type hint `: array` pada method styles
     */
    public function styles(Worksheet $sheet): array
    {
        return [
            'F' => ['alignment' => ['wrapText' => true]],
        ];
    }
}