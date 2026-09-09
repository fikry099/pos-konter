<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <!-- HEADER TABEL -->
    <div class="p-3.5 bg-slate-50 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
        <span class="text-xs font-extrabold text-slate-700 uppercase tracking-wider flex items-center">
            <i class="fa-solid fa-list-check text-indigo-600 mr-2 text-xs"></i> Daftar Barang & Penyesuaian Kuantitas Order
        </span>
        <span class="text-[10px] sm:text-xs text-slate-400 font-medium">* Rumus: Jumlah Dipesan = Target Stok Maksimum - Sisa Stok</span>
    </div>

    <!-- WRAPPER SCROLL HORIZONTAL -->
    <div class="overflow-x-auto no-scrollbar">
        <table class="w-full text-left border-collapse min-w-[600px]" id="reorderTable">
            <thead>
                <tr class="bg-slate-50 text-[10px] sm:text-xs font-extrabold uppercase text-slate-400 border-b border-slate-200/80">
                    <th class="py-2.5 px-3">Kode / Nama Produk</th>
                    <th class="py-2.5 px-3">Kategori</th>
                    <th class="py-2.5 px-3 text-center">Sisa Stok</th>
                    <th class="py-2.5 px-3 text-center">Stok Maks. Target</th>
                    <th class="py-2.5 px-3 text-center w-36">Jumlah Dipesan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-xs sm:text-sm font-medium text-slate-700">
                @forelse($lowStockProducts as $stockItem)
                    @php
                        $prod = $stockItem->product;
                        $currentStock = (int) $stockItem->stock;
                        $maxStock = (int) ($stockItem->min_stock ?? 5);
                        $saranOrder = max(1, $maxStock - $currentStock);
                    @endphp
                    <tr class="hover:bg-indigo-50/40 transition item-row" 
                        data-id="{{ $prod->id }}"
                        data-name="{{ $prod->name }}"
                        data-code="{{ $prod->code }}"
                        data-cost="{{ (int)$prod->cost_price }}">
                        
                        <!-- KODE & NAMA PRODUK -->
                        <td class="py-3 px-3">
                            <div class="font-bold text-slate-800 text-xs sm:text-sm leading-snug">{{ $prod->name }}</div>
                            <div class="text-[10px] font-mono text-slate-400 mt-0.5">{{ $prod->code ?? '-' }}</div>
                        </td>

                        <!-- KATEGORI DENGAN HIRARKI INDUK -->
                        <td class="py-3 px-3">
                            @php
                                $cat = $prod->category;
                                $parent = $cat ? $cat->parent : null;
                            @endphp
                            <span class="px-2 py-0.5 rounded-lg bg-slate-100 text-slate-700 font-bold text-[10px] sm:text-xs border border-slate-200 inline-block">
                                @if($parent)
                                    <span class="text-slate-400 font-normal">{{ $parent->name }} &rsaquo;</span>
                                @endif
                                <span class="text-indigo-700 font-extrabold">{{ $cat->name ?? 'Tanpa Kategori' }}</span>
                            </span>
                        </td>

                        <!-- SISA STOK CABANG -->
                        <td class="py-3 px-3 text-center">
                            <span class="px-2 py-0.5 rounded-full bg-rose-50 text-rose-700 font-bold text-xs inline-block whitespace-nowrap border border-rose-100">
                                {{ $currentStock }} Pcs
                            </span>
                        </td>

                        <!-- STOK MAKS TARGET -->
                        <td class="py-3 px-3 text-center font-mono text-xs sm:text-sm font-bold text-slate-800 whitespace-nowrap">
                            {{ $maxStock }} Pcs
                        </td>

                        <!-- INPUT ADJUSTMENT KUANTITAS ORDER -->
                        <td class="py-3 px-3 text-center">
                            <div class="flex items-center justify-center">
                                <input type="number" min="1" value="{{ $saranOrder }}" oninput="calculateTotals()" class="order-qty w-20 px-2 py-1 bg-slate-50 border border-slate-200 rounded-xl text-center font-bold text-xs sm:text-sm text-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:bg-white transition">
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-12 text-center text-slate-400">
                            <i class="fa-solid fa-circle-check text-3xl sm:text-4xl mb-2 text-emerald-500 block"></i>
                            <span class="font-bold text-slate-700 block text-xs sm:text-sm">Semua Stok Voucher & Produk Fisik Masih Penuh!</span>
                            <span class="text-[11px] text-slate-400 block mt-0.5">Tidak ada produk yang berada di bawah target stok minimal saat ini.</span>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- FOOTER PAGINASI DENGAN PADDING KANAN -->
    <div id="reorder_pagination_container" class="p-3 border-t border-slate-100 bg-slate-50 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
        <div class="flex items-center justify-between w-full sm:w-auto space-x-2 text-slate-500 font-bold">
            <span id="reorder_pagination_info">Menampilkan 0 dari 0 produk</span>
            <div class="flex items-center space-x-1 shrink-0">
                <span class="text-[11px] font-normal">Tampil:</span>
                <select id="reorder_per_page" onchange="changeReorderPerPage()" class="bg-white border border-slate-200 rounded-lg text-xs font-bold px-1.5 py-1 focus:outline-none focus:border-indigo-500 cursor-pointer">
                    <option value="10" selected>10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
        </div>

        <!-- Tambahkan pr-14 sm:pr-16 di sini agar tombol bergeser ke kiri -->
        <div id="reorder_pagination_buttons" class="flex items-center space-x-1 shrink-0 justify-center w-full sm:w-auto pr-14 sm:pr-16"></div>
    </div>
