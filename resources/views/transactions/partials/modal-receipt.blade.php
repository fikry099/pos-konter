<!-- MODAL POPUP RESI (STYLE STRUK THERMAL HITAM PUTIH KASIR) -->
<div id="receiptModal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-[9999] flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-xs w-full p-5 space-y-4 border border-slate-200 max-h-[90vh] overflow-y-auto my-auto">
        
        <!-- AREA KHUSUS STRUK YANG AKAN DICETAK PRINTER -->
        <div id="thermal-print-area" class="text-black font-mono text-[10px] leading-tight bg-white">
            
            <!-- HEADER STRUK DINAMIS CABANG -->
            <div class="text-center space-y-0.5 pb-1 border-b border-black border-dashed">
                <p class="font-bold text-xs tracking-wider uppercase" id="res_store_name">
                    ** {{ strtoupper(auth()->user()->store?->name ?? 'WANNCELL') }} **
                </p>
                <p class="text-[10px] uppercase font-bold text-slate-800" id="res_store_sub">
                    {{ auth()->user()->store?->subtitle ?? 'VOUCHER & CELLULAR' }}
                </p>
                <p class="text-[9px] px-1 leading-tight font-medium" id="res_store_address">
                    {{ auth()->user()->store?->address ?? 'Jl. Raya Konter' }}
                </p>
                @if(auth()->user()->store?->phone)
                    <p class="text-[9px] font-bold" id="res_store_phone">
                        Telp/WA: {{ auth()->user()->store->phone }}
                    </p>
                @endif
                <p class="text-[10px] pt-0.5 font-bold" id="res_date_time">--/--/---- --:-- WIB</p>
                <p id="res_invoice" class="text-[10px] font-bold">TRX-000000</p>
            </div>

            <div class="py-1 border-b border-black border-dashed text-center">
                <p class="text-[9px] uppercase tracking-wider font-bold">BUKTI PEMBELIAN / TRANSAKSI</p>
            </div>

            <!-- DETAIL ITEM BELANJA -->
            <div id="res_details" class="py-1 space-y-1 border-b border-black border-dashed">
                <!-- Data Item Diisi Otomatis via JS (Format Tabel) -->
            </div>

            <!-- DETAIL PEMBAYARAN -->
            <div class="py-1 border-b border-black border-dashed">
                <table class="trx-receipt-table">
                    <tr>
                        <td class="lbl">METODE BAYAR</td>
                        <td class="val font-bold" id="res_method">CASH</td>
                    </tr>
                    <tr>
                        <td class="lbl">TOTAL TAGIHAN</td>
                        <td class="val font-bold" id="res_total">Rp 0</td>
                    </tr>
                    <tr>
                        <td class="lbl">TUNAI / BAYAR</td>
                        <td class="val" id="res_pay">Rp 0</td>
                    </tr>
                </table>
                <div class="pt-1 mt-1 border-t border-black">
                    <table class="trx-receipt-table">
                        <tr class="font-bold text-[10px]">
                            <td class="lbl">KEMBALIAN</td>
                            <td class="val" id="res_change">Rp 0</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- BUKTI QRIS -->
            <div id="qris_proof_container" class="hidden py-2 border-b border-black border-dashed text-center no-print">
                <p class="text-xs font-bold mb-1">[ BUKTI TF QRIS KASIR ]</p>
                <img id="res_qris_img" src="" alt="Proof" class="w-full max-h-40 object-contain mx-auto border border-black">
            </div>

            <!-- FOOTER PESAN STRUK -->
            <div class="text-center pt-1.5 space-y-0.5 text-[9px]">
                <p class="font-bold">..:: Terima Kasih ::..</p>
                <p class="text-[8px] font-medium">Simpan Struk Ini Sebagai Bukti</p>
            </div>

        </div>

        <!-- TOMBOL AKSI -->
        <div class="flex space-x-2 pt-2 border-t border-slate-200 no-print">
            <button type="button" onclick="closeDetail()" class="w-1/2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold py-2.5 rounded-xl border border-slate-300 transition cursor-pointer">
                Tutup
            </button>
            <button type="button" onclick="printThermalReceipt()" class="w-1/2 bg-slate-900 hover:bg-black text-white text-xs font-bold py-2.5 rounded-xl shadow-md transition flex items-center justify-center space-x-1 cursor-pointer">
                <i class="fa-solid fa-print"></i>
                <span>Cetak Struk</span>
            </button>
        </div>

    </div>
</div>

<!-- CSS KHUSUS PRINTER THERMAL KASIR 58mm -->
<style type="text/css">
#thermal-print-area .trx-receipt-table {
    width: 100%;
    border-collapse: collapse;
    table-layout: fixed;
}

#thermal-print-area .trx-receipt-table td {
    padding: 1px 0;
    vertical-align: top;
    word-break: break-word;
}

