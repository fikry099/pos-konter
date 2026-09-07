<div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
    <!-- WRAPPER DENGAN NO-SCROLLBAR AGAR BISA DIGESER MULUS DI TABLET -->
    <div class="overflow-x-auto no-scrollbar">
        <table class="w-full text-left border-collapse min-w-[720px] text-xs">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-slate-400 text-[10px] uppercase font-extrabold">
                    <th class="py-3 px-3 pl-4 whitespace-nowrap">Waktu & Nota</th>
                    <th class="py-3 px-3 whitespace-nowrap">Kasir / Shift</th>
                    <th class="py-3 px-3">Item Belanja / Target / Server</th>
                    <th class="py-3 px-3 text-center whitespace-nowrap">Metode</th>
                    <th class="py-3 px-3 text-right whitespace-nowrap">Tagihan</th>
                    
                    <!-- HANYA TAMPILKAN HEADER PROFIT JIKA ROLE OWNER -->
                    @if(auth()->check() && auth()->user()->role === 'owner')
                        <th class="py-3 px-3 text-right whitespace-nowrap">Profit</th>
                    @endif
                    
                    <th class="py-3 px-3 text-center pr-4 whitespace-nowrap">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
                @forelse($transactions as $trx)
                    <tr class="hover:bg-slate-50/70 transition">
                        <!-- WAKTU & KODE NOTA -->
                        <td class="py-3 px-3 pl-4 whitespace-nowrap align-top">
                            <div class="font-mono font-bold text-slate-800 text-xs">{{ $trx->invoice_code }}</div>
                            <div class="text-[10px] text-slate-400 mt-0.5">
                                <i class="fa-regular fa-clock mr-1"></i>{{ $trx->created_at->format('d M Y, H:i') }}
                            </div>
                        </td>

                        <!-- KASIR & SHIFT -->
                        <td class="py-3 px-3 whitespace-nowrap align-top">
                            @php
                                $staffNames = $trx->details
                                    ->pluck('servedBy.name')
                                    ->filter()
                                    ->unique()
                                    ->implode(', ');

                                $cashierDisplay = !empty($staffNames) ? $staffNames : ($trx->user->name ?? 'Kasir Cabang');
                            @endphp
                            <div class="font-bold text-slate-800">{{ $cashierDisplay }}</div>
                            <span class="bg-slate-100 text-slate-600 text-[9px] px-1.5 py-0.5 rounded font-mono border border-slate-200 inline-block mt-1">
                                Shift #{{ $trx->shift_id }}
                            </span>
                        </td>

                        <!-- ITEM BELANJA (MAX 2 ITEM DITAMPILKAN DI TABEL) -->
                        <td class="py-3 px-3 align-top">
                            @php
                                $allDetails = $trx->details ?? collect();
                                $totalItemsCount = $allDetails->count();
                                $limit = 2; // Batas maksimal item yang tampil langsung
                                $visibleDetails = $allDetails->take($limit);
                                $remainingCount = $totalItemsCount - $limit;
                            @endphp

                            <div class="space-y-2 min-w-[220px]">
                                @foreach($visibleDetails as $detail)
                                    @php
                                        $rawName = $detail->custom_name ? $detail->custom_name : ($detail->product->name ?? 'Produk');
                                        $nameLower = strtolower($rawName);

                                        // AMBIL SERVER ASLI LANGSUNG DARI DATABASE
                                        $serverBadge = $detail->digital_provider ?? '';

                                        // Ekstrak nama akun/catatan dalam kurung jika ada (misal: "(koncol)")
                                        $accSuffix = '';
                                        if (preg_match('/\(([^)]+)\)/', $rawName, $accMatch)) {
                                            $accSuffix = ' (' . trim($accMatch[1]) . ')';
                                        }

                                        $formattedAmount = number_format($detail->selling_price ?? $detail->subtotal, 0, ',', '.');

                                        // Format Nama Tampilan tanpa menimpa server asli
                                        $displayName = $rawName;

                                        $isTransferLabel = str_contains(strtolower($displayName), 'transfer') || str_contains(strtolower($displayName), 'top-up');
                                        $targetLabel = $isTransferLabel ? 'TUJUAN/REK' : 'NO';
                                    @endphp

                                    <div class="text-xs">
                                        <div class="flex items-center space-x-1.5">
                                            <span class="font-extrabold text-slate-800 truncate max-w-[280px]">{{ $displayName }}</span>
                                            <span class="text-slate-400 font-bold shrink-0">x{{ $detail->qty }}</span>
                                        </div>
                                        
                                        @if($detail->target_phone || !empty($serverBadge))
                                            <div class="flex flex-wrap items-center gap-1 mt-0.5">
                                                @if($detail->target_phone)
                                                    <span class="bg-indigo-50 text-indigo-700 font-mono text-[9px] px-1.5 py-0.5 rounded border border-indigo-100 font-bold">
                                                        <i class="fa-solid fa-phone text-[8px] mr-0.5"></i>{{ $targetLabel }}: {{ $detail->target_phone }}
                                                    </span>
                                                @endif

                                                {{-- BADGE SERVER PRESISI DARI DATABASE (PROPANA, DIGIPOS, BANK BCA, MAXIM, DLL) --}}
                                                @if(!empty($serverBadge))
                                                    <span class="bg-purple-50 text-purple-700 font-bold text-[9px] px-1.5 py-0.5 rounded border border-purple-200 inline-flex items-center uppercase">
                                                        <i class="fa-solid fa-server text-[8px] mr-1 text-purple-500"></i>{{ $serverBadge }}
                                                    </span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                @endforeach

                                {{-- BADGE INDIKATOR ITEM TERSISA --}}
                                @if($remainingCount > 0)
                                    <button type="button" 
                                            onclick="showDetail({{ $trx->id }})" 
                                            class="inline-flex items-center gap-1 bg-slate-100 hover:bg-indigo-50 text-slate-600 hover:text-indigo-600 font-bold text-[10px] px-2 py-0.5 rounded-full border border-slate-200 hover:border-indigo-200 transition cursor-pointer mt-1">
                                        <i class="fa-solid fa-layer-group text-[9px]"></i>
                                        <span>+{{ $remainingCount }} item lainnya</span>
                                    </button>
                                @endif
                            </div>
                        </td>

                        <!-- METODE PEMBAYARAN -->
                        <td class="py-3 px-3 text-center whitespace-nowrap align-top">
                            @if(strtolower($trx->payment_method) === 'qris')
                                <span class="bg-blue-50 text-blue-700 border border-blue-200 font-bold text-[9px] px-2 py-0.5 rounded-full inline-flex items-center space-x-1">
                                    <i class="fa-solid fa-qrcode text-[10px]"></i>
                                    <span>QRIS</span>
                                </span>
                            @else
                                <span class="bg-emerald-50 text-emerald-700 border border-emerald-200 font-bold text-[9px] px-2 py-0.5 rounded-full inline-flex items-center space-x-1">
                                    <i class="fa-solid fa-money-bill-wave text-[10px]"></i>
                                    <span>TUNAI</span>
                                </span>
                            @endif
                        </td>

                        <!-- TOTAL TAGIHAN -->
                        <td class="py-3 px-3 text-right font-mono font-bold text-indigo-700 whitespace-nowrap align-top">
                            Rp {{ number_format($trx->total_price, 0, ',', '.') }}
                        </td>

                        <!-- HANYA TAMPILKAN ISI PROFIT JIKA ROLE OWNER -->
                        @if(auth()->check() && auth()->user()->role === 'owner')
                            <td class="py-3 px-3 text-right font-mono text-xs font-bold text-emerald-600 whitespace-nowrap align-top">
                                +Rp {{ number_format($trx->total_profit, 0, ',', '.') }}
                            </td>
                        @endif

                        <!-- AKSI -->
                        <td class="py-3 px-3 text-center pr-4 whitespace-nowrap align-top">
                            <button type="button" onclick="showDetail({{ $trx->id }})" class="bg-indigo-50 hover:bg-indigo-600 text-indigo-600 hover:text-white px-2.5 py-1 rounded-lg text-[11px] font-extrabold transition cursor-pointer inline-flex items-center space-x-1">
                                <i class="fa-solid fa-receipt text-[10px]"></i>
                                <span>Resi</span>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ auth()->check() && auth()->user()->role === 'owner' ? 7 : 6 }}" class="py-10 text-center text-slate-400">
                            <i class="fa-solid fa-receipt text-3xl mb-1 text-slate-300 block"></i>
                            <span class="text-xs font-bold text-slate-500">Belum ada data transaksi yang sesuai.</span>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- PAGINATION -->
    <div class="p-3.5 border-t border-slate-100 bg-slate-50 text-xs">
        {{ $transactions->links() }}
    </div>
</div>