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

    #receiptModal, #receiptModal *, #thermal-print-area, #thermal-print-area * {
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
    // VARIABEL GLOBAL STATUS BLUETOOTH
    let btDevice = null;
    let btCharacteristic = null;

    function formatRupiahIDR(val) {
        if (val === null || val === undefined || val === '') return 'Rp 0';
        let numberVal = Math.round(parseFloat(val));
        return 'Rp ' + numberVal.toLocaleString('id-ID');
    }

    // FUNGSI UTAMA CETAK AUTOMATIC CONNECT & DIRECT PRINT VIA BLUETOOTH
    async function printThermalReceipt() {
        let isAndroid = /Android/i.test(navigator.userAgent);

        if (navigator.bluetooth && isAndroid) {
            try {
                // 1. KONEKSI KE PRINTER BLUETOOTH JIKA BELUM TERHUBUNG
                if (!btDevice || !btDevice.gatt.connected || !btCharacteristic) {
                    btDevice = await navigator.bluetooth.requestDevice({
                        acceptAllDevices: true,
                        optionalServices: [
                            '000018f0-0000-1000-8000-00805f9b34fb',
                            '49535343-fe7d-4ae5-8fa9-9fafd205e455',
                            '0000ff00-0000-1000-8000-00805f9b34fb'
                        ]
                    });

                    const server = await btDevice.gatt.connect();
                    const services = await server.getPrimaryServices();

                    if (services.length > 0) {
                        const characteristics = await services[0].getCharacteristics();
                        btCharacteristic = characteristics.find(c => c.properties.write || c.properties.writeWithoutResponse);
                    }
                }

                if (!btCharacteristic) {
                    printViaNewWindow();
                    return;
                }

                // 2. CEK KETERSEDIAAN LIBRARY ESC-POS ENCODER
                if (typeof EscPosEncoder === 'undefined') {
                    console.warn('EscPosEncoder CDN belum dimuat di app.blade.php');
                    printViaNewWindow();
                    return;
                }

                // 3. ESTRAKSI TEKS DATA DARI STRUK
                let storeName = document.getElementById('res_store_name')?.innerText.replace(/\*/g, '').trim() || 'WANNCELL';
                let storeSub  = document.getElementById('res_store_sub')?.innerText || 'VOUCHER & CELLULAR';
                let storeAddr = document.getElementById('res_store_address')?.innerText || '';
                let invoice   = document.getElementById('res_invoice')?.innerText || '-';
                let dateTime  = document.getElementById('res_date_time')?.innerText || '-';
                let method    = document.getElementById('res_method')?.innerText || 'CASH';
                let total     = document.getElementById('res_total')?.innerText || 'Rp 0';
                let pay       = document.getElementById('res_pay')?.innerText || 'Rp 0';
                let change    = document.getElementById('res_change')?.innerText || 'Rp 0';

                // 4. SUSUN KOLEKSI BYTE COMMAND THERMAL
                let encoder = new EscPosEncoder();
                encoder
                    .initialize()
                    .codepage('cp437')
                    .align('center')
                    .bold(true)
                    .line(storeName)
                    .bold(false)
                    .line(storeSub)
                    .line(storeAddr)
                    .line('--------------------------------')
                    .align('left')
                    .line('No.Trx  : ' + invoice)
                    .line('Tgl/Jam : ' + dateTime)
                    .line('--------------------------------');

                // Iterasi item belanja
                let itemRows = document.querySelectorAll('#res_details .trx-receipt-table tr');
                itemRows.forEach(row => {
                    let name = row.querySelector('.lbl')?.innerText.replace(/\n/g, ' ').trim() || '';
                    let price = row.querySelector('.val')?.innerText.trim() || '';
                    if (name) {
                        encoder.align('left').line(name).align('right').line(price);
                    }
                });

                // Footer ringkasan belanja
                let resultData = encoder
                    .align('center')
                    .line('--------------------------------')
                    .align('left')
                    .line('METODE BAYAR : ' + method)
                    .bold(true)
                    .line('TOTAL        : ' + total)
                    .bold(false)
                    .line('BAYAR        : ' + pay)
                    .line('KEMBALIAN    : ' + change)
                    .line('--------------------------------')
                    .align('center')
                    .line('..:: Terima Kasih ::..')
                    .line('Simpan Struk Ini Sebagai Bukti')
                    .line('\n\n\n')
                    .encode();

                // 5. MENGIRIM CHUNK DATA KE PRINTER
                const chunkSize = 512;
                for (let i = 0; i < resultData.length; i += chunkSize) {
                    const chunk = resultData.slice(i, i + chunkSize);
                    await btCharacteristic.writeValue(chunk);
                }

            } catch (err) {
                console.error('Kendala cetak Bluetooth:', err);
                printViaNewWindow();
            }
        } else {
            printViaNewWindow();
        }
    }

    // FUNGSI KHUSUS PRINT VIA POPUP BROWSER UNTUK MENGUNCI UKURAN 58MM
    function printViaNewWindow() {
        const printArea = document.getElementById('thermal-print-area');
        if (!printArea) return;

        const printWindow = window.open('', '_blank', 'width=380,height=600');
        printWindow.document.write(`
            <html>
                <head>
                    <title>Cetak Struk</title>
                    <style>
                        body {
                            font-family: 'Courier New', Courier, monospace;
                            width: 58mm;
                            margin: 0 auto;
                            padding: 2mm;
                            font-size: 9px;
                            color: #000;
                        }
                        .text-center { text-align: center; }
                        .font-bold { font-weight: bold; }
                        .uppercase { text-transform: uppercase; }
                        .trx-receipt-table { width: 100%; border-collapse: collapse; table-layout: fixed; }
                        .trx-receipt-table td { padding: 1px 0; vertical-align: top; word-break: break-word; }
                        .trx-receipt-table .lbl { text-align: left; width: 55%; }
                        .trx-receipt-table .val { text-align: right; width: 45%; }
                        .border-b { border-bottom: 1px dashed #000; padding-bottom: 4px; margin-bottom: 4px; }
                        .border-t { border-top: 1px dashed #000; padding-top: 4px; margin-top: 4px; }
                        .no-print { display: none !important; }
                        @media print {
                            @page { size: 58mm auto; margin: 0; }
                            body { width: 58mm; margin: 0; padding: 2mm; }
                        }
                    </style>
                </head>
                <body>
                    ${printArea.innerHTML}
                </body>
            </html>
        `);

        printWindow.document.close();
        printWindow.focus();
        setTimeout(() => {
            printWindow.print();
            printWindow.close();
        }, 300);
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
                        let rawName = item.custom_name ? item.custom_name : (item.product ? item.product.name : 'Produk');
                        let subtotalVal = formatRupiahIDR(item.subtotal);
                        let numericSubtotal = formatRupiahIDR(item.subtotal);
                        
                        let provider = (item.digital_provider || '').toLowerCase();
                        let nameLower = rawName.toLowerCase();
                        
                        let formattedReceiptName = rawName;

                        if (nameLower.includes('transfer') || nameLower.includes('top-up') || nameLower.includes('nominal bebas')) {
                            let isEwallet = provider.includes('dana') || provider.includes('ovo') || provider.includes('gopay') || provider.includes('shopee') || provider.includes('linkaja') || provider.includes('propana') || nameLower.includes('dana') || nameLower.includes('ovo') || nameLower.includes('gopay');
                            let isBank = provider.includes('bank') || provider.includes('bca') || provider.includes('bri') || provider.includes('mandiri') || provider.includes('bni') || nameLower.includes('bank') || nameLower.includes('bca') || nameLower.includes('bri');

                            let accMatch = rawName.match(/\(([^)]+)\)/);
                            let accNameSuffix = accMatch ? ` (${accMatch[1]})` : '';

                            if (isEwallet && !provider.includes('bank')) {
                                let walletName = item.digital_provider ? item.digital_provider : 'E-Wallet';
                                formattedReceiptName = `Top-Up ${walletName} ${numericSubtotal}${accNameSuffix}`;
                            } else if (isBank || provider.includes('m-banking') || provider.includes('transfer')) {
                                let bankName = item.digital_provider ? item.digital_provider : 'Bank';
                                formattedReceiptName = `Transfer ${bankName} ${numericSubtotal}${accNameSuffix}`;
                            }
                        }

                        let isTransferOrTopup = formattedReceiptName.toLowerCase().includes('transfer') || formattedReceiptName.toLowerCase().includes('top-up');
                        let targetLabel = isTransferOrTopup ? 'TUJUAN/REK' : 'NO';

                        detailsContainer.innerHTML += `
                            <div class="py-1 border-b border-black/20 last:border-none">
                                <table class="trx-receipt-table">
                                    <tr>
                                        <td class="lbl">
                                            <div class="font-extrabold uppercase text-[9px] leading-tight text-black">${formattedReceiptName} x${item.qty}</div>
                                            ${item.target_phone ? `<div class="text-[8px] text-black font-bold mt-0.5">${targetLabel}: ${item.target_phone}</div>` : ''}
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