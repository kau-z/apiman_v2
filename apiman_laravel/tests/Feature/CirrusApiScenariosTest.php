<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\SyncApi;
use App\Models\SyncApiDetail;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CirrusApiScenariosTest extends TestCase
{
    use RefreshDatabase;

    protected array $authHeaders;

    protected function setUp(): void
    {
        parent::setUp();
        $this->authHeaders = [
            'Authorization' => 'Basic ' . base64_encode('admin:1234'),
        ];
    }

    /**
     * Helper to create JSON-to-DB SyncApi for vehicles (Scenario 3 from PDF)
     */
    protected function createVehicleJsonToDbApi(): SyncApi
    {
        $category = Category::create(['description' => 'Vehicle Sync']);

        return SyncApi::create([
            'api_name' => 'json_to_db',
            'category_id' => $category->id,
            'from_db' => null, // JSON to DB has empty from_db
            'to_db' => 'sqlite',
            'to_added_query' => 'INSERT INTO vehicles VALUES (null, {{num}}, {{by1}}, {{date1}})',
            'to_Updated_query' => 'UPDATE vehicles SET added_by = {{by1}}, added_date = {{date1}} WHERE vehicle_id = {{id}}',
            'to_delete_query' => 'DELETE FROM vehicles WHERE vehicle_id = {{id}}',
            'status_to_aync_api_detail' => 0,
            'wait_till_confirmation' => 0,
            'active_status' => 1,
        ]);
    }

    /**
     * Scenario 3: JSON To DB - Method 1: Header data not blank and single dataset (PDF Page 2-3)
     */
    public function test_scenario_3_method_1_single_dataset(): void
    {
        $this->createVehicleJsonToDbApi();

        $payload = [
            'header' => [
                'apiName' => 'json_to_db',
                'tableName' => 'vehicles',
                'last_added_time' => '2023-03-04 15:01:00',
                'last_updated_time' => '2023-03-04 15:01:00',
                'last_deleted_time' => '2023-03-04 15:00:00',
                'total_count' => '1',
            ],
            'data' => [
                [
                    'id' => '9',
                    'num' => '9',
                    'by1' => 'a122',
                    'date1' => '2020-05-23 15:00:57',
                ],
            ],
        ];

        $response = $this->withHeaders($this->authHeaders)->postJson('/api/datalayer/general', $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
            'message' => 'success',
        ]);

        // Verify data inserted into target table
        $this->assertDatabaseHas('vehicles', [
            'num' => '9',
            'added_by' => 'a122',
            'added_date' => '2020-05-23 15:00:57',
        ]);

        // Verify sync_api_detail logging
        $this->assertDatabaseHas('sync_api_detail', [
            'type' => 1, // Added
            'status' => 1,
        ]);
    }

    /**
     * Scenario 3: Up to date validation when same header is submitted again (PDF Page 1 & 2)
     */
    public function test_scenario_3_up_to_date_check(): void
    {
        $this->createVehicleJsonToDbApi();

        $payload = [
            'header' => [
                'apiName' => 'json_to_db',
                'tableName' => 'vehicles',
                'last_added_time' => '2023-03-04 15:01:00',
                'last_updated_time' => '2023-03-04 15:01:00',
                'last_deleted_time' => '2023-03-04 15:00:00',
                'total_count' => '1',
            ],
            'data' => [
                [
                    'id' => '9',
                    'num' => '9',
                    'by1' => 'a122',
                    'date1' => '2020-05-23 15:00:57',
                ],
            ],
        ];

        // First call inserts
        $this->withHeaders($this->authHeaders)->postJson('/api/datalayer/general', $payload);

        // Second call with same header should return "you are up to date"
        $secondResponse = $this->withHeaders($this->authHeaders)->postJson('/api/datalayer/general', $payload);

        $secondResponse->assertStatus(200);
        $this->assertEquals('you are up to date', $secondResponse->json());
    }

    /**
     * Scenario 3: JSON To DB - Method 2: Multiple dataset (PDF Page 3)
     */
    public function test_scenario_3_method_2_multiple_dataset(): void
    {
        $this->createVehicleJsonToDbApi();

        $payload = [
            'header' => [
                'apiName' => 'json_to_db',
                'tableName' => 'vehicles',
                'last_added_time' => '2023-03-04 15:01:00',
                'last_updated_time' => '2023-03-04 15:01:00',
                'last_deleted_time' => '2023-03-04 15:00:00',
                'total_count' => '2',
            ],
            'data' => [
                [
                    'id' => '13',
                    'num' => '9',
                    'by1' => 'bimalka',
                    'date1' => '2020-05-23 15:00:57',
                ],
                [
                    'id' => '14',
                    'num' => '10',
                    'by1' => 'bimalka',
                    'date1' => '2020-05-23 15:00:57',
                ],
            ],
        ];

        $response = $this->withHeaders($this->authHeaders)->postJson('/api/datalayer/general', $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
            'message' => 'success',
        ]);

        $this->assertDatabaseHas('vehicles', [
            'num' => '9',
            'added_by' => 'bimalka',
        ]);
        $this->assertDatabaseHas('vehicles', [
            'num' => '10',
            'added_by' => 'bimalka',
        ]);
    }

    /**
     * Scenario 3: JSON To DB - Method 3: Blank header data (PDF Page 3-4)
     * Times and total_count are blank, system sets current Asia/Colombo datetime and null total_count
     */
    public function test_scenario_3_method_3_blank_header_data(): void
    {
        $this->createVehicleJsonToDbApi();

        $payload = [
            'header' => [
                'apiName' => 'json_to_db',
                'tableName' => 'vehicles',
                'last_added_time' => '',
                'last_updated_time' => '',
                'last_deleted_time' => '',
                'total_count' => '',
            ],
            'data' => [
                [
                    'id' => '13',
                    'num' => '9',
                    'by1' => 'bimalka',
                    'date1' => '2020-05-23 15:00:57',
                ],
                [
                    'id' => '14',
                    'num' => '10',
                    'by1' => 'bimalka',
                    'date1' => '2020-05-23 15:00:57',
                ],
            ],
        ];

        $response = $this->withHeaders($this->authHeaders)->postJson('/api/datalayer/general', $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
            'message' => 'success',
        ]);

        $this->assertDatabaseHas('vehicles', ['num' => '9']);
        $this->assertDatabaseHas('vehicles', ['num' => '10']);
    }

    /**
     * Scenario 3: Incremental Update on existing vehicle (PDF Page 2)
     */
    public function test_scenario_3_update_existing_vehicle(): void
    {
        $this->createVehicleJsonToDbApi();

        // 1. Initial Insert
        $initialPayload = [
            'header' => [
                'apiName' => 'json_to_db',
                'tableName' => 'vehicles',
                'last_added_time' => '2023-03-04 15:01:00',
                'last_updated_time' => '2023-03-04 15:01:00',
                'last_deleted_time' => '2023-03-04 15:00:00',
                'total_count' => '1',
            ],
            'data' => [
                [
                    'id' => '1',
                    'num' => '9',
                    'by1' => 'bimalka',
                    'date1' => '2020-05-23 15:00:57',
                ],
            ],
        ];
        $this->withHeaders($this->authHeaders)->postJson('/api/datalayer/general', $initialPayload);

        $this->assertDatabaseHas('vehicles', [
            'vehicle_id' => 1,
            'added_by' => 'bimalka',
        ]);

        // 2. Incremental Update with newer last_updated_time
        $updatePayload = [
            'header' => [
                'apiName' => 'json_to_db',
                'tableName' => 'vehicles',
                'last_added_time' => '2023-03-04 15:01:00',
                'last_updated_time' => '2023-03-05 10:00:00', // Newer updated time
                'last_deleted_time' => '2023-03-04 15:00:00',
                'total_count' => '1',
            ],
            'data' => [
                [
                    'id' => '1',
                    'by1' => 'bimalka_updated',
                    'date1' => '2026-09-10 10:00:00',
                ],
            ],
        ];

        $updateResponse = $this->withHeaders($this->authHeaders)->postJson('/api/datalayer/general', $updatePayload);

        $updateResponse->assertStatus(200);
        $updateResponse->assertJson([
            'status' => true,
            'message' => 'updated successfully',
        ]);

        // Verify vehicle was updated in the DB
        $this->assertDatabaseHas('vehicles', [
            'vehicle_id' => 1,
            'added_by' => 'bimalka_updated',
            'added_date' => '2026-09-10 10:00:00',
        ]);
    }

    /**
     * Scenario 3: Incremental Delete on existing vehicle (PDF Page 2)
     */
    public function test_scenario_3_delete_existing_vehicle(): void
    {
        $this->createVehicleJsonToDbApi();

        // 1. Initial Insert
        $initialPayload = [
            'header' => [
                'apiName' => 'json_to_db',
                'tableName' => 'vehicles',
                'last_added_time' => '2023-03-04 15:01:00',
                'last_updated_time' => '2023-03-04 15:01:00',
                'last_deleted_time' => '2023-03-04 15:00:00',
                'total_count' => '1',
            ],
            'data' => [
                [
                    'id' => '1',
                    'num' => '9',
                    'by1' => 'bimalka',
                    'date1' => '2020-05-23 15:00:57',
                ],
            ],
        ];
        $this->withHeaders($this->authHeaders)->postJson('/api/datalayer/general', $initialPayload);

        $this->assertDatabaseHas('vehicles', ['vehicle_id' => 1]);

        // 2. Incremental Delete with newer last_deleted_time
        $deletePayload = [
            'header' => [
                'apiName' => 'json_to_db',
                'tableName' => 'vehicles',
                'last_added_time' => '2023-03-04 15:01:00',
                'last_updated_time' => '2023-03-04 15:01:00',
                'last_deleted_time' => '2023-03-05 12:00:00', // Newer deleted time
                'total_count' => '0',
            ],
            'data' => [
                [
                    'id' => '1',
                ],
            ],
        ];

        $deleteResponse = $this->withHeaders($this->authHeaders)->postJson('/api/datalayer/general', $deletePayload);

        $deleteResponse->assertStatus(200);
        $deleteResponse->assertJson([
            'status' => true,
            'message' => 'deleted successfully',
        ]);

        // Verify vehicle was deleted from DB
        $this->assertDatabaseMissing('vehicles', ['vehicle_id' => 1]);
    }

    /**
     * Scenario 1: DB To DB Sync (PDF Page 1 & Pages 5-6)
     * From ma_sub_contractor to acc_sub_contractor
     */
    public function test_scenario_1_db_to_db_first_time_and_up_to_date(): void
    {
        $category = Category::create(['description' => 'SubContractor']);

        // Configure SyncApi for DB to DB as specified in PDF pages 5-6
        $syncApi = SyncApi::create([
            'api_name' => 'subcontractor_db_to_db',
            'category_id' => $category->id,
            'from_db' => 'sqlite',
            'to_db' => 'sqlite',
            'from_added_query' => 'SELECT Sub_Contractor_Code as id, Sub_Contractor_Code, Sub_Contractor_Name, Sub_Contractor_Address, Mobile, add_by, add_date, edit_by, edit_date, SubCon_Type, Attn_Person FROM ma_sub_contractor WHERE add_date > {{AddedDate}}',
            'to_added_query' => 'INSERT INTO acc_sub_contractor(Sub_Contractor_Code, Sub_Contractor_Name, Sub_Contractor_Address, Mobile, add_by, add_date, edit_by, edit_date, SubCon_Type, Attn_Person) VALUES ({{Sub_Contractor_Code}}, {{Sub_Contractor_Name}}, {{Sub_Contractor_Address}}, {{Mobile}}, {{add_by}}, {{add_date}}, {{edit_by}}, {{edit_date}}, {{SubCon_Type}}, {{Attn_Person}})',
            'from_Updated_query' => 'SELECT Sub_Contractor_Code as id, Sub_Contractor_Code, Sub_Contractor_Name, Sub_Contractor_Address, Mobile, add_by, add_date, edit_by, edit_date, SubCon_Type, Attn_Person FROM ma_sub_contractor WHERE edit_date > {{UpdatedDate}}',
            'to_Updated_query' => 'UPDATE acc_sub_contractor SET Sub_Contractor_Name = {{Sub_Contractor_Name}}, Sub_Contractor_Address = {{Sub_Contractor_Address}} WHERE Sub_Contractor_Code = {{id}}',
            'last_added_time_query' => 'SELECT MAX(add_date) FROM ma_sub_contractor limit 1',
            'last_updated_time_query' => 'SELECT MAX(edit_date) FROM ma_sub_contractor limit 1',
            'total_count_query' => 'SELECT COUNT(Sub_Contractor_Code) FROM ma_sub_contractor',
            'last_deleted_time_query' => "SELECT MAX(delete_date) FROM database_log WHERE table_name = 'ma_sub_contractor'",
            'status_to_aync_api_detail' => 0,
            'wait_till_confirmation' => 0,
            'active_status' => 1,
        ]);

        // Insert initial record in source table
        DB::table('ma_sub_contractor')->insert([
            'Sub_Contractor_Code' => 'SC-100',
            'Sub_Contractor_Name' => 'Original Subcontractor',
            'Sub_Contractor_Address' => '100 Galle Road',
            'Mobile' => '0771234567',
            'add_by' => 'system',
            'add_date' => '2023-01-01 10:00:00',
            'edit_by' => 'system',
            'edit_date' => '2023-01-01 10:00:00',
            'SubCon_Type' => 'General',
            'Attn_Person' => 'Mr. Silva',
        ]);

        // 1. First time DB-to-DB sync
        $response = $this->withHeaders($this->authHeaders)->postJson('/api/datalayer/general', [
            'apiName' => 'subcontractor_db_to_db',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
            'message' => 'success',
        ]);

        // Check target acc_sub_contractor has the record
        $this->assertDatabaseHas('acc_sub_contractor', [
            'Sub_Contractor_Code' => 'SC-100',
            'Sub_Contractor_Name' => 'Original Subcontractor',
        ]);

        // Check 3 records added to sync_api_detail (added, updated, deleted)
        $this->assertDatabaseHas('sync_api_detail', ['main_id' => $syncApi->main_id, 'type' => 1]);
        $this->assertDatabaseHas('sync_api_detail', ['main_id' => $syncApi->main_id, 'type' => 2]);
        $this->assertDatabaseHas('sync_api_detail', ['main_id' => $syncApi->main_id, 'type' => 3]);

        // 2. Second call with no changes should return "you are up to date"
        $secondResponse = $this->withHeaders($this->authHeaders)->postJson('/api/datalayer/general', [
            'apiName' => 'subcontractor_db_to_db',
        ]);

        $secondResponse->assertStatus(200);
        $this->assertEquals('you are up to date', $secondResponse->json());
    }

    /**
     * Scenario 2: DB To JSON (PDF Page 1)
     */
    public function test_scenario_2_db_to_json(): void
    {
        $category = Category::create(['description' => 'Export']);

        DB::table('vehicles')->insert([
            'vehicle_id' => 99,
            'num' => 'CAR-99',
            'added_by' => 'admin',
            'added_date' => '2026-09-08 12:00:00',
        ]);

        SyncApi::create([
            'api_name' => 'vehicles_db_to_json',
            'category_id' => $category->id,
            'from_db' => 'sqlite',
            'to_db' => null, // empty to_db means DB to JSON
            'from_added_query' => 'SELECT num, added_by FROM vehicles WHERE vehicle_id = 99',
            'to_added_query' => '{"num": {{num}}, "added_by": {{added_by}}}',
            'Prefix' => '{"vehicles": [',
            'Suffix' => ']}',
            'last_added_time_query' => 'SELECT MAX(added_date) FROM vehicles',
            'last_updated_time_query' => 'SELECT MAX(added_date) FROM vehicles',
            'total_count_query' => 'SELECT COUNT(vehicle_id) FROM vehicles',
            'last_deleted_time_query' => 'SELECT null',
            'status_to_aync_api_detail' => 0,
            'wait_till_confirmation' => 0,
            'active_status' => 1,
        ]);

        $response = $this->withHeaders($this->authHeaders)->postJson('/api/datalayer/general', [
            'apiName' => 'vehicles_db_to_json',
        ]);

        $response->assertStatus(200);
        $data = $response->json();
        $this->assertArrayHasKey('vehicles', $data);
        $this->assertEquals('CAR-99', $data['vehicles'][0]['num']);
    }
}
