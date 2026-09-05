<!-- MODAL STRUK RESI SHIFT (STYLE STRUK THERMAL HITAM PUTIH KASIR) -->
<div id="shiftReceiptModal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-[9999] flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-xs w-full p-5 space-y-4 border border-slate-200 max-h-[90vh] overflow-y-auto my-auto">
        
        <!-- AREA KHUSUS STRUK YANG AKAN DICETAK PRINTER -->
        <div id="thermal-print-shift-area" class="text-black font-mono text-[10px] leading-tight bg-white">
            
            <!-- HEADER STRUK DINAMIS CABANG -->
            <div class="text-center space-y-0.5 pb-1 border-b border-black border-dashed">
                <p class="font-bold text-xs tracking-wider uppercase">
                    ** {{ strtoupper(auth()->user()->store?->name ?? 'WANNCELL') }} **
                </p>
                <p class="text-[10px] uppercase font-bold">
                    {{ auth()->user()->store?->subtitle ?? 'VOUCHER & CELLULAR' }}
                </p>
                <p class="text-[9px] px-1 leading-tight font-medium">
                    {{ auth()->user()->store?->address ?? 'Jl. Raya Konter' }}
                </p>
                <p class="text-[10px] pt-0.5 font-extrabold" id="receipt_shift_id">SHIFT # -</p>
            </div>

            <div class="py-1 border-b border-black border-dashed text-center">
                <p class="text-[9px] uppercase tracking-wider font-extrabold">REKAPITULASI SHIFT KASIR</p>
            </div>

            <!-- DETAIL INFORMASI KASIR & WAKTU -->
            <div class="py-1 border-b border-black border-dashed">
                <table class="receipt-table">
                    <tr>
                        <td class="lbl">KASIR</td>
                        <td class="val uppercase font-bold" id="receipt_staff">-</td>
                    </tr>
                    <tr>
                        <td class="lbl">MULAI</td>
                        <td class="val font-bold" id="receipt_start">-</td>
                    </tr>
                    <tr>
                        <td class="lbl">SELESAI</td>
                        <td class="val font-bold" id="receipt_end">-</td>
                    </tr>
                </table>
            </div>

            <!-- RINCIAN ARUS KAS -->
            <div class="py-1 border-b border-black border-dashed">
                <table class="receipt-table">
                    <tr>
                        <td class="lbl">MODAL AWAL LACI</td>
                        <td class="val font-bold" id="receipt_initial">Rp 0</td>
                    </tr>
                    <tr>
                        <td class="lbl">(+) PENJUALAN TUNAI</td>
                        <td class="val font-bold" id="receipt_cash_sales">Rp 0</td>
                    </tr>
                    <tr>
                        <td class="lbl">(+) PENJUALAN QRIS</td>
                        <td class="val font-bold" id="receipt_qris_sales">Rp 0</td>
                    </tr>
                    <tr>
                        <td class="lbl">(-) PENGELUARAN KAS</td>
                        <td class="val font-bold" id="receipt_expenses">Rp 0</td>
                    </tr>
                </table>
            </div>

            <!-- RINGKASAN SALDO LACI & SELISIH -->
            <div class="py-1 border-b border-black border-dashed">
                <table class="receipt-table">
                    <tr>
                        <td class="lbl">EKSPEKTASI KAS LACI</td>
                        <td class="val font-bold" id="receipt_expected">Rp 0</td>
                    </tr>
                    <tr>
                        <td class="lbl">FISIK LACI ASLI</td>
                        <td class="val font-bold" id="receipt_actual">Rp 0</td>
                    </tr>
                </table>
                <div class="pt-1 mt-1 border-t border-black">
                    <table class="receipt-table">
                        <tr class="font-bold text-[10px]">
                            <td class="lbl">SELISIH PEMBUKUAN</td>
                            <td class="val" id="receipt_diff">Rp 0</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- FOOTER PESAN STRUK -->
            <div class="text-center pt-1.5 space-y-0.5 text-[9px]">
                <p class="font-bold">Laporan Rekapitulasi Pembukuan</p>
                <p class="font-extrabold">..:: POS COUNTER SYSTEM ::..</p>
                <p class="text-[8px]">Dicetak Otomatis Oleh Sistem Kasir</p>
            </div>

        </div>

        <!-- TOMBOL AKSI -->
        <div class="flex space-x-2 pt-2 border-t border-slate-200 no-print">
            <button type="button" onclick="closeShiftReceiptModal()" class="w-1/2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold py-2.5 rounded-xl border border-slate-300 transition cursor-pointer">
                Tutup
            </button>
            <button type="button" onclick="printShiftReceipt()" class="w-1/2 bg-slate-900 hover:bg-black text-white text-xs font-bold py-2.5 rounded-xl shadow-md transition flex items-center justify-center space-x-1 cursor-pointer">
                <i class="fa-solid fa-print"></i>
                <span>Cetak Resi</span>
            </button>
        </div>

    </div>
