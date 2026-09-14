<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Alter sync_api table columns to allow NULL or empty string smoothly
        try {
            DB::statement("ALTER TABLE `sync_api` MODIFY COLUMN `from_db` VARCHAR(50) NULL DEFAULT ''");
            DB::statement("ALTER TABLE `sync_api` MODIFY COLUMN `to_db` VARCHAR(50) NULL DEFAULT ''");
            DB::statement("ALTER TABLE `sync_api` MODIFY COLUMN `last_added_time_query` VARCHAR(255) NULL DEFAULT ''");
            DB::statement("ALTER TABLE `sync_api` MODIFY COLUMN `last_updated_time_query` VARCHAR(255) NULL DEFAULT ''");
            DB::statement("ALTER TABLE `sync_api` MODIFY COLUMN `total_count_query` VARCHAR(255) NULL DEFAULT ''");
        } catch (\Throwable $e) {
            // Log or ignore if MySQL table differences exist
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
