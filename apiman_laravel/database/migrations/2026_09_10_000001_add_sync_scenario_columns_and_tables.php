<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add missing sync columns to acc_sub_contractor
        if (Schema::hasTable('acc_sub_contractor')) {
            Schema::table('acc_sub_contractor', function (Blueprint $table) {
                if (!Schema::hasColumn('acc_sub_contractor', 'add_date')) {
                    $table->dateTime('add_date')->nullable()->after('add_by');
                }
                if (!Schema::hasColumn('acc_sub_contractor', 'edit_by')) {
                    $table->string('edit_by')->nullable()->after('add_date');
                }
                if (!Schema::hasColumn('acc_sub_contractor', 'edit_date')) {
                    $table->dateTime('edit_date')->nullable()->after('edit_by');
                }
                if (!Schema::hasColumn('acc_sub_contractor', 'SubCon_Type')) {
                    $table->string('SubCon_Type')->nullable()->after('edit_date');
                }
            });
        }

        // 2. Create vehicles table if not exists
        if (!Schema::hasTable('vehicles')) {
            Schema::create('vehicles', function (Blueprint $table) {
                $table->id('vehicle_id');
                $table->string('num')->nullable();
                $table->string('added_by')->nullable();
                $table->dateTime('added_date')->nullable();
            });
        }

        // 3. Create database_log table if not exists
        if (!Schema::hasTable('database_log')) {
            Schema::create('database_log', function (Blueprint $table) {
                $table->id();
                $table->text('record_data')->nullable();
                $table->dateTime('delete_date')->nullable();
                $table->string('table_name')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('database_log');
        Schema::dropIfExists('vehicles');

        if (Schema::hasTable('acc_sub_contractor')) {
            Schema::table('acc_sub_contractor', function (Blueprint $table) {
                $columns = ['add_date', 'edit_by', 'edit_date', 'SubCon_Type'];
                foreach ($columns as $col) {
                    if (Schema::hasColumn('acc_sub_contractor', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
};
