<?php

namespace App\Listeners;

use App\Events\DuplicateFundWarning;
use Illuminate\Contracts\Queue\ShouldQueue;

class DuplicateFundWarningListener implements ShouldQueue
{

    public function handle(DuplicateFundWarning $event)
    {
        // Handle the duplicate fund warning event here
        \Log::info("Duplicate Fund Warning: Fund '{$event->new_fund->name}' (#{$event->new_fund->id}) with manager '{$event->new_fund->fundManager->name}' (#{$event->new_fund->fundManager->id}) might be a duplicate.");
    }

}