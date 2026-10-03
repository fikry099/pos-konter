<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class BookkeepingExport implements FromView, ShouldAutoSize
{
    protected $financialSummary;
    protected $employeeReport;
    protected $month;
    protected $year;

    public function __construct($financialSummary, $employeeReport, $month, $year)
    {
        $this->financialSummary = $financialSummary;
        $this->employeeReport   = $employeeReport;
        $this->month            = $month;
        $this->year             = $year;
    }

    public function view(): View
    {
        return view('owner.bookkeeping.export-excel', [
            'summary'   => $this->financialSummary,
            'employees' => $this->employeeReport,
            'month'     => $this->month,
            'year'      => $this->year,
        ]);
    }
}