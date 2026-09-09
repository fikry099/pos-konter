@extends('layouts.app')

@section('content')
<div class="space-y-4 pb-16">

    <!-- HEADER PAGE & FILTER PERIODE -->
    <div class="flex flex-col gap-3 bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-base shrink-0 border border-indigo-100">
                    <i class="fa-solid fa-calculator"></i>
                </div>
                <div>
                    <h2 class="text-base sm:text-lg font-black text-slate-800">Pembukuan & Rekap Bonus</h2>
                    <p class="text-[11px] text-slate-400 font-medium">Laba/Rugi Bersih & Komisi Aksesoris (Rp 1.000 / Pcs)</p>
                </div>
            </div>

            <!-- TOMBOL BUKA MODAL REKAP ABSENSI -->
            <button type="button" onclick="openAttendanceModal()" class="bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 px-3.5 py-2 rounded-xl text-xs font-extrabold transition flex items-center justify-center space-x-2 cursor-pointer shadow-xs">
                <i class="fa-solid fa-clock-rotate-left text-xs"></i>
                <span>Rekap Absensi Karyawan</span>
            </button>
        </div>

        <!-- FORM FILTER BULAN & TAHUN -->
        <form action="{{ route('owner.bookkeeping') }}" method="GET" class="grid grid-cols-3 gap-2 pt-2 border-t border-slate-100">
            <select name="month" class="bg-slate-50 border border-slate-200 rounded-xl px-2.5 py-2 text-xs font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 cursor-pointer">
                @for($m = 1; $m <= 12; $m++)
                    @php $mVal = str_pad($m, 2, '0', STR_PAD_LEFT); @endphp
                    <option value="{{ $mVal }}" {{ $month == $mVal ? 'selected' : '' }}>
                        {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                    </option>
                @endfor
            </select>

            <select name="year" class="bg-slate-50 border border-slate-200 rounded-xl px-2.5 py-2 text-xs font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 cursor-pointer">
                @for($y = date('Y'); $y >= date('Y') - 2; $y--)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>

            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-2 rounded-xl text-xs font-bold transition shadow-xs cursor-pointer flex items-center justify-center space-x-1">
                <i class="fa-solid fa-filter text-[10px]"></i>
                <span>Filter</span>
            </button>
        </form>
    </div>

    <!-- WIDGET RINGKASAN PEMBUKUAN KEUANGAN & PEMBAYARAN -->
    <div class="space-y-3">
        <!-- BARIS 1: WIDGET UTAMA (4 CARD) -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
            <!-- TOTAL OMSET -->
            <div class="bg-white p-3.5 sm:p-4 rounded-2xl border border-slate-200 shadow-xs space-y-1">
                <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block truncate">Total Omset</span>
                <div class="text-sm sm:text-lg font-black text-slate-800 font-mono truncate">Rp {{ number_format($financialSummary['total_omset'], 0, ',', '.') }}</div>
                <span class="text-[9px] text-slate-400 block truncate">Pendapatan Kotor</span>
            </div>

            <!-- LABA KOTOR -->
            <div class="bg-white p-3.5 sm:p-4 rounded-2xl border border-slate-200 shadow-xs space-y-1">
                <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block truncate">Laba Kotor (Gross)</span>
                <div class="text-sm sm:text-lg font-black text-indigo-600 font-mono truncate">Rp {{ number_format($financialSummary['gross_profit'], 0, ',', '.') }}</div>
                <span class="text-[9px] text-slate-400 block truncate">Omset − HPP Barang</span>
            </div>

            <!-- PENGELUARAN & ALOKASI BONUS -->
            <div class="bg-white p-3.5 sm:p-4 rounded-2xl border border-slate-200 shadow-xs space-y-1">
                <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block truncate">Beban & Bonus</span>
                <div class="text-sm sm:text-lg font-black text-rose-600 font-mono truncate">
                    Rp {{ number_format($financialSummary['total_expenses'] + $financialSummary['total_bonus_allocation'], 0, ',', '.') }}
                </div>
                <div class="text-[9px] text-slate-500 font-medium truncate">
                    Ops: Rp {{ number_format($financialSummary['total_expenses'], 0, ',', '.') }} | B: Rp {{ number_format($financialSummary['total_bonus_allocation'], 0, ',', '.') }}
                </div>
            </div>

            <!-- LABA BERSIH OWNER -->
            <div class="bg-gradient-to-br from-indigo-600 to-purple-700 p-3.5 sm:p-4 rounded-2xl text-white shadow-md shadow-indigo-600/20 space-y-1">
                <span class="text-[10px] font-extrabold text-indigo-200 uppercase tracking-wider block truncate">Laba Bersih (Net)</span>
                <div class="text-sm sm:text-lg font-black font-mono truncate">Rp {{ number_format($financialSummary['net_profit'], 0, ',', '.') }}</div>
                <span class="text-[9px] text-indigo-100 block truncate">Bersih Diterima Owner</span>
            </div>
        </div>

        <!-- BARIS 2: RINCIAN SALDO MASUK (TUNAI / LACI vs QRIS / REKENING) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <!-- CARD TOTAL TUNAI (CASH) -->
            <div class="bg-emerald-50/50 p-3.5 sm:p-4 rounded-2xl border border-emerald-200/80 shadow-xs flex items-center justify-between">
                <div class="space-y-0.5">
                    <span class="text-[10px] font-extrabold text-emerald-800 uppercase tracking-wider block flex items-center">
                        <i class="fa-solid fa-money-bill-wave text-emerald-600 mr-1.5"></i> Total Uang Tunai (Laci Kasir)
                    </span>
                    <div class="text-base sm:text-xl font-black text-emerald-900 font-mono">
                        Rp {{ number_format($financialSummary['total_cash'] ?? 0, 0, ',', '.') }}
                    </div>
                    <span class="text-[10px] text-emerald-700/80 font-medium block">Fisik uang yang wajib ada di laci konter</span>
                </div>
                <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-lg shrink-0 border border-emerald-200">
                    <i class="fa-solid fa-wallet"></i>
                </div>
            </div>

            <!-- CARD TOTAL SALDO QRIS / TRANSFER -->
            <div class="bg-purple-50/50 p-3.5 sm:p-4 rounded-2xl border border-purple-200/80 shadow-xs flex items-center justify-between">
                <div class="space-y-0.5">
                    <span class="text-[10px] font-extrabold text-purple-800 uppercase tracking-wider block flex items-center">
                        <i class="fa-solid fa-qrcode text-purple-600 mr-1.5"></i> Total Saldo Masuk QRIS / Non-Tunai
                    </span>
                    <div class="text-base sm:text-xl font-black text-purple-900 font-mono">
                        Rp {{ number_format($financialSummary['total_qris'] ?? 0, 0, ',', '.') }}
                    </div>
                    <span class="text-[10px] text-purple-700/80 font-medium block">Uang masuk langsung ke rekening / E-Wallet</span>
                </div>
                <div class="w-10 h-10 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center text-lg shrink-0 border border-purple-200">
                    <i class="fa-solid fa-building-columns"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- TABEL REKAP BONUS & PERFORMA PENJUALAN KARYAWAN -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden space-y-2">
        <div class="p-3.5 sm:p-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div>
                <h3 class="text-xs sm:text-sm font-black text-slate-800">Rekapitulasi Bonus Aksesoris</h3>
                <p class="text-[10px] text-slate-400 font-medium">Komisi Rp 1.000 / pcs aksesoris terjual per karyawan</p>
            </div>
            <span class="bg-indigo-50 text-indigo-700 text-[10px] font-bold px-2.5 py-1 rounded-xl border border-indigo-100 self-start sm:self-auto">
                Rate: Rp 1.000 / Pcs
            </span>
        </div>

        <div class="overflow-x-auto no-scrollbar">
            <table class="w-full text-left border-collapse min-w-[650px] text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-400 text-[10px] uppercase font-extrabold">
                        <th class="py-3 px-3 pl-4">Nama Karyawan</th>
                        <th class="py-3 px-3 text-center">Kedisiplinan</th>
                        <th class="py-3 px-3 text-center">Qty Jual</th>
                        <th class="py-3 px-3 text-right">Omset Aksesoris</th>
                        <th class="py-3 px-3 text-center">Rate</th>
                        <th class="py-3 px-3 text-right pr-4">Total Komisi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($employeeReport as $emp)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="py-3 px-3 pl-4">
                                <button type="button" onclick="openUserAttendanceDetail({{ $emp['user_id'] }})" class="font-extrabold text-indigo-600 hover:text-indigo-800 text-xs text-left cursor-pointer underline">
                                    {{ $emp['name'] }}
                                </button>
                                <span class="text-[9px] text-slate-400 uppercase font-semibold block">{{ $emp['role'] }}</span>
                            </td>
                            <td class="py-3 px-3 text-center">
                                <div class="flex items-center justify-center space-x-1">
                                    <span class="bg-emerald-50 text-emerald-700 border border-emerald-200 font-bold px-2 py-0.5 rounded-md text-[10px]" title="Hadir Tepat Waktu">
                                        ⚡ {{ $emp['total_ontime'] }}x Tepat
                                    </span>
                                    @if($emp['total_late'] > 0)
                                        <span class="bg-rose-50 text-rose-700 border border-rose-200 font-bold px-2 py-0.5 rounded-md text-[10px]" title="Terlambat">
                                            {{ $emp['total_late'] }}x Telat
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-3 px-3 text-center">
                                <span class="bg-amber-50 text-amber-800 font-black px-2 py-0.5 rounded-lg border border-amber-200 text-[11px]">
                                    {{ $emp['accessories_qty'] }} Pcs
                                </span>
                            </td>
                            <td class="py-3 px-3 text-right font-mono font-bold text-slate-800">
                                Rp {{ number_format($emp['accessories_omset'], 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-3 text-center text-[11px] font-bold text-slate-500">
                                Rp {{ number_format($emp['bonus_rate'], 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-3 text-right pr-4 font-mono font-black text-emerald-600 text-sm">
                                +Rp {{ number_format($emp['total_bonus'], 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-10 text-center text-slate-400">
                                <i class="fa-solid fa-users-slash text-3xl mb-1 text-slate-300 block"></i>
                                <span class="text-xs font-bold text-slate-500">Belum ada data shift atau penjualan pada periode ini.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- CARD ACTION: PEMISAHAN REKAP HISTORI RESTOK -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 transition hover:border-indigo-200">
        <div class="flex items-center space-x-3.5">
            <div class="w-11 h-11 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-lg shrink-0 border border-indigo-100">
                <i class="fa-solid fa-boxes-packing"></i>
            </div>
            <div>
                <h4 class="font-extrabold text-slate-800 text-xs sm:text-sm">Histori Restok & Reorder Barang</h4>
                <p class="text-[11px] text-slate-400 font-medium mt-0.5">
                    Estimasi Modal Restok Periode Ini: 
                    <span class="font-mono font-bold text-indigo-600">Rp {{ number_format($totalRestockCost ?? 0, 0, ',', '.') }}</span>
                </p>
            </div>
        </div>

        <a href="{{ route('owner.restock.history', ['month' => $month, 'year' => $year]) }}" 
           class="w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white px-4 py-2.5 rounded-xl text-xs font-extrabold transition shadow-xs flex items-center justify-center space-x-2 shrink-0 cursor-pointer">
            <i class="fa-solid fa-list-check"></i>
            <span>Buka Tabel Histori Restok</span>
            <i class="fa-solid fa-arrow-right text-[10px]"></i>
        </a>
    </div>

</div>
@endsection

@push('scripts')
<!-- MODAL REKAP ABSENSI KARYAWAN -->
<div id="attendance_modal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 transition-all duration-300">
    <div class="bg-white w-full max-w-2xl rounded-3xl shadow-2xl border border-slate-100 overflow-hidden flex flex-col max-h-[90vh]">
        
        <!-- HEADER MODAL -->
        <div class="p-4 bg-slate-900 text-white flex items-center justify-between shrink-0">
            <div class="flex items-center space-x-2.5">
                <div class="w-8 h-8 rounded-xl bg-indigo-600 flex items-center justify-center text-white text-xs font-bold shrink-0">
                    <i class="fa-solid fa-user-clock"></i>
                </div>
                <div>
                    <h3 class="font-extrabold text-sm sm:text-base leading-tight">Rekap Kehadiran & Absensi Karyawan</h3>
                    <p class="text-[10px] text-slate-400 font-medium">Batas Tepat Waktu: Pagi ≤ 07.05 | Sore ≤ 15.05</p>
                </div>
            </div>
            <button type="button" onclick="closeAttendanceModal()" class="text-slate-400 hover:text-white p-1 rounded-lg hover:bg-slate-800 transition cursor-pointer">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <!-- CONTENT MODAL: DAFTAR KARYAWAN -->
        <div class="p-4 overflow-y-auto space-y-2.5 flex-1">
            @forelse($employeeReport as $emp)
                <div id="user_att_box_{{ $emp['user_id'] }}" class="bg-slate-50 hover:bg-slate-100/80 rounded-2xl p-3 border border-slate-200/80 flex items-center justify-between transition">
                    <div class="flex items-center space-x-3">
                        <div class="w-9 h-9 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center font-black text-xs shrink-0">
                            {{ strtoupper(substr($emp['name'], 0, 2)) }}
                        </div>
                        <div>
                            <h4 class="font-extrabold text-slate-800 text-xs sm:text-sm">{{ $emp['name'] }}</h4>
                            <span class="text-[10px] text-slate-400 font-semibold uppercase block">{{ $emp['role'] }}</span>
                        </div>
                    </div>

                    <div class="flex items-center space-x-3">
                        <div class="hidden sm:flex items-center space-x-1.5 text-xs">
                            <span class="bg-emerald-50 text-emerald-700 border border-emerald-200 font-bold px-2 py-0.5 rounded-md text-[10px]">
                                ⚡ {{ $emp['total_ontime'] }}x Tepat
                            </span>
                            <span class="bg-rose-50 text-rose-700 border border-rose-200 font-bold px-2 py-0.5 rounded-md text-[10px]">
                                {{ $emp['total_late'] }}x Telat
                            </span>
                        </div>

                        <a href="{{ route('owner.attendances.detail', ['user' => $emp['user_id'], 'month' => $month, 'year' => $year]) }}" 
                           class="bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white text-xs font-bold px-3 py-1.5 rounded-xl transition flex items-center space-x-1.5 cursor-pointer shadow-xs">
                            <span>Lihat Detail</span>
                            <i class="fa-solid fa-arrow-right text-[10px]"></i>
                        </a>
                    </div>
                </div>
            @empty
                <div class="text-center py-8 text-slate-400 text-xs">
                    Tidak ada data karyawan ditemukan.
                </div>
            @endforelse
        </div>

    </div>
</div>

<script>
    function openAttendanceModal() {
        document.getElementById('attendance_modal').classList.remove('hidden');
    }

    function closeAttendanceModal() {
        document.getElementById('attendance_modal').classList.add('hidden');
    }

    function openUserAttendanceDetail(userId) {
        openAttendanceModal();
        let targetEl = document.getElementById('user_att_box_' + userId);
        if (targetEl) {
            targetEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
            targetEl.classList.add('ring-2', 'ring-indigo-500');
            setTimeout(() => {
                targetEl.classList.remove('ring-2', 'ring-indigo-500');
            }, 2000);
        }
    }
</script>
@endpush