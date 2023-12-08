<?php

namespace App\Jobs;

use App\Helpers\PartitionManager;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessPartitioning implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $sheet_name, $model, $table_name;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($sheet_name, $table_name)
    {
        $this->sheet_name = $sheet_name;
        $this->table_name = $table_name;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(PartitionManager $partitionManager)
    {
        $partitionManager->moveDataToPartition($this->sheet_name, $this->table_name);
    }
}
