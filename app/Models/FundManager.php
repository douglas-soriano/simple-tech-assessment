<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FundManager extends Model
{
    use HasFactory;

    protected $table = 'funds_managers';

    protected $fillable = ['name', 'company_id'];

    # RELATIONSHIP :: Manager's company.
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    # RELATIONSHIP :: Funds managed by this manager.
    public function funds()
    {
        return $this->belongsToMany(Fund::class, 'fund_manager_fund', 'fund_manager_id', 'fund_id');
    }
}