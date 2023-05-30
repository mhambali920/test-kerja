<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseFormatter;
use App\Models\Penjualan;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use Exception;

class PenjualanController extends Controller
{
    protected function hitungkomisi($omset)
    {
        $komisi = 0;

        if ($omset >= 500000000) {
            $komisi = 0.1;
        } elseif ($omset >= 200000000) {
            $komisi = 0.05;
        } elseif ($omset >= 100000000) {
            $komisi = 0.025;
        }
        return $komisi;
    }

    public function index()
    {

        try {
            $penjualan = Penjualan::with('marketing')
                ->selectRaw('marketing_id, MONTH(date) as month, SUM(total_balance) as total')
                ->groupBy('marketing_id', 'month')
                ->get();

            $result = $penjualan->map(function ($item) {
                $omzet = $item->total;
                $komisi = $this->hitungKomisi($omzet);
                $komisiNominal = $omzet * $komisi;

                return [
                    'Marketing' => $item->marketing->name,
                    'Bulan' => Carbon::createFromFormat('n', $item->month)->translatedFormat('F'),
                    'Omzet' => $omzet,
                    'Komisi %' => $komisi * 100 . '%',
                    'Komisi Nominal' => $komisiNominal,
                ];
            });

            return ResponseFormatter::success(data: $result, message: 'success');
        } catch (Exception $e) {
            return ResponseFormatter::error(message: $e->getMessage(), code: 500);
        }
    }
}
