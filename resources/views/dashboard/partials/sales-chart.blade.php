<div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

    <!-- CHART 1: HARIAN (7 HARI TERAKHIR) -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-4 sm:p-5 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div class="flex items-center space-x-2">
                <span class="p-2 bg-indigo-50 text-indigo-600 rounded-xl text-xs font-bold">
                    <i class="fa-solid fa-chart-line"></i>
                </span>
                <div>
                    <h3 class="font-extrabold text-slate-800 text-sm">Performa Harian</h3>
                    <p class="text-[11px] text-slate-400 font-medium">Omset & Profit 7 Hari Terakhir</p>
                </div>
            </div>
            <div class="flex items-center space-x-3 text-[11px] font-bold">
                <div class="flex items-center space-x-1">
                    <span class="w-2.5 h-2.5 rounded-full bg-indigo-600 inline-block"></span>
                    <span class="text-slate-600">Omset</span>
                </div>
                <div class="flex items-center space-x-1">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 inline-block"></span>
                    <span class="text-slate-600">Profit</span>
                </div>
            </div>
        </div>

        <div class="w-full h-64">
            <canvas id="dailySalesChart"></canvas>
        </div>
    </div>

    <!-- CHART 2: BULANAN (6 BULAN TERAKHIR) -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-4 sm:p-5 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div class="flex items-center space-x-2">
                <span class="p-2 bg-purple-50 text-purple-600 rounded-xl text-xs font-bold">
                    <i class="fa-solid fa-chart-column"></i>
                </span>
                <div>
                    <h3 class="font-extrabold text-slate-800 text-sm">Performa Bulanan</h3>
                    <p class="text-[11px] text-slate-400 font-medium">Tren Keuangan 6 Bulan Terakhir</p>
                </div>
            </div>
            <div class="flex items-center space-x-3 text-[11px] font-bold">
                <div class="flex items-center space-x-1">
                    <span class="w-2.5 h-2.5 rounded-full bg-indigo-600 inline-block"></span>
                    <span class="text-slate-600">Omset</span>
                </div>
                <div class="flex items-center space-x-1">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 inline-block"></span>
                    <span class="text-slate-600">Profit</span>
                </div>
            </div>
        </div>

        <div class="w-full h-64">
            <canvas id="monthlySalesChart"></canvas>
        </div>
    </div>

</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const dailyData = @json($dailyChartData ?? []);
    const monthlyData = @json($monthlyChartData ?? []);

    // CONFIG UMUM CHART
    const commonOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        let label = context.dataset.label || '';
                        if (label) label += ': ';
                        if (context.parsed.y !== null) {
                            label += new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(context.parsed.y);
                        }
                        return label;
                    }
                }
            }
        },
        scales: {
            x: {
                grid: { display: false },
                ticks: { font: { family: 'Arial', size: 10, weight: 'bold' }, color: '#64748b' }
            },
            y: {
                grid: { color: '#f1f5f9' },
                ticks: {
                    font: { family: 'Arial', size: 10 },
                    color: '#94a3b8',
                    callback: function(value) {
                        return 'Rp ' + (value / 1000) + 'k';
                    }
                }
            }
        }
    };

    // 1. RENDER CHART HARIAN (7 HARI)
    if (dailyData.length > 0) {
        const ctxDaily = document.getElementById('dailySalesChart').getContext('2d');
        new Chart(ctxDaily, {
            type: 'bar',
            data: {
                labels: dailyData.map(item => item.day_label),
                datasets: [
                    {
                        label: 'Omset (Rp)',
                        data: dailyData.map(item => item.sales),
                        backgroundColor: 'rgba(79, 70, 229, 0.85)',
                        borderColor: '#4f46e5',
                        borderWidth: 1,
                        borderRadius: 6,
                        barPercentage: 0.65,
                    },
                    {
                        label: 'Profit (Rp)',
                        data: dailyData.map(item => item.profit),
                        backgroundColor: 'rgba(16, 185, 129, 0.85)',
                        borderColor: '#10b981',
                        borderWidth: 1,
                        borderRadius: 6,
                        barPercentage: 0.65,
                    }
                ]
            },
            options: commonOptions
        });
    }

    // 2. RENDER CHART BULANAN (6 BULAN)
    if (monthlyData.length > 0) {
        const ctxMonthly = document.getElementById('monthlySalesChart').getContext('2d');
        new Chart(ctxMonthly, {
            type: 'bar',
            data: {
                labels: monthlyData.map(item => item.month_name),
                datasets: [
                    {
                        label: 'Total Omset (Rp)',
                        data: monthlyData.map(item => item.sales),
                        backgroundColor: 'rgba(79, 70, 229, 0.85)',
                        borderColor: '#4f46e5',
                        borderWidth: 1,
                        borderRadius: 6,
                        barPercentage: 0.65,
                    },
                    {
                        label: 'Total Profit (Rp)',
                        data: monthlyData.map(item => item.profit),
                        backgroundColor: 'rgba(16, 185, 129, 0.85)',
                        borderColor: '#10b981',
                        borderWidth: 1,
                        borderRadius: 6,
                        barPercentage: 0.65,
                    }
                ]
            },
            options: commonOptions
        });
    }
});
</script>