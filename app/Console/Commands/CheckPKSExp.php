<?php

namespace App\Console\Commands;

use App\Models\GapPks;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckPKSExp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pks:checkexp';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check PKS Expired in 3 months';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): void
    {
        $this->line("Checking PKS will expired in 3 months");
        $jatuh_tempo = Carbon::now()->addMonths(3);

        $gap_pks = GapPks::where('need_update', false)
            ->where('akhir', '<=', $jatuh_tempo->format('Y-m-d'))
            ->where('akhir', '>=', Carbon::now()->format('Y-m-d'))->get();

        $bar = $this->output->createProgressBar(count($gap_pks));

        $bar->start();

        foreach ($gap_pks as $pks) {
            $pks->update(['need_update' => true]);

            $bar->advance();
        }
    }
}
