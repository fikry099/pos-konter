@extends('layouts.app')

@section('content')
<div class="space-y-4">

    <!-- HEADER HALAMAN & TOTAL SELISIH -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
        <div>
            <h1 class="text-base sm:text-xl font-black text-slate-800 flex items-center">
                <i class="fa-solid fa-clipboard-user text-indigo-600 mr-2 text-lg sm:text-xl"></i> Monitoring & Audit Toko
            </h1>
            <p class="text-[11px] sm:text-xs text-slate-500 font-medium mt-0.5">Pantau absensi karyawan dan selisih uang kasir secara real-time</p>
        </div>

        <!-- CARD RINGKASAN TOTAL SELISIH KAS BULAN INI -->
        <div class="bg-slate-50 p-3 rounded-xl border border-slate-200/80 flex items-center space-x-3 shrink-0">
            <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-sm border border-amber-200/60 shrink-0">
                <i class="fa-solid fa-scale-balanced"></i>
            </div>
            <div>
                <p class="text-[9px] font-black uppercase text-slate-400 tracking-wider">Total Selisih (Bulan Ini)</p>
                <p class="text-sm sm:text-base font-black font-mono {{ $totalSelisihBulanIni < 0 ? 'text-rose-600' : ($totalSelisihBulanIni > 0 ? 'text-emerald-600' : 'text-slate-800') }}">
                    Rp {{ number_format($totalSelisihBulanIni ?? 0, 0, ',', '.') }}
                </p>
            </div>
        </div>
    </div>

    <!-- 1. STATUS SHIFT TOKO SAAT INI (COMPACT SINGLE CARD) -->
    <div class="bg-white p-3.5 sm:p-4 rounded-2xl border border-slate-200 shadow-sm space-y-2.5">
        <div class="flex items-center justify-between border-b border-slate-100 pb-2">
            <h2 class="text-[10px] font-black text-slate-400 uppercase tracking-wider flex items-center space-x-2">
                <span class="w-2 h-2 rounded-full {{ $activeShift ? 'bg-emerald-500 animate-ping' : 'bg-rose-500' }}"></span>
                <span>Status Shift Toko Saat Ini</span>
            </h2>
            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold {{ $activeShift ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                {{ $activeShift ? 'Toko Buka (Aktif)' : 'Toko Tutup' }}
            </span>
        </div>

        @if($activeShift)
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                <!-- KASIR BERTUGAS -->
                <div class="flex items-center space-x-2.5 bg-indigo-50/50 p-2.5 rounded-xl border border-indigo-100">
                    <div class="w-8 h-8 rounded-lg bg-indigo-600 text-white font-black text-xs flex items-center justify-center shrink-0 shadow-sm">
                        {{ strtoupper(substr($activeShift->staff_names ?? 'K', 0, 2)) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-[9px] font-bold text-indigo-400 uppercase tracking-wider truncate">Kasir Bertugas</p>
                        <p class="font-extrabold text-slate-800 text-xs truncate">{{ $activeShift->staff_names }}</p>
                    </div>
                </div>

                <!-- MODAL KAS AWAL -->
                <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-200/60 flex items-center justify-between sm:block">
                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Modal Kas Awal</p>
                    <p class="text-xs sm:text-sm font-black font-mono text-slate-800 sm:mt-0.5">
                        Rp {{ number_format($activeShift->cash_initial ?? 0, 0, ',', '.') }}
                    </p>
                </div>

                <!-- ESTIMASI KAS DI LACI -->
                <div class="bg-purple-50/50 p-2.5 rounded-xl border border-purple-100 flex items-center justify-between sm:block">
                    <p class="text-[9px] font-bold text-purple-400 uppercase tracking-wider">Estimasi Kas di Laci</p>
                    <p class="text-xs sm:text-sm font-black font-mono text-purple-700 sm:mt-0.5">
                        Rp {{ number_format(($activeShift->cash_initial ?? 0) + ($currentCashSales ?? 0) - ($currentExpenses ?? 0), 0, ',', '.') }}
                    </p>
                </div>
            </div>
        @else
            <div class="py-3 text-center text-slate-400">
                <i class="fa-solid fa-store-slash text-xl mb-1 text-slate-300"></i>
                <p class="text-xs font-bold text-slate-500">Saat ini belum ada kasir yang membuka shift toko.</p>
            </div>
        @endif
    </div>

    <!-- 2. RIWAYAT ABSENSI & AUDIT LACI KASIR -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-3.5 sm:p-4 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-xs sm:text-sm font-black text-slate-800 flex items-center space-x-2">
                <i class="fa-solid fa-user-clock text-indigo-600"></i>
                <span>Riwayat Absensi & Audit Laci Kasir</span>
            </h2>
        </div>

        <div class="overflow-x-auto no-scrollbar">
            <table class="w-full text-left border-collapse min-w-[700px]">
                <thead>
                    <tr class="bg-slate-50 text-[10px] font-extrabold uppercase text-slate-400 border-b border-slate-200">
                        <th class="py-2.5 px-3">Nama Staff</th>
                        <th class="py-2.5 px-3">Jam Shift</th>
                        <th class="py-2.5 px-3">Modal Awal</th>
                        <th class="py-2.5 px-3">Penjualan Tunai</th>
                        <th class="py-2.5 px-3">Pengeluaran</th>
                        <th class="py-2.5 px-3">Uang Fisik</th>
                        <th class="py-2.5 px-3">Selisih</th>
                        <th class="py-2.5 px-3 text-center">Status</th>
                        <th class="py-2.5 px-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
                    @forelse($shifts as $shift)
                        @php
                            $cashSales     = $shift->total_sales;
                            $shiftExpenses = $shift->total_expenses;
                            $expected      = $shift->calculateExpectedCash();
                            $actual        = $shift->cash_actual;
                            $difference    = $shift->status === 'closed' ? ($actual - $expected) : null;
                            $photoUrl      = $shift->photo_url ?? '';
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-2.5 px-3">
                                <div class="font-extrabold text-slate-900 text-xs">{{ $shift->staff_names }}</div>
                                <div class="text-[9px] text-slate-400 font-bold">{{ $shift->created_at->format('d M Y') }}</div>
                            </td>

                            <td class="py-2.5 px-3 font-mono text-xs whitespace-nowrap">
                                <span class="font-bold text-slate-800">{{ $shift->created_at->format('H:i') }}</span>
                                <span class="text-slate-400"> &rsaquo; </span>
                                @if($shift->status === 'closed')
                                    <span class="font-bold text-slate-800">{{ $shift->updated_at->format('H:i') }}</span>
                                @else
                                    <span class="text-emerald-600 font-black">Aktif</span>
                                @endif
                            </td>

                            <td class="py-2.5 px-3 font-mono text-slate-600 whitespace-nowrap">
                                Rp {{ number_format($shift->cash_initial ?? 0, 0, ',', '.') }}
                            </td>

                            <td class="py-2.5 px-3 font-mono text-indigo-600 font-bold whitespace-nowrap">
                                +Rp {{ number_format($cashSales, 0, ',', '.') }}
                            </td>

                            <td class="py-2.5 px-3 font-mono text-rose-500 whitespace-nowrap">
                                -Rp {{ number_format($shiftExpenses, 0, ',', '.') }}
                            </td>

                            <td class="py-2.5 px-3 font-mono font-bold text-slate-800 whitespace-nowrap">
                                @if($shift->status === 'closed')
                                    Rp {{ number_format($actual, 0, ',', '.') }}
                                @else
                                    <span class="text-slate-400 font-normal">-</span>
                                @endif
                            </td>

                            <td class="py-2.5 px-3 font-mono font-bold whitespace-nowrap">
                                @if($shift->status === 'closed')
                                    @if($difference < 0)
                                        <span class="text-rose-600">Rp {{ number_format($difference, 0, ',', '.') }} (Minus)</span>
                                    @elseif($difference > 0)
                                        <span class="text-emerald-600">+Rp {{ number_format($difference, 0, ',', '.') }} (Plus)</span>
                                    @else
                                        <span class="text-slate-500">Rp 0 (Pas)</span>
                                    @endif
                                @else
                                    <span class="text-slate-400 font-normal">-</span>
                                @endif
                            </td>

                            <td class="py-2.5 px-3 text-center whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase {{ $shift->status === 'closed' ? 'bg-slate-100 text-slate-600' : 'bg-emerald-100 text-emerald-700' }}">
                                    {{ $shift->status === 'closed' ? 'Selesai' : 'Aktif' }}
                                </span>
                            </td>

                            <td class="py-2.5 px-3 text-center whitespace-nowrap">
                                <button type="button" 
                                    onclick="showShiftDetail({
                                        name: '{{ addslashes($shift->staff_names) }}',
                                        date: '{{ $shift->created_at->format('d M Y, H:i') }} WIB',
                                        closeDate: '{{ $shift->status === 'closed' ? $shift->updated_at->format('d M Y, H:i') . ' WIB' : 'Masih Aktif' }}',
                                        startCash: 'Rp {{ number_format($shift->cash_initial ?? 0, 0, ',', '.') }}',
                                        cashSales: 'Rp {{ number_format($cashSales, 0, ',', '.') }}',
                                        expenses: 'Rp {{ number_format($shiftExpenses, 0, ',', '.') }}',
                                        expected: 'Rp {{ number_format($expected, 0, ',', '.') }}',
                                        actual: '{{ $shift->status === 'closed' ? 'Rp ' . number_format($actual, 0, ',', '.') : '-' }}',
                                        difference: '{{ $shift->status === 'closed' ? 'Rp ' . number_format($difference, 0, ',', '.') : '-' }}',
                                        photo: '{{ $photoUrl }}',
                                        status: '{{ $shift->status }}'
                                    })"
                                    class="bg-indigo-50 hover:bg-indigo-600 text-indigo-600 hover:text-white px-2 py-1 rounded-lg text-[11px] font-extrabold transition cursor-pointer inline-flex items-center space-x-1">
                                    <i class="fa-solid fa-eye text-[10px]"></i>
                                    <span>Detail</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-8 text-center text-slate-400">
                                <i class="fa-solid fa-clock-rotate-left text-2xl mb-1 text-slate-300"></i>
                                <p class="text-xs font-bold text-slate-500">Belum ada riwayat shift kasir tercatat.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- 3. LOG PENGELUARAN KAS TOKO -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-3.5 sm:p-4 border-b border-slate-100">
            <h2 class="text-xs sm:text-sm font-black text-slate-800 flex items-center space-x-2">
                <i class="fa-solid fa-wallet text-indigo-600"></i>
                <span>Log Pengeluaran Kas Toko (Operasional)</span>
            </h2>
        </div>

        <div class="overflow-x-auto no-scrollbar">
            <table class="w-full text-left border-collapse min-w-[550px]">
                <thead>
                    <tr class="bg-slate-50 text-[10px] font-extrabold uppercase text-slate-400 border-b border-slate-200">
                        <th class="py-2.5 px-3">Waktu</th>
                        <th class="py-2.5 px-3">Dicatat Oleh</th>
                        <th class="py-2.5 px-3">Keterangan</th>
                        <th class="py-2.5 px-3 text-right">Jumlah Uang</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
                    @forelse($expenses as $expense)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-2.5 px-3 font-mono text-slate-500 whitespace-nowrap">
                                {{ $expense->created_at->format('d M Y, H:i') }}
                            </td>
                            <td class="py-2.5 px-3 font-bold text-slate-800 whitespace-nowrap">
                                {{ $expense->user->name ?? 'Kasir' }}
                            </td>
                            <td class="py-2.5 px-3 text-slate-600 font-medium">
                                {{ $expense->description ?? '-' }}
                            </td>
                            <td class="py-2.5 px-3 font-mono font-bold text-rose-600 text-right whitespace-nowrap">
                                -Rp {{ number_format($expense->amount ?? 0, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-8 text-center text-slate-400">
                                <i class="fa-solid fa-receipt text-2xl mb-1 text-slate-300"></i>
                                <p class="text-xs font-bold text-slate-500">Belum ada pengeluaran kas yang dicatat.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- MODAL POPUP DETAIL AUDIT SHIFT -->
<div id="detail_shift_modal" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4 hidden transition-all duration-300">
    <div class="bg-white rounded-3xl max-w-lg w-full p-4 sm:p-6 shadow-2xl border border-slate-100 space-y-3.5 transform transition-all max-h-[90vh] overflow-y-auto no-scrollbar">
        
        <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
            <h3 class="text-xs sm:text-sm font-black text-slate-800 flex items-center">
                <i class="fa-solid fa-file-invoice text-indigo-600 mr-2"></i> Detail Audit Shift Kasir
            </h3>
            <button type="button" onclick="closeShiftDetail()" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-xl hover:bg-slate-100 transition cursor-pointer">
                <i class="fa-solid fa-xmark text-base"></i>
            </button>
        </div>

        <div class="space-y-3">
            <!-- FOTO BUKTI ABSENSI -->
            <div>
                <p class="text-[9px] font-extrabold uppercase text-slate-400 tracking-wider mb-1.5">Foto Bukti Buka Shift / Absensi</p>
                <div id="modal_photo_wrapper" class="w-full h-40 sm:h-48 rounded-xl overflow-hidden bg-slate-100 border border-slate-200 flex items-center justify-center relative">
                    <img id="modal_photo_img" src="" alt="Foto Absensi Kasir" class="w-full h-full object-cover hidden">
                    <div id="modal_photo_empty" class="text-center p-3 text-slate-400">
                        <i class="fa-solid fa-image text-2xl mb-1 text-slate-300"></i>
                        <p class="text-[10px] font-bold text-slate-400">Tidak ada lampiran foto absensi</p>
                    </div>
                </div>
            </div>

            <!-- RINCIAN DATA SHIFT -->
            <div class="grid grid-cols-2 gap-2 bg-slate-50 p-3 rounded-xl border border-slate-100 text-xs">
                <div>
                    <p class="text-[9px] text-slate-400 font-bold uppercase">Nama Kasir</p>
                    <p id="modal_kasir_name" class="font-extrabold text-slate-800 text-xs mt-0.5 truncate">-</p>
                </div>
                <div>
                    <p class="text-[9px] text-slate-400 font-bold uppercase">Waktu Buka</p>
                    <p id="modal_start_date" class="font-bold text-slate-700 mt-0.5 truncate">-</p>
                </div>
                <div>
                    <p class="text-[9px] text-slate-400 font-bold uppercase">Waktu Tutup</p>
                    <p id="modal_close_date" class="font-bold text-slate-700 mt-0.5 truncate">-</p>
                </div>
                <div>
                    <p class="text-[9px] text-slate-400 font-bold uppercase">Modal Kas Awal</p>
                    <p id="modal_start_cash" class="font-mono font-extrabold text-slate-800 mt-0.5 truncate">-</p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-2 bg-indigo-50/50 p-3 rounded-xl border border-indigo-100 text-xs">
                <div>
                    <p class="text-[9px] text-indigo-400 font-bold uppercase">Penjualan Tunai</p>
                    <p id="modal_cash_sales" class="font-mono font-extrabold text-indigo-700 mt-0.5 truncate">-</p>
                </div>
                <div>
                    <p class="text-[9px] text-indigo-400 font-bold uppercase">Pengeluaran Kas</p>
                    <p id="modal_expenses" class="font-mono font-extrabold text-rose-600 mt-0.5 truncate">-</p>
                </div>
                <div>
                    <p class="text-[9px] text-indigo-400 font-bold uppercase">Estimasi Harus Ada</p>
                    <p id="modal_expected" class="font-mono font-extrabold text-slate-800 mt-0.5 truncate">-</p>
                </div>
                <div>
                    <p class="text-[9px] text-indigo-400 font-bold uppercase">Fisik Uang Kasir</p>
                    <p id="modal_actual" class="font-mono font-extrabold text-slate-800 mt-0.5 truncate">-</p>
                </div>
            </div>

            <!-- SELISIH KAS -->
            <div class="p-3 rounded-xl border border-slate-200 flex items-center justify-between bg-slate-50 text-xs">
                <span class="font-extrabold text-slate-700 uppercase text-[10px]">Selisih Kasir:</span>
                <span id="modal_difference" class="font-mono text-xs sm:text-sm font-black">-</span>
            </div>
        </div>

        <button type="button" onclick="closeShiftDetail()" class="w-full bg-slate-900 hover:bg-slate-800 text-white font-extrabold py-2.5 rounded-xl text-xs transition cursor-pointer">
            Tutup Detail
        </button>
    </div>
</div>

<script>
    function showShiftDetail(data) {
        document.getElementById('modal_kasir_name').innerText = data.name;
        document.getElementById('modal_start_date').innerText = data.date;
        document.getElementById('modal_close_date').innerText = data.closeDate;
        document.getElementById('modal_start_cash').innerText = data.startCash;
        document.getElementById('modal_cash_sales').innerText = data.cashSales;
        document.getElementById('modal_expenses').innerText = data.expenses;
        document.getElementById('modal_expected').innerText = data.expected;
        document.getElementById('modal_actual').innerText = data.actual;
        
        const diffEl = document.getElementById('modal_difference');
        diffEl.innerText = data.difference;
        
        const photoImg = document.getElementById('modal_photo_img');
        const photoEmpty = document.getElementById('modal_photo_empty');

        if (data.photo && data.photo.trim() !== '') {
            photoImg.src = data.photo;
            photoImg.classList.remove('hidden');
            photoEmpty.classList.add('hidden');
        } else {
            photoImg.src = '';
            photoImg.classList.add('hidden');
            photoEmpty.classList.remove('hidden');
        }

        document.getElementById('detail_shift_modal').classList.remove('hidden');
    }

    function closeShiftDetail() {
        document.getElementById('detail_shift_modal').classList.add('hidden');
    }
</script>
@endsection