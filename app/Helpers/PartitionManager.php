<?php

namespace App\Helpers;

use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PartitionManager
{
    public static function moveDataToPartition($sheetName, $tableName)
    {
        $previousMonthDate = Carbon::parse($sheetName)->firstOfMonth()->subMonth()->toDateString();
        $partitionName = Carbon::parse($sheetName)->firstOfMonth()->subMonth()->format('Ymd');
        $currentMonthDate = Carbon::parse($sheetName)->firstOfMonth()->toDateString();

        $sheetName = strtolower(str_replace(" ", "", $sheetName));
        $partitionTableName = $tableName . '_' . $sheetName;


        try {


            // Perform the SQL queries to move data to the partition
            // (Include the transaction logic from the previous example here)

            DB::transaction(function () use ($sheetName, $tableName, $partitionTableName, $partitionName, $previousMonthDate, $currentMonthDate) {
                // Check if the table is already partitioned by querying the INFORMATION_SCHEMA.PARTITIONS table
                $isPartitioned = DB::table('INFORMATION_SCHEMA.PARTITIONS')
                    ->where('TABLE_SCHEMA', env('DB_DATABASE')) // Replace with your actual database name if not using an environment variable
                    ->where('TABLE_NAME', $tableName)
                    ->count() > 0;

                // If the table is not partitioned, deFine a new partitioning scheme
                if (!$isPartitioned) {
                    DB::statement("
        ALTER TABLE {$tableName} PARTITION BY RANGE COLUMNS(periode) (
        PARTITION p{$partitionName} VALUES LESS THAN ('{$currentMonthDate}'),
        PARTITION pmax VALUES LESS THAN (MAXVALUE)
        )
        ");
                } else {
                    // If the table is already partitioned, reorganize the pmax partition to add a new partition
                    DB::statement("
            ALTER TABLE {$tableName}
            REORGANIZE PARTITION pmax INTO (
                PARTITION p{$partitionName} VALUES LESS THAN ('{$currentMonthDate}'),
                PARTITION pmax VALUES LESS THAN (MAXVALUE)
            )
        ");
                }

                // Assuming you have a valid reason to move rows to a new storage table and then delete them from the main table,
                // here is the code that does that. As mentioned before, this should not be necessary for a correctly partitioned
                // table, because the database engine will automatically place the rows in the correct partition based on the
                // range deFinitions provided.
                /*
    DB::statement("
        INSERT INTO {$partitionTableName} (SELECT * FROM {$tableName}
        WHERE periode >= '{$previousMonthDate}' AND periode < '{$currentMonthDate}');
            ");

    DB::table($tableName)
        ->where('periode', '>=', $previousMonthDate)
        ->where('periode', '<', $currentMonthDate)
        ->delete();
    */
            });
        } catch (\Exception $e) {
            // Rollback the transaction on error.
            DB::rollBack();

            // Handle the exception, e.g., log the error or notify the team.

            Log::error("Transaction failed: " . $e->getMessage());
            throw new Exception("awdawdawda");
        }
    }
}
