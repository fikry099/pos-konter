<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Store;

class StoreController extends Controller
{
    /**
     * Memproses pergantian cabang untuk akun Owner
     */
    public function switchStore(Request $request)
    {
        $request->validate([
            'store_id' => 'nullable|exists:stores,id',
        ]);

        // Jika store_id diisi (misal: 1 atau 2), simpan ke session.
        // Jika null/kosong, hapus session (pilih Semua Cabang)
        if ($request->filled('store_id')) {
            session(['selected_store_id' => $request->store_id]);
            $store = Store::find($request->store_id);
            $message = "Berhasil berpindah ke " . ($store ? $store->name : 'Cabang');
        } else {
            session()->forget('selected_store_id');
            $message = "Berhasil menampilkan data Gabungan (Semua Cabang)";
        }

        return redirect()->back()->with('success', $message);
    }
}