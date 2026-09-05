<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\Expense;
use Gemini;
use Carbon\Carbon;

class AiAssistantController extends Controller
{
    public function index()
    {
        return view('ai.index');
    }

    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $userMessage = trim($request->message);
        $user = auth()->user();

        try {
            $apiKey = env('GEMINI_API_KEY');
            if (!$apiKey) {
                return response()->json(['status' => 'error', 'reply' => 'API Key Gemini belum diatur di .env'], 500);
            }

            $client = Gemini::client($apiKey);

            // ================================================
            // 1. KUMPULKAN DATA REAL-TIME DARI DATABASE POS
            // ================================================
            $today = Carbon::today();
            $yesterday = Carbon::yesterday();

            $todayTransactions = Transaction::whereDate('created_at', $today)
                ->with('details.product')
                ->get();

            $totalTransactionsCount = $todayTransactions->count();

            $totalOmzetToday = $todayTransactions->sum(function ($trx) {
                return $trx->total_price ?? $trx->grand_total ?? $trx->total_amount ?? $trx->total ?? 0;
            });

            // Omzet kemarin, buat bahan perbandingan biar AI bisa kasih insight tren
            $totalOmzetYesterday = Transaction::whereDate('created_at', $yesterday)
                ->get()
                ->sum(function ($trx) {
                    return $trx->total_price ?? $trx->grand_total ?? $trx->total_amount ?? $trx->total ?? 0;
                });

            $omzetTrendText = 'Belum ada data kemarin buat dibandingin.';
            if ($totalOmzetYesterday > 0) {
                $diffPercent = round((($totalOmzetToday - $totalOmzetYesterday) / $totalOmzetYesterday) * 100, 1);
                $omzetTrendText = $diffPercent >= 0
                    ? "Naik {$diffPercent}% dibanding kemarin (Rp " . number_format($totalOmzetYesterday, 0, ',', '.') . ")"
                    : "Turun " . abs($diffPercent) . "% dibanding kemarin (Rp " . number_format($totalOmzetYesterday, 0, ',', '.') . ")";
            }

            // Jam paling rame hari ini
            $busiestHour = $todayTransactions
                ->groupBy(fn ($trx) => $trx->created_at->format('H:00'))
                ->map->count()
                ->sortDesc()
                ->keys()
                ->first();
            $busiestHourText = $busiestHour ? "sekitar jam {$busiestHour}" : 'belum keliatan pola jam ramenya';

            // Hitung Estimasi Keuntungan/Profit Bersih (Khusus Owner) & Rincian Produk
            $totalProfitToday = 0;
            $soldItemsSummary = [];

            foreach ($todayTransactions as $trx) {
                $details = $trx->details ?? [];

                foreach ($details as $detail) {
                    $prodName = $detail->product->name ?? $detail->product_name ?? 'Produk';
                    $qty = $detail->quantity ?? $detail->qty ?? 1;

                    if (!isset($soldItemsSummary[$prodName])) {
                        $soldItemsSummary[$prodName] = 0;
                    }
                    $soldItemsSummary[$prodName] += $qty;

                    if ($detail->product) {
                        $costPrice = $detail->product->cost_price ?? 0;
                        $sellingPrice = $detail->selling_price ?? $detail->product->selling_price ?? 0;
                        $totalProfitToday += ($sellingPrice - $costPrice) * $qty;
                    }
                }
            }

            // Urutkan produk terlaris biar AI bisa langsung jawab "produk paling laku apa"
            arsort($soldItemsSummary);
            $topProduct = array_key_first($soldItemsSummary);

            $totalExpenseToday = Expense::whereDate('created_at', $today)->sum('amount');

            $soldTextList = "";
            foreach ($soldItemsSummary as $name => $qty) {
                $soldTextList .= "- {$name}: {$qty} Pcs\n";
            }
            if (empty($soldTextList)) {
                $soldTextList = "Belum ada produk yang terjual hari ini.";
            }

            $lowStockProducts = Product::where('type', 'physical')
                ->whereColumn('stock', '<=', 'min_stock')
                ->get()
                ->map(function ($p) {
                    return "- {$p->name} (Sisa stok: {$p->stock} Pcs)";
                })->implode("\n");

            if (empty($lowStockProducts)) {
                $lowStockProducts = "Semua stok produk fisik aman di atas batas minimum.";
            }

            // ================================================
            // 2. AMBIL RIWAYAT PERCAKAPAN DARI SESSION
            //    (biar AI "inget" konteks obrolan sebelumnya)
            // ================================================
            $historyKey = 'ai_chat_history_' . $user->id;
            $chatHistory = session($historyKey, []);

            $historyText = '';
            foreach ($chatHistory as $turn) {
                $historyText .= "User: {$turn['user']}\nKamu: {$turn['ai']}\n";
            }
            if (empty($historyText)) {
                $historyText = '(Belum ada obrolan sebelumnya di sesi ini)';
            }

            // ================================================
            // 3. KONTROL KONTEKS & PERSONA AI BERDASARKAN ROLE USER
            // ================================================
            $namaToko = 'WANNCELL';
            $tanggalHariIni = $today->translatedFormat('d M Y');

            $baseTone = "Kamu adalah asisten toko {$namaToko}, ngobrol kayak rekan kerja yang deket, bukan robot laporan.\n" .
                        "Gaya ngomongmu: santai, hangat, to the point, boleh sesekali pakai emoji kalau pas, dan jangan kaku " .
                        "kayak baca laporan formal — anggap kamu lagi chat WhatsApp sama tim toko.\n" .
                        "Kalau user cuma nyapa (\"hai\", \"pagi\"), bales santai aja, gak perlu langsung dump semua data.\n" .
                        "Jawab secukupnya sesuai yang ditanya, jangan bertele-tele, tapi tetap kasih insight kalau relevan " .
                        "(misal kalau ditanya omzet, boleh nyambungin ke tren naik/turun dibanding kemarin).\n" .
                        "Ingat obrolan sebelumnya di sesi ini biar nyambung, jangan ulang-ulang nanya hal yang udah dibahas.\n\n" .
                        "RIWAYAT OBROLAN SEBELUMNYA:\n{$historyText}\n";

            if ($user && $user->isOwner()) {
                $systemInstruction = $baseTone . "\n" .
                    "Kamu lagi ngobrol sama Owner/pemilik toko, jadi boleh bahas semua data keuangan & margin secara terbuka.\n\n" .
                    "DATA REAL-TIME TOKO HARI INI ({$tanggalHariIni}):\n" .
                    "• Total Transaksi: {$totalTransactionsCount} transaksi\n" .
                    "• Total Omzet/Kotor: Rp " . number_format($totalOmzetToday, 0, ',', '.') . "\n" .
                    "• Tren Omzet: {$omzetTrendText}\n" .
                    "• Jam paling rame: {$busiestHourText}\n" .
                    "• Estimasi Keuntungan/Profit Bersih: Rp " . number_format($totalProfitToday, 0, ',', '.') . "\n" .
                    "• Total Pengeluaran Kas Toko: Rp " . number_format($totalExpenseToday, 0, ',', '.') . "\n" .
                    "• Produk paling laku: " . ($topProduct ?? 'belum ada') . "\n" .
                    "• Rincian Produk Terjual:\n{$soldTextList}\n" .
                    "• Stok Menipis/Habis:\n{$lowStockProducts}\n\n" .
                    "Kalau Owner nanya angka, jawab presisi tapi tetap ngobrol santai. Pakai bold/list kalau memang bikin " .
                    "lebih jelas, tapi jangan maksa format laporan kalau pertanyaannya simpel.";
            } else {
                $systemInstruction = $baseTone . "\n" .
                    "Kamu lagi ngobrol sama karyawan/kasir, jadi bantu urusan operasional harian aja.\n\n" .
                    "DATA REAL-TIME OPERASIONAL HARI INI ({$tanggalHariIni}):\n" .
                    "• Total Transaksi Diproses: {$totalTransactionsCount} transaksi\n" .
                    "• Jam paling rame: {$busiestHourText}\n" .
                    "• Produk paling laku: " . ($topProduct ?? 'belum ada') . "\n" .
                    "• Rincian Produk Terjual Hari Ini:\n{$soldTextList}\n" .
                    "• Stok Produk Menipis/Perlu Restok:\n{$lowStockProducts}\n\n" .
                    "BATASAN (jangan dilanggar):\n" .
                    "1. JANGAN PERNAH kasih tau angka Profit/Keuntungan Bersih, Margin Modal/HPP, atau laporan keuangan internal Owner.\n" .
                    "2. Kalau ditanya soal itu, tolak dengan santai & sopan, misal bilang itu info khusus buat Owner, gak perlu kaku.\n" .
                    "3. Fokus bantu cek stok, produk terjual, atau hal operasional kasir sehari-hari.";
            }

            $prompt = "{$systemInstruction}\n\nPertanyaan terbaru dari user: {$userMessage}";

            // ================================================
            // 4. KIRIM KE GEMINI API
            // ================================================
            $response = $client->generativeModel(model: 'gemini-3.5-flash-lite')->generateContent($prompt);
            $aiReply = $response->text();

            // ================================================
            // 5. SIMPAN KE RIWAYAT SESSION (maks 6 turn terakhir)
            // ================================================
            $chatHistory[] = ['user' => $userMessage, 'ai' => $aiReply];
            if (count($chatHistory) > 6) {
                $chatHistory = array_slice($chatHistory, -6);
            }
            session([$historyKey => $chatHistory]);

            return response()->json([
                'status'  => 'success',
                'reply'   => $aiReply,
                'time'    => now()->format('H:i'),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'reply'   => 'Waduh, ada kendala teknis nih: ' . $e->getMessage(),
                'time'    => now()->format('H:i'),
            ], 500);
        }
    }

    /**
     * Reset riwayat obrolan (opsional, panggil kalau user klik "chat baru")
     */
    public function resetChat(Request $request)
    {
        session()->forget('ai_chat_history_' . auth()->id());
        return response()->json(['status' => 'success']);
    }
}