<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FundManagerFund;

class FundManagerFundTableSeeder extends Seeder
{
    public function run(): void
    {
        FundManagerFund::factory()->count(30)->create();
    }
}