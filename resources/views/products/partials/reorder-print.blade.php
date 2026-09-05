<!-- ========================================== -->
<!-- MODAL POPUP RESI RESTOK PO (THERMAL 58mm) -->
<!-- ========================================== -->
<div id="reorderReceiptModal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-[9999] flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-xs w-full p-5 space-y-4 border border-slate-200 max-h-[90vh] overflow-y-auto my-auto">
        
        <!-- AREA KHUSUS STRUK YANG AKAN DICETAK PRINTER -->
        <div id="thermal-print-po-area" class="text-black font-mono text-[10px] leading-tight bg-white">
            
            <!-- HEADER STRUK RESTOK (DINAMIS CABANG) -->
            <div class="text-center space-y-0.5 pb-1 border-b border-black border-dashed">
                <p class="font-bold text-xs tracking-wider uppercase">
                    ** {{ strtoupper(auth()->user()->store?->name ?? 'WANNCELL') }} **
                </p>
                <p class="text-[10px] font-extrabold uppercase">REKAP ORDER SUPPLIER / PO</p>
                <p class="text-[9px] font-bold" id="print_date">--/--/---- --:-- WIB</p>
            </div>

            <div class="py-1 border-b border-black border-dashed text-center">
                <p class="text-[9px] uppercase tracking-wider font-extrabold">[ DAFTAR BARANG DIORDER ]</p>
            </div>

            <!-- LIST ITEM BARANG DIORDER -->
            <div id="print_tbody" class="py-1 space-y-1 border-b border-black border-dashed">
                <!-- Data Item Diisi Otomatis via JS (Format Tabel) -->
            </div>

            <!-- TOTAL ITEM & PCS -->
            <div class="py-1 border-b border-black border-dashed">
                <table class="po-receipt-table">
                    <tr>
                        <td class="lbl">TOTAL VARIASI ITEM</td>
                        <td class="val font-bold" id="print_total_items">0 Item</td>
                    </tr>
                    <tr class="font-bold text-[10px]">
                        <td class="lbl">TOTAL PCS DIORDER</td>
                        <td class="val" id="print_total_pcs">0 Pcs</td>
                    </tr>
                </table>
            </div>

            <!-- FOOTER CATATAN KARYAWAN -->
            <div class="text-center pt-1.5 space-y-0.5 text-[9px]">
                <p class="font-bold">** CATATAN KARYAWAN **</p>
                <p class="font-medium">Serahkan Struk Ini ke Sales / Supplier</p>
                <p class="font-medium">Cek Kembali Fisik Barang Saat Datang</p>
                <p class="pt-0.5 font-bold">..:: SIMPAN SEBAGAI BUKTI PO ::..</p>
            </div>

        </div>

        <!-- TOMBOL AKSI MODAL -->
        <div class="flex space-x-2 pt-2 border-t border-slate-200 no-print">
            <button type="button" onclick="closeReorderReceiptModal()" class="w-1/2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold py-2.5 rounded-xl border border-slate-300 transition cursor-pointer">
                Tutup
            </button>
            <button type="button" onclick="executePrintOrder()" class="w-1/2 bg-slate-900 hover:bg-black text-white text-xs font-bold py-2.5 rounded-xl shadow-md transition flex items-center justify-center space-x-1 cursor-pointer">
                <i class="fa-solid fa-print"></i>
                <span>Cetak Struk</span>
            </button>
        </div>

    </div>
</div>

<!-- CSS KHUSUS PRINT PRINTER THERMAL KASIR 58mm -->
<style type="text/css">
#thermal-print-po-area .po-receipt-table {
    width: 100%;
    border-collapse: collapse;
    table-layout: fixed;
}

#thermal-print-po-area .po-receipt-table td {
    padding: 1px 0;
    vertical-align: top;
    word-break: break-word;
}

#thermal-print-po-area .po-receipt-table .lbl {
    text-align: left;
    width: 65%;
}

