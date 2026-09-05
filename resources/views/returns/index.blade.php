@extends('layouts.app')

@section('content')
<div class="space-y-3.5">
    
    <!-- 1. HEADER HALAMAN & TOMBOL TAMBAH RETUR -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-base sm:text-xl font-black text-slate-800 flex items-center">
                <i class="fa-solid fa-rotate-left text-indigo-600 mr-2 text-lg sm:text-xl"></i> Riwayat Retur & Penukaran Barang
            </h1>
            <p class="text-[11px] sm:text-xs text-slate-500 font-medium mt-0.5">Kelola data pengembalian produk dan penukaran barang dari customer.</p>
        </div>

        <div class="flex items-center shrink-0">
            <a href="{{ route('returns.create') }}" class="bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white text-xs px-4 py-2 rounded-xl font-extrabold transition shadow-md shadow-indigo-200 flex items-center space-x-1.5 whitespace-nowrap">
                <i class="fa-solid fa-plus text-xs"></i>
                <span>Proses Retur Baru</span>
            </a>
        </div>
    </div>

    <!-- 2. TABEL RIWAYAT RETUR -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
        <div class="overflow-x-auto no-scrollbar">
            <table class="w-full text-left border-collapse min-w-[700px]">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200/80 text-slate-400 text-[10px] sm:text-xs uppercase font-extrabold">
                        <th class="py-2.5 px-3.5">Kode Retur / Waktu</th>
                        <th class="py-2.5 px-3.5">Nota Asli</th>
                        <th class="py-2.5 px-3.5">Kasir / Shift</th>
                        <th class="py-2.5 px-3.5">Alasan Retur</th>
                        <th class="py-2.5 px-3.5 text-right">Nilai Tukar</th>
                        <th class="py-2.5 px-3.5 text-center">Selisih Kas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs sm:text-sm font-medium text-slate-700">
                    @forelse($returns as $ret)
                        <tr class="hover:bg-indigo-50/30 transition">
                            <!-- KODE RETUR / WAKTU -->
                            <td class="py-2.5 px-3.5">
                                <div class="font-mono font-bold text-slate-800 text-xs">{{ $ret->return_code }}</div>
                                <div class="text-[10px] text-slate-400 mt-0.5 whitespace-nowrap">
                                    <i class="fa-regular fa-clock mr-1"></i>{{ $ret->created_at->format('d M Y, H:i') }} WIB
                                </div>
                            </td>

                            <!-- NOTA ASLI -->
                            <td class="py-2.5 px-3.5 whitespace-nowrap">
                                <span class="font-mono font-bold text-indigo-600 text-[11px] bg-indigo-50 px-2 py-0.5 rounded-md border border-indigo-100 inline-block">
                                    {{ $ret->transaction->invoice_code ?? '-' }}
                                </span>
                            </td>

                            <!-- KASIR / SHIFT -->
                            <td class="py-2.5 px-3.5 whitespace-nowrap">
                                <div class="text-xs font-bold text-slate-700">{{ $ret->user->name ?? 'Kasir' }}</div>
                                <span class="text-[10px] text-slate-400 font-mono border border-slate-200 bg-slate-50 px-1.5 py-0.5 rounded-md inline-block mt-0.5">Shift #{{ $ret->shift_id }}</span>
                            </td>

                            <!-- ALASAN RETUR -->
                            <td class="py-2.5 px-3.5">
                                <span class="text-xs text-slate-600 font-medium line-clamp-1" title="{{ $ret->reason }}">{{ $ret->reason }}</span>
                            </td>

                            <!-- NILAI TUKAR -->
                            <td class="py-2.5 px-3.5 text-right font-mono text-xs whitespace-nowrap">
                                <div class="text-rose-600 font-bold">Retur: Rp {{ number_format($ret->returned_total, 0, ',', '.') }}</div>
                                <div class="text-emerald-600 font-bold">Baru: Rp {{ number_format($ret->replacement_total, 0, ',', '.') }}</div>
                            </td>

                            <!-- SELISIH KAS -->
                            <td class="py-2.5 px-3.5 text-center font-mono whitespace-nowrap">
                                @if($ret->price_difference > 0)
                                    <span class="bg-emerald-50 text-emerald-700 border border-emerald-200 font-bold text-[10px] px-2 py-0.5 rounded-full inline-block">
                                        +Rp {{ number_format($ret->price_difference, 0, ',', '.') }} (Bayar)
                                    </span>
                                @elseif($ret->price_difference < 0)
                                    <span class="bg-rose-50 text-rose-700 border border-rose-200 font-bold text-[10px] px-2 py-0.5 rounded-full inline-block">
                                        -Rp {{ number_format(abs($ret->price_difference), 0, ',', '.') }} (Refund)
                                    </span>
                                @else
                                    <span class="bg-slate-100 text-slate-600 border border-slate-200 font-bold text-[10px] px-2 py-0.5 rounded-full inline-block">
                                        Imbang (Rp 0)
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-10 text-center text-slate-400 text-xs font-medium">
                                <i class="fa-solid fa-rotate-left text-3xl mb-2 text-slate-300 block"></i>
                                <p class="text-xs font-bold text-slate-600">Belum ada riwayat retur barang.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3 border-t border-slate-100 bg-slate-50">
            {{ $returns->links() }}
        </div>
    </div>

</div>
@endsection