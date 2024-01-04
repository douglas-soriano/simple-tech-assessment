<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FundManager;

class FundManagersTableSeeder extends Seeder
{
    public function run(): void
    {
        FundManager::factory()->count(10)->create();
    }
}