</div>

<script>
    let reorderCurrentPage = 1;
    let reorderPerPage = 10;
    let allReorderRows = [];

    document.addEventListener('DOMContentLoaded', function() {
        initReorderPagination();
    });

    function initReorderPagination() {
        allReorderRows = Array.from(document.querySelectorAll('#reorderTable .item-row'));
        renderReorderPagination();
    }

    function renderReorderPagination() {
        let totalItems = allReorderRows.length;
        let infoText = document.getElementById('reorder_pagination_info');
        let buttonsWrapper = document.getElementById('reorder_pagination_buttons');

        if (totalItems === 0) {
            if (infoText) infoText.innerText = "Menampilkan 0 dari 0 produk";
            if (buttonsWrapper) buttonsWrapper.innerHTML = '';
            return;
        }

        let totalPages = Math.ceil(totalItems / reorderPerPage) || 1;
        if (reorderCurrentPage > totalPages) reorderCurrentPage = totalPages;

        let startIdx = (reorderCurrentPage - 1) * reorderPerPage;
        let endIdx = Math.min(startIdx + reorderPerPage, totalItems);

        allReorderRows.forEach((row, index) => {
            if (index >= startIdx && index < endIdx) {
                row.classList.remove('hidden');
            } else {
                row.classList.add('hidden');
            }
        });

        if (infoText) {
            infoText.innerText = `Menampilkan ${startIdx + 1}-${endIdx} dari ${totalItems} produk`;
        }

        if (!buttonsWrapper) return;
        buttonsWrapper.innerHTML = '';
        buttonsWrapper.className = "flex items-center space-x-1 shrink-0 justify-center w-full sm:w-auto pr-14 sm:pr-16";

        // Tombol Previous
        let prevBtn = document.createElement('button');
        prevBtn.type = 'button';
        prevBtn.disabled = reorderCurrentPage === 1;
        prevBtn.onclick = () => goToReorderPage(reorderCurrentPage - 1);
        prevBtn.className = `px-2 py-1 rounded-lg text-xs font-bold transition border border-slate-200 cursor-pointer ${reorderCurrentPage === 1 ? 'opacity-40 cursor-not-allowed bg-slate-100 text-slate-400' : 'bg-white hover:bg-slate-100 text-slate-700'}`;
        prevBtn.innerHTML = '<i class="fa-solid fa-chevron-left text-[10px]"></i>';
        buttonsWrapper.appendChild(prevBtn);

        // Angka Halaman
        for (let i = 1; i <= totalPages; i++) {
            if (totalPages > 4 && (i < reorderCurrentPage - 1 || i > reorderCurrentPage + 1) && i !== 1 && i !== totalPages) {
                if (i === reorderCurrentPage - 2 || i === reorderCurrentPage + 2) {
                    let dots = document.createElement('span');
                    dots.className = 'px-1 text-slate-400 text-xs';
                    dots.innerText = '...';
                    buttonsWrapper.appendChild(dots);
                }
                continue;
            }

            let pageBtn = document.createElement('button');
            pageBtn.type = 'button';
            pageBtn.onclick = () => goToReorderPage(i);
            pageBtn.className = `px-2.5 py-1 rounded-lg text-xs font-bold transition cursor-pointer ${i === reorderCurrentPage ? 'bg-indigo-600 text-white shadow-xs' : 'bg-white border border-slate-200 hover:bg-slate-100 text-slate-700'}`;
            pageBtn.innerText = i;
            buttonsWrapper.appendChild(pageBtn);
        }

        // Tombol Next
        let nextBtn = document.createElement('button');
        nextBtn.type = 'button';
        nextBtn.disabled = reorderCurrentPage === totalPages;
        nextBtn.onclick = () => goToReorderPage(reorderCurrentPage + 1);
        nextBtn.className = `px-2 py-1 rounded-lg text-xs font-bold transition border border-slate-200 cursor-pointer ${reorderCurrentPage === totalPages ? 'opacity-40 cursor-not-allowed bg-slate-100 text-slate-400' : 'bg-white hover:bg-slate-100 text-slate-700'}`;
        nextBtn.innerHTML = '<i class="fa-solid fa-chevron-right text-[10px]"></i>';
        buttonsWrapper.appendChild(nextBtn);
    }

    function goToReorderPage(page) {
        reorderCurrentPage = page;
        renderReorderPagination();
    }

    function changeReorderPerPage() {
        let selectEl = document.getElementById('reorder_per_page');
        reorderPerPage = parseInt(selectEl.value) || 10;
        reorderCurrentPage = 1;
        renderReorderPagination();
    }
</script>