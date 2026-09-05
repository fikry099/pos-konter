<header class="bg-white border-b border-slate-200 px-3 sm:px-6 py-2.5 flex flex-col gap-2 shrink-0">
    
    @php
        $user = auth()->user();
        $currentStoreId = $user->store_id ?? session('selected_store_id');
        $headerActiveShift = \App\Models\Shift::getActiveShift($currentStoreId);
        $allStores = $user->isOwner() ? \App\Models\Store::where('is_active', true)->get() : collect();
    @endphp

    <!-- BARIS 1 (ATAS): INFORMASI TIM SHIFT KASIR & TANGGAL REALTIME -->
    <div class="flex items-center justify-between gap-2 w-full">
        
        <!-- STATUS SHIFT KASIR -->
        <div class="flex-1 min-w-0">
            @if($headerActiveShift)
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-3 py-1.5 rounded-xl text-xs font-semibold flex items-center justify-center sm:justify-start shadow-sm">
                    <span class="w-2 h-2 bg-emerald-500 rounded-full mr-2 animate-pulse shrink-0"></span>
                    <span class="truncate">
                        Tim: <strong>{{ preg_replace('/\s*\([^)]*\)/', '', $headerActiveShift->staff_names ?? ($headerActiveShift->user->name ?? 'Kasir')) }}</strong>
                    </span>
                </div>
            @else
                <div class="bg-rose-50 border border-rose-200 text-rose-600 px-3 py-1.5 rounded-xl text-xs font-semibold flex items-center justify-center sm:justify-start">
                    <i class="fa-solid fa-lock mr-2 text-[11px]"></i>
                    <span>Shift Belum Dibuka</span>
                </div>
            @endif
        </div>

        <!-- TANGGAL REALTIME -->
        <div class="text-xs font-extrabold text-slate-800 flex items-center space-x-1.5 shrink-0 bg-slate-100 px-2.5 py-1.5 rounded-xl border border-slate-200/80">
            <i class="fa-regular fa-calendar-days text-indigo-600"></i>
            <span>{{ \Carbon\Carbon::now()->translatedFormat('d M Y') }}</span>
        </div>

    </div>

    <!-- BARIS 2 (BAWAH): TAB SWITCHER PINDAH CABANG (HANYA DITAMPILKAN KHUSUS DI HALAMAN DASHBOARD) -->
    @if($user->isOwner() && request()->routeIs('dashboard'))
        <div class="w-full overflow-x-auto no-scrollbar pt-0.5">
            <form action="{{ route('stores.switch') }}" method="POST" id="header_store_form" class="flex items-center w-full">
                @csrf
                <input type="hidden" name="store_id" id="selected_store_id_input" value="{{ session('selected_store_id') }}">

                <!-- CONTAINER TAB PILLS FULL WIDTH -->
                <div class="inline-flex items-center w-full bg-slate-100 p-1 rounded-2xl border border-slate-200/80 shadow-inner gap-1">
                    @foreach($allStores as $store)
                        @php
                            $isSelected = (session('selected_store_id') == $store->id);
                        @endphp
                        <button type="button" 
                                onclick="submitStoreSwitch('{{ $store->id }}')" 
                                class="flex-1 px-3 py-1.5 rounded-xl text-xs font-black transition-all duration-200 flex items-center justify-center space-x-1.5 cursor-pointer whitespace-nowrap {{ $isSelected ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/60' }}">
                            <i class="fa-solid fa-store text-[11px]"></i>
                            <span>{{ $store->name }}</span>
                        </button>
                    @endforeach
                </div>
            </form>

            <script>
                function submitStoreSwitch(storeId) {
                    document.getElementById('selected_store_id_input').value = storeId;
                    document.getElementById('header_store_form').submit();
                }
            </script>
        </div>
    @endif

</header>