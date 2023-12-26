<?php

namespace App\Jobs;

use App\Helpers\PartitionManager;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

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

        try {
            // Your job logic
            $partitionManager->moveDataToPartition($this->sheet_name, $this->table_name);
        } catch (\Exception $exception) {
            // Log the exception
            Log::error("Error processing job: " . $exception->getMessage());
            // Optionally, you can re-throw the exception to let Laravel handle it as a failed job
            throw $exception;
        }
    }
}
