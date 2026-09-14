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
        // 1. Users table (tbl_users / sys_users compatibility)
        if (!Schema::hasTable('tbl_users')) {
            Schema::create('tbl_users', function (Blueprint $table) {
                $table->id();
                $table->string('username')->unique();
                $table->string('password');
                $table->string('email')->nullable();
                $table->string('full_name')->nullable();
                $table->tinyInteger('status')->default(1);
                $table->rememberToken();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('sys_users')) {
            Schema::create('sys_users', function (Blueprint $table) {
                $table->id('user_id');
                $table->string('username')->unique();
                $table->string('password');
                $table->tinyInteger('user_status')->default(1);
                $table->timestamps();
            });
        }

        // 2. Categories table
        if (!Schema::hasTable('category')) {
            Schema::create('category', function (Blueprint $table) {
                $table->id();
                $table->string('description');
                $table->timestamps();
            });
        }

        // 3. Sync API main definitions table
        if (!Schema::hasTable('sync_api')) {
            Schema::create('sync_api', function (Blueprint $table) {
                $table->id('main_id');
                $table->string('api_name')->unique();
                $table->unsignedBigInteger('category_id')->nullable();
                $table->string('from_db')->nullable();
                $table->string('to_db')->nullable();
                $table->longText('from_added_query')->nullable();
                $table->string('Prefix')->nullable();
                $table->string('Suffix')->nullable();
                $table->longText('to_added_query')->nullable();
                $table->longText('from_Updated_query')->nullable();
                $table->longText('to_Updated_query')->nullable();
                $table->longText('from_delete_query')->nullable();
                $table->longText('to_delete_query')->nullable();
                $table->string('last_added_time_query')->nullable();
                $table->string('last_updated_time_query')->nullable();
                $table->string('last_deleted_time_query')->nullable();
                $table->string('total_count_query')->nullable();
                $table->string('status_to_aync_api_detail')->nullable();
                $table->tinyInteger('wait_till_confirmation')->default(0);
                $table->timestamps();
            });
        }

        // 4. Sync API details/logs table
        if (!Schema::hasTable('sync_api_detail')) {
            Schema::create('sync_api_detail', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('main_id')->nullable();
                $table->dateTime('last_time')->nullable();
                $table->integer('total_records')->nullable();
                $table->string('from_primary_key')->nullable();
                $table->string('to_primary_key')->nullable();
                $table->integer('status')->default(1); // 1 = success, 9 = waitTillConfirm
                $table->tinyInteger('type')->default(1); // 1 = added/insert, 2 = updated, 3 = deleted
                $table->text('remarks')->nullable();
                $table->dateTime('sync_time')->nullable();
                $table->timestamps();
            });
        }

        // 5. API Configurations
        if (!Schema::hasTable('api_configurations')) {
            Schema::create('api_configurations', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('base_url');
                $table->string('auth_type')->default('none');
                $table->string('auth_username')->nullable();
                $table->string('auth_password')->nullable();
                $table->text('auth_token')->nullable();
                $table->string('request_method')->default('GET');
                $table->string('request_body_format')->nullable();
                $table->string('endpoint')->nullable();
                $table->string('header_key')->nullable();
                $table->text('header_value')->nullable();
                $table->string('param_key')->nullable();
                $table->text('param_value')->nullable();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->timestamps();
            });
        }

        // 6. DB Configurations
        if (!Schema::hasTable('db_configurations')) {
            Schema::create('db_configurations', function (Blueprint $table) {
                $table->id();
                $table->string('active_group');
                $table->string('active_record')->default('TRUE');
                $table->string('hostname');
                $table->string('username');
                $table->string('password')->nullable();
                $table->string('database');
                $table->string('dbdriver')->default('mysqli');
                $table->string('port')->nullable();
                $table->string('dbprefix')->nullable();
                $table->string('pconnect')->default('FALSE');
                $table->string('db_debug')->default('TRUE');
                $table->string('cache_on')->default('FALSE');
                $table->string('cachedir')->nullable();
                $table->string('char_set')->default('utf8');
                $table->string('dbcollat')->default('utf8_general_ci');
                $table->string('swap_pre')->nullable();
                $table->string('autoinit')->default('TRUE');
                $table->string('stricton')->default('FALSE');
                $table->unsignedBigInteger('user_id')->nullable();
                $table->timestamps();
            });
        }

        // 7. Company Profile
        if (!Schema::hasTable('company_profile')) {
            Schema::create('company_profile', function (Blueprint $table) {
                $table->id();
                $table->string('company_legal_name');
                $table->string('company_reg_no');
                $table->date('incorporation_date')->nullable();
                $table->string('financial_year')->nullable();
                $table->string('tin_no')->nullable();
                $table->string('vat_svat_no')->nullable();
                $table->string('nbt_reg_no')->nullable();
                $table->string('epf_etf_reg_no')->nullable();
                $table->string('payee_tax_no')->nullable();
                $table->text('address')->nullable();
                $table->string('image')->nullable();
                $table->timestamps();
            });
        }

        // 8. Business modules tables (subcontractor, client, bill, attendance, leave, project, vehicle)
        if (!Schema::hasTable('acc_sub_contractor')) {
            Schema::create('acc_sub_contractor', function (Blueprint $table) {
                $table->id();
                $table->string('Sub_Contractor_Code')->unique();
                $table->string('Sub_Contractor_Name');
                $table->string('Sub_Contractor_Address', 200)->nullable();
                $table->string('Land_Phone_No')->nullable();
                $table->string('Mobile')->nullable();
                $table->string('Fax')->nullable();
                $table->string('add_by')->nullable();
                $table->dateTime('add_date')->nullable();
                $table->string('edit_by')->nullable();
                $table->dateTime('edit_date')->nullable();
                $table->string('SubCon_Type')->nullable();
                $table->string('NBT')->nullable();
                $table->string('Is_VAT_Registered')->nullable();
                $table->string('Registation_No')->nullable();
                $table->string('VAT_For_Transport')->nullable();
                $table->string('Attn_Person', 100)->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('ma_client')) {
            Schema::create('ma_client', function (Blueprint $table) {
                $table->id('Client_Code');
                $table->string('Client_Id')->unique();
                $table->string('Client_Name');
                $table->string('Official_Contact_Dtl')->nullable();
                $table->string('Project_Contact_Dtl')->nullable();
                $table->string('Contact_Person_Name')->nullable();
                $table->string('Contact_Person_Email')->nullable();
                $table->string('Contact_Person_Phone')->nullable();
                $table->string('Contact_Person_Mobile')->nullable();
                $table->string('Added_By')->nullable();
                $table->text('Client_Address')->nullable();
                $table->string('Vat_No')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('acc_business')) {
            Schema::create('acc_business', function (Blueprint $table) {
                $table->id('project_id');
                $table->string('business_name')->unique();
                $table->tinyInteger('status')->default(1);
                $table->string('PMS_project_id')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('payment_application')) {
            Schema::create('payment_application', function (Blueprint $table) {
                $table->id('bill_id');
                $table->date('bill_date')->nullable();
                $table->string('Client_Code')->nullable();
                $table->string('Client_ID_PMS')->nullable();
                $table->text('particulars')->nullable();
                $table->decimal('amount', 15, 2)->default(0);
                $table->decimal('retention_amount', 15, 2)->default(0);
                $table->string('invoice_status')->default('active');
                $table->string('added_by')->nullable();
                $table->string('business_id')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('acc_payable')) {
            Schema::create('acc_payable', function (Blueprint $table) {
                $table->id('payable_id');
                $table->date('due_date')->nullable();
                $table->string('project_id')->nullable();
                $table->string('payee_id')->nullable();
                $table->decimal('payable_amount', 15, 2)->default(0);
                $table->text('description')->nullable();
                $table->string('expense_category')->nullable();
                $table->date('period_from')->nullable();
                $table->date('period_to')->nullable();
                $table->string('added_by')->nullable();
                $table->string('payable_status')->default('Not Paid');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('acc_attendence')) {
            Schema::create('acc_attendence', function (Blueprint $table) {
                $table->id();
                $table->string('project_code')->nullable();
                $table->string('Employee_Code')->nullable();
                $table->date('Date')->nullable();
                $table->time('Time')->nullable();
                $table->string('Machine')->nullable();
                $table->string('Location')->nullable();
                $table->string('Row')->nullable();
                $table->dateTime('UpdatedDate')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('acc_leave')) {
            Schema::create('acc_leave', function (Blueprint $table) {
                $table->id();
                $table->string('Employee_No')->nullable();
                $table->string('leave_type')->nullable();
                $table->date('leave_date')->nullable();
                $table->string('leave_apply_type')->nullable();
                $table->text('Remarks')->nullable();
                $table->tinyInteger('mark_type')->default(1);
                $table->string('added_by')->nullable();
                $table->dateTime('added_date')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('acc_expense_category')) {
            Schema::create('acc_expense_category', function (Blueprint $table) {
                $table->id('exp_id');
                $table->string('expense_category');
                $table->unsignedBigInteger('parent_id')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('acc_vehicle_cost')) {
            Schema::create('acc_vehicle_cost', function (Blueprint $table) {
                $table->id();
                $table->string('vehicle_no')->nullable();
                $table->string('cost_category')->nullable();
                $table->decimal('amount', 15, 2)->default(0);
                $table->date('cost_date')->nullable();
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('vehicles')) {
            Schema::create('vehicles', function (Blueprint $table) {
                $table->id('vehicle_id');
                $table->string('num')->nullable();
                $table->string('added_by')->nullable();
                $table->dateTime('added_date')->nullable();
            });
        }

        if (!Schema::hasTable('ma_sub_contractor')) {
            Schema::create('ma_sub_contractor', function (Blueprint $table) {
                $table->id();
                $table->string('Sub_Contractor_Code')->nullable();
                $table->string('Sub_Contractor_Name')->nullable();
                $table->string('Sub_Contractor_Address', 200)->nullable();
                $table->string('Mobile')->nullable();
                $table->string('add_by')->nullable();
                $table->dateTime('add_date')->nullable();
                $table->string('edit_by')->nullable();
                $table->dateTime('edit_date')->nullable();
                $table->string('SubCon_Type')->nullable();
                $table->string('Attn_Person', 100)->nullable();
            });
        }

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
        Schema::dropIfExists('ma_sub_contractor');
        Schema::dropIfExists('vehicles');
        Schema::dropIfExists('acc_vehicle_cost');
        Schema::dropIfExists('acc_expense_category');
        Schema::dropIfExists('acc_leave');
        Schema::dropIfExists('acc_attendence');
        Schema::dropIfExists('acc_payable');
        Schema::dropIfExists('payment_application');
        Schema::dropIfExists('acc_business');
        Schema::dropIfExists('ma_client');
        Schema::dropIfExists('acc_sub_contractor');
        Schema::dropIfExists('company_profile');
        Schema::dropIfExists('db_configurations');
        Schema::dropIfExists('api_configurations');
        Schema::dropIfExists('sync_api_detail');
        Schema::dropIfExists('sync_api');
        Schema::dropIfExists('category');
        Schema::dropIfExists('sys_users');
        Schema::dropIfExists('tbl_users');
    }
};
