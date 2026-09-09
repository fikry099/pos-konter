<?php

namespace App\Http\Controllers;

use App\Models\PpobServer;
use App\Models\PpobDeposit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PpobServerController extends Controller
{
    /**
     * Menampilkan halaman utama monitoring saldo server dan riwayat isi ulang
     */
    public function index(Request $request)
    {
        // 1. Ambil data server PPOB
        $servers = PpobServer::with(['deposits' => fn($q) => $q->latest()->limit(5)])->get();

        // 2. Ambil parameter bulan dari request (Default: bulan & tahun saat ini)
        $month = $request->input('month', date('Y-m'));

        // 3. Query riwayat isi ulang saldo berdasarkan bulan & tahun yang dipilih
        $recentDeposits = PpobDeposit::with(['server', 'user'])
            ->whereYear('created_at', substr($month, 0, 4))
            ->whereMonth('created_at', substr($month, 5, 2))
            ->latest()
            ->paginate(10)
            ->appends(['month' => $month]); // Menjaga filter bulan tetap aktif saat berpindah pagenation

        return view('ppob_servers.index', compact('servers', 'recentDeposits'));
    }

    /**
     * Menyimpan server / aplikasi PPOB baru (misal: Propana, Seabank, Mitra Shopee)
     */
    public function storeServer(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:ppob_servers,name|max:50',
            'initial_balance' => 'nullable|numeric|min:0'
        ], [
            'name.unique' => 'Nama server/aplikasi ini sudah terdaftar!',
        ]);

        PpobServer::create([
            'name' => trim($request->name),
            'balance' => $request->initial_balance ?? 0,
        ]);

        return back()->with('success', 'Server PPOB baru berhasil ditambahkan!');
    }

    /**
     * Menambahkan isi ulang saldo (deposit) ke server tertentu
     */
    public function addDeposit(Request $request)
    {
        $request->validate([
            'ppob_server_id' => 'required|exists:ppob_servers,id',
            'amount' => 'required|numeric|min:1000',
            'notes' => 'nullable|string|max:255'
        ]);

        DB::beginTransaction();
        try {
            $server = PpobServer::findOrFail($request->ppob_server_id);
            
            // 1. Tambah saldo pada server terkait
            $server->increment('balance', $request->amount);

            // 2. Catat riwayat isi ulang ke tabel deposit
            PpobDeposit::create([
                'ppob_server_id' => $server->id,
                'amount' => $request->amount,
                'notes' => $request->notes,
                'user_id' => Auth::id(),
            ]);

            DB::commit();
            return back()->with('success', 'Isi ulang saldo ' . $server->name . ' sebesar Rp ' . number_format($request->amount, 0, ',', '.') . ' berhasil!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menambahkan saldo: ' . $e->getMessage());
        }
    }
}