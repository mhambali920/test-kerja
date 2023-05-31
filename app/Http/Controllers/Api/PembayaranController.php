<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Penjualan;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class PembayaranController extends Controller
{
    public function index(Request $request)
    {
        try {
            $pembayaran = Pembayaran::with('penjualan');
            if ($request->has('penjualan_id')) {
                $pembayaran->where('penjualan_id', '=', $request->penjualan_id);
            }
            $data =  $pembayaran->paginate();
            return ResponseFormatter::success(data: $data, message: 'data pembayaran berhasil di ambil');
        } catch (Exception $e) {
            return ResponseFormatter::error(message: $e->getMessage(), code: $e->getCode());
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'penjualan_id' => 'required|exists:penjualans,id',
                'tanggal' => 'required|date',
                'jumlah' => 'required|numeric|min:0',
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors();
                return ResponseFormatter::error(message: $errors, code: 422);
            }
            // ambil data penjualan yang akan di bayar
            $penjualan = Penjualan::find($request->penjualan_id);
            // cek sisa pembayaran
            $sisa_pembayaran = $penjualan->grand_total - $penjualan->pembayarans()->sum('jumlah');

            if ($request->jumlah > $sisa_pembayaran) {
                return ResponseFormatter::error(message: 'Jumlah pembayaran melebihi sisa pembayaran.', data: ['sisa pembayaran' => $sisa_pembayaran]);
            }
            // simpan data pembayaran
            $pembayaran = new Pembayaran();
            $pembayaran->penjualan_id = $request->penjualan_id;
            $pembayaran->tanggal = $request->tanggal;
            $pembayaran->jumlah = $request->jumlah;
            $pembayaran->save();

            return ResponseFormatter::success(data: $pembayaran, message: 'Pembayaran berhasil disimpan.');
        } catch (Exception $e) {
            ResponseFormatter::error(message: $e->getMessage(), code: $e->getCode());
        }
    }
}
