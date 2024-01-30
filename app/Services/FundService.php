<?php

namespace App\Services;

use App\Models\Fund;
use DB;

class FundService
{
    public function getPotentialDuplicates()
    {
        return Fund::selectRaw(
                'GROUP_CONCAT(funds.id) as duplicate_ids,
                funds.name as fund_name,
                funds_managers.name as fund_manager,
                GROUP_CONCAT(funds_aliases.title) as aliases'
            )

            ->join('fund_manager_fund', 'funds.id', '=', 'fund_manager_fund.fund_id')
            ->join('funds_managers', 'fund_manager_fund.fund_manager_id', '=', 'funds_managers.id')
            ->leftJoin('funds_aliases', 'funds.id', '=', 'funds_aliases.fund_id')
            ->whereExists(function ($query) {

                $query->select(DB::raw(1))
                    ->from('funds as f')
                    ->join('fund_manager_fund', 'f.id', '=', 'fund_manager_fund.fund_id')
                    ->leftJoin('funds_aliases', 'f.id', '=', 'funds_aliases.fund_id')
                    ->whereRaw('f.id <> funds.id')
                    # Search with the same manager
                    ->whereRaw('fund_manager_fund.fund_manager_id = funds_managers.id')
                    ->where(function ($q) {
                        # Search by duplicated name
                        $q->whereRaw('f.name = funds.name');
                        # Search by duplicated aliases
                        $q->orWhereExists(function ($sub_query) {
                            $sub_query->select(DB::raw(1))->from('funds_aliases')->whereRaw('funds_aliases.fund_id = f.id')->whereRaw('funds_aliases.title = funds.name');
                        });
                    });

            })
            ->groupBy('funds.name', 'funds_managers.name')
            ->orderBy('funds.id')
            ->get();
    }

    public function getFunds($filters = [])
    {
        $query = Fund::query();

        # Filter :: by name
        if (array_key_exists('name', $filters)) {
            $query->where('name', 'LIKE', '%' . $filters['name'] . '%');
        }

        # Filter :: by manager's name
        if (array_key_exists('fund_manager_name', $filters)) {
            $query->whereHas('fundManager', function ($sub_query) use ($filters) {
                $sub_query->where('name', 'LIKE', '%' . $filters['fund_manager_name'] . '%');
            });
        }

        # Filter :: by manager's id
        if (array_key_exists('fund_manager_id', $filters)) {
            $query->whereHas('fundManager', function ($sub_query) use ($filters) {
                $sub_query->where('id', $filters['fund_manager_id']);
            });
        }

        # Filter :: by start date
        if (array_key_exists('start_year', $filters)) {
            $query->where('start_year', $filters['start_year']);
        }

        $funds = $query->with('fundManager.company', 'aliases')->paginate(10);
        return $funds;
    }

    public function getFundById($fundId) : Fund
    {
        return Fund::findOrFail($fundId);
    }

    public function updateFund(Fund $fund, array $data) : void
    {
        // Update fund attributes
        $fund->fill($data);

        // Update manager (if provided)
        if (array_key_exists('fund_manager_id', $data)) {
            $fund->updateManager($data['fund_manager_id']);
        }

        // Update aliases (if provided)
        if (array_key_exists('aliases', $data)) {
            $fund->updateAliases($data['aliases']);
        }

        // Save changes
        $fund->save();

        // Optional: Check and trigger duplicate warnings
        $fund->checkAndTriggerDuplicateWarning();
    }
}