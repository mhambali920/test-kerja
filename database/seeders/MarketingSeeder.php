<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MarketingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('marketings')->insert([
            ['name' => 'Alfandy'],
            ['name' => 'Mery'],
            ['name' => 'Danang'],
        ]);
    }
}
