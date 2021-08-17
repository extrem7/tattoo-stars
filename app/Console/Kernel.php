<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Modules\Api\Services\AdvertisingService;
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
                \Log::info('Daily bonus for balance.');
                app(StoryService::class)->dailyBalanceBonus();
            } catch (\Exception $e) {
                \Log::error('Exception while making daily story balance bonus.\n' . $e->getMessage());
            }

            try {
                \Log::info('Calculating contest results.');
                app(ContestService::class)->calculateResults();
            } catch (\Exception $e) {
                \Log::error('Exception while calculating contest results.\n' . $e->getMessage());
            }

            try {
                \Log::info('Starting daily contest.');
                app(ContestService::class)->startDailyContest();
            } catch (\Exception $e) {
                \Log::error('Exception while starting daily contest.\n' . $e->getMessage());
            }

            try {
                \Log::info('Sending advertising expiration notifications.');
                app(AdvertisingService::class)->sendExpirationNotifications();
            } catch (\Exception $e) {
                \Log::error('Exception while sending advertising expiration notifications.\n' . $e->getMessage());
            }
        })->dailyAt('00:01');
    }

    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
