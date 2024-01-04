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
        Schema::create('funds_aliases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fund_id')->constrained(table: 'funds', indexName: 'funds_aliases_fund_id');
            $table->string('title');
            $table->timestamps();

            $table->index(['title']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('funds_aliases');
    }
};
