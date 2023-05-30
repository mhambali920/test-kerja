<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseFormatter;
use App\Models\Penjualan;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Exception;

class PembayaranController extends Controller
{
    public function index(Request $request)
    {
        try {
            $pembayaran = Pembayaran::with('penjualan');
            if ($request->has('penjualan_id')) {
                $pembayaran->where('penjualan_id', '=', $request->penjualan_id);
            }
            $pembayaran->paginate();
            return ResponseFormatter::success(data: $pembayaran, message: 'data pembayaran berhasil di ambil');
        } catch (Exception $e) {
            return ResponseFormatter::error(message: $e->getMessage(), code: $e->getCode());
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'penjualan_id' => 'required|exists:penjualans,id',
            'tanggal_pembayaran' => 'required|date',
            'jumlah_pembayaran' => 'required|numeric|min:0',
        ]);

        $penjualan = Penjualan::find($request->penjualan_id);
        $sisa_pembayaran = $penjualan->grand_total - $penjualan->pembayarans()->sum('jumlah_pembayaran');

        if ($request->jumlah_pembayaran > $sisa_pembayaran) {
            return ResponseFormatter::error(message: 'Jumlah pembayaran melebihi sisa pembayaran.', data: ['sisa pembayaran' => $sisa_pembayaran]);
        }

        $pembayaran = new Pembayaran();
        $pembayaran->penjualan_id = $request->penjualan_id;
        $pembayaran->tanggal_pembayaran = $request->tanggal_pembayaran;
        $pembayaran->jumlah_pembayaran = $request->jumlah_pembayaran;
        $pembayaran->save();

        return ResponseFormatter::success(data: $pembayaran, message: 'Pembayaran berhasil disimpan.');
    }
}
