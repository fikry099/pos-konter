@extends('layouts.app')

@section('content')
<div class="w-full space-y-4 pb-16">

    <!-- HEADER PAGE -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-base shrink-0 border border-indigo-100">
                <i class="fa-solid fa-wallet"></i>
            </div>
            <div>
                <h2 class="text-base sm:text-lg font-black text-slate-800">Pengeluaran & Pembelian Voucher Toko</h2>
                <p class="text-[11px] text-slate-400 font-medium">Rekapitulasi beban operasional & modal pembelian stok harian</p>
            </div>
        </div>

        <!-- RINGKASAN TOTAL PENGELUARAN BULAN INI -->
        <div class="bg-rose-50 border border-rose-200 px-4 py-2 rounded-xl flex items-center space-x-3">
            <div class="text-rose-600 text-base">
                <i class="fa-solid fa-money-bill-transfer"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold text-rose-800 uppercase block">Total Pengeluaran Bulan Ini</span>
                <span class="text-sm font-black font-mono text-rose-700">Rp {{ number_format($totalExpenses, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    <!-- KONTROL UTAMA: FORM INPUT & FILTER -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        
        <!-- KOLOM 1: FORM TAMBAH PENGELUARAN BARU -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs space-y-3">
            <h3 class="text-xs font-black text-slate-800 uppercase tracking-wider flex items-center">
                <i class="fa-solid fa-circle-plus text-indigo-600 mr-1.5"></i> Catat Pengeluaran Baru
            </h3>

            <form action="{{ route('owner.expenses.store') }}" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-[10px] font-extrabold text-slate-500 uppercase mb-1">Keterangan / Keperluan</label>
                    <input type="text" name="description" required autocomplete="off" placeholder="Misal: Bayar restok voucher / Isi saldo server" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-[10px] font-extrabold text-slate-500 uppercase mb-1">Nominal (Rp)</label>
                    <input type="number" name="amount" required min="1" placeholder="Contoh: 150000" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-xs font-bold font-mono text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white font-extrabold py-2.5 rounded-xl text-xs transition shadow-md shadow-indigo-200 cursor-pointer">
                    Simpan Pengeluaran
                </button>
            </form>
        </div>

        <!-- KOLOM 2 & 3: FILTER & TABEL RIWAYAT -->
        <div class="lg:col-span-2 space-y-4">
            
            <!-- FORM FILTER BULAN & TAHUN -->
            <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
                <form action="{{ route('owner.expenses.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-2 items-center">
                    <select name="month" class="bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 cursor-pointer">
                        @for($m = 1; $m <= 12; $m++)
                            @php $mVal = str_pad($m, 2, '0', STR_PAD_LEFT); @endphp
                            <option value="{{ $mVal }}" {{ $month == $mVal ? 'selected' : '' }}>
                                {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                            </option>
                        @endfor
                    </select>

                    <select name="year" class="bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 cursor-pointer">
                        @for($y = date('Y'); $y >= date('Y') - 2; $y--)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>

                    <button type="submit" class="bg-slate-800 hover:bg-black text-white px-4 py-2 rounded-xl text-xs font-bold transition shadow-xs cursor-pointer flex items-center justify-center space-x-1.5">
                        <i class="fa-solid fa-filter text-[10px]"></i>
                        <span>Filter Periode</span>
                    </button>
                </form>
            </div>

            <!-- TABEL DAFTAR PENGELUARAN -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="p-3.5 sm:p-4 border-b border-slate-100 font-bold text-xs text-slate-800">
                    Riwayat Pengeluaran Bulan {{ date('F', mktime(0, 0, 0, (int)$month, 1)) }} {{ $year }}
                </div>

                <div class="overflow-x-auto no-scrollbar">
                    <table class="w-full text-left border-collapse min-w-[500px] text-xs">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-slate-400 text-[10px] uppercase font-extrabold">
                                <th class="py-3 px-3 pl-4">Waktu</th>
                                <th class="py-3 px-3">Keterangan / Keperluan</th>
                                <th class="py-3 px-3">Pencatat</th>
                                <th class="py-3 px-3 text-right pr-4">Nominal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium">
                            @forelse($expenses as $exp)
                                <tr class="hover:bg-slate-50/70 transition">
                                    <td class="py-3 px-3 pl-4 font-mono text-slate-600 whitespace-nowrap">
                                        {{ $exp->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="py-3 px-3 text-slate-800 font-bold">
                                        {{ $exp->description }}
                                    </td>
                                    <td class="py-3 px-3 text-slate-600">
                                        {{ $exp->user->name ?? '-' }}
                                        <span class="text-[10px] text-slate-400 block font-normal">({{ ucfirst($exp->user->role ?? 'owner') }})</span>
                                    </td>
                                    <td class="py-3 px-3 text-right pr-4 font-mono font-black text-rose-600 text-sm whitespace-nowrap">
                                        -Rp {{ number_format($exp->amount, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-10 text-center text-slate-400">
                                        <i class="fa-solid fa-receipt text-3xl mb-1 text-slate-300 block"></i>
                                        <span class="text-xs font-bold text-slate-500">Belum ada catatan pengeluaran pada periode ini.</span>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($expenses->hasPages())
                    <div class="p-3 border-t border-slate-100 bg-slate-50">
                        {{ $expenses->links() }}
                    </div>
                @endif
            </div>

        </div>

    </div>

</div>
@endsection