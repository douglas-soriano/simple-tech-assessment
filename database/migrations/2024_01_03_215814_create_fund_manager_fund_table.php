<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('fund_manager_fund', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fund_id')->constrained(table: 'funds', indexName: 'fund_manager_funds_ids');
            $table->foreignId('fund_manager_id')->constrained(table: 'funds_managers', indexName: 'fund_manager_fund_managers_ids');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fund_manager_fund');
    }
};
