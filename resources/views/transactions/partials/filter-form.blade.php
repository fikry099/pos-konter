<div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-sm">
    <form action="{{ route('transactions.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
        
        <!-- 1. CARI NOTA / NO HP (3 Kolom) -->
        <div class="md:col-span-3 space-y-1">
            <label class="text-[11px] font-bold text-gray-500 uppercase tracking-wider block">Cari Nota / No. HP</label>
            <div class="relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Kode TRX / No HP..." 
                       class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-9 pr-3 py-2 text-xs font-bold text-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition">
            </div>
        </div>

        <!-- 2. FILTER TANGGAL (2 Kolom) -->
        <div class="md:col-span-2 space-y-1">
            <label class="text-[11px] font-bold text-gray-500 uppercase tracking-wider block">Filter Tanggal</label>
            <div class="relative flex items-center">
                <input type="date" id="filter_date_input" name="date" value="{{ request('date') }}" 
                       class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-3.5 pr-9 py-2 text-xs font-bold text-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition cursor-pointer [&::-webkit-calendar-picker-indicator]:hidden [&::-webkit-inner-spin-button]:hidden">
                <button type="button" 
                        onclick="document.getElementById('filter_date_input').showPicker ? document.getElementById('filter_date_input').showPicker() : document.getElementById('filter_date_input').focus()" 
                        class="absolute right-3 text-indigo-600 hover:text-indigo-800 cursor-pointer p-1">
                    <i class="fa-solid fa-calendar-days text-sm"></i>
                </button>
            </div>
        </div>

        <!-- 3. SHIFT KERJA (2 Kolom) -->
        <div class="md:col-span-2 space-y-1 relative">
            <label class="text-[11px] font-bold text-gray-500 uppercase tracking-wider block">Shift Kerja</label>
            <input type="hidden" name="shift_id" id="input_shift_id" value="{{ request('shift_id') }}">
            
            <div onclick="toggleCardDropdown('modal_shift_card')" class="w-full bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-xl px-3 py-2 text-xs font-bold text-gray-800 cursor-pointer flex items-center justify-between transition">
                <span id="label_shift_id">
                    @php
                        $activeShiftText = '-- Semua Shift --';
                        if(request('shift_id') && isset($shifts)) {
                            $foundS = $shifts->firstWhere('id', request('shift_id'));
                            if($foundS) {
                                $cName = $foundS->staff_names ?? $foundS->user->name ?? 'Kasir Cabang';
                                $activeShiftText = "Shift #{$foundS->id} ({$cName})";
                            }
                        }
                    @endphp
                    {{ $activeShiftText }}
                </span>
                <i class="fa-solid fa-chevron-down text-slate-400 text-[10px]"></i>
            </div>

            <div id="modal_shift_card" class="hidden absolute left-0 right-0 top-full mt-1.5 bg-white rounded-2xl shadow-xl border border-slate-200 p-2 z-50 max-h-60 overflow-y-auto space-y-1.5">
                <div onclick="selectCardOption('shift_id', '', '-- Semua Shift --', 'modal_shift_card', 'label_shift_id')" 
                     class="p-2.5 rounded-xl text-xs font-bold cursor-pointer transition flex items-center justify-between {{ request('shift_id') == '' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-slate-50 text-slate-700 hover:bg-slate-100' }}">
                    <span>-- Semua Shift --</span>
                    @if(request('shift_id') == '') <i class="fa-solid fa-check text-xs"></i> @endif
                </div>
                @if(isset($shifts))
                    @foreach($shifts as $shift)
                        @php 
                            $cashierName = $shift->staff_names ?? $shift->user->name ?? 'Kasir Cabang';
                            $shiftLabel = "Shift #{$shift->id} ({$cashierName})";
                            $isSelected = request('shift_id') == $shift->id;
                        @endphp
                        <div onclick="selectCardOption('shift_id', '{{ $shift->id }}', '{{ $shiftLabel }}', 'modal_shift_card', 'label_shift_id')" 
                             class="p-2.5 rounded-xl text-xs font-bold cursor-pointer transition flex items-center justify-between {{ $isSelected ? 'bg-indigo-600 text-white shadow-sm' : 'bg-slate-50 text-slate-700 hover:bg-slate-100' }}">
                            <span>{{ $shiftLabel }}</span>
                            @if($isSelected) <i class="fa-solid fa-check text-xs"></i> @endif
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

        <!-- 4. METODE PEMBAYARAN (2 Kolom) -->
        <div class="md:col-span-2 space-y-1 relative">
            <label class="text-[11px] font-bold text-gray-500 uppercase tracking-wider block">Metode Bayar</label>
            <input type="hidden" name="payment_method" id="input_payment_method" value="{{ request('payment_method') }}">
            
            <div onclick="toggleCardDropdown('modal_payment_card')" class="w-full bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-xl px-3 py-2 text-xs font-bold text-gray-800 cursor-pointer flex items-center justify-between transition">
                <span id="label_payment_method">
                    @php
                        $pm = request('payment_method');
                        $pmText = '-- Semua --';
                        if($pm == 'cash') $pmText = 'Cash (Tunai)';
                        elseif($pm == 'qris') $pmText = 'QRIS';
                    @endphp
                    {{ $pmText }}
                </span>
                <i class="fa-solid fa-chevron-down text-slate-400 text-[10px]"></i>
            </div>

            <div id="modal_payment_card" class="hidden absolute left-0 right-0 top-full mt-1.5 bg-white rounded-2xl shadow-xl border border-slate-200 p-2 z-50 space-y-1.5">
                <div onclick="selectCardOption('payment_method', '', '-- Semua --', 'modal_payment_card', 'label_payment_method')" 
                     class="p-2.5 rounded-xl text-xs font-bold cursor-pointer transition flex items-center justify-between {{ request('payment_method') == '' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-slate-50 text-slate-700 hover:bg-slate-100' }}">
                    <span>-- Semua --</span>
                    @if(request('payment_method') == '') <i class="fa-solid fa-check text-xs"></i> @endif
                </div>
                <div onclick="selectCardOption('payment_method', 'cash', 'Cash (Tunai)', 'modal_payment_card', 'label_payment_method')" 
                     class="p-2.5 rounded-xl text-xs font-bold cursor-pointer transition flex items-center justify-between {{ request('payment_method') == 'cash' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-slate-50 text-slate-700 hover:bg-slate-100' }}">
                    <span>Cash (Tunai)</span>
                    @if(request('payment_method') == 'cash') <i class="fa-solid fa-check text-xs"></i> @endif
                </div>
                <div onclick="selectCardOption('payment_method', 'qris', 'QRIS', 'modal_payment_card', 'label_payment_method')" 
                     class="p-2.5 rounded-xl text-xs font-bold cursor-pointer transition flex items-center justify-between {{ request('payment_method') == 'qris' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-slate-50 text-slate-700 hover:bg-slate-100' }}">
                    <span>QRIS</span>
                    @if(request('payment_method') == 'qris') <i class="fa-solid fa-check text-xs"></i> @endif
                </div>
            </div>
        </div>

        <!-- 5. KATEGORI UTAMA (3 Kolom) -->
        <div class="md:col-span-3 space-y-1 relative">
            <label class="text-[11px] font-bold text-gray-500 uppercase tracking-wider block">Kategori / Katalog</label>
            <input type="hidden" name="category_id" id="input_category_id" value="{{ request('category_id') }}">
            
            <div onclick="toggleCardDropdown('modal_category_card')" class="w-full bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-xl px-3 py-2 text-xs font-bold text-gray-800 cursor-pointer flex items-center justify-between transition">
                <span id="label_category_id">
                    @php
                        $activeCatText = '-- Semua Kategori --';
                        if(request('category_id') && isset($categories)) {
                            $foundCat = $categories->firstWhere('id', request('category_id'));
                            if($foundCat) $activeCatText = $foundCat->name;
                        }
                    @endphp
                    {{ $activeCatText }}
                </span>
                <i class="fa-solid fa-chevron-down text-slate-400 text-[10px]"></i>
            </div>

            <div id="modal_category_card" class="hidden absolute left-0 right-0 top-full mt-1.5 bg-white rounded-2xl shadow-xl border border-slate-200 p-2 z-50 max-h-60 overflow-y-auto space-y-1.5">
                <div onclick="selectCardOption('category_id', '', '-- Semua Kategori --', 'modal_category_card', 'label_category_id'); fetchSubCategories('');" 
                     class="p-2.5 rounded-xl text-xs font-bold cursor-pointer transition flex items-center justify-between {{ request('category_id') == '' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-slate-50 text-slate-700 hover:bg-slate-100' }}">
                    <span>-- Semua Kategori --</span>
                    @if(request('category_id') == '') <i class="fa-solid fa-check text-xs"></i> @endif
                </div>
                @if(isset($categories))
                    @foreach($categories as $cat)
                        @php $isCatSelected = request('category_id') == $cat->id; @endphp
                        <div onclick="selectCardOption('category_id', '{{ $cat->id }}', '{{ $cat->name }}', 'modal_category_card', 'label_category_id'); fetchSubCategories('{{ $cat->id }}');" 
                             class="p-2.5 rounded-xl text-xs font-bold cursor-pointer transition flex items-center justify-between {{ $isCatSelected ? 'bg-indigo-600 text-white shadow-sm' : 'bg-slate-50 text-slate-700 hover:bg-slate-100' }}">
                            <span>{{ $cat->name }}</span>
                            @if($isCatSelected) <i class="fa-solid fa-check text-xs"></i> @endif
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

        <!-- 6. SUB-KATEGORI / PROVIDER (DINAMIS BERANTAI) (3 Kolom) -->
        <div class="md:col-span-3 space-y-1 relative {{ request('category_id') ? '' : 'hidden' }}" id="sub_category_container">
            <label class="text-[11px] font-bold text-indigo-600 uppercase tracking-wider block">Sub-Kategori / Provider</label>
            <input type="hidden" name="sub_category_id" id="input_sub_category_id" value="{{ request('sub_category_id') }}">
            
            <div onclick="toggleCardDropdown('modal_sub_category_card')" class="w-full bg-indigo-50/60 hover:bg-indigo-50 border border-indigo-200 rounded-xl px-3 py-2 text-xs font-bold text-indigo-900 cursor-pointer flex items-center justify-between transition">
                <span id="label_sub_category_id">
                    @php
                        $activeSubText = '-- Semua Sub-Kategori --';
                        // Label akan otomatis di-handle oleh script saat fetch
                    @endphp
                    {{ $activeSubText }}
                </span>
                <i class="fa-solid fa-chevron-down text-indigo-400 text-[10px]"></i>
            </div>

            <div id="modal_sub_category_card" class="hidden absolute left-0 right-0 top-full mt-1.5 bg-white rounded-2xl shadow-xl border border-slate-200 p-2 z-50 max-h-60 overflow-y-auto space-y-1.5">
                <div onclick="selectCardOption('sub_category_id', '', '-- Semua Sub-Kategori --', 'modal_sub_category_card', 'label_sub_category_id')" 
                     class="p-2.5 rounded-xl text-xs font-bold cursor-pointer transition flex items-center justify-between bg-slate-50 text-slate-700 hover:bg-slate-100">
                    <span>-- Semua Sub-Kategori --</span>
                </div>
                <!-- Diisi secara dinamis melalui JavaScript -->
            </div>
        </div>

        <!-- 7. TOMBOL AKSI: FILTER & EXPORT EXCEL -->
        <div class="md:col-span-12 lg:col-span-3 flex items-center space-x-2 mt-2 md:mt-0">
            <button type="submit" class="w-1/2 bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-xs py-2 px-3 rounded-xl transition cursor-pointer flex items-center justify-center gap-1.5 shadow-sm">
                <i class="fa-solid fa-filter"></i>
                <span>Filter</span>
            </button>

            <a href="{{ route('transactions.export_excel', request()->all()) }}" class="w-1/2 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs py-2 px-3 rounded-xl transition flex items-center justify-center gap-1.5 shadow-sm cursor-pointer">
                <i class="fa-solid fa-file-excel"></i>
                <span>Excel</span>
            </a>
        </div>

    </form>
