<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;
use App\Models\Fund;
use App\Models\FundManager;

class FundManagerFund extends Pivot
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $table = 'fund_manager_fund';

    protected $fillable = ['fund_id', 'fund_manager_id'];

    public function fund()
    {
        return $this->belongsTo(Fund::class);
    }

    public function fund_manager()
    {
        return $this->belongsTo(FundManager::class);
    }

}