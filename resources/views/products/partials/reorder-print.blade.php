<div id="reorderReceiptModal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-[9999] flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-xs w-full p-5 space-y-4 border border-slate-200 max-h-[90vh] overflow-y-auto my-auto">
        
        <div id="thermal-print-po-area" class="text-black font-mono text-[10px] leading-tight bg-white">
            
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

            <div id="print_tbody" class="py-1 space-y-1 border-b border-black border-dashed">
            </div>

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

            <div class="text-center pt-1.5 space-y-0.5 text-[9px]">
                <p class="font-bold">** CATATAN KARYAWAN **</p>
                <p class="font-medium">Serahkan Struk Ini ke Sales / Supplier</p>
                <p class="font-medium">Cek Kembali Fisik Barang Saat Datang</p>
                <p class="pt-0.5 font-bold">..:: SIMPAN SEBAGAI BUKTI PO ::..</p>
            </div>

        </div>

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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    let btDevice = null;
    let btCharacteristic = null;

    let selectedMainCategory = 'all';
    let selectedSubCategory = 'all';

    document.addEventListener("DOMContentLoaded", function() {
        applyReorderFilters();
    });

    function formatRupiahIDR(val) {
        if (val === null || val === undefined || val === '') return 'Rp 0';
        let numberVal = Math.round(parseFloat(val));
        return 'Rp ' + numberVal.toLocaleString('id-ID');
    }

    // HITUNG REKAP STATISTIK BERDASARKAN HASIL FILTER
    function calculateTotals() {
        let rows = document.querySelectorAll('.item-row');
        let grandPcs = 0;
        let grandCost = 0;
        let grandItems = 0;

        rows.forEach(row => {
            if (row.getAttribute('data-filtered-visible') !== 'false') {
                let cost = parseInt(row.getAttribute('data-cost')) || 0;
                let qtyInput = row.querySelector('.order-qty');
                let qty = parseInt(qtyInput ? qtyInput.value : 0) || 0;

                let subtotal = cost * qty;
                grandPcs += qty;
                grandCost += subtotal;
                grandItems++;

                let subEl = row.querySelector('.item-subtotal');
                if (subEl) subEl.innerText = formatRupiahIDR(subtotal);
            }
        });

        let itemsEl = document.getElementById('grand_total_items');
        let pcsEl = document.getElementById('grand_total_pcs');
        let costEl = document.getElementById('grand_total_cost');

        if (itemsEl) itemsEl.innerText = grandItems.toLocaleString('id-ID') + ' Item';
        if (pcsEl) pcsEl.innerText = grandPcs.toLocaleString('id-ID') + ' Pcs';
        if (costEl) costEl.innerText = formatRupiahIDR(grandCost);
    }

    // MEMBERSIHKAN SIMBOL/KARAKTER UNTUK PENCOCOKAN
    function cleanFilterKey(str) {
        if (!str) return '';
        return str.toString()
                  .toLowerCase()
                  .replace(/&/g, 'and')
                  .replace(/[^a-z0-9]/g, '');
    }

    function filterMainCategory(catSlug, element) {
        selectedMainCategory = (catSlug || '').toLowerCase().trim();
        selectedSubCategory = 'all';

        document.querySelectorAll('.main-cat-btn').forEach(btn => {
            btn.classList.remove('bg-indigo-600', 'text-white', 'shadow-sm', 'shadow-indigo-200', 'font-black');
            btn.classList.add('bg-slate-100', 'hover:bg-slate-200', 'text-slate-600', 'font-bold');
        });

        element.classList.remove('bg-slate-100', 'hover:bg-slate-200', 'text-slate-600', 'font-bold');
        element.classList.add('bg-indigo-600', 'text-white', 'shadow-sm', 'shadow-indigo-200', 'font-black');

        let subCatsData = [];
        try {
            let subAttr = element.getAttribute('data-subcategories');
            if (subAttr) subCatsData = JSON.parse(subAttr);
        } catch (e) {
            subCatsData = [];
        }

        let subContainer = document.getElementById('sub_category_pills_container');
        if (subContainer) {
            let html = `
                <button type="button" onclick="filterSubCategory('all', this)" class="sub-cat-pill px-3 py-1.5 rounded-lg text-xs font-black transition whitespace-nowrap bg-indigo-100 text-indigo-700 active:scale-95 cursor-pointer">
                    Semua
                </button>
            `;

            if (subCatsData && subCatsData.length > 0) {
                subCatsData.forEach(sub => {
                    let subVal = sub.name || sub.slug || '';
                    let safeVal = subVal.replace(/'/g, "\\'");
                    html += `
                        <button type="button" onclick="filterSubCategory('${safeVal}', this)" class="sub-cat-pill px-3 py-1.5 rounded-lg text-xs font-bold transition whitespace-nowrap bg-slate-100 hover:bg-slate-200 text-slate-600 active:scale-95 cursor-pointer">
                            ${subVal}
                        </button>
                    `;
                });
            }

            subContainer.innerHTML = html;
        }

        applyReorderFilters();
    }

    function filterSubCategory(subSlug, element) {
        selectedSubCategory = (subSlug || '').toLowerCase().trim();

        document.querySelectorAll('.sub-cat-pill').forEach(btn => {
            btn.classList.remove('bg-indigo-100', 'text-indigo-700', 'font-black');
            btn.classList.add('bg-slate-100', 'hover:bg-slate-200', 'text-slate-600', 'font-bold');
        });

        element.classList.remove('bg-slate-100', 'hover:bg-slate-200', 'text-slate-600', 'font-bold');
        element.classList.add('bg-indigo-100', 'text-indigo-700', 'font-black');

        applyReorderFilters();
    }

    function applyReorderFilters() {
        let rows = document.querySelectorAll('.item-row');
        let searchKeyword = (document.getElementById('reorder_search_input')?.value || '').trim();

        let cleanSelectedMain = cleanFilterKey(selectedMainCategory);
        let cleanSelectedSub  = cleanFilterKey(selectedSubCategory);
        let cleanSearch       = cleanFilterKey(searchKeyword);

        rows.forEach(row => {
            let catAttr    = row.getAttribute('data-category') || '';
            let subCatAttr = row.getAttribute('data-subcategory') || '';
            let nameAttr   = row.getAttribute('data-name') || '';
            let codeAttr   = row.getAttribute('data-code') || '';

            let cleanCat    = cleanFilterKey(catAttr);
            let cleanSubCat = cleanFilterKey(subCatAttr);
            let cleanName   = cleanFilterKey(nameAttr);
            let cleanCode   = cleanFilterKey(codeAttr);

            // Match Kategori Utama
            let matchMain = (selectedMainCategory === 'all') || 
                            cleanCat.includes(cleanSelectedMain) || 
                            cleanSubCat.includes(cleanSelectedMain);

            // Match Sub Kategori
            let matchSub = (selectedSubCategory === 'all') || 
                           cleanSubCat.includes(cleanSelectedSub) || 
                           cleanCat.includes(cleanSelectedSub) || 
                           cleanName.includes(cleanSelectedSub);

            // Match Search Bar
            let matchSearch = !cleanSearch || 
                              cleanName.includes(cleanSearch) || 
                              cleanCode.includes(cleanSearch);

            if (matchMain && matchSub && matchSearch) {
                row.setAttribute('data-filtered-visible', 'true');
            } else {
                row.setAttribute('data-filtered-visible', 'false');
            }
        });

        if (typeof reorderCurrentPage !== 'undefined') {
            reorderCurrentPage = 1;
        }

        if (typeof renderReorderPagination === 'function') {
            renderReorderPagination();
        }

        calculateTotals();
    }

    // CETAK PO BERDASARKAN HASIL FILTER
    function printOrderReceipt() {
        let rows = document.querySelectorAll('.item-row');
        let visibleRows = Array.from(rows).filter(r => r.getAttribute('data-filtered-visible') !== 'false');

        if (visibleRows.length === 0) {
            Swal.fire({
                icon: 'info',
                title: 'Tidak Ada Barang!',
                text: 'Tidak ada barang yang tampil pada filter saat ini.',
                confirmButtonColor: '#4f46e5',
                confirmButtonText: 'Mengerti',
                customClass: {
                    popup: 'rounded-3xl',
                    confirmButton: 'rounded-xl text-xs font-bold px-5 py-2.5'
                }
            });
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

        visibleRows.forEach(row => {
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
            Swal.fire({
                icon: 'warning',
                title: 'Kuantitas Masih 0!',
                text: 'Masukkan jumlah order terlebih dahulu sebelum mencetak struk.',
                confirmButtonColor: '#4f46e5',
                confirmButtonText: 'Paham',
                customClass: {
                    popup: 'rounded-3xl',
                    confirmButton: 'rounded-xl text-xs font-bold px-5 py-2.5'
                }
            });
            return;
        }

        document.getElementById('print_total_items').innerText = totalItems + ' Item';
        document.getElementById('print_total_pcs').innerText = totalPcs + ' Pcs';

        document.getElementById('reorderReceiptModal').classList.remove('hidden');
    }

    function closeReorderReceiptModal() {
        document.getElementById('reorderReceiptModal').classList.add('hidden');
    }

    async function executePrintOrder() {
        let isAndroid = /Android/i.test(navigator.userAgent);

        if (navigator.bluetooth && isAndroid) {
            try {
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
                    alert('Tidak dapat mendeteksi layanan printer Bluetooth.');
                    window.print();
                    return;
                }

                if (typeof EscPosEncoder === 'undefined') {
                    console.warn('EscPosEncoder CDN belum dimuat di app.blade.php');
                    window.print();
                    return;
                }

                let dateStr    = document.getElementById('print_date')?.innerText || '-';
                let totalItems = document.getElementById('print_total_items')?.innerText || '0 Item';
                let totalPcs   = document.getElementById('print_total_pcs')?.innerText || '0 Pcs';

                let encoder = new EscPosEncoder();
                encoder
                    .initialize()
                    .codepage('cp437')
                    .align('center')
                    .bold(true)
                    .line('** {{ strtoupper(auth()->user()->store?->name ?? "WANNCELL") }} **')
                    .bold(false)
                    .line('REKAP ORDER SUPPLIER / PO')
                    .line(dateStr)
                    .line('--------------------------------')
                    .line('[ DAFTAR BARANG DIORDER ]')
                    .line('--------------------------------');

                let itemRows = document.querySelectorAll('#print_tbody .po-receipt-table tr');
                itemRows.forEach(row => {
                    let name = row.querySelector('.lbl .font-extrabold')?.innerText.replace(/\n/g, ' ').trim() || '';
                    let codeEl = row.querySelector('.lbl .text-\\[8px\\]');
                    let code = codeEl ? ' (' + codeEl.innerText.replace('KODE:', '').trim() + ')' : '';
                    let qty  = row.querySelector('.val')?.innerText.trim() || '';

                    if (name) {
                        encoder.align('left').line(name + code).align('right').line(qty);
                    }
                });

                let resultData = encoder
                    .align('center')
                    .line('--------------------------------')
                    .align('left')
                    .line('TOTAL VARIASI: ' + totalItems)
                    .bold(true)
                    .line('TOTAL PCS    : ' + totalPcs)
                    .bold(false)
                    .line('--------------------------------')
                    .align('center')
                    .line('** CATATAN KARYAWAN **')
                    .line('Serahkan Struk Ini ke Sales / Supplier')
                    .line('Cek Kembali Fisik Barang Saat Datang')
                    .line('..:: SIMPAN SEBAGAI BUKTI PO ::..')
                    .line('\n\n\n')
                    .encode();

                const chunkSize = 512;
                for (let i = 0; i < resultData.length; i += chunkSize) {
                    const chunk = resultData.slice(i, i + chunkSize);
                    await btCharacteristic.writeValue(chunk);
                }

            } catch (err) {
                console.error('Kendala cetak Bluetooth PO:', err);
                window.print();
            }
        } else {
            window.print();
        }
    }
</script>
@endpush