#thermal-print-po-area .po-receipt-table .val {
    text-align: right;
    width: 35%;
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

    #reorderReceiptModal, #thermal-print-po-area, #thermal-print-po-area * {
        visibility: visible !important;
    }

    #reorderReceiptModal {
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

    #thermal-print-po-area {
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

    #thermal-print-po-area .po-receipt-table .lbl {
        width: 65% !important;
    }

    #thermal-print-po-area .po-receipt-table .val {
        width: 35% !important;
    }
}
</style>

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        if (typeof calculateTotals === "function") {
            calculateTotals();
        }
    });

    function formatRupiahIDR(val) {
        if (val === null || val === undefined || val === '') return 'Rp 0';
        let numberVal = Math.round(parseFloat(val));
        return 'Rp ' + numberVal.toLocaleString('id-ID');
    }

    function calculateTotals() {
        let rows = document.querySelectorAll('.item-row');
        let grandPcs = 0;
        let grandCost = 0;

        rows.forEach(row => {
            let cost = parseInt(row.getAttribute('data-cost')) || 0;
            let qtyInput = row.querySelector('.order-qty');
            let qty = parseInt(qtyInput ? qtyInput.value : 0) || 0;

            let subtotal = cost * qty;
            grandPcs += qty;
            grandCost += subtotal;

            let subEl = row.querySelector('.item-subtotal');
            if (subEl) subEl.innerText = formatRupiahIDR(subtotal);
        });

        let pcsEl = document.getElementById('grand_total_pcs');
        let costEl = document.getElementById('grand_total_cost');
        if (pcsEl) pcsEl.innerText = grandPcs.toLocaleString('id-ID') + ' Pcs';
        if (costEl) costEl.innerText = formatRupiahIDR(grandCost);
    }

    // FUNGSI UNTUK MENAMPILKAN MODAL RESI RESTOK PO
    function printOrderReceipt() {
        let rows = document.querySelectorAll('.item-row');
        if (rows.length === 0) {
            alert("Tidak ada barang yang perlu dipesan.");
            return;
        }

        let printTbody = document.getElementById('print_tbody');
        printTbody.innerHTML = '';
        
        let totalPcs = 0;
        let totalItems = 0;

        const now = new Date();
        let day = String(now.getDate()).padStart(2, '0');
        let month = String(now.getMonth() + 1).padStart(2, '0');
        let year = now.getFullYear();
        let hours = String(now.getHours()).padStart(2, '0');
        let minutes = String(now.getMinutes()).padStart(2, '0');
        
        document.getElementById('print_date').innerText = `${day}/${month}/${year} ${hours}:${minutes} WIB`;

        rows.forEach(row => {
            let name = row.getAttribute('data-name');
            let code = row.getAttribute('data-code');
            let qtyInput = row.querySelector('.order-qty');
            let qty = parseInt(qtyInput ? qtyInput.value : 0) || 0;

            if (qty > 0) {
                totalItems++;
                totalPcs += qty;

                printTbody.innerHTML += `
                    <div class="py-1 border-b border-black/20 last:border-none">
                        <table class="po-receipt-table">
                            <tr>
                                <td class="lbl">
                                    <div class="font-extrabold uppercase text-[9px] leading-tight text-black">${name}</div>
                                    ${code ? `<div class="text-[8px] text-black font-bold mt-0.5">KODE: ${code}</div>` : ''}
                                </td>
                                <td class="val font-black text-xs text-black">
                                    x${qty}
                                </td>
                            </tr>
                        </table>
                    </div>
                `;
            }
        });

        if (totalItems === 0) {
            alert("Jumlah barang diorder masih 0. Masukkan jumlah order terlebih dahulu.");
            return;
        }

        document.getElementById('print_total_items').innerText = totalItems + ' Item';
        document.getElementById('print_total_pcs').innerText = totalPcs + ' Pcs';

        // TAMPILKAN MODAL
        document.getElementById('reorderReceiptModal').classList.remove('hidden');
    }

    function closeReorderReceiptModal() {
        document.getElementById('reorderReceiptModal').classList.add('hidden');
    }

    function executePrintOrder() {
        window.print();
    }
</script>
@endpush