#thermal-print-area .trx-receipt-table .lbl {
    text-align: left;
    width: 60%;
}

#thermal-print-area .trx-receipt-table .val {
    text-align: right;
    width: 40%;
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

    #receiptModal, #thermal-print-area, #thermal-print-area * {
        visibility: visible !important;
    }

    #receiptModal {
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

    #thermal-print-area {
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

    #thermal-print-area .trx-receipt-table .lbl {
        width: 55% !important;
    }

    #thermal-print-area .trx-receipt-table .val {
        width: 45% !important;
    }
}
</style>

<script>
    function formatRupiahIDR(val) {
        if (val === null || val === undefined || val === '') return 'Rp 0';
        let numberVal = Math.round(parseFloat(val));
        return 'Rp ' + numberVal.toLocaleString('id-ID');
    }

    function printThermalReceipt() {
        window.print();
    }

    function closeDetail() {
        document.getElementById('receiptModal').classList.add('hidden');
    }

    function showDetail(id) {
        fetch(`/transactions/${id}?_t=${new Date().getTime()}`)
            .then(res => res.json())
            .then(data => {
                // 1. INJEKSI HEADER STORE / CABANG
                if (data.store) {
                    if (data.store.name) {
                        document.getElementById('res_store_name').innerText = `** ${data.store.name.toUpperCase()} **`;
                    }
                    if (data.store.address) {
                        document.getElementById('res_store_address').innerText = data.store.address;
                    }
                }

                // 2. INJEKSI INVOICE
                document.getElementById('res_invoice').innerText = data.invoice_code || '-';

                // 3. INJEKSI TANGGAL & JAM
                let dateStr = data.formatted_date;
                if (!dateStr || dateStr === '-') {
                    let invCode = data.invoice_code || '';
                    let cleanDigits = invCode.replace(/\D/g, ''); 
                    if (cleanDigits.length >= 12) {
                        let y  = cleanDigits.substring(0, 4); 
                        let m  = cleanDigits.substring(4, 6); 
                        let d  = cleanDigits.substring(6, 8); 
                        let hh = cleanDigits.substring(8, 10);
                        let mm = cleanDigits.substring(10, 12);
                        dateStr = `${d}/${m}/${y} ${hh}:${mm} WIB`;
                    } else {
                        dateStr = '-';
                    }
                }

                let dateEl = document.getElementById('res_date_time');
                if (dateEl) dateEl.innerText = dateStr;

                // 4. RENDER ITEM BELANJA MENGGUNAKAN TABEL FORMAL THERMAL
                let detailsContainer = document.getElementById('res_details');
                detailsContainer.innerHTML = '';

                if (data.details && data.details.length > 0) {
                    data.details.forEach(item => {
                        let prodName = item.product ? item.product.name : 'Produk';
                        let subtotalVal = formatRupiahIDR(item.subtotal);
                        
                        detailsContainer.innerHTML += `
                            <div class="py-1 border-b border-black/20 last:border-none">
                                <table class="trx-receipt-table">
                                    <tr>
                                        <td class="lbl">
                                            <div class="font-extrabold uppercase text-[9px] leading-tight text-black">${prodName} x${item.qty}</div>
                                            ${item.target_phone ? `<div class="text-[8px] text-black font-bold mt-0.5">NO: ${item.target_phone}</div>` : ''}
                                            ${item.digital_provider ? `<div class="text-[8px] text-black font-bold">SERVER: ${item.digital_provider}</div>` : ''}
                                        </td>
                                        <td class="val font-black text-[9px] text-black">
                                            ${subtotalVal}
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        `;
                    });
                }

                // 5. METODE BAYAR & BUKTI QRIS
                let methodEl = document.getElementById('res_method');
                let qrisContainer = document.getElementById('qris_proof_container');
                let qrisImg = document.getElementById('res_qris_img');

                if (data.payment_method && data.payment_method.toLowerCase() === 'qris') {
                    methodEl.innerText = 'QRIS';
                    if (data.payment_proof) {
                        qrisImg.src = data.payment_proof.startsWith('data:') ? data.payment_proof : `/storage/${data.payment_proof}`;
                        qrisContainer.classList.remove('hidden');
                    } else {
                        qrisContainer.classList.add('hidden');
                    }
                } else {
                    methodEl.innerText = 'CASH';
                    qrisContainer.classList.add('hidden');
                }

                // 6. RINGKASAN SALDO
                document.getElementById('res_total').innerText = formatRupiahIDR(data.total_price);
                document.getElementById('res_pay').innerText = formatRupiahIDR(data.pay_amount);
                document.getElementById('res_change').innerText = formatRupiahIDR(data.change_amount);

                document.getElementById('receiptModal').classList.remove('hidden');
            })
            .catch(err => {
                console.error("Gagal memuat detail transaksi:", err);
            });
    }
</script>