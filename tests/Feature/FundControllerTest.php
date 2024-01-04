<?php

namespace Tests\Feature;

use App\Models\Fund;
use App\Models\FundAlias;
use App\Models\FundManager;
use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FundControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * GET :: /api/funds/potential-duplicates
     * */
    public function testGetPotentialDuplicates()
    {
        # Dependencies
        $new_company = Company::factory()->create();
        $new_manager = FundManager::factory()->create();

        # Create a fund so we can duplicate it
        $existing_fund = Fund::factory()->hasAliases(3)->create();
        $existing_fund->updateManager($new_manager->id);
        $existing_fund = Fund::find($existing_fund->id);

        # Create a duplicate
        $duplicate_fund = $this->createDuplicateFund($existing_fund);

        # Check for response
        $response = $this->get('/api/funds/potential-duplicates');
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'response' => [
                    '*' => [

                        'duplicate_ids',
                        'fund_name',
                        'fund_manager',
                        'aliases',

                    ],
                ],
            ]);
    }

    # Create a duplicated with the name equals to the existing fund alias.
    protected function createDuplicateFund(Fund $existing_fund)
    {
        # Duplicate alias
        $duplicated_alias = $existing_fund->aliases->first();

        # Create new fund
        $duplicate_fund = new Fund;
        $duplicate_fund->name = $duplicated_alias->title;
        $duplicate_fund->start_year = '2023-10-10';
        $duplicate_fund->save();

        # Duplicate manager
        $duplicate_fund->updateManager($existing_fund->fundManager->id);

        return $duplicate_fund;
    }

    /**
     * GET :: /api/funds
     * */
    public function testGetFunds()
    {
        $response = $this->get('/api/funds');
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'response' => [
                    'current_page',
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                            'start_year',
                            'fund_manager',
                            'aliases'
                        ]
                    ],
                    'first_page_url',
                    'from',
                    'next_page_url',
                    'path',
                    'per_page',
                    'prev_page_url',
                    'to',
                ],
            ]);
    }

    /**
     * GET :: /api/funds/{fund_id}
     * */
    public function testGetFundDetails()
    {
        # Dependencies
        $new_company = Company::factory()->create();

        # Test
        $fund = Fund::factory()->hasFundManager()->hasAliases(3)->create();
        $response = $this->get('/api/funds/' . $fund->id);
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'response' => [
                    'id',
                    'name',
                    'start_year',
                    'fund_manager',
                    'aliases'
                ],
            ]);
    }

    /**
     * PUT :: /api/funds/{fund_id}
     * */
    public function testUpdateFund()
    {
        $fund = Fund::factory()->hasFundManager()->hasAliases(3)->create();
        $response = $this->put('/api/funds/' . $fund->id, [
            'name' => 'Test Fund Name',
            'start_year' => '2024-01-01',
            'fund_manager_id' => \App\Models\FundManager::inRandomOrder()->first()->id,
            'aliases' => ['Test Name 1', 'Test Name 2']
        ]);
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'response' => [
                    'id',
                    'name',
                    'start_year',
                    'fund_manager',
                    'aliases'
                ],
            ]);

        # Check if the fund was actually updated in the database.
        $this->assertDatabaseHas('funds', ['id' => $fund->id, 'name' => 'Test Fund Name']);
    }

}