<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Fund;

class FundsTableSeeder extends Seeder
{
    public function run(): void
    {
        Fund::factory()->count(20)->create();
    }
}