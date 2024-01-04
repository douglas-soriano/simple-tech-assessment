<?php

namespace App\Console\Commands;

use Artisan;
use Illuminate\Console\Command;
use App\Models\Fund;
use App\Models\FundManager;
use App\Models\FundManagerFund;
use App\Models\FundAlias;
use App\Models\Company;

class PopulateDummyData extends Command
{
    protected $signature = 'dummy:populate {count=100 : The number of records to populate}';

    protected $description = 'Populate dummy data into the tables.';

    public function handle()
    {
        $count = (int) $this->argument('count');

        $this->info("Cleaning database records...");

        Artisan::call('migrate:fresh');

        $this->info("Populating {$count} dummy records...");

        Company::factory($count)->create();
        Fund::factory($count)->create();
        FundManager::factory($count)->create();
        FundAlias::factory($count)->create();
        FundManagerFund::factory($count)->create();

        $this->info('Dummy data populated successfully.');
    }
}
