<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class UpdateRankingsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:rankings';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Get ambassadors
        $ambassadors = User::ambassadors()->get();

        // Create progress bar
        $bar = $this->output->createProgressBar($ambassadors->count());
        $bar->start();

        // Add ambassadors with revenue to redis sorted set
        $ambassadors->each(function (User $ambassador) use ($bar) {
            Redis::zadd('rankings', (int)$ambassador->revenue, $ambassador->name);

            // Increment progress bar
            $bar->advance(1);
        });

        $bar->finish();
        echo " Finished\n";
    }
}
