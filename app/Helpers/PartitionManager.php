<?php

namespace App\Helpers;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PartitionManager
{
    public static function moveDataToPartition($sheetName, $tableName)
    {
        $previousMonthDate = Carbon::parse($sheetName)->firstOfMonth()->subMonth()->toDateString();
        $currentMonthDate = Carbon::parse($sheetName)->firstOfMonth()->toDateString();

        $sheetName = strtolower(str_replace(" ", "_", $sheetName));
        $partitionTableName = $tableName . '_' . $sheetName;


        try {


            // Perform the SQL queries to move data to the partition
            // (Include the transaction logic from the previous example here)
            DB::transaction(function () use ($sheetName, $tableName, $partitionTableName, $previousMonthDate, $currentMonthDate) {
                // Create a new partition for 'October 2023', if needed
                DB::statement("
                ALTER TABLE {$partitionTableName}
                ADD PARTITION (PARTITION {$sheetName}
                VALUES LESS THAN (UNIX_TIMESTAMP('{$currentMonthDate} 00:00:00')))
            ");

                // Move data from the main table to the October 2023 partition
                DB::statement("
                INSERT INTO {$partitionTableName} (SELECT * FROM {$tableName}
                WHERE periode >= '{$previousMonthDate}' AND periode < '{$currentMonthDate}')
            ");

                // Remove the data from the main table after moving it to the partition
                DB::table($tableName)
                    ->where('periode', '>=', $previousMonthDate)
                    ->where('periode', '<', $currentMonthDate)
                    ->delete();
            });
        } catch (\Exception $e) {
            // Rollback the transaction on error.
            DB::rollBack();

            // Handle the exception, e.g., log the error or notify the team.
            Log::error("Transaction failed: " . $e->getMessage());
        }
    }
}
