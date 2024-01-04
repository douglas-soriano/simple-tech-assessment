<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\FundAlias;
use App\Models\Fund;

class FundAliasFactory extends Factory
{
    protected $model = FundAlias::class;

    public function definition(): array
    {
        $random_fund_id = Fund::inRandomOrder()->first()->id;
        return [
            'fund_id' => $random_fund_id,
            'title' => $this->faker->words(2, true)
        ];
    }
}