</div>

<!-- CSS KHUSUS PRINTER THERMAL KASIR (POS 58mm) -->
<style type="text/css">
#thermal-print-shift-area .receipt-table {
    width: 100%;
    border-collapse: collapse;
    table-layout: fixed;
}

#thermal-print-shift-area .receipt-table td {
    padding: 1px 0;
    vertical-align: top;
    word-break: break-word;
}

#thermal-print-shift-area .receipt-table .lbl {
    text-align: left;
    width: 55%;
}

#thermal-print-shift-area .receipt-table .val {
    text-align: right;
    width: 45%;
}

@media print {
    @page {
        size: 58mm auto;
        margin: 0mm !important;
    }

    body * {
        visibility: hidden !important;
    }

    .no-print, .no-print * {
        display: none !important;
    }

    #shiftReceiptModal, #thermal-print-shift-area, #thermal-print-shift-area * {
        visibility: visible !important;
    }

    #shiftReceiptModal {
        display: block !important;
        position: absolute !important;
        left: 0 !important;
        top: 0 !important;
        width: 58mm !important;
        height: auto !important;
        background: #fff !important;
        padding: 0 !important;
        margin: 0 !important;
        border: none !important;
        box-shadow: none !important;
    }

    #thermal-print-shift-area {
        width: 100% !important;
        max-width: 54mm !important;
        padding: 2mm !important;
        margin: 0 auto !important;
        color: #000 !important;
        font-family: 'Courier New', Courier, monospace !important;
        font-size: 9px !important;
        line-height: 1.2 !important;
        box-sizing: border-box !important;
    }

    #thermal-print-shift-area .receipt-table .lbl {
        width: 50% !important;
    }

    #thermal-print-shift-area .receipt-table .val {
        width: 50% !important;
    }
}
</style>

<!-- SCRIPT UNTUK MEMBUKA DAN MENCETAK RESI SHIFT -->
<script>
    function formatRupiahIDR(val) {
        if (val === null || val === undefined || val === '') return 'Rp 0';
        let numberVal = Math.round(parseFloat(val));
        return 'Rp ' + numberVal.toLocaleString('id-ID');
    }

    function openShiftReceipt(data) {
        document.getElementById('receipt_shift_id').innerText = 'SHIFT #' + data.id;
        document.getElementById('receipt_staff').innerText = data.staff;
        document.getElementById('receipt_start').innerText = data.start;
        document.getElementById('receipt_end').innerText = data.end;

        document.getElementById('receipt_initial').innerText = formatRupiahIDR(data.initial);
        document.getElementById('receipt_cash_sales').innerText = formatRupiahIDR(data.cash_sales);
        document.getElementById('receipt_qris_sales').innerText = formatRupiahIDR(data.qris_sales);
        document.getElementById('receipt_expenses').innerText = formatRupiahIDR(data.expenses);

        document.getElementById('receipt_expected').innerText = formatRupiahIDR(data.expected_cash);
        document.getElementById('receipt_actual').innerText = data.status === 'open' ? 'MASIH AKTIF' : formatRupiahIDR(data.actual_cash);

        let diffElement = document.getElementById('receipt_diff');
        let diffVal = Math.round(parseFloat(data.difference));

        if (data.status === 'open') {
            diffElement.innerText = '-';
        } else if (diffVal === 0) {
            diffElement.innerText = 'Rp 0 (PAS)';
        } else if (diffVal > 0) {
            diffElement.innerText = '+' + formatRupiahIDR(diffVal);
        } else {
            diffElement.innerText = '-' + formatRupiahIDR(Math.abs(diffVal));
        }

        document.getElementById('shiftReceiptModal').classList.remove('hidden');
    }

    function closeShiftReceiptModal() {
        document.getElementById('shiftReceiptModal').classList.add('hidden');
    }

    function printShiftReceipt() {
        window.print();
    }
</script>