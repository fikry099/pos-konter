@extends('layouts.app')

@section('content')
<div class="space-y-5 w-full pb-24 sm:pb-12">
    
    <!-- 1. HEADER HALAMAN -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-4 sm:p-5 rounded-2xl sm:rounded-3xl border border-slate-200 shadow-sm">
        <div>
            <h1 class="text-base sm:text-xl font-black text-slate-800 flex items-center">
                <i class="fa-solid fa-server text-amber-500 mr-2 text-lg sm:text-xl"></i> Monitor & Saldo Server PPOB
            </h1>
            <p class="text-[11px] sm:text-xs text-slate-500 font-medium mt-0.5">Pantau sisa deposit aplikasi transaksi secara real-time untuk kelancaran operasional konter.</p>
        </div>

        <!-- TOMBOL AKSI -->
        <div class="flex items-center gap-2 shrink-0">
            <button type="button" onclick="document.getElementById('modalAddServer').classList.remove('hidden')" class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs px-3.5 py-2.5 rounded-xl font-extrabold transition flex items-center space-x-1.5 whitespace-nowrap cursor-pointer active:scale-95 border border-slate-200/80">
                <i class="fa-solid fa-plus text-xs"></i>
                <span>Tambah Server</span>
            </button>

            <button type="button" onclick="document.getElementById('modalDeposit').classList.remove('hidden')" class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs px-4 py-2.5 rounded-xl font-extrabold transition shadow-md shadow-indigo-200 flex items-center space-x-1.5 whitespace-nowrap cursor-pointer active:scale-95">
                <i class="fa-solid fa-wallet text-xs"></i>
                <span>Isi Ulang Saldo</span>
            </button>
        </div>
    </div>

    <!-- 2. GRID CARDS SALDO SERVER PPOB -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @forelse($servers as $server)
            @php
                // Saldo dianggap menipis jika di bawah atau sama dengan Rp 100.000 namun masih lebih besar dari 0
                $isLow = $server->balance <= 100000 && $server->balance > 0;
                $isEmpty = $server->balance <= 0;
            @endphp
            <div class="bg-white p-5 rounded-2xl sm:rounded-3xl border transition-all duration-200 relative overflow-hidden group shadow-sm hover:shadow-md
                {{ $isEmpty ? 'border-rose-200 bg-rose-50/20' : ($isLow ? 'border-amber-200 bg-amber-50/20' : 'border-slate-200/80 hover:border-indigo-300') }}">
                
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-2xl {{ $isEmpty ? 'bg-rose-100 text-rose-600' : ($isLow ? 'bg-amber-100 text-amber-600' : 'bg-indigo-50 text-indigo-600') }} flex items-center justify-center font-black text-xs transition-transform group-hover:scale-110 shadow-xs">
                            <i class="fa-solid fa-microchip text-sm"></i>
                        </div>
                        <span class="text-xs font-black text-slate-800 uppercase tracking-wider">{{ $server->name }}</span>
                    </div>

                    <span class="relative flex h-2.5 w-2.5">
                        @if($isEmpty || $isLow)
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full {{ $isEmpty ? 'bg-rose-400' : 'bg-amber-400' }} opacity-75"></span>
                        @endif
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 {{ $isEmpty ? 'bg-rose-500' : ($isLow ? 'bg-amber-500' : 'bg-emerald-500') }}"></span>
                    </span>
                </div>

                <div class="space-y-2">
                    <!-- Tampilan saldo mendukung nominal perak secara presisi -->
                    <div class="text-2xl font-black font-mono tracking-tight {{ $isEmpty ? 'text-rose-600' : ($isLow ? 'text-amber-600' : 'text-slate-900') }}">
                        Rp {{ number_format($server->balance, 0, ',', '.') }}
                    </div>

                    <div class="flex items-center justify-between pt-1">
                        <span class="text-[11px] font-extrabold flex items-center gap-1 {{ $isEmpty ? 'text-rose-600' : ($isLow ? 'text-amber-600' : 'text-emerald-600') }}">
                            @if($isEmpty)
                                <i class="fa-solid fa-triangle-exclamation"></i> Saldo Kosong
                            @elseif($isLow)
                                <i class="fa-solid fa-circle-exclamation"></i> Saldo Menipis
                            @else
                                <i class="fa-solid fa-circle-check"></i> Saldo Aman
                            @endif
                        </span>

                        <button type="button" onclick="quickDeposit({{ $server->id }}, '{{ $server->name }}', 'Rp {{ number_format($server->balance, 0, ',', '.') }}')" class="text-[11px] font-extrabold text-indigo-600 hover:text-white bg-indigo-50 hover:bg-indigo-600 px-3 py-1.5 rounded-xl transition cursor-pointer active:scale-95 shadow-xs">
                            + Top Up
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white p-8 rounded-2xl sm:rounded-3xl text-center text-slate-400 font-bold text-xs border border-slate-200 shadow-sm">
                Belum ada data server PPOB. Silakan klik tombol "Tambah Server" di atas.
            </div>
        @endforelse
    </div>

    <!-- 3. TABEL RIWAYAT TOP-UP SALDO -->
    <div class="bg-white rounded-2xl sm:rounded-3xl shadow-sm border border-slate-200/80 overflow-hidden">
        
        <!-- HEADER TABEL & FILTER BULAN -->
        <div class="p-4 sm:p-5 border-b border-slate-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 bg-slate-50/50">
            <h3 class="font-black text-slate-800 text-xs uppercase tracking-wider flex items-center gap-2">
                <i class="fa-solid fa-clock-rotate-left text-indigo-600"></i> Riwayat Isi Ulang Saldo
            </h3>

            <!-- FORM FILTER PER BULAN -->
            <form method="GET" action="{{ url()->current() }}" class="flex items-center gap-2">
                <div class="relative">
                    <input type="month" name="month" value="{{ request('month', date('Y-m')) }}" onchange="this.form.submit()" class="bg-white border border-slate-200 rounded-xl px-3 py-1.5 text-xs font-bold text-slate-700 focus:outline-none focus:border-indigo-500 shadow-2xs cursor-pointer">
                </div>
                @if(request('month'))
                    <a href="{{ url()->current() }}" class="bg-slate-200 hover:bg-slate-300 text-slate-600 p-2 rounded-xl text-xs transition active:scale-95 flex items-center justify-center" title="Reset Filter">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-400 text-[10px] uppercase font-black border-b border-slate-100">
                    <tr>
                        <th class="py-3.5 px-5">Waktu</th>
                        <th class="py-3.5 px-5">Server / Aplikasi</th>
                        <th class="py-3.5 px-5 text-right">Nominal Top-Up</th>
                        <th class="py-3.5 px-5">Catatan</th>
                        <th class="py-3.5 px-5">Petugas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-bold text-slate-700">
                    @forelse($recentDeposits as $deposit)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-3.5 px-5 text-slate-400 font-mono text-[11px]">
                                {{ $deposit->created_at->format('d M Y, H:i') }}
                            </td>
                            <td class="py-3.5 px-5">
                                <span class="bg-indigo-50 text-indigo-700 px-2.5 py-1 rounded-lg text-[11px] font-black border border-indigo-100">
                                    {{ $deposit->server->name ?? '-' }}
                                </span>
                            </td>
                            <td class="py-3.5 px-5 text-right font-mono font-black text-emerald-600 text-sm">
                                +Rp {{ number_format($deposit->amount, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-5 text-slate-500 italic text-[11px]">{{ $deposit->notes ?? '-' }}</td>
                            <td class="py-3.5 px-5 font-extrabold text-slate-700">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-slate-100 text-indigo-600 flex items-center justify-center text-[10px] font-black border border-slate-200">
                                        {{ strtoupper(substr($deposit->user->name ?? 'S', 0, 1)) }}
                                    </div>
                                    <span>{{ $deposit->user->name ?? 'System' }}</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-400 font-bold">
                                <div class="flex flex-col items-center justify-center space-y-2">
                                    <i class="fa-solid fa-receipt text-3xl text-slate-300"></i>
                                    <span class="text-xs">Belum ada riwayat isi ulang saldo tercatat.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- PAGINASI KUSTOM -->
        <div class="p-4 border-t border-slate-100 bg-white flex flex-col sm:flex-row items-center justify-between gap-3 text-xs font-bold text-slate-500">
            <div>
                Showing {{ $recentDeposits->firstItem() ?? 0 }} to {{ $recentDeposits->lastItem() ?? 0 }} of {{ $recentDeposits->total() }} results
            </div>

            @if ($recentDeposits->hasPages())
                <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center space-x-1.5 pr-12 sm:pr-16">
                    @if ($recentDeposits->onFirstPage())
                        <span class="w-9 h-9 flex items-center justify-center rounded-2xl bg-slate-50 text-slate-300 cursor-not-allowed border border-slate-100">
                            <i class="fa-solid fa-chevron-left text-xs"></i>
                        </span>
                    @else
                        <a href="{{ $recentDeposits->previousPageUrl() }}" class="w-9 h-9 flex items-center justify-center rounded-2xl bg-slate-50 hover:bg-slate-100 text-slate-600 transition active:scale-95 border border-slate-100">
                            <i class="fa-solid fa-chevron-left text-xs"></i>
                        </a>
                    @endif

                    @foreach ($recentDeposits->links()->elements as $element)
                        @if (is_string($element))
                            <span class="px-1 text-slate-400 font-bold">{{ $element }}</span>
                        @endif

                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $recentDeposits->currentPage())
                                    <span class="w-9 h-9 flex items-center justify-center rounded-2xl bg-indigo-600 text-white font-black shadow-md shadow-indigo-200 text-xs">
                                        {{ $page }}
                                    </span>
                                @else
                                    <a href="{{ $url }}" class="w-9 h-9 flex items-center justify-center rounded-2xl bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold transition active:scale-95 border border-slate-100 text-xs">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    @if ($recentDeposits->hasMorePages())
                        <a href="{{ $recentDeposits->nextPageUrl() }}" class="w-9 h-9 flex items-center justify-center rounded-2xl bg-slate-50 hover:bg-slate-100 text-slate-600 transition active:scale-95 border border-slate-100">
                            <i class="fa-solid fa-chevron-right text-xs"></i>
                        </a>
                    @else
                        <span class="w-9 h-9 flex items-center justify-center rounded-2xl bg-slate-50 text-slate-300 cursor-not-allowed border border-slate-100">
                            <i class="fa-solid fa-chevron-right text-xs"></i>
                        </span>
                    @endif
                </nav>
            @endif
        </div>
    </div>
</div>

<!-- MODAL ISI ULANG SALDO -->
<div id="modalDeposit" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-[99999] flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-sm w-full p-6 space-y-4 shadow-2xl border border-slate-100">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h4 class="font-black text-slate-800 text-sm flex items-center gap-2">
                <i class="fa-solid fa-wallet text-indigo-600"></i> Isi Ulang Saldo Server
            </h4>
            <button type="button" onclick="document.getElementById('modalDeposit').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>
        <form action="{{ route('ppob_servers.deposit') }}" method="POST" class="space-y-3.5" onsubmit="prepareForm(this)">
            @csrf
            
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Pilih Server PPOB</label>
                
                @php
                    $firstServer = $servers->first();
                    $defaultServerId = $firstServer ? $firstServer->id : '';
                    $defaultServerName = $firstServer ? $firstServer->name . ' (Sisa: Rp ' . number_format($firstServer->balance, 0, ',', '.') . ')' : 'Pilih Server';
                @endphp

                <input type="hidden" name="ppob_server_id" id="select_server_id" value="{{ $defaultServerId }}" required>

                <button type="button" 
                        onclick="openServerSelectionModal()" 
                        class="w-full text-xs font-black bg-indigo-50 border-2 border-indigo-200 hover:border-indigo-400 text-indigo-950 rounded-2xl px-4 py-3 flex items-center justify-between cursor-pointer active:scale-98 transition-all shadow-xs">
                    <span class="flex items-center space-x-2 truncate">
                        <i class="fa-solid fa-microchip text-indigo-600 text-sm"></i>
                        <span id="selected_server_display" class="truncate font-black">{{ $defaultServerName }}</span>
                    </span>
                    <i class="fa-solid fa-chevron-down text-indigo-600 text-xs shrink-0 ml-2"></i>
                </button>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Nominal Saldo Ditambahkan (Rp)</label>
                <!-- DUKUNGAN INPUT MINIMAL DARI RP 1 (PERAK) -->
                <input type="text" id="display_amount" required autocomplete="off" placeholder="Contoh: 200 atau 1.000.000" class="w-full text-xs p-3 bg-slate-50 border border-slate-200 rounded-2xl font-bold font-mono focus:outline-none focus:border-indigo-500" oninput="formatRupiah(this, 'raw_amount')">
                <input type="hidden" name="amount" id="raw_amount" required>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Catatan (Opsional)</label>
                <input type="text" name="notes" autocomplete="off" placeholder="Contoh: Top-up saldo harian" class="w-full text-xs p-3 bg-slate-50 border border-slate-200 rounded-2xl focus:outline-none focus:border-indigo-500">
            </div>
            <div class="flex space-x-2 pt-2">
                <button type="button" onclick="document.getElementById('modalDeposit').classList.add('hidden')" class="w-1/2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold py-3 rounded-2xl cursor-pointer transition">Batal</button>
                <button type="submit" class="w-1/2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-extrabold py-3 rounded-2xl shadow-md shadow-indigo-600/30 cursor-pointer transition">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- SUB-MODAL SELEKSI DROPDOWN SERVER PPOB -->
<div id="modalServerSelect" class="fixed -inset-10 z-[999999] hidden backdrop-blur-none flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/80" onclick="closeServerSelectionModal()"></div>
    <div class="bg-white rounded-3xl w-full max-w-sm p-5 space-y-3 shadow-2xl border border-indigo-100 relative z-10 my-auto">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h4 class="font-black text-slate-800 text-sm flex items-center">
                <i class="fa-solid fa-server text-indigo-600 mr-2"></i> Pilih Aplikasi / Server PPOB
            </h4>
            <button type="button" onclick="closeServerSelectionModal()" class="text-slate-400 hover:text-slate-600 p-1 cursor-pointer">
                <i class="fa-solid fa-xmark text-base"></i>
            </button>
        </div>

        <div class="space-y-2 max-h-72 overflow-y-auto pt-1">
            @foreach($servers as $s)
                @php
                    $label = $s->name . ' (Sisa: Rp ' . number_format($s->balance, 0, ',', '.') . ')';
                @endphp
                <button type="button" 
                        onclick="selectServerModal('{{ $s->id }}', '{{ $label }}')" 
                        class="server-modal-item w-full text-left px-4 py-3.5 rounded-2xl text-xs font-black transition-all flex items-center justify-between cursor-pointer active:scale-95
                        {{ $loop->first ? 'bg-indigo-600 text-white shadow-md' : 'bg-slate-50 text-slate-700 hover:bg-slate-100' }}"
                        data-val="{{ $s->id }}"
                        data-label="{{ $label }}">
                    <span class="flex items-center space-x-2.5 truncate">
                        <i class="fa-solid fa-microchip text-sm"></i>
                        <span class="truncate">{{ $label }}</span>
                    </span>
                    <i class="fa-solid fa-check text-xs check-icon {{ $loop->first ? '' : 'hidden' }}"></i>
                </button>
            @endforeach
        </div>
    </div>
</div>

<!-- MODAL TAMBAH SERVER BARU -->
<div id="modalAddServer" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-[99999] flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-sm w-full p-6 space-y-4 shadow-2xl border border-slate-100">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h4 class="font-black text-slate-800 text-sm flex items-center gap-2">
                <i class="fa-solid fa-circle-plus text-indigo-600"></i> Tambah Server PPOB Baru
            </h4>
            <button type="button" onclick="document.getElementById('modalAddServer').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>
        <form action="{{ route('ppob_servers.store') }}" method="POST" class="space-y-3.5" onsubmit="prepareServerForm(this)">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Nama Server / Aplikasi</label>
                <input type="text" name="name" required autocomplete="off" placeholder="Contoh: Otomax, Flash, dll" class="w-full text-xs p-3 bg-slate-50 border border-slate-200 rounded-2xl font-bold focus:outline-none focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Saldo Awal (Opsional)</label>
                <input type="text" id="display_initial_balance" autocomplete="off" placeholder="0" class="w-full text-xs p-3 bg-slate-50 border border-slate-200 rounded-2xl font-bold font-mono focus:outline-none focus:border-indigo-500" oninput="formatRupiah(this, 'raw_initial_balance')">
                <input type="hidden" name="initial_balance" id="raw_initial_balance" value="0">
            </div>
            <div class="flex space-x-2 pt-2">
                <button type="button" onclick="document.getElementById('modalAddServer').classList.add('hidden')" class="w-1/2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold py-3 rounded-2xl cursor-pointer transition">Batal</button>
                <button type="submit" class="w-1/2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-extrabold py-3 rounded-2xl shadow-md shadow-indigo-600/30 cursor-pointer transition">Tambah</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openServerSelectionModal() {
        document.getElementById('modalServerSelect').classList.remove('hidden');
    }

    function closeServerSelectionModal() {
        document.getElementById('modalServerSelect').classList.add('hidden');
    }

    function selectServerModal(id, label) {
        document.getElementById('select_server_id').value = id;
        document.getElementById('selected_server_display').innerText = label;

        document.querySelectorAll('.server-modal-item').forEach(btn => {
            let isSelected = btn.getAttribute('data-val') === id.toString();
            let check = btn.querySelector('.check-icon');

            if (isSelected) {
                btn.className = 'server-modal-item w-full text-left px-4 py-3.5 rounded-2xl text-xs font-black transition-all flex items-center justify-between cursor-pointer active:scale-95 bg-indigo-600 text-white shadow-md';
                if (check) check.classList.remove('hidden');
            } else {
                btn.className = 'server-modal-item w-full text-left px-4 py-3.5 rounded-2xl text-xs font-black transition-all flex items-center justify-between cursor-pointer active:scale-95 bg-slate-50 text-slate-700 hover:bg-slate-100';
                if (check) check.classList.add('hidden');
            }
        });

        closeServerSelectionModal();
    }

    function quickDeposit(serverId, serverName, balanceFormatted) {
        let label = serverName + ' (Sisa: ' + balanceFormatted + ')';
        selectServerModal(serverId, label);
        document.getElementById('modalDeposit').classList.remove('hidden');
    }

    function formatRupiah(inputElement, rawInputId) {
        let value = inputElement.value.replace(/[^,\d]/g, '');
        let split = value.split(',');
        let sisa = split[0].length % 3;
        let rupiah = split[0].substr(0, sisa);
        let ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            let separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        rupiah = split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
        inputElement.value = rupiah;

        let rawValue = value.replace(/\./g, '');
        document.getElementById(rawInputId).value = rawValue;
    }

    function prepareForm(form) {
        let displayVal = document.getElementById('display_amount').value;
        let rawVal = displayVal.replace(/\./g, '');
        document.getElementById('raw_amount').value = rawVal;
    }

    function prepareServerForm(form) {
        let displayVal = document.getElementById('display_initial_balance').value;
        let rawVal = displayVal ? displayVal.replace(/\./g, '') : '0';
        document.getElementById('raw_initial_balance').value = rawVal;
    }
</script>
@endsection