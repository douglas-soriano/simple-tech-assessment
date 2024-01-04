<?php

namespace Database\Factories;

use App\Models\FundManagerFund;
use App\Models\Fund;
use App\Models\FundManager;
use Illuminate\Database\Eloquent\Factories\Factory;

class FundManagerFundFactory extends Factory
{
    protected $model = FundManagerFund::class;

    public function definition(): array
    {
        $random_fund_id = Fund::inRandomOrder()->first()->id;
        $random_manager_id = FundManager::inRandomOrder()->first()->id;
        return [
            'fund_id' => $random_fund_id,
            'fund_manager_id' => $random_manager_id
        ];
    }
}