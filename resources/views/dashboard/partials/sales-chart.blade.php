<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5 space-y-4">
    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
        <div class="flex items-center space-x-2">
            <span class="p-2 bg-purple-50 text-purple-600 rounded-xl text-xs font-bold">
                <i class="fa-solid fa-chart-column"></i>
            </span>
            <div>
                <h3 class="font-extrabold text-slate-800 text-sm">Grafik Penjualan & Profit Bulanan</h3>
                <p class="text-[11px] text-slate-400 font-medium">Tren performa keuangan 6 bulan terakhir</p>
            </div>
        </div>
        <div class="flex items-center space-x-4 text-xs font-bold">
            <div class="flex items-center space-x-1.5">
                <span class="w-3 h-3 rounded-full bg-indigo-600 inline-block"></span>
                <span class="text-slate-600">Omset</span>
            </div>
            <div class="flex items-center space-x-1.5">
                <span class="w-3 h-3 rounded-full bg-emerald-500 inline-block"></span>
                <span class="text-slate-600">Profit</span>
            </div>
        </div>
    </div>

    <div class="w-full h-72">
        <canvas id="monthlySalesChart"></canvas>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const chartData = @json($monthlyChartData ?? []);

    if (chartData.length === 0) return;

    const labels = chartData.map(item => item.month_name);
    const salesData = chartData.map(item => item.sales);
    const profitData = chartData.map(item => item.profit);

    const ctx = document.getElementById('monthlySalesChart').getContext('2d');
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Total Omset (Rp)',
                    data: salesData,
                    backgroundColor: 'rgba(79, 70, 229, 0.85)',
                    borderColor: '#4f46e5',
                    borderWidth: 1,
                    borderRadius: 8,
                    barPercentage: 0.6,
                },
                {
                    label: 'Total Profit (Rp)',
                    data: profitData,
                    backgroundColor: 'rgba(16, 185, 129, 0.85)',
                    borderColor: '#10b981',
                    borderWidth: 1,
                    borderRadius: 8,
                    barPercentage: 0.6,
                }
            ]
        },
        options: {
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
                    ticks: { font: { family: 'Arial', size: 11, weight: 'bold' }, color: '#64748b' }
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
        }
    });
});
</script>