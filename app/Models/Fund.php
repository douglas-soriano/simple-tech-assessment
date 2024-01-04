<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\FundManager;
use App\Models\FundManagerFund;
use App\Events\DuplicateFundWarning;

class Fund extends Model
{
    use HasFactory;

    protected $table = 'funds';

    protected $fillable = ['name', 'start_year'];

    /**
     * HELPERS
    **/

    # HELPER :: Check if it's the current manager, otherwise set as new manager.
    public function updateManager($new_manager_id): void
    {
        if ($new_manager_id && (!$this->fundManager || $new_manager_id !== $this->fundManager->id)) {
            $new_manager = new FundManagerFund([
                'fund_manager_id' => $new_manager_id
            ]);
            $this->fundManagerPivot()->save($new_manager);
        }
    }

    # HELPER :: Sync aliases of this fund.
    public function updateAliases($alias_titles): void
    {
        if (!empty($alias_titles)) {

            # Handle new and existing aliases
            $existing_aliases_ids = $this->aliases ? $this->aliases->pluck('id')->toArray() : [];
            foreach ($alias_titles as $alias_title) {
                if ($alias_title && $alias_title !== '') {
                    # Check if alias exists
                    $found_alias = $this->aliases()->where('title', $alias_title)->first();
                    if ($found_alias) {
                        # Remove it from the list of existing aliases
                        $existing_aliases_ids = array_diff($existing_aliases_ids, [$found_alias->id]);
                    } else {
                        # Create and attach a new alias
                        $new_alias = new FundAlias([
                            'fund_id' => $this->id,
                            'title' => $alias_title
                        ]);
                        $this->aliases()->save($new_alias);
                    }
                }
            }

            # Remove all aliases that aren't present in the request
            if (!empty($existing_aliases_ids)) {
                $this->aliases()->whereIn('id', $existing_aliases_ids)->delete();
            }

        } else {
            # If aliases are provided but empty, remove all existing aliases
            $fund->aliases()->delete();
        }
    }

    # HELPER :: Check if there are potential duplicates. If so, it triggers the warning event.
    public function checkAndTriggerDuplicateWarning()
    {
        $existing_fund = $this->getExistingDuplicate();
        if ($existing_fund) {
            event(new DuplicateFundWarning($this, $existing_fund));
        }
    }

    # HELPER :: Look for potentital duplicates.
    public function getExistingDuplicate()
    {
        # Check if there are funds created with the same name, manager and alias.
        $fund = $this;
        return Fund::where('funds.id', '<>', $this->id)->where(function ($query) use ($fund) {
                # Similar Name OR Alias
                $query->where('funds.name', $fund->name)->orWhereHas('aliases', function ($sub_query) use ($fund) {
                    $sub_query->where('funds_aliases.title', $fund->name);
                });
            })->whereHas('fundManager', function ($sub_query) use ($fund) {
                # Same Manager
                $sub_query->where('funds_managers.id', $fund->fundManager->id ?? false);
            })
            ->with(['fundManager', 'aliases'])
            ->first();
    }

    /**
     * RELATIONSHIPS
    **/

    # RELATIONSHIP :: Managers
    public function fundManager()
    {
        return $this->hasOneThrough(FundManager::class, FundManagerFund::class, 'fund_id', 'id', 'id', 'fund_manager_id')->orderBy('fund_manager_fund.created_at', 'DESC');
    }

    # RELATIONSHIP :: Pivot table between Managers <> Funds
    public function fundManagerPivot()
    {
        return $this->hasOne(FundManagerFund::class)->orderBy('fund_manager_fund.created_at', 'DESC');
    }

    # RELATIONSHIP :: Fund aliases.
    public function aliases()
    {
        return $this->hasMany(FundAlias::class);
    }
}