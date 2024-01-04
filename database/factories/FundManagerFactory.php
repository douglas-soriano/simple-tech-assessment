<?php

namespace Database\Factories;

use App\Models\FundManager;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class FundManagerFactory extends Factory
{
    protected $model = FundManager::class;

    public function definition(): array
    {
        $random_company_id = Company::inRandomOrder()->first()->id;
        return [
            'name' => $this->faker->name,
            'company_id' => $random_company_id
        ];
    }
}