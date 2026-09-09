@extends('layouts.app')

@section('content')
<div class="space-y-4 pb-16">

    <!-- HEADER PAGE & TOMBOL KEMBALI -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="flex items-center space-x-3">
            <a href="{{ route('owner.bookkeeping', ['month' => $month, 'year' => $year]) }}" 
               class="w-10 h-10 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center transition shrink-0 cursor-pointer border border-slate-200">
                <i class="fa-solid fa-arrow-left text-sm"></i>
            </a>
            <div>
                <h2 class="text-base sm:text-lg font-black text-slate-800">Riwayat Restok & Purchasing Barang</h2>
                <p class="text-[11px] text-slate-400 font-medium">Rekap penambahan stok fisik & voucher oleh karyawan/owner</p>
            </div>
        </div>

        <div class="bg-indigo-50 text-indigo-700 text-xs font-black px-3.5 py-2 rounded-xl border border-indigo-100 self-start sm:self-auto font-mono">
            Total Modal PO Periode Ini: Rp {{ number_format($totalRestockCost, 0, ',', '.') }}
        </div>
    </div>

    <!-- FILTER PERIODE & PENCARIAN -->
    <div class="bg-white p-3.5 rounded-2xl border border-slate-200 shadow-xs space-y-3">
        <form action="{{ route('owner.restock.history') }}" method="GET" class="flex flex-col sm:flex-row items-center justify-between gap-2.5">
            
            <div class="flex items-center space-x-2 w-full sm:w-auto">
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

                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3.5 py-2 rounded-xl text-xs font-bold transition shadow-xs cursor-pointer flex items-center space-x-1">
                    <i class="fa-solid fa-filter text-[10px]"></i>
                    <span>Filter</span>
                </button>
            </div>

            <div class="relative w-full sm:w-72">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama produk / kode SKU..." 
                       class="w-full pl-9 pr-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:bg-white font-medium text-slate-800 transition">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-slate-400 text-xs"></i>
            </div>
        </form>
    </div>

    <!-- TABEL RIWAYAT RESTOK -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto no-scrollbar">
            <table class="w-full text-left border-collapse min-w-[700px] text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-400 text-[10px] uppercase font-extrabold">
                        <th class="py-3 px-4">Waktu & Petugas</th>
                        <th class="py-3 px-4">Nama Produk / Item</th>
                        <th class="py-3 px-4 text-center">Tambahan Stok</th>
                        <th class="py-3 px-4 text-right">Harga Modal / Pcs</th>
                        <th class="py-3 px-4 text-right pr-4">Total Modal PO</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                    @forelse($restockHistories as $restock)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <div class="font-bold text-slate-800 text-xs">{{ $restock->user->name ?? 'Kasir Cabang' }}</div>
                                <div class="text-[10px] text-slate-400 font-mono mt-0.5">
                                    <i class="fa-regular fa-clock mr-1"></i>{{ $restock->created_at->format('d M Y, H:i') }}
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-extrabold text-slate-800">{{ $restock->product->name ?? 'Produk Dihapus' }}</span>
                                @if($restock->notes)
                                    <span class="block text-[10px] text-slate-400 italic">Ket: {{ $restock->notes }}</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <span class="bg-emerald-50 text-emerald-700 font-black px-2.5 py-1 rounded-lg border border-emerald-200 text-xs">
                                    +{{ $restock->qty_add }} Pcs
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono text-slate-600">
                                Rp {{ number_format($restock->cost_price, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-right pr-4 font-mono font-black text-indigo-700 text-xs sm:text-sm">
                                Rp {{ number_format($restock->total_cost, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-400">
                                <i class="fa-solid fa-box-open text-4xl mb-2 text-slate-300 block"></i>
                                <span class="text-xs font-bold text-slate-500">Belum ada riwayat restok barang pada periode bulan ini.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- PAGINATION BAR -->
        @if($restockHistories->hasPages())
            <div class="p-3.5 border-t border-slate-100 bg-slate-50/50">
                {{ $restockHistories->links() }}
            </div>
        @endif
    </div>

</div>
@endsection