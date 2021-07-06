<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Modules\Api\Services\ContestService;
use Modules\Api\Services\StoryService;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        //
    ];

    protected function schedule(Schedule $schedule): void
    {
        $schedule->call(function () {
            try {
                app(StoryService::class)->dailyBalanceBonus();
            } catch (\Exception $e) {
                \Log::error('Exception while making daily story balance bonus.\n' . $e->getMessage());
            }

            try {
                app(ContestService::class)->calculateResults();
            } catch (\Exception $e) {
                \Log::error('Exception while calculating contest results.\n' . $e->getMessage());
            }

            try {
                app(ContestService::class)->startDailyContest();
            } catch (\Exception $e) {
                \Log::error('Exception while starting daily contest.\n' . $e->getMessage());
            }
        })->daily();
    }

    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
