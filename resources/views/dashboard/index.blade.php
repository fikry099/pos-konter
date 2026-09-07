@extends('layouts.app')

@section('content')
<div class="space-y-3.5">

    <!-- 2. SUB-KOMPONEN: METRICS CARDS (FINANSIAL HARI INI & BULAN INI) -->
    @include('dashboard.partials.metrics-cards')

    <!-- 3. SECTION GRID DUA KOLOM (RESPONSIVE UNTUK TABLET & DESKTOP) -->
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-3.5 items-start">

        <!-- SISI KIRI: REKAP SHIFT TERAKHIR (COL-SPAN 7 DI DESKTOP) -->
        <div class="xl:col-span-7">
            @include('dashboard.partials.recent-shifts')
        </div>

        <!-- SISI KANAN: TRANSAKSI TERBARU (COL-SPAN 5 DI DESKTOP) -->
        <div class="xl:col-span-5">
            @include('dashboard.partials.latest-transactions')
        </div>

    </div>

    <!-- 4. SUB-KOMPONEN BARU: CHART GRAFIK PENJUALAN & PROFIT BULANAN (FULL WIDTH) -->
    @include('dashboard.partials.sales-chart')

</div>
@endsection

@push('scripts')
<!-- PUSTAKA CHART.JS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // JS LIVE CLOCK REALTIME UNTUK SHIFT AKTIF
    function updateLiveClock() {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        
        const timeString = `${hours}:${minutes}:${seconds}`;
        
        document.querySelectorAll('.live-clock').forEach(el => {
            el.innerText = timeString;
        });
    }

    setInterval(updateLiveClock, 1000);
    updateLiveClock();
</script>
@endpush