<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Penjualan;
use Illuminate\Http\Request;

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
        $penjualan =  Penjualan::with('marketing');
    }
}
