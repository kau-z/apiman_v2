<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\SyncApi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ApiManTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_renders(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Cirrus API');
    }

    public function test_user_registration_and_login(): void
    {
        $response = $this->post('/register', [
            'username' => 'testuser',
            'password' => 'secret123',
            'email' => 'test@example.com',
            'full_name' => 'Test User',
        ]);

        $response->assertRedirect(route('sync-api.create'));
        $this->assertAuthenticated();

        // Check user exists in tbl_users
        $this->assertDatabaseHas('tbl_users', [
            'username' => 'testuser',
            'email' => 'test@example.com',
        ]);
    }

    public function test_legacy_md5_password_login(): void
    {
        // Insert user with raw MD5 password as was stored in CodeIgniter
        $user = User::create([
            'username' => 'ci_user',
            'password' => md5('password123'),
            'email' => 'ci@example.com',
            'status' => 1,
        ]);

        $response = $this->post('/login', [
            'username' => 'ci_user',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('sync-api.create'));
        $this->assertAuthenticatedAs($user);

        // Verify that the password was automatically rehashed to Bcrypt
        $user->refresh();
        $this->assertTrue(Hash::check('password123', $user->password));
    }

    public function test_authenticated_dashboard_access(): void
    {
        $user = User::create([
            'username' => 'admin_user',
            'password' => Hash::make('password123'),
            'status' => 1,
        ]);

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('API Synchronization Dashboard');
    }

    public function test_sync_api_create_page_renders(): void
    {
        $user = User::create([
            'username' => 'admin_user',
            'password' => Hash::make('password123'),
            'status' => 1,
        ]);

        $response = $this->actingAs($user)->get('/sync-api/create');
        $response->assertStatus(200);
        $response->assertSee('Create New Sync API');
    }

    public function test_sync_api_creation(): void
    {
        $user = User::create([
            'username' => 'admin_user_2',
            'password' => Hash::make('password123'),
            'status' => 1,
        ]);

        $category = Category::create(['description' => 'Accounting']);

        $response = $this->actingAs($user)->post('/sync-api', [
            'api_name' => 'test_sync_api',
            'category_id' => $category->id,
            'from_db' => 'mysql_pms',
            'to_db' => 'mysql_finance',
            'from_added_query' => 'SELECT * FROM source',
            'to_added_query' => 'INSERT INTO target VALUES ({{id}})',
        ]);

        $response->assertRedirect(route('sync-api.index'));
        $this->assertDatabaseHas('sync_api', [
            'api_name' => 'test_sync_api',
            'from_db' => 'mysql_pms',
        ]);
    }

    public function test_sync_api_creation_with_empty_to_db(): void
    {
        $user = User::create([
            'username' => 'admin_user_db_to_json',
            'password' => Hash::make('password123'),
            'status' => 1,
        ]);

        $category = Category::create(['description' => 'Vehicle']);

        // Test submitting form with empty to_db (like in DB-to-JSON api)
        $response = $this->actingAs($user)->post('/sync-api', [
            'api_name' => 'vehicle_read_test',
            'category_id' => $category->id,
            'from_db' => 'mysql_vehicle',
            'to_db' => '', // or null
            'from_added_query' => 'SELECT * FROM vehicles',
        ]);

        $response->assertRedirect(route('sync-api.index'));
        $this->assertDatabaseHas('sync_api', [
            'api_name' => 'vehicle_read_test',
            'from_db' => 'mysql_vehicle',
        ]);
    }

    public function test_api_basic_auth_unauthorized(): void
    {
        $response = $this->postJson('/api/datalayer/magiclight', [
            'username' => 'john',
            'password' => '1234',
        ]);

        $response->assertStatus(401);
    }

    public function test_api_basic_auth_authorized(): void
    {
        // Admin:1234 is configured in rest_valid_logins compatibility
        $response = $this->withHeaders([
            'Authorization' => 'Basic ' . base64_encode('admin:1234'),
        ])->postJson('/api/datalayer/magiclight', [
            'username' => 'john',
            'password' => 'secret',
        ]);

        $response->assertStatus(201);
        $response->assertJson([
            'user name' => 'john',
            'password' => 'secret',
        ]);
    }

    public function test_api_subcontractor_creation(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Basic ' . base64_encode('admin:1234'),
        ])->postJson('/api/datalayer/subcontractor', [
            'Sub_Contractor_Code' => 901,
            'Sub_Contractor_Name' => 'Acme Engineering',
            'Sub_Contractor_Address' => '123 Main Street',
            'Attn_Person' => 'John Doe',
        ]);

        $response->assertStatus(201);
        $response->assertJson([
            'message' => 'record added successfully.',
        ]);

        $this->assertDatabaseHas('acc_sub_contractor', [
            'Sub_Contractor_Code' => 901,
            'Sub_Contractor_Name' => 'Acme Engineering',
        ]);
    }

    public function test_api_client_and_billing(): void
    {
        $authHeader = ['Authorization' => 'Basic ' . base64_encode('admin:1234')];

        // 1. Register Client
        $clientRes = $this->withHeaders($authHeader)->postJson('/api/datalayer/clientreg', [
            'Client_Id' => 'CLI-001',
            'Client_Name' => 'Test Client Org',
        ]);
        $clientRes->assertStatus(201);
        $this->assertDatabaseHas('ma_client', ['Client_Id' => 'CLI-001']);

        // 2. Register Project
        $projRes = $this->withHeaders($authHeader)->postJson('/api/datalayer/project_register', [
            'project_name' => 'Project Alpha',
            'PMS_project_id' => 'PMS-100',
        ]);
        $projRes->assertStatus(201);
        $this->assertDatabaseHas('acc_business', ['PMS_project_id' => 'PMS-100']);

        // 3. Client Bill
        $billRes = $this->withHeaders($authHeader)->postJson('/api/datalayer/clientbill', [
            'bill_date' => '2026-09-08',
            'Client_ID_PMS' => 'CLI-001',
            'Project_id' => 'PMS-100',
            'amount' => 50000.00,
            'retention_amount' => 5000.00,
            'particulars' => 'Phase 1 development',
        ]);
        $billRes->assertStatus(201);
        $this->assertDatabaseHas('payment_application', [
            'Client_ID_PMS' => 'CLI-001',
            'amount' => 50000.00,
        ]);
    }

    public function test_api_daily_attendance(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Basic ' . base64_encode('admin:1234'),
        ])->postJson('/api/datalayer/daily_attendance', [
            'Project_Code' => 'PRJ-1',
            'Employee_Code' => 'EMP-007',
            'Date' => '2026-09-08',
            'Time' => '08:30:00',
            'Machine' => 'Biometric-1',
            'Location' => 'HQ',
            'Row' => '1',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('acc_attendence', [
            'Employee_Code' => 'EMP-007',
            'Date' => '2026-09-08',
        ]);
    }

    public function test_api_configurations_crud(): void
    {
        $user = User::create([
            'username' => 'admin_user',
            'password' => Hash::make('password123'),
            'status' => 1,
        ]);

        // Create
        $response = $this->actingAs($user)->post('/api-configurations', [
            'name' => 'Payment API',
            'base_url' => 'https://api.payment.com',
            'auth_type' => 'bearer',
            'auth_token' => 'sample_token',
        ]);
        $response->assertRedirect(route('api-configurations.index'));
        $this->assertDatabaseHas('api_configurations', [
            'name' => 'Payment API',
            'auth_type' => 'bearer',
        ]);
    }

    public function test_db_configurations_crud(): void
    {
        $user = User::create([
            'username' => 'admin_user',
            'password' => Hash::make('password123'),
            'status' => 1,
        ]);

        // Create
        $response = $this->actingAs($user)->post('/db-configurations', [
            'active_group' => 'mysql_warehouse',
            'active_record' => 'TRUE',
            'hostname' => '127.0.0.1',
            'database' => 'warehouse_db',
            'username' => 'root',
            'password' => '',
            'dbdriver' => 'mysqli',
            'pconnect' => 'FALSE',
            'db_debug' => 'TRUE',
            'cache_on' => 'FALSE',
            'char_set' => 'utf8',
            'dbcollat' => 'utf8_general_ci',
            'autoinit' => 'TRUE',
            'stricton' => 'FALSE',
        ]);
        $response->assertRedirect(route('db-configurations.index'));
        $this->assertDatabaseHas('db_configurations', [
            'active_group' => 'mysql_warehouse',
            'database' => 'warehouse_db',
        ]);
    }
}

