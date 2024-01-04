<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FundResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        # Manager details
        $fund_manager = null;
        if ($this->fundManager) {
            $fund_manager = [
                'id' => $this->fundManager->id,
                'name' => $this->fundManager->name,
                'company_id' => $this->fundManager->company ? $this->fundManager->company_id : null,
                'company_name' => $this->fundManager->company ? $this->fundManager->company->name : null
            ];
        }

        # Aliases
        $aliases = null;
        if ($this->aliases) {
            $aliases = $this->aliases->pluck('title');
        }

        # Response
        return [
            'id' => $this->id,
            'name' => $this->name,
            'start_year' => $this->start_year,
            'fund_manager' => $fund_manager,
            'aliases' => $aliases
        ];
    }
}
