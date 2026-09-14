# Cirrus API Management & Data Sync System (Laravel Edition)

A modern, high-performance migration of the **apiman_v1** CodeIgniter 3 platform to **Laravel 12 / PHP 8.2+**.

---

## Key Features

1. **Multi-Database Incremental Synchronization Engine**:
   - High-throughput synchronization between databases and JSON pipelines (`DB-to-DB`, `DB-to-JSON`, `JSON-to-DB`).
   - Dynamic query templating with placeholder replacement (`{{id}}`, `{{col_name}}`, `{{AddedDate}}`).
   - Audit trail and status logging with `sync_api_detail` (Success, Waiting, Failed).
2. **REST API & Legacy CodeIgniter Compatibility**:
   - HTTP Basic Authentication middleware matching CodeIgniter's `REST_Controller` and `Middle_layer`.
   - Backward-compatible URL mapping: `/api/datalayer/general`, `/api/general`, `/welcome/enter`, `/welcome/dashboard`, etc.
   - Transparent legacy password verification (supports existing MD5 / plaintext passwords with automatic migration to Bcrypt on login).
3. **Web Management Portal**:
   - Modern Bootstrap 4.6 responsive interface with FontAwesome icons.
   - Sync API definition builder with dynamic AJAX category creation modal.
   - API Configurations CRUD (base URL, authentication headers, query parameters, method).
   - Database Configurations CRUD (multi-connection active groups, drivers, hostnames).
   - Real-time synchronization dashboard with filtering, search, and pagination.
   - Company profile registration module.

---

## Directory Structure & Architecture

```
apiman_laravel/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/
│   │   │   │   └── DatalayerController.php  # Replaces Datalayer.php REST API
│   │   │   ├── ApiConfigurationController.php
│   │   │   ├── AuthController.php            # Web login, register, logout
│   │   │   ├── CompanyController.php
│   │   │   ├── DashboardController.php       # Sync logs dashboard
│   │   │   ├── DbConfigurationController.php
│   │   │   └── SyncApiController.php         # Sync API pipeline builder
│   │   └── Middleware/
│   │       └── ApiBasicAuth.php              # Replaces Middle_layer::my_login
│   ├── Models/
│   │   ├── ApiConfiguration.php
│   │   ├── Attendance.php
│   │   ├── Business.php
│   │   ├── Category.php
│   │   ├── Client.php
│   │   ├── Company.php
│   │   ├── DbConfiguration.php
│   │   ├── ExpenseCategory.php
│   │   ├── Leave.php
│   │   ├── Payable.php
│   │   ├── PaymentApplication.php
│   │   ├── SubContractor.php
│   │   ├── SyncApi.php
│   │   ├── SyncApiDetail.php
│   │   ├── User.php                          # Compatible with tbl_users / sys_users
│   │   └── VehicleCost.php
│   └── Services/
│       └── SyncEngineService.php             # Core logic from Common_model.php
├── config/
│   └── database.php                          # Configured with mysql, mysql_finance, mysql_pms, mysql_vehicle
├── database/
│   └── migrations/                           # Migrations for all core & business tables
├── resources/
│   └── views/                                # Blade templates (layouts, auth, sync_api, configs, dashboard)
├── routes/
│   ├── api.php                               # REST API endpoints
│   └── web.php                               # Web portal & legacy compatibility routes
└── tests/
    └── Feature/
        └── ApiManTest.php                    # 14 automated unit & feature tests
```

---

## Setup & Running

### 1. Requirements
- PHP >= 8.2
- MySQL / MariaDB (or XAMPP)
- Composer

### 2. Environment Configuration
Edit `.env` inside `apiman_laravel`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=api_man
DB_USERNAME=root
DB_PASSWORD=

# Additional database connections:
DB_FINANCE_DATABASE=smsl_finance_sqm
DB_PMS_DATABASE=pms_demo
DB_VEHICLE_DATABASE=vehicle
```

### 3. Run Database Migrations
```bash
php artisan migrate
```

### 4. Run Automated Tests
```bash
php artisan test
```

### 5. Launch the Development Server
```bash
php artisan serve
```
Access the application at: `http://localhost:8000`

---

## API Endpoints (Basic Auth Protected)

| Method | URL | Description |
|---|---|---|
| `POST` | `/api/datalayer/general` | Multi-database and JSON synchronization |
| `POST` | `/api/datalayer/subcontractor` | Register subcontractor |
| `POST` | `/api/datalayer/clientreg` | Register client |
| `POST` | `/api/datalayer/clientbill` | Record client billing |
| `POST` | `/api/datalayer/payable` | Record accounts payable |
| `POST` | `/api/datalayer/project-register` | Register project |
| `GET`  | `/api/datalayer/expense-categories` | List expense categories |
| `GET`  | `/api/datalayer/expense-sub-categories/{id}` | List expense subcategories |
| `POST` | `/api/datalayer/daily-attendance` | Record daily attendance |
| `POST` | `/api/datalayer/vehicle-cost` | Record vehicle cost |
| `POST` | `/api/datalayer/apply-leave` | Apply employee leave |