</div>

<script>
    function toggleCardDropdown(cardId) {
        document.querySelectorAll('[id$="_card"]').forEach(card => {
            if (card.id !== cardId) card.classList.add('hidden');
        });

        const targetCard = document.getElementById(cardId);
        if (targetCard) {
            targetCard.classList.toggle('hidden');
        }
    }

    window.addEventListener('click', function(e) {
        if (!e.target.closest('.relative')) {
            document.querySelectorAll('[id$="_card"]').forEach(card => {
                card.classList.add('hidden');
            });
        }
    });

    function selectCardOption(fieldKey, value, labelText, cardId, labelId) {
        document.getElementById(`input_${fieldKey}`).value = value;
        document.getElementById(labelId).innerText = labelText;
        document.getElementById(cardId).classList.add('hidden');
    }

    function fetchSubCategories(parentId, selectedSubId = null) {
        const container = document.getElementById('sub_category_container');
        const cardPanel = document.getElementById('modal_sub_category_card');
        const labelEl = document.getElementById('label_sub_category_id');
        const hiddenInput = document.getElementById('input_sub_category_id');

        if (!parentId) {
            container.classList.add('hidden');
            hiddenInput.value = '';
            labelEl.innerText = '-- Semua Sub-Kategori --';
            cardPanel.innerHTML = `<div onclick="selectCardOption('sub_category_id', '', '-- Semua Sub-Kategori --', 'modal_sub_category_card', 'label_sub_category_id')" class="p-2.5 rounded-xl text-xs font-bold cursor-pointer transition flex items-center justify-between bg-slate-50 text-slate-700 hover:bg-slate-100"><span>-- Semua Sub-Kategori --</span></div>`;
            return;
        }

        fetch(`/categories/${parentId}/sub-categories`)
            .then(res => res.json())
            .then(data => {
                let html = `<div onclick="selectCardOption('sub_category_id', '', '-- Semua Sub-Kategori --', 'modal_sub_category_card', 'label_sub_category_id')" class="p-2.5 rounded-xl text-xs font-bold cursor-pointer transition flex items-center justify-between ${(selectedSubId == '' || !selectedSubId) ? 'bg-indigo-600 text-white shadow-sm' : 'bg-slate-50 text-slate-700 hover:bg-slate-100'}"><span>-- Semua Sub-Kategori --</span>${(selectedSubId == '' || !selectedSubId) ? '<i class="fa-solid fa-check text-xs"></i>' : ''}</div>`;
                
                let foundLabel = '-- Semua Sub-Kategori --';

                if (data && data.length > 0) {
                    data.forEach(sub => {
                        const isSelected = (selectedSubId && selectedSubId == sub.id);
                        if (isSelected) {
                            foundLabel = sub.name;
                            hiddenInput.value = sub.id;
                        }
                        html += `<div onclick="selectCardOption('sub_category_id', '${sub.id}', '${sub.name}', 'modal_sub_category_card', 'label_sub_category_id')" class="p-2.5 rounded-xl text-xs font-bold cursor-pointer transition flex items-center justify-between ${isSelected ? 'bg-indigo-600 text-white shadow-sm' : 'bg-slate-50 text-slate-700 hover:bg-slate-100'}"><span>${sub.name}</span>${isSelected ? '<i class="fa-solid fa-check text-xs"></i>' : ''}</div>`;
                    });

                    labelEl.innerText = foundLabel;
                    cardPanel.innerHTML = html;
                    container.classList.remove('hidden');
                } else {
                    container.classList.add('hidden');
                    hiddenInput.value = '';
                }
            })
            .catch(err => console.error('Error fetching subcategories:', err));
    }

    document.addEventListener("DOMContentLoaded", function() {
        const activeCatId = "{{ request('category_id') }}";
        const activeSubCatId = "{{ request('sub_category_id') }}";

        if (activeCatId) {
            fetchSubCategories(activeCatId, activeSubCatId);
        }
    });
</script>