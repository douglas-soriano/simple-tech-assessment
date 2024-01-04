<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FundAlias extends Model
{
    use HasFactory;

    protected $table = 'funds_aliases';

    protected $fillable = ['fund_id', 'title'];

    # RELATIONSHIP :: Parent father fund
    public function fund()
    {
        return $this->belongsTo(Fund::class);
    }
}