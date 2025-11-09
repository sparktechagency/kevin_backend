<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Dream;
use Carbon\Carbon;

class UpdateDreamStatus extends Command
{
    protected $signature = 'dreams:update-status';
    protected $description = 'Update dream status automatically after end date';

    public function handle()
    {
        $today = Carbon::today();

        // Update completed dreams
        $completed = Dream::whereDate('end_date', '<', $today)
            ->where('status', '!=', 'Completed')
            ->update(['status' => 'Completed']);

        // Update upcoming dreams (if start date > today)
        $upcoming = Dream::whereDate('start_date', '>', $today)
            ->where('status', '!=', 'Upcoming')
            ->update(['status' => 'Upcoming']);

        // Update active dreams
        $active = Dream::whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->where('status', '!=', 'Active')
            ->update(['status' => 'Active']);

        $this->info("Dream statuses updated successfully: Completed={$completed}, Upcoming={$upcoming}, Active={$active}");
    }
}
