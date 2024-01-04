<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FundAlias;

class FundAliasTableSeeder extends Seeder
{
    public function run()
    {
        FundAlias::factory()->count(30)->create();
    }
}